<template>
  <b-modal id="reason-description-modal" hide-footer hide-header size="md">
    <v-app id="brand__container">
      <v-card>
        <v-card-title class="title blue-grey lighten-4 text--secondary">
          <div>
            {{ this.title }}
          </div>
          <v-spacer></v-spacer>
          <v-btn
            icon
            color="error lighten-2"
            @click="$bvModal.hide('reason-description-modal')"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-container fluid>
             <span v-html="this.description"></span>
          </v-container>
        </v-card-text>
        <v-card-actions class="modal__footer blue-grey lighten-5">
          <v-spacer></v-spacer>
          <v-btn color="error" text tile @click="$bvModal.hide('reason-description-modal')">
            Close
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-app>
  </b-modal>
</template>

<script>

import { mapActions, mapGetters } from "vuex";

import toast from "./../../../helpers/toast.js";
export default {
  props: ["this", "title", "description"],
  data() {
    return {
        
    }
  }
};
</script>
