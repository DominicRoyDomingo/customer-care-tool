<template>
    <div style="display: flex">
        <div class="navbar-workspace">
            <v-card
                class="mx-auto d-flex flex-row"
                max-width="215"
                elevation="0"
                color="#333333"
                key=3
                v-if="isWorkspace == false"
            >
                <div class="pl-1 d-flex align-center" style="height: 53px">
                    <v-avatar
                        color="#54c0ff"
                        size="45"
                        tile
                        style="border-radius: 5px !important;"
                        v-if="active_organization.organization_model.logo != null"
                    >
                        <img
                            :src="active_organization.organization_model.logo"
                        >
                    </v-avatar>

                    <v-avatar
                        color="#54c0ff"
                        size="45"
                        tile
                        v-else
                        style="border-radius: 5px !important;"
                    >
                        <span class="white--text headline">{{ active_organization.organization_name.charAt(0).toUpperCase() }}</span>
                    </v-avatar>

                </div>
                <div class="text-md-left pl-1 workspace-wrapper">
                    
                    <span class="text-caption white--text" style="font-size: 0.60rem !important">WORKSPACE</span><br>
                    <span class="white--text">{{active_organization.organization_name}}</span>
                </div>
                <div class="d-flex align-center" style="height: 53px">
                    <v-btn
                        icon
                        color="#fff"
                        @click="isWorkspace = true"
                    >
                        <v-icon color="#fff">mdi-chevron-down</v-icon>
                    </v-btn>
                </div>
            </v-card>
            
            <transition name="fade"> 
                <keep-alive>
                    <v-card
                        class="d-flex flex-row pl-1"
                        elevation="0"
                        color="#333333"
                        v-if="isWorkspace"
                        style="position: absolute; z-index: 1; height: 55px; left: 0"
                    >
                        <!-- <div
                            v-scrollbar="{
                                useBothWheelAxes: true
                            }"
                            style="width: 184px;"
                        > -->
                            <div class="d-flex align-center" style="height: 55px; ">
                                <v-tooltip 
                                    bottom
                                    v-for="workspace in workspaces"
                                    :key='workspace.id'
                                    z-index="1000000000000000000000000000"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn 
                                            icon 
                                            width="auto" 
                                            height="auto"
                                            @click="changeWorkspace(workspace)"
                                            class="mr-1"
                                            v-bind="attrs"
                                            v-on="on"
                                        >
                                            <v-avatar
                                                color="#54c0ff"
                                                size="45"
                                                tile
                                                style="border-radius: 5px !important;"
                                                v-if="workspace.organization_model.logo != null"
                                            >
                                                <img
                                                    :src="workspace.organization_model.logo"
                                                >
                                            </v-avatar>

                                            <v-avatar
                                                color="#54c0ff"
                                                size="45"
                                                tile
                                                v-else
                                                style="border-radius: 5px !important;"
                                            >
                                                <span class="white--text headline">{{ workspace.organization_name.charAt(0).toUpperCase() }}</span>
                                            </v-avatar>
                                        </v-btn> 
                                    </template>
                                    <span>{{workspace.organization_name}}</span>
                                </v-tooltip>  
                                <create-organization-component :parent="this"></create-organization-component>
                            </div>
                        <!-- </div> -->
                        <div class="d-flex align-center" style="height: 53px">
                            <v-btn
                                icon
                                color="#fff"
                                @click="isWorkspace = false"
                            >
                                <v-icon color="#fff">mdi-chevron-left</v-icon>
                            </v-btn>
                        </div>
                    </v-card>
                </keep-alive>
            </transition>
        </div>
        <div class="navbar-brands" v-if="isWorkspace == false">
            <v-card
                class="px-2 mx-auto d-flex flex-row"
                max-width="221"
                elevation="0"
                v-if="isBrand == false"
                color="#2f80ed"
            >
                <div class="pl-3 d-flex align-center" style="height: 53px">
                    <v-avatar
                        color="#54c0ff"
                        size="45"
                        tile
                        style="border-radius: 5px !important;"
                        v-if="brand_session.logo != null"
                    >
                        <img
                            :src="brand_session.logo"
                        >
                    </v-avatar>

                    <v-avatar
                        color="#54c0ff"
                        size="45"
                        tile
                        v-else
                        style="border-radius: 5px !important;"
                    >
                        <span class="white--text headline">{{ brand_session != false ? brand_session.name.charAt(0).toUpperCase() : "N"}}</span>
                    </v-avatar>
                </div>
                <div class="text-md-left pl-1 workspace-wrapper">
                    
                    <span class="text-caption white--text" style="font-size: 0.60rem !important">BRAND</span><br>
                    <span class="white--text">{{brand_session != false ? brand_session.name : 'NO BRAND'}}</span>
                </div>
                <div class="d-flex align-center" style="height: 53px">
                    <v-btn
                        icon
                        color="#fff"
                        @click="isBrand = true"
                    >
                        <v-icon color="#fff">mdi-chevron-right</v-icon>
                    </v-btn>
                </div>
            </v-card>

            <transition name="fade"> 
                <keep-alive>
                    <v-card
                        class="d-flex flex-row pl-1"
                        elevation="0"
                        color="#2f80ed"
                        v-if="isBrand"
                        style="position: absolute; z-index: 1; height: 55px; left: 215px"
                    >
                        <div class="d-flex align-center" style="height: 55px; ">
                            <v-tooltip 
                                bottom
                                v-for="brand in brands"
                                :key='brand.id'
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn 
                                        icon 
                                        width="auto" 
                                        height="auto"
                                        @click="changeBrand(brand.id)"
                                        class="mr-1"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                        <v-avatar
                                            color="#54c0ff"
                                            size="45"
                                            tile
                                            style="border-radius: 5px !important;"
                                            v-if="brand.logo != null"
                                        >
                                            <img
                                                :src="brand.logo"
                                            >
                                        </v-avatar>

                                        <v-avatar
                                            color="#54c0ff"
                                            size="45"
                                            tile
                                            v-else
                                            style="border-radius: 5px !important;"
                                        >
                                            <span class="white--text headline">{{ brand.name.charAt(0).toUpperCase() }}</span>
                                        </v-avatar>
                                    </v-btn> 
                                </template>
                                <span>{{brand.name}}</span>
                            </v-tooltip>  
                            <create-brand-component :parent="this"></create-brand-component>
                        </div>
                        <div class="d-flex align-center" style="height: 53px">
                            <v-btn
                                icon
                                color="#fff"
                                @click="isBrand = false"
                            >
                                <v-icon color="#fff">mdi-chevron-left</v-icon>
                            </v-btn>
                        </div>
                        
                    </v-card>
                </keep-alive>
            </transition>
        </div>
    </div>
