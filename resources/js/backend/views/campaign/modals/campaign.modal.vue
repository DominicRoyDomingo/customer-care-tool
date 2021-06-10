<template>
  <div>
    <b-modal
      size="lg"
      no-close-on-backdrop
      hide-footer
      hide-header
      id="modal-campaign"
    >
      <v-app id="templates__modal">
        <v-card>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{ parent.modal.title }}
              <span
                class="body-1 blue-grey lighten-4 text--disabled font-weight-light"
              >
              </span>
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="$bvModal.hide('modal-campaign')"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <b-form class="p-2" @submit.prevent="onSubmit">
              <v-row>
                <v-spacer></v-spacer>
                <v-col cols="3">
                  <label class="mr-sm-2" for="inline-form-custom-select-pref">
                    {{ $t("label.language") }}
                  </label>
                  <b-form-select
                    id="inline-form-custom-select-pref"
                    class="mb-2 mr-sm-2 mb-sm-0"
                    v-model="parent.language"
                    :options="parent.languageOptions"
                  />
                </v-col>
              </v-row>
              <v-row>
                <v-col>
                  <b-form-group
                    id="input-group-1"
                    :label="$t('label.campaign_name')"
                    label-for="campaign"
                  >
                    <b-form-input
                      id="campaign"
                      name="campaign"
                      v-model="parent.form.campaign"
                      type="text"
                      :placeholder="$t('required')"
                      autocomplete="off"
                    ></b-form-input>
                    <small
                      v-if="parent.form.errors.has('campaign')"
                      class="text-danger"
                      v-text="parent.form.errors.get('campaign')"
                    />
                  </b-form-group>
                </v-col>
              </v-row>
            </b-form>
          </v-card-text>
          <v-divider style="margin-bottom: 0"></v-divider>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-spacer></v-spacer>
            <v-btn
              color="error"
              text
              tile
              @click="$bvModal.hide('modal-campaign')"
            >
              {{ $t("cancel") }}
            </v-btn>
            <v-btn color="success" tile dark @click="onUpdate">
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
                  <v-icon left>mdi-text-box-plus</v-icon>
                  {{ $t("button.save") }}
                </div>
                <div v-else>
                  <v-icon left>mdi-file-document-edit</v-icon>
                  {{ parent.modal.button.name }}
                </div>
              </div>
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-app>
    </b-modal>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
export default {
  props: ["parent"],

  data() {
    return {
      isSendEmail: false,
      isSave: true,
      showRecipient: false,
      tabName: "first",
      showForm: true,
      button: {
        isEmail: false,
        isSave: false,
      },
    };
  },

  computed: {
    ...mapGetters({
      campaign: "campaigns/campaign",
      status: "campaigns/campaign_status",
    }),
  },

  methods: {
    ...mapActions("campaigns", ["post_campaign_email", "post_campaign"]),

    onNext() {
      this.parent.btnLoading = true;

      let params = {
        api_token: this.$user.api_token,
        campaign: this.parent.form.campaign,
        locale: this.$i18n.locale,
        type: "create",
      };

      this.post_campaign(params)
        .then((resp) => {
          if (this.campaign) {
            this.parent.form.id = this.campaign.id;
            this.parent.isNextPage = true;
            this.parent.btnLoading = false;
            this.parent.loadItems();
            this.parent.listbrands();
          }
        })
        .catch((error) => {
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnLoading = false;
        });
    },

    onUpdate() {
      this.parent.btnLoading = true;

      let params = {
        api_token: this.$user.api_token,
        id: this.parent.form.id,
        campaign: this.parent.form.campaign,
        locale: this.parent.language,
        type: "update",
      };

      this.post_campaign(params)
        .then((resp) => {
          if (this.campaign) {
            this.parent.btnLoading = false;
            this.parent.loadItems();
            this.parent.makeToast(
              "success",
              this.$t("record_updated"),
              this.$t("updated_message1") +
                this.$t("label.campaign") +
                `ID: ${this.parent.form.id}` +
                this.$t("updated_message2")
            );
            this.$bvModal.hide("modal-campaign");
          }
        })
        .catch((error) => {
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnLoading = false;
        });
    },

    handleClick() {},

    onAddRecipient() {
      this.showRecipient = true;
      this.parent.form.sent = true;
      this.isSendEmail = true;
      this.isSave = false;
    },

    onCancel() {
      this.parent.isNextPage = false;
      this.$bvModal.hide("modal-campaign");
    },

    onCancelRecipient() {
      this.showRecipient = false;
      this.parent.form.recipient = "";
      this.parent.form.sent = false;
      this.isSendEmail = false;
      this.isSave = true;
    },

    setDataList() {
      return {
        id: this.parent.form.id,
        api_token: this.$user.api_token,
        campaign: this.parent.form.campaign,
        recipient: this.parent.form.recipient,
        subject: this.parent.form.subject,
        content: this.parent.form.content,
        sender_name: this.parent.form.sender_name,
        sender_email: this.parent.form.sender_email,
        brand: this.parent.form.brand.id,
        locale: this.$i18n.locale,
      };
    },

    onSubmit() {
      this.button.isSave = true;
      let params = {
        ...this.setDataList(),
        sent: this.parent.form.sent,
        isEmailed: false,
      };

      this.post_campaign_email(params)
        .then((resp) => {
          this.button.isSave = false;

          this.$bvModal.hide("modal-campaign");

          if (this.status) {
            this.parent.loadItems();
            this.parent.makeToast(
              "success",
              this.$t("new_record_created")`${this.parent.form.campaign}` +
                this.$t("created_message") +
                this.$t("label.campaign")
            );

            this.parent.form.reset();
            this.parent.recipients = [];
            this.parent.isNextPage = false;
            this.showRecipient = false;
          }
        })
        .catch((error) => {
          this.parent.form.errors.record(error.response.data.errors);
          this.button.isSave = false;
        });
    },

    onSaveSend() {
      this.button.isEmail = true;

      let params = {
        ...this.setDataList(),
        sent: true,
        isEmailed: true,
      };

      this.post_campaign_email(params)
        .then((resp) => {
          this.isSendEmail = false;
          this.showRecipient = false;
          this.button.isEmail = false;
          this.$bvModal.hide("modal-campaign");

          if (this.status) {
            this.parent.loadItems();
            this.parent.makeToast(
              "success",
              this.$t("new_record_created")`${this.parent.form.campaign}` +
                this.$t("created_message") +
                this.$t("label.campaign")
            );
          }
          this.parent.form.reset();
          this.parent.recipients = [];
          this.parent.isNextPage = false;
          this.showRecipient = false;
        })
        .catch((error) => {
          this.parent.form.errors.record(error.response.data.errors);
          this.button.isEmail = false;
        });
    },
  },
};
</script>
