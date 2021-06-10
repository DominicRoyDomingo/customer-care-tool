<template>
    <span>
        <a class="nav-link" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <v-badge
                :value="!unreadNotif"
                bordered
                color="#fff"
                dot
                overlap
            >
                 <v-icon dark>mdi-bell-outline</v-icon>
            </v-badge>
           
            <!-- <i class="fas fa-bell" :class="{ 'bell-badge': !unreadNotif }"></i> -->
        </a>
        <ul class="dropdown-menu dropdown-menu-right">
            <li v-for="(notification, index) in notifications" :key="notification.id">
                <a href="#" class="top-text-block notification-href" @click.stop="onRead(notification,index)" :style=" notification.read_at ? '' : 'background: #a2dbee;' ">
                    <div class="top-text-heading">You've been invited to {{notification.data['organization_name']}}</div>
                    <div class="top-text-light">{{ notification.created_at | timeago}}</div>
                </a>
                <div class="notif-buttons-wrapper" v-if="notification.isClicked" @click.stop="notification.isClicked = false;">
                    <div class="notif-buttons">
                        <b-button-group size="sm">
                            <b-button variant="primary" class="mr-2" @click.stop="confirm(notification)">{{ $t("confirm") }}</b-button>
                            <b-button variant="danger" @click.stop="decline(notification)">{{ $t("decline") }}</b-button>
                        </b-button-group>
                    </div>
                </div>
            </li>
        </ul>
        <!-- <InviteUser :$this="this"></InviteUser> -->
    </span>
</template>

<script>
// Components
import InviteUser from "./includes/invite-user/InviteComponent.vue";

import Loading from "vue-loading-overlay";

// Custom Script
import Form from "./../shared/form.js";
// var test = require('moment')
import * as moment from "moment/moment";
import toast from "./../helpers/toast.js";

import {
  readNotification,
  userNotifs,
  declineNotif,
} from "./../api/notification.js";

export default {
  props: ["user_notifications"],

  mixins: [toast],

//   components: {
//     InviteUser,
//   },  

  data: function() {

        return {

        modal: {
            Read: {
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

        notifications: [],

        unreadNotif: false,

        form: new Form({
            notif_id: "",
        }),

        formData: new FormData(),
        };
    },

    filters: {
        timeago: (value) => {
            if (!value){
                return null;
            }
            
            return moment.utc(value).local().fromNow();
        }
    },

    mounted() {
        this.loadUserNotifs();
    },

    methods: {

        loadUserNotifs(index) {
            userNotifs().then((resp) => {
                this.notifications = resp.notifs
                this.unreadNotif = resp.unreadNotif
                if(index != undefined) this.notifications[index].isClicked = true 
            });
        },

        onRead(user_notification,index) {
            let formData = new FormData();
      
            formData.append("notif_id", user_notification.id);

            readNotification(formData)
                .then((resp) => {
                    this.loadUserNotifs(index); 
            })
        },

        hideWrapper(notification) {
            notification.isClicked = false;
        },

        confirm(notification) {

            window.location = notification.data['link'];

        },

        decline(notification) {
            declineNotif(notification.id)
                .then((resp) => {
                    this.loadUserNotifs();
            })
        },
    },
};
</script>

<style lang="scss">

li {
    position: relative
}
.notif-buttons-wrapper {
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0;
    background-color: rgba(99, 194, 226, 0.7);
}
.notif-buttons {
    position: absolute;
    top: 50%;
    right: -15%;
    transform: translate(-50%, -50%);
}


</style>
