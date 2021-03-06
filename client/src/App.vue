<template>
  <div id="app" class="container">
    <div id="nav" class="navbar">
      <div class="navbar_brand">
        <router-link to="/">Awesome Photos</router-link>
      </div>
      <ul class="navbar__list">
				<li class="navbar__item" v-if="guest" style="cursor:pointer;">

          <a @click="signIn()">LOGIN/REGISTER</a>
				</li>

        <li class="navbar__item" v-if="auth">
					<router-link to="/"><b>Welcome {{authState.user.nickname}}</b></router-link>
				</li>

        <li class="navbar__item" v-if="auth">
					<router-link to="/">ALL PHOTOS</router-link>
				</li>

				<li class="navbar__item" v-if="auth">
					<router-link to="/photos/create">ADD PHOTO</router-link>
				</li>

        <li class="navbar__item" v-if="auth">
					<router-link to="/bookmark">MY BOOKMARKS</router-link>
				</li>

				<li class="navbar__item" v-if="auth" style="cursor:pointer;">
					<a @click.stop="signOut()">LOGOUT</a>
				</li>
			</ul>

    </div>
    <div class="flash flash__success" v-if="flash.success">
			  {{flash.success}}
		</div>
    <div class="flash flash__error" v-if="flash.error">
      {{flash.error}}
    </div>
    <router-view/>
  </div>
</template>

<script>
import Flash from "@/helpers/flash";
import auth0Client from "@/auth";
import check from "@/check";
import { interceptors } from "@/helpers/interceptor";
export default {
  created() {
    check.initialize();

    // global error http handler
    interceptors(err => {
      if (err.response.status === 500) {
        Flash.setError(err.response.statusText);
      }
      if (err.response.status === 404) {
        this.$router.push("/not-found");
      }
      if (err.response.status === 403) {
        this.$router.push("/forbidden");
      }
    });
  },
  data() {
    return {
      flash: Flash.state,
      authState: check.state
    };
  },

  computed: {
    auth() {
      if (this.authState.isAuthenticated) {
        // console.log(this.authState.api_token)
        // console.log(this.authState.access_token)
        return true;
      }
      return false;
    },
    guest() {
      return !this.auth;
    }
  },

  methods: {
    signIn() {
      auth0Client.signIn();
    },

    async signOut() {
      await auth0Client.signOut();
    }
  }
};
</script>

