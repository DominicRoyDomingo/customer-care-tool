<template>
  <b-modal
    id="client-engagements-modal"
    @hide="$bvModal.hide('client-engagements-modal')"
    hide-header
    hide-footer
    size="md"
    scrollable
  >
    <v-app id="client_engagements__modal">
      <v-card>
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{ $t("label.add_client_engagements") }}
              <span
                class="body-1 blue-grey lighten-4 text--disabled font-weight-light"
              >
                ({{ parent.form.profile_name }})
              </span>
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="$bvModal.hide('client-engagements-modal')"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text class="modal__content">
            <v-tabs
              show-arrows
              center-active
              grow
              background-color="blue-grey lighten-5"
              slider-color="blue-grey darken-2"
              color="blue-grey darken-2"
            >
              <v-tab class="caption font-weight-bold">
                {{ $t("label.new") }}
              </v-tab>
              <v-tab class="caption font-weight-bold">
                {{ $t("label.existing_client_engagements") }}
              </v-tab>
              <v-tab-item>
                <clientEngagementsDiv
                  v-for="(engagement, index) in new_client_engagements"
                  v-bind:key="'engagement_' + index"
                  :root_parent="parent"
                  :parent="vm"
                  :index="engagement.index"
                  :is_deletable="true"
                  ref="client-engagements"
                ></clientEngagementsDiv>
                <div class="row">
                  <div class="col-md-12">
                    <v-btn
                      tile
                      block
                      color="success lighten-1"
                      @click.prevent="parent.addClientEngagement"
                    >
                      <v-icon>mdi-thumbs-up-down</v-icon>&nbsp;
                      {{ $t("label.add_client_engagement") }}
                    </v-btn>
                  </div>
                </div>
              </v-tab-item>
              <v-tab-item>
                <clientEngagementsDisplay
                  v-for="(engagement, index) in old_client_engagements"
                  v-bind:key="'engagement_' + index"
                  :root_parent="parent"
                  :parent="vm"
                  :display_array="old_client_engagements"
                  :index="engagement.index"
                  :is_editable="false"
                  ref="client-engagements-display"
                ></clientEngagementsDisplay>
              </v-tab-item>
            </v-tabs>
          </v-card-text>
          <v-divider style="margin-bottom: 0"></v-divider>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-spacer></v-spacer>
            <v-btn
              color="error"
              text
              tile
              @click="$bvModal.hide('client-engagements-modal')"
            >
              {{ $t("cancel") }}
            </v-btn>
            <v-btn color="success" tile dark @click="onSave">
              <div v-if="parent.btnloading">
                <v-progress-circular
                  size="20"
                  width="1"
                  color="white"
                  indeterminate
                >
                </v-progress-circular>
              </div>
              <div v-else>
                <div v-if="parent.form.action == 'Add'">
                  <v-icon left>mdi-account-plus</v-icon>
                  {{ $t("button.save") }}
                </div>
                <div v-else>
                  <v-icon left>mdi-account-edit</v-icon>
                  {{ $t("button.save_change") }}
                </div>
              </div>
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-app>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import clientEngagementsDiv from "./../components/client_engagement_div.vue";
import clientEngagementsDisplay from "./../components/client_engagement_display.vue";

export default {
  name: "client_engagements_modal",
  props: ["parent"],

  data() {
    return {
      loading: true,
      vm: this,
      valid: true,
    };
  },

  components: {
    clientEngagementsDiv,
    clientEngagementsDisplay,
  },

  computed: {
    ...mapGetters("profile", {
      responseStatus: "get_response_status",
    }),

    old_client_engagements() {
      let filtered = [];
      this.parent.form.client_engagements.forEach((data, index) => {
        data.index = index;
        if (data.id != undefined) filtered.push(data);
      });

      return filtered;
    },

    new_client_engagements() {
      let filtered = [];
      this.parent.form.client_engagements.forEach((data, index) => {
        data.index = index;
        if (data.id == undefined) filtered.push(data);
      });

      return filtered;
    },
  },

  methods: {
    ...mapActions("profile", ["post_client_engagement"]),

    hide() {
      this.$emit("hide");
    },

    onSave() {
      this.parent.btnloading = true;

      let params = {
        api_token: this.$user.api_token,
        action: "Add",
        action_route: "/add_client_engagements",
        profile_id: this.parent.form.profile_id,
        client_engagements: this.new_client_engagements,
      };

      this.post_client_engagement(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("client-engagements-modal");
          resp = resp.data;
          if (resp.responseStatus) {
            let notification_message = this.$t(
              "toasts.added_client_engagement"
            );
            notification_message = notification_message.replace(
              /%variable%/g,
              this.parent.itemSelected.profile_name
            );

            let message = {
              create: notification_message,
              update:
                this.$t("updated_message1") +
                this.$t("label.client_profile") +
                ` ID: ${this.parent.form.id}` +
                this.$t("updated_message2"),
              title: {
                create: this.$t("new_record_created"),
                update: this.$t("record_updated"),
              },
            };

            this.parent.makeToast(
              "success",
              message.title.create,
              message.create
            );

            //Add callback function here if necessary
            this.parent.addedNewClientEngagementSuccessfully(
              resp.client_engagements
            );
          }
        })
        .catch((error) => {
          console.log(error);
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnloading = false;
        });
    },
  },
};
</script>
