import auth0Client from '@/auth';

export default {
  state: {
    api_token: null,
    access_token: null,
    user: null,
    isAuthenticated: null
  },
  async initialize() {
    await auth0Client.silentAuth()
    this.state.api_token = auth0Client.idToken
    this.state.access_token = auth0Client.accessToken
    this.state.user = auth0Client.getProfile()
    this.state.isAuthenticated = auth0Client.isAuthenticated()
  },

}