<style>
@import url(https://fonts.googleapis.com/css?family=Nunito);
* {
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}

body {
  margin: 0;
  font-size: 14px;
  font-family: helvetica;
  background: #ebeef3;
  color: #616161;
}

a {
  text-decoration: none;
  color: #616161;
}

img {
  display: block;
  width: 100%;
}

.container {
  margin-bottom: 50px;
}

.navbar {
  background: #fff;
  padding: 15px 25px;
  -webkit-box-shadow: rgba(0, 0, 0, 0.09) 0 2px 0;
  box-shadow: rgba(0, 0, 0, 0.09) 0 2px 0;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -ms-flex-pack: justify;
  justify-content: space-between;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
}

.navbar__brand a {
  color: #e91e63;
  font-weight: bold;
}

.navbar__list {
  margin: 0;
  padding: 0;
}

.navbar__item {
  display: inline;
  margin-left: 25px;
}

.form {
  background: #fff;
  -webkit-box-shadow: rgba(0, 0, 0, 0.09) 0 1px 0;
  box-shadow: rgba(0, 0, 0, 0.09) 0 1px 0;
  width: 30%;
  margin: 30px auto;
  padding: 15px;
}

.form__group {
  margin-top: 15px;
}

.form__group label {
  display: block;
  margin-bottom: 5px;
}

.form__control {
  font-size: 14px;
  width: 100%;
  display: block;
  background: #fafafa;
  border: none;
  border-radius: 2px;
  -webkit-box-shadow: inset 0 1px 1px 0 rgba(0, 0, 0, 0.1);
  box-shadow: inset 0 1px 1px 0 rgba(0, 0, 0, 0.1);
  outline: none;
  padding: 8px;
}

.form__control:focus {
  background: rgba(225, 245, 254, 0.31);
}

.form__margin {
  margin-right: 10px;
}

.form__description {
  height: 152px;
}

.btn {
  border: none;
  border-radius: 3px;
  font-size: 13px;
  height: 30px;
  padding: 8px 16px;
  position: relative;
  text-shadow: none;
  vertical-align: top;
  cursor: pointer;
  background: #f4f4f4;
  color: #484746;
  -webkit-box-shadow: 0 1px lightgray;
  box-shadow: 0 1px lightgray;
  outline: none;
  text-decoration: none;
  display: inline-block;
  line-height: 1.15;
}

.btn:active {
  -webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
  box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
}

.btn[disabled] {
  opacity: 0.75;
  cursor: not-allowed;
}

.btn__primary {
  background: #3aa3e3;
  color: #fff;
  -webkit-box-shadow: 0 1px #1b81bf;
  box-shadow: 0 1px #1b81bf;
}

.btn__danger {
  background: #e75650;
  color: #fff;
  -webkit-box-shadow: 0 1px #d7251e;
  box-shadow: 0 1px #d7251e;
}

.error__control {
  color: #e53935;
}

.error__bg {
  background: #ffcdd2 !important;
}

.flash {
  width: 50%;
  margin: 0 auto;
  margin-top: 15px;
  padding: 15px;
  border-radius: 2px;
}

.flash__success {
  background: #a5d6a7;
  border: 1px solid #81c784;
  color: #1b5e20;
}

.flash__error {
  background: #ef9a9a;
  border: 1px solid #e57373;
  color: #b71c1c;
}

.photo__header {
  -webkit-box-shadow: rgba(0, 0, 0, 0.09) 0 1px 0;
  box-shadow: rgba(0, 0, 0, 0.09) 0 1px 0;
  background: #fff;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -ms-flex-pack: justify;
  justify-content: space-between;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  padding: 0px 15px;
  margin-top: 10px;
}

.photo__list {
  width: 960px;
  margin: 0 auto;
  padding-top: 25px;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
}

.photo__item {
  width: 33.333%;
}

.photo__inner {
  display: block;
  -webkit-box-shadow: rgba(0, 0, 0, 0.09) 0 1px 0;
  box-shadow: rgba(0, 0, 0, 0.09) 0 1px 0;
  background: #fff;
  margin: 10px;
}

.photo__name {
  padding: 10px;
  margin: 0px;
}

.photo__show {
  width: 960px;
  margin: 0 auto;
  padding: 15px;
}

.photo__row {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
  margin-top: 25px;
}

.photo__box {
  -webkit-box-shadow: rgba(0, 0, 0, 0.09) 0 1px 0;
  box-shadow: rgba(0, 0, 0, 0.09) 0 1px 0;
  background: #fff;
  padding: 15px;
}

.photo__title {
  color: #e91e63;
}

.photo__image {
  width: 40%;
}

.photo__details {
  width: 60%;
}

.photo__details_inner {
  margin-left: 25px;
  -webkit-box-shadow: rgba(0, 0, 0, 0.09) 0 1px 0;
  box-shadow: rgba(0, 0, 0, 0.09) 0 1px 0;
  background: #fff;
  padding: 15px;
}

.photo__ingredients {
  width: 40%;
}

.photo__ingredients ul {
  margin: 0;
  padding: 0;
}

.photo__ingredients li {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -ms-flex-pack: justify;
  justify-content: space-between;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  padding: 5px 0px;
  border-top: 1px dashed #e9e9e9;
}

.photo__directions {
  width: 60%;
}

.photo__directions ul {
  padding: 0;
}

.photo__directions li {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -ms-flex-pack: justify;
  justify-content: space-between;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  border-top: 1px dashed #e9e9e9;
}

.photo__directions_inner {
  margin-left: 25px;
  -webkit-box-shadow: rgba(0, 0, 0, 0.09) 0 1px 0;
  box-shadow: rgba(0, 0, 0, 0.09) 0 1px 0;
  background: #fff;
  padding: 15px;
}

.photo__form {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -ms-flex-pack: distribute;
  justify-content: space-around;
  margin-bottom: 10px;
}

.image__upload {
  height: 257px;
  background: #fafafa;
  border: 1px dashed rgba(0, 0, 0, 0.1);
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
}

.image__preview {
  position: relative;
}

.image__close {
  position: absolute;
  right: 0;
  top: 0;
}

.not__found {
  -webkit-box-shadow: rgba(0, 0, 0, 0.09) 0 1px 0;
  box-shadow: rgba(0, 0, 0, 0.09) 0 1px 0;
  background: #fff;
  padding: 15px;
  width: 960px;
  margin: 30px auto;
  text-align: center;
}

.loader {
  border: 16px solid #f3f3f3; /* Light grey */
  border-top: 16px solid #3498db; /* Blue */
  border-radius: 50%;
  width: 120px;
  height: 120px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
