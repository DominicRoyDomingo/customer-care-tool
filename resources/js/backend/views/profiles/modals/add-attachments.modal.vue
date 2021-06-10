<template>
  <b-modal
    id="add-attachments-modal"
    @hide="$bvModal.hide('add-attachments-modal')"
    hide-header
    hide-footer
    size="lg"
  >
    <v-app id="attachments__modal">
      <v-card>
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{ $t("label.add_attachments") }}
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
              @click="$bvModal.hide('add-attachments-modal')"
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
                {{ $t("label.existing_attachments") }}
              </v-tab>
              <v-tab-item>
                <attachmentsDiv
                  v-for="(attachment, index) in new_attachments_info"
                  v-bind:key="'attachment_' + index"
                  :root_parent="parent"
                  :parent="vm"
                  :index="attachment.index"
                  :is_deletable="true"
                  ref="attachments"
                ></attachmentsDiv>
                <p class='text-danger' v-if="is_required">Please select first your attachment</p>
                <div class="row">
                  <div class="col-md-12">
                    <v-btn
                      tile
                      block
                      color="success lighten-1"
                      @click.prevent="parent.addAttachment"
                    >
                      <v-icon>mdi-book-account</v-icon>&nbsp;
                      {{ $t("label.add_attachment") }}
                    </v-btn>
                  </div>
                </div>
              </v-tab-item>
              <v-tab-item>
                <attachmentsDisplay
                  v-for="(attachment, index) in old_attachments"
                  v-bind:key="'attachment_' + index"
                  :root_parent="parent"
                  :display_array="old_attachments"
                  :parent="vm"
                  :index="attachment.index"
                  :is_editable="false"
                  ref="attachments-display"
                ></attachmentsDisplay>
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
              @click="$bvModal.hide('add-attachments-modal')"
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
import attachmentsDiv from "./../components/attachment_div.vue";
import attachmentsDisplay from "./../components/attachment_display.vue";

export default {
  name: "add_attachments_modal",
  props: ["parent"],

  data() {
    return {
      loading: true,
      vm: this,
      valid: true,
      attachments: [],
      is_required : false
    };
  },

  components: {
    attachmentsDiv,
    attachmentsDisplay,
  },

  computed: {
    ...mapGetters("profile", {
      responseStatus: "get_response_status",
    }),

    old_attachments() {
      let filtered = [];
      this.parent.form.attachments_info.forEach((data, index) => {
        data.index = index;
        if (data.id != undefined) filtered.push(data);
      });

      return filtered;
    },

    new_attachments_info() {
      let filtered = [];
      this.is_required = false
      let vm = this;
      this.parent.form.attachments_info.forEach((data, index) => {
        data.index = index;
        vm.attachments.push(index);
        if (data.id == undefined) filtered.push(data);
      });
      return filtered;
    },
  },

  methods: {
    ...mapActions("profile", ["post_form_data_to_action_route"]),

    hide() {
      this.$emit("hide");
    },

    onSave() {
      this.parent.btnloading = true;

      let params = {
        api_token: this.$user.api_token,
        action: "Add",
        action_route: "/add_attachments",
        profile_id: this.parent.form.profile_id,
        attachments: this.new_attachments,
      };

      let formData = new FormData();
      
      formData.append("api_token", this.$user.api_token);
      formData.append("action", "Add");
      formData.append("action_route", "/add_attachments");
      formData.append("profile_id", this.parent.form.profile_id);

      var filter = this.parent.form.attachments.filter((item, index) => {
          if(item != null){
              return item
          }
      })

      this.is_required = false
      if(!filter.length){
        this.is_required = true
        this.parent.btnloading = false;
        return
      }

      let vm = this;
      if(!this.attachments.length){

          return false
      }
      else if (vm.new_attachments_info.length > 0){
        formData.append(
          "attachments_info",
          JSON.stringify(vm.new_attachments_info)
        );
        
        vm.new_attachments_info.forEach(function(item, index) {
          
          formData.append("attachments_" + index, vm.parent.form.attachments[item.index]);
        });
      }

      this.post_form_data_to_action_route(formData)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("add-attachments-modal");
          resp = resp.data;
          if (resp.responseStatus) {
            //Add callback function here if necessary
              
            console.log("response true");

            let notification_message = this.$t("toasts.added_attachment");
            notification_message = notification_message.replace(
              /%variable%/g,
              this.parent.itemSelected.profile_name
            );

            let message = {
              create: notification_message,
              update:
                this.$t("updated_message1") +
                this.$t("label.client_profile") +
                ` ID: ${this.parent.form.profile_id}` +
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

            this.parent.addedAttachmentsSuccessfully(resp.attachments);
          }
        })
        .catch((error) => {
          console.log("error");
          console.log(error);
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnloading = false;
        });
    },
  },
};
</script>
