<template>
  <div class="photo__list">
    <div class="loader" v-if="isProcessing == true"></div>
    <div v-if="error !== null" class="row">
      <h1>{{error}}</h1>
    </div>
    <div class="photo__item" v-for="photo in photos">
      <router-link class="photo__inner" :to="`/photos/${photo.id}`" style="margin-bottom:0px;">
        <img :src="'http://localhost:8000/images/' + photo.image" v-if="photo.image">
        <p class="photo__name">{{photo.name}}</p>
      </router-link>
    </div>
  </div>
</template>
<script type="text/javascript">
import axios from "axios";
export default {
  data() {
    return {
      photos: [],
      isProcessing: false,
      error: null
    };
  },

  created() {
    this.getPhotos();
  },

  methods: {
    getPhotos() {
      this.isProcessing = true;
      axios.get("http://localhost:8000/api/photos").then(res => {
        this.isProcessing = false;
        this.photos = res.data.photos;
        if (this.photos === undefined || this.photos.length == 0) {
          this.error = "No photos have been uploaded yet";
        }
      });
    }
  }
};
</script>
