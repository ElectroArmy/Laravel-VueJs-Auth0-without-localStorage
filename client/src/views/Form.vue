<template>
    <div class="photo__show">
		<div class="photo__header">
			<h3>{{action}} Photo</h3>
			<div>
				<button class="btn btn__primary" @click="save" :disabled="isProcessing">Save</button>
				<button class="btn" @click="$router.back()" :disabled="isProcessing">Cancel</button>
			</div>
		</div>
		<div class="photo__row">
			<div class="photo__image">
				<div class="photo__box">
					<image-upload v-if="isProcessing == false" v-model="form.image"></image-upload>
                    <div class="loader" v-if="isProcessing == true"></div>
					<small class="error__control" v-if="error.image">{{error.image[0]}}</small>
				</div>
			</div>
			<div class="photo__details">
				<div class="photo__details_inner">
					<div class="form__group">
					    <label>Name</label>
					    <input type="text" class="form__control" v-model="form.name">
					    <small class="error__control" v-if="error.name">{{error.name[0]}}</small>
					</div>
					<div class="form__group">
					    <label>Description</label>
					    <textarea class="form__control form__description" v-model="form.description"></textarea>
					    <small class="error__control" v-if="error.description">{{error.description[0]}}</small>
					</div>
				</div>
			</div>

		</div>

	</div>
</template>

<script>
import Vue from "vue";
import Flash from "@/helpers/flash";
import { toMulipartedForm } from "@/helpers/form";
import ImageUpload from "@/components/ImageUpload";
import axios from "axios";
import check from "@/check";

export default {
  components: {
    ImageUpload
  },

  data() {
    return {
      form: {
        user: null
      },
      error: {},
      isProcessing: false,
      initializeURL: `http://localhost:8000/api/photos/create`,
      storeURL: `http://localhost:8000/api/photos`,
      action: "Create",
      authState: check.state
    };
  },
  created() {
    this.isProcessing = true;
    if (this.$route.meta.mode === "edit") {
      this.initializeURL = `http://localhost:8000/api/photos/${
        this.$route.params.id
      }/edit`;
      this.storeURL = `http://localhost:8000/api/photos/${
        this.$route.params.id
      }?_method=PUT`;
      this.action = "Update";
    }
    check.initialize().then(res => {
      axios({
        method: "get",
        url: this.initializeURL,
        headers: {
          Authorization: "Bearer " + this.authState.access_token
        }
      }).then(res => {
        this.isProcessing = false;
        Vue.set(this.$data, "form", res.data.form);
      });
    });
  },

  methods: {
    save() {
      //send logged in user details to server
      this.isProcessing = true;
      this.form.user = this.authState.user;
      const form = toMulipartedForm(this.form, this.$route.meta.mode);

      axios({
        method: "post",
        url: this.storeURL,
        data: form,
        headers: {
          Authorization: "Bearer " + this.authState.access_token
        }
      })
        .then(res => {
          if (res.data.saved) {
            Flash.setSuccess(res.data.message);
            this.$router.push(`/photos/${res.data.id}`);
          }
          this.isProcessing = false;
        })
        .catch(err => {
          if (err.response.status === 422) {
            this.error = err.response.data.errors;
          }
          if (err.response.status === 500) {
            this.error = err.response.data.errors;
          }
          this.isProcessing = false;
        });
    },

    remove(type, index) {
      if (this.form[type].length > 1) {
        this.form[type].splice(index, 1);
      }
    }
  }
};
</script>

