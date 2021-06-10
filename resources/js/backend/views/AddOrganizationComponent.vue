<template>
    <span>
        <v-btn
            tile
            small
            elevation="1"
            width="45" 
            height="45"
            dark
            color="rgb(255, 255, 255, 0.2)"
            @click="onCreate()"
            style="min-width: 45px; border-radius: 5px !important;"
            >
            <v-icon>
            mdi-plus-circle-outline
            </v-icon>
        </v-btn>
        <!-- <v-btn
            tile
            small
            elevation="0"
            text
            @click="onCreate()"
        >
        <v-icon left>
            mdi-plus
        </v-icon>
        Add New
        </v-btn> -->
        <CreateOrganization :$this="this" :parent="parent"></CreateOrganization>
    </span>
</template>

<script>
// Components
import { mapActions, mapGetters } from "vuex";

import CreateOrganization from "./includes/organization/CreateComponent.vue";

import Loading from "vue-loading-overlay";

// Custom Script
import Form from "./../shared/form.js";

import toast from "./../helpers/toast.js";


export default {
  mixins: [toast],

  props: ["$this", "parent"],

  components: {
    CreateOrganization,
  },  

  data: function() {

        return {

            modal: {
                create: {
                    name: "workspace_create_new",

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
                org_name: "",
                isDefault: 0,
                logo: "",
            }),

            formData: new FormData(),
        };
    },


    methods: {

        ...mapActions("organization", ["get_organization"]),

        onCreate() {
            this.modal.create.isVisible = true;
        }
    }, 

    computed: {
        ...mapGetters("organization", {
        // categories: "categories",
        responseStatus: "get_response_status"
        }),

    }
};
</script>

<style lang="scss">
</style>
