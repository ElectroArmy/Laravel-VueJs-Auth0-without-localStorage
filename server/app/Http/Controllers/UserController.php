<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Photo;

class UserController extends Controller
{
    public function adduser(Request $request) {
        
        $user = User::where( 'sub', $request->sub )->first();
        
        if(!$user) {

            $user = new User();

            $user->nickname = $request->nickname;
            $user->name = $request->name;
            $user->sub = $request->sub;

            $user->save();

            return response()
                ->json([
                    'status' => 200
                ], 200);  

        }

        return response()
            ->json([
                'message' => 'User exists'
            ]); 
    
        
    }

    public function getUser(Request $request) {
        
        $user = User::where( 'sub', $request->sub )->first();
        
        if ($user)
        {           
            $photo = Photo::with(['user'])
                ->findOrFail((int)$request->photo_id);          
            $exists = $photo->users->contains($user->sub);

            if($exists == false) 
            {
                return response()
                    ->json([
                    'photo' => $photo,
                    'bookmarked' => 'Bookmark This Image',
                            
                ]);
            }
        
        
            return response()
                ->json([
                    'photo' => $photo,
                    'bookmarked' => 'Bookmarked'
                ]);
        
        }

        

    }
}
