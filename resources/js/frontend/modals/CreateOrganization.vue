<template>
    <ValidationObserver ref="observer">
        <v-layout row justify-center>
            <v-dialog v-model="parent.createOrgModal" persistent max-width="600px">
                 <v-snackbar
                    v-if="parent.createOrgModalSnackbar == true"
                    v-model="parent.snackbar"
                    :color="parent.snackbarColor"
                    top
                    class="snackbar__container"
                >
                    <span v-html="parent.snackbarMessage"></span>
                    <v-btn color="white" text @click="parent.snackbar = false">
                    Close
                    </v-btn>
                </v-snackbar>
                <v-card dark>
                <v-card-title>
                    <span class="headline">Create Organization</span>
                </v-card-title>
                <v-card-text>
                    <v-container grid-list-md>
                    <v-layout wrap>
                        <v-flex xs12>
                            <ValidationProvider
                                v-slot="{ errors }"
                                name="Name"
                                rules="required|max:191"
                            >
                                <v-text-field
                                v-model="parent.org_name"
                                label="Name"
                                name="org_name"
                                id="org_name"
                                append-icon="mdi-account-circle"
                                outlined
                                rounded
                                required
                                dark
                                filled
                                :counter="191"
                                :error-messages="errors"
                                class="form__input-field"
                                ></v-text-field>
                            </ValidationProvider>
                            <!-- <v-text-field 
                                label="Name*" required
                                v-model="parent.org_name"
                            ></v-text-field> -->
                        </v-flex>
                        <v-flex xs12>
                            <ValidationProvider
                                v-slot="{ errors }"
                                name="Location"
                                rules="required"
                            >
                                <v-text-field
                                    v-model="parent.org_location"
                                    label="Location"
                                    name="org_location"
                                    id="org_location"
                                    append-icon="mdi-account-circle"
                                    outlined
                                    rounded
                                    required
                                    dark
                                    filled
                                    :error-messages="errors"
                                    class="form__input-field"
                                ></v-text-field>
                            </ValidationProvider>
                        </v-flex>
                        <v-flex xs12>
                            <ValidationProvider
                                v-slot="{ errors }"
                                name="Organization Category"
                                rules="required"
                            >
                                <v-select
                                    v-model="parent.org_category"
                                    :items="parent.organizationCategories"
                                    item-text="category"
                                    item-value="id"
                                    label="Organization Category"
                                    :menu-props="{light: true}"
                                    :error-messages="errors"
                                    outlined
                                    rounded
                                    dark
                                    filled
                                ></v-select>
                            </ValidationProvider>
                        </v-flex>
                        <v-flex xs12>
                            <ValidationProvider
                                v-slot="{ errors }"
                                name="Other Information"
                                rules="required"
                            >
                                <v-text-field
                                v-model="parent.org_other_info"
                                label="Other Information"
                                name="org_other_info"
                                id="org_other_info"
                                append-icon="mdi-account-circle"
                                outlined
                                rounded
                                required
                                dark
                                filled
                                :error-messages="errors"
                                class="form__input-field"
                                ></v-text-field>
                            </ValidationProvider>
                        </v-flex>
                    </v-layout>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn 
                        rounded
                        color="yellow darken-1"
                        @click="closeOrgModal"
                    >Close</v-btn>
                    <v-btn 
                        rounded
                        color="yellow darken-1"
                    @click="submitRegister"
                    >Save</v-btn>
                </v-card-actions>
                </v-card>
            </v-dialog>
        </v-layout>
    </ValidationObserver>
</template>

<script>
// require("../../plugins/vee-validate.js");
import { ValidationObserver, ValidationProvider } from "vee-validate";

export default {
    props: ['parent'],
    data: () => ({
        selectedOrganization: '',
        snackbar: false,
        snackbarMessage: "",
        snackbarColor: "primary",
    }),
    components: {
        ValidationProvider,
        ValidationObserver,
    },
    methods: {
        closeOrgModal() {
            this.parent.createOrgModal = false
            this.parent.createOrgModalSnackbar = false;
        },
        submitRegister() {
            this.$refs.observer.validate().then((formIsValid) => {
                if (formIsValid) {
                    this.parent.org_selected = ''
                    this.parent.createOrgModalSnackbar = false;
                    this.parent.finishRegister()
                }
            });
        },
    },
};
</script>
