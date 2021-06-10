<template>
  <b-modal
    id="add-contacts-modal"
    @show="onShow"
    @hide="onHide"
    @ok="handleOk"
    hide-header
    hide-footer
    size="lg"
  >
    <v-app id="contacts__modal">
      <v-card>
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{ $t("label.add_contacts") }}
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
              @click="onHideClick"
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
                {{ $t("label.existing_contacts") }}
              </v-tab>
              <v-tab-item>
                <contactsDiv
                  v-for="(contact, index) in new_contacts"
                  v-bind:key="'contact_' + index"
                  :root_parent="parent"
                  :parent="vm"
                  :index="contact.index"
                  :is_deletable="true"
                  ref="contacts"
                ></contactsDiv>
                <div class="row">
                  <div class="col-md-12">
                    <v-btn
                      tile
                      block
                      color="success lighten-1"
                      @click.prevent="parent.addContact"
                    >
                      <v-icon>mdi-book-account</v-icon>&nbsp;
                      {{ $t("label.add_contact") }}
                    </v-btn>
                  </div>
                </div>
              </v-tab-item>
              <v-tab-item>
                <contactsDisplay
                  v-for="(contact, index) in old_contacts"
                  v-bind:key="'contact_' + index"
                  :root_parent="parent"
                  :display_array="old_contacts"
                  :parent="vm"
                  :index="contact.index"
                  :is_editable="false"
                  ref="contacts-display"
                ></contactsDisplay>
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
              @click="onHideClick"
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
import contactsDiv from "./../components/contact_div.vue";
import contactsDisplay from "./../components/contact_display.vue";

export default {
  name: "add_contacts_modal",
  props: ["parent"],

  data() {
    return {
      loading: true,
      vm: this,
      valid: true,
      isHideBtn : false
    };
  },

  components: {
    contactsDiv,
    contactsDisplay,
  },

  computed: {
    ...mapGetters("profile", {
      responseStatus: "get_response_status",
    }),

    old_contacts() {
      let filtered = [];
      this.parent.form.contacts.forEach((data, index) => {
        data.index = index;
        if (data.id != undefined) filtered.push(data);
      });

      return filtered;
    },

    new_contacts() {
      let filtered = [];
      this.parent.form.contacts.forEach((data, index) => {
        data.index = index;
        if (data.id == undefined) filtered.push(data);
      });

      return filtered;
    },
  },

  methods: {
    ...mapActions("profile", ["post_to_action_route"]),
    handleOk(bvModalEvt){
        bvModalEvt.preventDefault()
    },
    onHide(bvModalEvt){
        if(!this.isHideBtn){
            bvModalEvt.preventDefault()  
        }
    },
    onShow(){
        this.isHideBtn = false
    },
    onHideClick(){
        this.isHideBtn = true
        this.$bvModal.hide('add-contacts-modal')
    },
    hide() {
      this.$emit("hide");
    },

    onSave() {
      this.parent.btnloading = true;

      let params = {
        api_token: this.$user.api_token,
        action: "Add",
        action_route: "/add_contacts",
        profile_id: this.parent.form.profile_id,
        contacts: this.new_contacts,
      };

      this.post_to_action_route(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.onHideClick()
          resp = resp.data;
          if (resp.responseStatus) {
            //Add callback function here if necessary

            let notification_message = this.$t("toasts.added_contact");
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

            this.parent.addedContactsSuccessfully(resp.contacts);
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
