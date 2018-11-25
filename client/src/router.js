import Vue from 'vue'
import Router from 'vue-router'
import PhotoIndex from './views/Index.vue'
import Callback from '@/views/Callback.vue'
import PhotoShow from '@/views/Show.vue'
import PhotoForm from '@/views/Form.vue'
import Forbidden from '@/views/Forbidden.vue';
import NotFound from '@/views/NotFound.vue';
import Bookmark from '@/views/Bookmark.vue';

import check from '@/check'

Vue.use(Router)

check.initialize()

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,

  routes: [
    {
      path: '/',
      name: 'home',
      component: PhotoIndex
    },
    {
      path: '/callback',
      name: 'callback',
      component: Callback

    },

    {
      path: '/photos/create', component: PhotoForm, meta: { mode: 'create' },
      beforeEnter: (to, from, next) => {

        if (check.state.isAuthenticated) { // if authenticated allow access
          next()
        } else {
          window.location.href = "/"

        }
      }
    },

    {
      path: '/photos/:id/edit', component: PhotoForm, meta: { mode: 'edit' },
      beforeEnter: (to, from, next) => {

        if (check.state.isAuthenticated) { // if authenticated allow access
          next()
        } else {
          window.location.href = "/"

        }
      }
    },

    {
      path: '/photos/:id', component: PhotoShow
    },

    {
      path: '/photos', component: PhotoIndex
    },

    {
      path: '/bookmark', component: Bookmark,
      beforeEnter: (to, from, next) => {

        if (check.state.isAuthenticated) { // if authenticated allow access
          next()
        } else {
          window.location.href = "/"

        }
      }
    },

    { path: '/forbidden', component: Forbidden },


    { path: '*', component: NotFound },

  ]
})
