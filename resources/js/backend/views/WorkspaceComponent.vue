<template>
    <div class="navbar-workspace">
        <v-card
            class="mx-auto d-flex flex-row"
            max-width="215"
            elevation="0"
            color="#333333"
            key=3
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
    <!-- <div class="navbar-workspace">
        
          <v-menu
            key="b-xl"
            rounded="Custom"
            :offset-y="true"
            :nudge-bottom="10"
            >
            <template v-slot:activator="{ attrs, on }">
                <v-card
                    class="mx-auto d-flex flex-row px-4"
                    max-width="400"
                    elevation="0"
                    v-bind="attrs"
                    v-on="on"
                >
                    <div class="d-flex align-center" style="height: 53px">
                        <v-avatar
                            color="primary"
                            size="30"
                            tile
                        >
                            <img
                                :src="active_organization.organization_model.logo"
                            >
                        </v-avatar>
                    </div>
                    <div style="height: 53px; width: 139px; line-height: 1rem !important" class="text-md-left pl-2">
                        <span class="text-caption" style="font-size: 0.60rem !important">WORKSPACE</span><br>
                        <span>{{active_organization.organization_name}}</span>
                    </div>
                    <div class="d-flex align-center" style="height: 53px">
                        <v-icon>mdi-chevron-down</v-icon>
                    </div>
                </v-card>
            </template>
            <v-card
                class="mx-auto"
                max-width="400"
                elevation="0"
            >
                <v-text-field @click.stop hide-details="true" v-model="search">
                    <template v-slot:label>
                        <span class="pl-5">Search a workspace...</span>
                    </template>
                </v-text-field>
                <v-list color="#f5f9fc">
                    <template v-for="(workspace, index) in workspaces" >
                        <v-list-item :key='workspace.id'>
                            <v-card
                                class="mx-auto d-flex flex-row"
                                max-width="400"
                                elevation="0"
                                color="#f5f9fc"
                                @click="changeWorkspace(workspace)"
                            >
                                <div class="d-flex align-center" style="height: 53px">
                                    <v-avatar
                                        color="primary"
                                        size="30"
                                        tile
                                    >
                                        <img
                                            :src="workspace.organization_model.logo"
                                        >
                                    </v-avatar>
                                </div>
                                <div style="height: 53px; width: 139px;" class="text-md-left pl-2 d-flex align-center">
                                    <span>{{workspace.organization_name}}</span>
                                </div>
                                <div class="d-flex align-center" style="height: 53px; width: 33px">
                                    <v-icon left color="success" v-if="active_organization.organization == workspace.organization">
                                        mdi-check
                                    </v-icon>
                                </div>
                            </v-card>
                        </v-list-item>
                        
                        <v-divider
                            v-if="index < workspaces.length - 1"
                            :key="index"
                        ></v-divider>
                    </template>
                </v-list>
                <div class="border-top-workspace d-flex flex-row py-2 d-flex justify-space-between"> 
                    <create-organization-component :parent="this"></create-organization-component>
                    <div class="vl"></div>
                    <v-btn
                        tile
                        small
                        elevation="0"
                        text
                        @click="viewAll()"
                    >
                    <v-icon left>
                        mdi-format-list-bulleted
                    </v-icon>
                    View All
                    </v-btn>
                </div>
            </v-card>
        </v-menu>
    </div> -->
</template>

<script>
// Components
import { mapActions, mapGetters } from "vuex";

import SelectBrand from "./includes/providers/SelectBrandComponent.vue";

import Loading from "vue-loading-overlay";

// Custom Script
import Form from "./../shared/form.js";

import toast from "./../helpers/toast.js";


export default {
  mixins: [toast],
  props:['user_organizations','active_organization'],
  components: {
  },  

  mounted() {
      this.loadWorkspaces()
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

        search: "",

        searched: "",

        form: new Form({
        }),

        formData: new FormData(),
        };
    },


    methods: {
        ...mapActions("workspace", ["get_workspaces", "get_all_workspaces"]),

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
    },
};
</script>
