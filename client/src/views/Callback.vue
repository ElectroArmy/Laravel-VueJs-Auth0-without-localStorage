<template>
    <div class="callback">Callback</div>
</template>

<script>
import auth0Client from "@/auth";
import axios from "axios";
import Flash from "@/helpers/flash";
export default {
  name: "callback",
  async created() {
    await auth0Client.handleAuthentication();
    Flash.setSuccess("You have successfully signed in.");

    let form = {
      nickname: auth0Client.getProfile().nickname,
      name: auth0Client.getProfile().name,
      sub: auth0Client.getProfile().sub
    };
    let error = {};

    axios({
      method: "post",
      url: "http://localhost:8000/api/adduser",
      data: form
    })
      .then(res => {
        if (res.data) {
          this.$router.push("/");
        }
      })
      .catch(err => {
        if (err.response.status === 422) {
          error = err.response.data.errors;
          console.log(error);
        }
      });
  }
};
</script>
