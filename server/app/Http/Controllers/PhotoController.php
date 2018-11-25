<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Photo;
use App\User;
use File;

class PhotoController extends Controller
{
    public function __construct()
    {
    	$this->middleware('auth0')
    		->except(['index', 'show', 'create']);
    }


    public function index()
    {
    	$photos = Photo::orderBy('created_at', 'desc')
    		->get(['id', 'name', 'image']);
    	return response()
    		->json([
                'photos' => $photos
    		]);
    }

    public function show($id, Request $request)
    {
        $photo = Photo::findOrFail($id);

        return response()
            ->json([
            'photo' => $photo,
        ]);
    }

    public function create()
    {
        $form = Photo::form();
    	return response()
    		->json([
    			'form' => $form
    		]);
    }

    public function store(Request $request)
    {

        $this->validate($request, [
    		'name' => 'required|max:255',
    		'description' => 'required|max:3000',
    		'image' => 'required|image'
        ]);

        //Get signed in user's id
        $user = User::where('sub', $request->user['sub'])->first();

        //if user is not authenticated
        if (!$user)
        {
            return response()
    	        ->json([
                'message' => 'Unauthorized',
                'status' => 401
                ], 401);
        }

    	if(!$request->hasFile('image') && !$request->file('image')->isValid()) {
    		return abort(404, 'Image not uploaded!');
        }

    	$filename = $this->getFileName($request->image);
        $request->image->move(base_path('public/images'), $filename);

        $photo = new Photo();
        $photo->name = $request->name;
        $photo->description = $request->description;

        $photo->user_id = $user->id;

        $photo->image = $filename;

        $photo->save();

    	return response()
    	    ->json([
    	        'saved' => true,
    	        'id' => $photo->id,
                'message' => 'You have successfully created a photo!'
            ], 200);
    }

    public function edit($id, Request $request)
    {
        $id = (int)$id;

        $form = Photo::findOrFail($id);
        return response()
            ->json([
                'form' => $form
            ]);
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'description' => 'required|max:3000',
            'image' => 'image'
        ]);



        $id = (int)$id;
        $photo = Photo::findOrFail($id);

        //If photo owner is not performing this operation
        if ($photo->user->sub != $request->user['sub'] ) {

            return response()
            ->json([

                'message' => 'Forbidden the photo is not yours',
                'status' => 403
            ], 403);
        }

        $photo->name = $request->name;
        $photo->description = $request->description;
        // upload image
        if ($request->hasfile('image') && $request->file('image')->isValid()) {
            $filename = $this->getFileName($request->image);
            $request->image->move(base_path('/public/images'), $filename);
            // remove old image
            File::delete(base_path('/public/images/'.$photo->image));
            $photo->image = $filename;
        }
        $photo->save();

        return response()
            ->json([
                'saved' => true,
                'id' => $photo->id,
                'user' => $request->user,
                'message' => 'You have successfully updated a photo!',
                'status' => 200
            ], 200);
    }

    public function destroy($id, Request $request)
    {

        $id = (int)$id;
        $photo = Photo::findOrFail($id);

        //If photo owner is not performing this operation
        if ($photo->user->sub != $request->sub ) {

            return response()
            ->json([

                'message' => 'Forbidden the photo is not yours',
                'status' => 403
            ], 403);
        }

        // remove image
        File::delete(base_path('/public/images/'.$photo->image));
        $photo->users()->detach();
        $photo->delete();
        return response()
            ->json([
                'deleted' => true,
                'status' => 200,
                'sub' => $request->sub
            ], 200);
    }


    public function bookmark(Request $request)
    {

        //Get signed in user's id
        $user = User::where( 'sub', $request->sub )->first();

        $photo_id = $request->photo['id'];

        //Get photo
        $photo = Photo::find($photo_id);

        $exists = $photo->users->contains($user->id);

        if(!$exists)
        {
            $photo->users()->attach($user);

            return response()
    	        ->json([
                'bookmarked' => 'Bookmarked',
                'message' => 'You have bookmarked this image',
                'status' => 200

                ], 200);
        }

        $photo->users()->detach($user);
        return response()
    	        ->json([
                'bookmarked' => 'Bookmark This Image',
                'message' => 'You have removed the bookmark from this image',
                'status' => 200
                ], 200);

    }

    public function bookmarked_photos(Request $request)
    {

        //Get signed in user's sub
        $user = User::where('sub', $request->sub)->first();

        return response()
    	        ->json([
                'photos' => $user->images()->get(),
                'status' => 200
                ], 200);

    }


    private function getFileName($file)
    {
    	return str_random(32).'.'.$file->extension();
    }
}