</template>

<script>
// Components
import { mapActions, mapGetters } from "vuex";

import Loading from "vue-loading-overlay";

// Custom Script
import Form from "./../shared/form.js";

import toast from "./../helpers/toast.js";


export default {
  mixins: [toast],
  props:['user_organizations','active_organization', 'brand_session'],
  components: {
  },  

  mounted() {
      this.loadWorkspaces()
      this.loadBrands();
  },

  data: function() {

    return {
        
        selectedBrand: '',

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

        isWorkspace: false,

        isBrand: false,

        search: "",

        searched: "",

        form: new Form({
        }),

        formData: new FormData(),
        };
    },


    methods: {
        ...mapActions("workspace", ["get_workspaces", "get_all_workspaces"]),

        ...mapActions("brand", ["get_brands"]),

        async loadBrands() {
            let params = {
                api_token: this.$user.api_token,
                org_id: this.$org_id,
            };
            this.get_brands(params).then((_) => {
            });
        },

        async loadWorkspaces() {
            let params = {
                api_token: this.$user.api_token,
                search: this.searched
            };
            this.get_workspaces(params).then((_) => {
            });
            
            if (window.location.href.indexOf("/admin/workspaces") > -1) {
                this.get_all_workspaces(params).then((_) => {
                });
            }
            
        },

        changeWorkspace(organization) {

            window.location.href ="/admin/auth/user/switchorg/" + organization.organization
        },

        viewAll() {

            window.location.href ="/admin/workspaces" 
            
        },
    

        changeBrand(brandId) {

            window.location.href = "/admin/brands/swap/" + brandId;
            
        },

        performSearch: _.debounce(function(query) {
            this.searched = query
        }, 1000)
    }, 

    watch: {
        search: {
            handler: function(value) {
                this.performSearch(value)
            }
        },

        searched: {
            handler: function(value) {
                this.loadWorkspaces()
            }
        }
    },

    computed: {

        ...mapGetters("workspace", {
            workspaces: "workspaces",
        }),

        ...mapGetters("brand", {
            brands: "brands",
        }),
    },
};
</script>
