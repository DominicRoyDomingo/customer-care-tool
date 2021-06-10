<template>
    <div >
        <v-container>
            <v-row>
                <v-col>
                    <h4 class="text-center grey--text text--darken-3 font-weight-bold">All Workspaces</h4>
                </v-col>
            </v-row>
            <v-row justify="center">
                <v-col
                md="6"
                >
                    <p class="text-center grey--text text--lighten-1">
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas a blandit magna. Vestibulum commodo consectetur ligula vitae tempus. Integer ut arcu suscipit, porttitor elit quis, varius orci.
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas a blandit magna. Vestibulum commodo consectetur ligula vitae tempus. Integer ut arcu suscipit, porttitor elit quis, varius orci.</p>
                </v-col>
            </v-row>

            <v-row>
                <v-col md="3">
                    <v-card
                        class="mx-auto"
                        max-width="400"
                        height="220"
                    >
                        <v-list-item three-line class="text-center">
                            <v-list-item-content>
                                <v-list-item-title class="headline mb-2 mt-3">
                                    Create a workspace
                                </v-list-item-title>
                                <div class="mb-3">
                                    <v-btn
                                        fab
                                        large
                                        elevation="1"
                                        color="white"
                                        @click="onCreate()"
                                        >
                                        <v-icon color="blue">
                                        mdi-plus
                                        </v-icon>
                                    </v-btn>
                                </div>
                                <v-list-item-subtitle>e.g : Nike, Pepsi, Disney</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        
                    </v-card>
                </v-col>
                <v-col
                md="3"
                v-for="(workspace,index) in allWorkspaces"
                :key="index"
                class="mb-3"
                >
                    <v-card
                        class="mx-auto"
                        max-width="400"
                        height="220"
                    >
                    
                        <div class="workspace-offset-image">
                            <v-avatar color="blue" size="50">
                                <img
                                    :src="workspace.logo"
                                >
                            </v-avatar>
                        </div>
                        <div style="height: 38px;">
                            <v-btn
                            v-if="active_organization.organization == workspace.id"
                            x-small
                            class="ma-2"
                            outlined
                            color="#3d91e2"
                            style="pointer-events: none; background-color: #d9e0fb;"
                            >
                            Default
                            </v-btn>
                        </div>
                        <v-list-item three-line class="text-center">
                            <v-list-item-content>
                                <v-list-item-title class="headline mb-2">
                                    {{workspace.name}}
                                </v-list-item-title>
                                <div class="mb-3">
                                    <v-btn
                                        class="mx-2"
                                        elevation="1"
                                        fab
                                        dark
                                        x-small
                                        color="red"
                                        v-if="workspace.workspaceValid"
                                        @click="onDelete(workspace)"
                                    >
                                        <v-icon dark>
                                            mdi-delete
                                        </v-icon>
                                    </v-btn>
                                    <v-btn
                                        class="mx-2"
                                        elevation="1"
                                        fab
                                        x-small
                                        disabled
                                        v-else
                                    >
                                        <v-icon>
                                            mdi-delete
                                        </v-icon>
                                    </v-btn>
                                </div>
                                <v-list-item-subtitle>Created: {{workspace.created_at | moment("MMM DD, YYYY ")}}</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
        <CreateOrganization :$this="this" :parent="this"></CreateOrganization>
    </div>
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
  components: {
    CreateOrganization,
  },  
  props:['active_organization'],
  mounted() {
      this.loadWorkspaces()
  },

  data: function() {

    return {

        selectedBrand: '',

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
        
        search: "",

        searched: "",
        };
    },


    methods: {
        ...mapActions("workspace", ["get_all_workspaces", "get_workspaces", "delete_workspace"]),

        async loadWorkspaces() {
            let params = {
                api_token: this.$user.api_token
            };
            this.get_all_workspaces(params).then((_) => {
            });
            this.get_workspaces(params).then((_) => {
            });
        },

        changeWorkspace(organization) {
            window.location.href ="/admin/auth/user/switchorg/" + JSON.stringify(organization)
        },

        onCreate() {
            this.modal.create.isVisible = true;
        },

        onDelete(workspace) {
            let vm = this;
            $.confirm({
                title:
                vm.$t("confirmation_record_delete").charAt(0) +
                vm
                    .$t("confirmation_record_delete")
                    .slice(1)
                    .toLowerCase(),
                content:
                vm.$t("question_record_delete") +
                `${workspace.name.bold()}` +
                vm.$t("from") +
                vm.$t("label.workspaces") +
                vm.$t("records") + "?",
                type: "red",
                typeAnimated: true,
                columnClass: "medium",
                buttons: {
                yes: {
                    text: vm.$t("yes"),
                    btnClass:
                    "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
                    action: function(value) {
                        if (value) {
                            let params = {
                            api_token: vm.$user.api_token,
                            id: workspace.id,
                            };
                            vm.delete_workspace(params)
                            .then((resp) => {
                                if (vm.workspacesResponseStatus) {
                                    vm.loadWorkspaces()
                                    vm.makeToast(
                                        "danger",
                                        vm.$t("delete_record"),
                                        `${workspace.name}` +
                                        vm.$t("delete_record_message") +
                                        vm.$t("from") +
                                        vm.$t("label.workspaces") +
                                        vm.$t("records")
                                    );
                                }
                                
                                if(vm.workspacesResponseStatus == false) {
                                    vm.makeToast(
                                        "danger",
                                        vm.$t("delete_record"),
                                        `Can't delete ${workspace.name} from workspaces records`
                                    );
                                }
                            })
                            .catch((error) => {
                                console.log(error);
                            });
                        }
                    },
                },
                no: {
                    text: vm.$t("no"),
                    btnClass:
                    "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
                    action: function() {},
                },
                },
            });
        }
    }, 

    watch: {
    },

    computed: {

        ...mapGetters("workspace", {
            allWorkspaces: "allWorkspaces",
            workspacesResponseStatus: "get_response_status"
        }),

        ...mapGetters("organization", {
            responseStatus: "get_response_status",
        }),
        
        
    },
};
</script>
