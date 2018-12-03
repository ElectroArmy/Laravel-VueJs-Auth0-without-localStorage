<template>
  <div class="photo__show">
    <div class="photo__row">
      <div class="photo__image">
        <div class="photo__box">
          <img :src="'http://localhost:8000/images/' + photo.image" v-if="photo.image">
        </div>
      </div>
      <div class="photo__details">
        <div class="photo__details_inner">
          <small v-if="photo_name">Submitted by: {{photo_name}}</small>
          <h1 class="photo__title">{{photo.name}}</h1>
          <p class="photo__description">{{photo.description}}</p>

          <div v-if="auth">
            <router-link
              :to="`/photos/${photo.id}/edit`"
              class="btn btn-primary"
              v-if="user_name === photo_name"
            >Edit</router-link>
            <button
              class="btn btn__danger"
              @click="remove"
              :disabled="isProcessing"
              v-if="user_name === photo_name"
            >Delete</button>
            <button class="btn btn-info" @click="bookmark" :disabled="isProcessing">{{ bookmarked }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script type="text/javascript">
import check from "@/check";
import Flash from "@/helpers/flash";
import axios from "axios";

export default {
  data() {
    return {
      authState: check.state,
      user_name: "",
      photo_name: "",
      isProcessing: false,
      photo: {
        user: {}
      },
      storeURL: `http://localhost:8000/api/bookmark`,
      bookmarked: ""
    };
  },
  created() {
    this.getPhoto();
  },
  computed: {
    auth() {
      if (this.authState.isAuthenticated) {
        this.user_name = this.authState.user.name;
        return true;
      }
      return false;
    }
  },
  methods: {
    remove() {
      this.isProcessing = true;
      //send logged in user details to server
      let form = this.authState.user;
      axios({
        method: "DELETE",
        url: `http://localhost:8000/api/photos/${this.$route.params.id}`,
        data: form,
        headers: {
          Authorization: "Bearer " + this.authState.access_token
        }
      })
        .then(res => {
          Flash.setSuccess("You have successfully deleted this photo!");
          this.$router.push("/");
        })
        .catch(function(error) {
          // handle error
          console.log(error.data);
        });
    },

    bookmark() {
      this.isProcessing = true;
      let form = {
        photo: this.photo,
        sub: this.authState.user.sub
      };
      if (this.authState.isAuthenticated) {
        axios({
          method: "post",
          url: this.storeURL,
          data: form,
          headers: {
            Authorization: "Bearer " + this.authState.access_token
          }
        }).then(res => {
          this.isProcessing = false;
          if (res.data.bookmarked) {
            this.bookmarked = res.data.bookmarked;

            Flash.setSuccess(res.data.message);
            this.$router.push(`/photos/${this.photo.id}`);
          }
        });
      } else {
        Flash.setSuccess("Please log in");
      }
    },

    getPhoto() {
      this.isProcessing = true;
      axios({
        method: "get",
        url: `http://localhost:8000/api/photos/${this.$route.params.id}`
      }).then(res => {
        this.photo = res.data.photo;

        check.initialize().then(data => {
          let form = {
            photo_id: this.$route.params.id,
            sub: this.authState.user.sub
          };
          axios({
            method: "post",
            url: `http://localhost:8000/api/getuser`,
            data: form
          }).then(res => {
            this.isProcessing = false;
            console.log(res.data.photo.user);
            if (res.data.photo.user) {
              this.photo_name = res.data.photo.user.name;
            }
            this.bookmarked = res.data.bookmarked;
          });
        });
      });
    }
  }
};
</script>
