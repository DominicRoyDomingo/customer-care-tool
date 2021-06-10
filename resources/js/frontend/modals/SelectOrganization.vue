<template>
    <!-- <ValidationObserver ref="observer"> -->
        <v-layout row justify-center>
            <v-dialog v-model="parent.selectOrgModal" persistent max-width="600px">
                <v-card light>
                <v-card-title>
                    <span class="headline">Select Organization</span>
                </v-card-title>
                <v-card-text>
                    <v-container fluid>
                    <v-select
                        v-model="parent.org_selected"
                        :items="parent.organizations"
                        label="Organization"
                        :menu-props="{light: true}"
                    >
                        <template v-slot:prepend-item>
                        <v-btn 
                            class="ml-2"
                            rounded
                            color="yellow darken-1"
                            @click="addOrganization"
                        >Add Organization</v-btn>
                        <v-divider class="mt-2"></v-divider>
                        </template>
                    </v-select>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn 
                        rounded
                        color="yellow darken-1"
                        @click="parent.selectOrgModal = false"
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
    <!-- </ValidationObserver> -->
</template>

<script>
// require("../../plugins/vee-validate.js");
import { ValidationObserver, ValidationProvider } from "vee-validate";

export default {
    props: ['parent'],
    data: () => ({
    }),
    components: {
        ValidationProvider,
        ValidationObserver,
    },
    mounted() {
    },
    methods: {

        submitRegister() {
            if(this.parent.org_selected == '') this.parent.org_selected = 'none'
            this.parent.finishRegister()
        },

        addOrganization() {
            axios.get('/organizationCategories').then(resp => {
                this.parent.organizationCategories = resp.data
                this.parent.createOrgModal = true
            });
        }
    },

    computed: {
    },
};
</script>
