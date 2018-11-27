<template>
	<div class="photo__list" >
    <div class="loader" v-if="isProcessing == true"></div>
    <div v-if="error !== null" class="row"><h1>You have no bookmarks</h1></div>
		<div class="photo__item" v-for="photo in photos">
			<router-link class="photo__inner" :to="`/photos/${photo.id}`" style="margin-bottom:0px;">
				<img :src="'http://localhost:8000/images/' + photo.image" v-if="photo.image">
				<p class="photo__name">{{photo.name}} </p>
			</router-link>
		</div>
	</div>
</template>

<script type="text/javascript">
import check from "@/check";
import axios from "axios";

export default {
  data() {
    return {
      photos: [],
      authState: check.state,
      isProcessing: false,
      error: null
    };
  },
  created() {
    this.isProcessing = true
    check.initialize().then(res => {
      let form = {
        sub: this.authState.user.sub
      };
      axios({
        method: "post",
        url: `http://localhost:8000/api/bookmarked`,
        data: form,
        headers: {
          Authorization: "Bearer " + this.authState.access_token
        }
      }).then(res => {
        this.isProcessing = false;
        this.photos = res.data.photos;
        if (this.photos === undefined || this.photos.length == 0) {
          this.error = "You have no bookmarks"

        }
      });
    });
  }
};
</script>
