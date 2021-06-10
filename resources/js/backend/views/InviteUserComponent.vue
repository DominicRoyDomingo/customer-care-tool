<template>
    <span>
    <a class="dropdown-item" href="#" @click="onCreate()">Invite user</a>
      <InviteUser :$this="this"></InviteUser>
    </span>
</template>

<script>
// Components
import InviteUser from "./includes/invite-user/InviteComponent.vue";

import Loading from "vue-loading-overlay";

// Custom Script
import Form from "./../shared/form.js";

import toast from "./../helpers/toast.js";

import {
  fetchList,
  softDelete,
  fetchNextStatus,
  getLastStatus,
  fetchUsers,
} from "./../api/content.js";

export default {
  mixins: [toast],

  components: {
    InviteUser,
  },  

  data: function() {

        return {

        modal: {
            create: {
                name: "invite_user_title",

                isVisible: false,

                button: {
                    save: "save_button",

                    cancel: "cancel",

                    new: "content_add_new_button",

                    loading: false,
                },
            },
        },

        form: new Form({
            email: "",
        }),

        formData: new FormData(),
        };
    },


    methods: {
        onCreate() {
            this.modal.create.isVisible = true;
        }
    }, 

    mounted() {
        // conola
    },
};
</script>

<style lang="scss">
</style>
