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
        <CreateBrand :$this="this" :parent="parent"></CreateBrand>
    </span>
</template>

<script>
// Components
import { mapActions, mapGetters } from "vuex";

import CreateBrand from "./includes/brands/CreateeComponent.vue";

import Loading from "vue-loading-overlay";

// Custom Script
import Form from "./../shared/form.js";

import toast from "./../helpers/toast.js";


export default {
  mixins: [toast],

  props: ["$this", "parent"],

  components: {
    CreateBrand,
  },  

  data: function() {

        return {

            newSpecializationCategories: [],

            modal: {
                createBrand: {
                    name: "brand_add",

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
                id: "",
                brand_name: "",
                brandCategories: [],
                website: "",
                logo: "",
                isDefault: 0,
                action: ""
            }),

            formData: new FormData(),
        };
    },


    methods: {

        ...mapActions("organization", ["get_organization"]),
        ...mapActions("jobs", ["get_jobs", "get_job_categories", "delete_job_description", "get_filtered_job_professions"]),

        onCreate() {
            this.loadSpecializationCategories();
            console.log(this.newSpecializationCategories);
            this.modal.createBrand.isVisible = true;
        },

        loadSpecializationCategories() {
            let params = {
                api_token: this.$user.api_token,
                locale: this.$i18n.locale,
            };

            this.get_job_categories(params).then(_ => {
                let mapSpecializationCategories = this.categories.map(s => ({
                    category: s.category,
                    category_name: s.category_name,
                    created_at: s.created_at,
                    id: s.id,
                    is_english: s.is_english,
                    is_german: s.is_german,
                    is_italian: s.is_italian,
                    is_loading: s.is_loading,
                    updated_at: s.updated_at,
                }));
                this.newSpecializationCategories = mapSpecializationCategories;
            });
        },
    }, 

    computed: {
        ...mapGetters("organization", {
        // categories: "categories",
            responseStatus: "get_response_status"
        }),

        ...mapGetters({
            specializations: "jobs/get_job_items",
            categories: "jobs/get_job_categories",
            jobStatus: "jobs/get_job_status"
        }),
    }
};
</script>

<style lang="scss">
</style>
