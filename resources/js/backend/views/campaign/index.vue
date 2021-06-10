<template>
  <div class="animated fadeIn">
    <v-app id="campaign__container" light>
      <v-window touchless v-model="currentWindow">
        <v-window-item :value="0">
          <v-tabs
            show-arrows
            background-color="#F0F3F5"
            color="#2F353A"
            v-model="tab"
            center-active
            >
            <v-tab key="campaigns">
              {{ $t("label.campaigns") }}
            </v-tab>
            <v-tab
              key="campaign"
              v-if="selectedCampaign != null"
              style="background-color: #ebf4fe; color: #329ef4;"
              >
              <span>
                {{
                  this.$t("label.campaign") + ": " + selectedCampaign.campaign_name
                }}
              </span>
              <v-btn
                icon
                @click="[(selectedCampaign = null), (tab = 0)]"
                color="error"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-tab>
            
            <v-divider vertical inset></v-divider>
            <v-tab-item key="campaigns">
              <v-row>
                <v-col>
                  <div class="title font-weight-light text--secondary">
                    Emails
                  </div>
                  <div class="subheading font-weight-medium text--disabled">
                    {{ $t("label.campaigns") }}
                  </div>
                </v-col>
                <v-col>
                  <div class="float-right">
                    <v-btn @click="onCreate" tile color="success">
                      <v-icon left>mdi-flag-plus</v-icon>
                      {{ $t("button.new") }} {{ $t("label.campaign") }}
                    </v-btn>
                  </div>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="12" md="2" sm="12">
                  <div class="form-inline">
                    <label
                      class="caption font-weight-regular text--secondary"
                      for="inline-form-custom-select-pref"
                      style="margin-right: 10px"
                    >
                      {{ $t("button.show") }}
                    </label>
                    <b-form-select
                      id="inline-form-custom-select-pref"
                      class="mb-2 mr-sm-2 mb-sm-0"
                      :options="showEntriesOpt"
                      v-model="perPage"
                      style="border-radius: 0"
                    />
                    <div class="caption font-weight-regular text--secondary">
                      {{ $t("label.entries") }}
                    </div>
                  </div>
                </v-col>
                <v-spacer></v-spacer>
                <v-col cols="12" md="5" sm="12">
                  <div class="float-right">
                    <b-input-group>
                      <template v-slot:prepend>
                        <b-input-group-text
                          style="background-color: rgba(0,0,0,0.05); 
                          border-radius: 0;"
                        >
                          <v-icon size="21">mdi-magnify</v-icon>
                        </b-input-group-text>
                      </template>
                      <b-form-input
                        v-model="filter"
                        :placeholder="$t('search_here')"
                        type="search"
                        style="border-radius: 0; border-left: 0"
                      ></b-form-input>
                    </b-input-group>
                  </div>
                </v-col>
              </v-row>
              <v-row>
                <v-col>
                  <b-table
                    striped
                    show-empty
                    stacked="md"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :filter="filter"
                    :busy="isLoading"
                    :fields="fields"
                    :items="items"
                  >
                    <template v-slot:table-busy>
                      <v-fade-transition>
                        <v-overlay opacity="0.8" style="z-index: 999999 !important">
                          <v-progress-circular
                            indeterminate
                            size="80"
                            width="2"
                            color="white"
                            class="text-center"
                          ></v-progress-circular>
                        </v-overlay>
                      </v-fade-transition>
                    </template>

                    <template v-slot:cell(index)="data">
                      <div style="margin-top: 20px">
                        <sub class="body-2 text--disabled">
                          {{ data.index + 1 }}
                        </sub>
                      </div>
                    </template>

                    <template v-slot:cell(campaign_name)="data">
                      <b-link
                        class="overline"
                        @click="
                          data.item.sent_at
                            ? onViewCampaign(data.item)
                            : onSendEmail(data.item)
                        "
                        href="#"
                      >
                        <span
                          style="font-size: 16px !important"
                          v-html="listDisplay(data.item.campaign_name)"
                        >
                        </span>
                        <i
                          class="fas fa fa-spin fa-spinner"
                          v-show="data.item.isLoading"
                        ></i>
                      </b-link>
                      <div>
                        <div class="body-2 text--secondary">
                          {{ $t("label.created_by") }}:
                          <span class="body-2 text--disabled">
                            {{ data.item.creator.full_name }}
                          </span>
                        </div>
                        <div class="body-2 text--secondary">
                          {{ $t("label.edited") }}:
                          <span class="body-2 text--disabled">
                            {{ data.item.updated_at | toLocaleStringDateTime }}
                          </span>
                        </div>
                      </div>
                      <b-link
                        v-if="data.item.campaign_emails"
                        variant="link"
                        @click="viewRecipient(data.item.campaign_emails.recipients)"
                      >
                        <small>
                          <strong>{{
                            data.item.campaign_emails.recipients != null
                              ? data.item.campaign_emails.recipients.length
                              : 0
                          }}</strong>
                          {{ $t("label.recipient") }}
                        </small>
                      </b-link>
                    </template>

                    <template v-slot:cell(status)="data">
                      <v-chip
                        :color="
                          data.item.sent_at ? 'success darken-1' : 'grey darken-2'
                        "
                        style="padding: 10px 20px; min-width: 120px; margin-top: 25px;"
                        class="overline font-weight-bold text-center"
                        dark
                        outlined
                      >
                        <v-avatar left>
                          <v-icon small>
                            {{
                              data.item.sent_at
                                ? `mdi-email-check-outline`
                                : `mdi-email-edit-outline`
                            }}
                          </v-icon>
                        </v-avatar>
                        <div class="text-center">
                          {{ data.item.sent_at ? $t("sent") : $t("draft") }}
                        </div>
                      </v-chip>
                    </template>

                    <template v-slot:cell(actions)="data">
                      <v-container>
                        <b-dropdown
                          class="action__button"
                          split
                          block
                          @click="
                            data.item.sent_at
                              ? onReuseCampaign(data.item)
                              : onSendEmail(data.item)
                          "
                          :variant="data.item.sent_at ? 'info' : 'success'"
                          style="color: white"
                        >
                          <template v-slot:button-content>
                            <span v-if="data.item.sent_at">
                              <v-icon left small dark>
                                mdi-email-sync-outline
                              </v-icon>
                              <span>{{ $t("button.reuse_campaign") }} </span>
                            </span>
                            <span v-else>
                              <v-icon left small dark>
                                mdi-email-send-outline
                              </v-icon>
                              <span>{{ $t("button.send_email") }}</span>
                            </span>
                          </template>
                          <b-dropdown-item
                            @click="onEdit(data.item)"
                          >
                            {{ $t("button.edit") }}
                          </b-dropdown-item>
                          <b-dropdown-item @click="onDelete(data.item)">
                            {{ $t("button.delete") }}
                          </b-dropdown-item>
                        </b-dropdown>
                      </v-container>
                    </template>
                  </b-table>
                </v-col>
              </v-row>
              <v-row>
                <v-spacer></v-spacer>
                <b-pagination
                  v-if="!isLoading"
                  v-model="currentPage"
                  :total-rows="totalRows"
                  :per-page="perPage"
                ></b-pagination>
              </v-row>

              <CampaignModal :parent="this"></CampaignModal>
              <RecipientModal :parent="this"></RecipientModal>
              <CreateModal ref="createModal" :parent="this"></CreateModal>
              <SendModal :parent="this"></SendModal>
            </v-tab-item>

            <!-- View Campaign -->
            <v-tab-item key="campaign" v-if="selectedCampaign != null">
              <v-list two-line>
                <v-list-item-group>
                  <template
                    v-for="(item, index) in selectedCampaign.campaign_emails"
                  >
                    <v-list-item
                      :key="item.title"
                      @click="
                        item.email_template_id != null
                          ? viewEmail(item)
                          : noTemplateError()
                      "
                    >
                      <v-list-item-content>
                        <v-row>
                          <v-col>
                            <v-list-item-title
                              v-if="item.brand != null"
                              class="text-subtitle-1 text-capitalize font-weight-medium"
                            >
                              <v-list-item-avatar
                                style="box-shadow: 0 2px 1px -1px rgba(0,0,0,.2),0 1px 1px 0 rgba(0,0,0,.14),0 1px 3px 0 rgba(0,0,0,.12) !important;"
                                color="blue lighten-5"
                              >
                                <span v-if="item.brand.logo == null">
                                  {{ item.brand.brand_name.charAt(0) }}
                                </span>
                                <v-img v-else :src="item.brand.logo"></v-img>
                              </v-list-item-avatar>
                              <span v-text="item.brand.brand_name"></span>
                            </v-list-item-title>
                            <v-list-item-title v-else>
                              <span v-text="'N/A'"></span>
                            </v-list-item-title>
                          </v-col>
                        </v-row>
                        <v-list-item-subtitle
                          v-html="item.content_name"
                          style="height: 24px !important; "
                          class="text--secondary text-subtitle-2"
                        ></v-list-item-subtitle>
                      </v-list-item-content>
                      <v-list-item-action>
                        <div class="caption text-right text--disabled">
                          {{ item.created_at | toLocaleString }}
                          <br />
                          {{ item.created_at | toLocaleTime }}
                        </div>
                        <v-btn
                          v-if="item.email_template_id == null"
                          color="error"
                          fab
                          small
                          bottom
                        >
                          <v-icon small dark>
                            mdi-email-alert
                          </v-icon>
                        </v-btn>
                        <v-btn v-else color="primary" fab small bottom>
                          <v-icon small dark>
                            mdi-email-search
                          </v-icon>
                        </v-btn>
                      </v-list-item-action>
                    </v-list-item>
                    <v-divider :key="index"></v-divider>
                  </template>
                </v-list-item-group>
              </v-list>
            </v-tab-item>
          </v-tabs>

        </v-window-item>
        <v-window-item :value="1">
          <CampaignForm :parent="vm" ref="campaignForm"></CampaignForm>
        </v-window-item>
      </v-window>
      <ViewMailModal @on-send-complete="onSendComplete()"  :parent="vm" ref="viewEmailModal"></ViewMailModal>
      <CreateDialog :parent="vm" ref="createDialog"></CreateDialog>
      <SendDialog :parent="vm" ref="sendDialog"></SendDialog>
    </v-app>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import CampaignModal from "./modals/campaign.modal";
import RecipientModal from "./modals/recipients.modal";
import CreateModal from "./modals/create2.modal";
import SendModal from "./modals/send2.modal";

import ViewMailModal from "./modals/view_mail.modal";

import CampaignMixin from "./mixins/campaignMixin.js";

import CreateDialog from "./modals/create.dialog";
import SendDialog from "./modals/send.dialog";

import CampaignForm from "./forms/campaign.form";

export default {
  mixins: [CampaignMixin],
  components: {
    CampaignModal,
    RecipientModal,
    CreateModal,
    SendModal,
    ViewMailModal,
    CreateDialog,
    SendDialog,
    CampaignForm,
  },
  data() {
    return {
      vm: this,
      currentWindow: 0,
      campaignDialog: false,
      sendDialog: false,
      tiles: [
        { img: "keep.png", title: "Keep" },
        { img: "inbox.png", title: "Inbox" },
        { img: "hangouts.png", title: "Hangouts" },
        { img: "messenger.png", title: "Messenger" },
        { img: "google.png", title: "Google+" },
      ],
      isNextPage: false,
      isSendEmail: false,
      recipients: [],
      tabName: "first",
      fields: [
        {
          key: "index",
          label: "SL",
          thStyle: { width: "2%", fontSize: "15px" },
          tdClass: "text-center",
        },
        {
          key: "campaign_name",
          label: this.$t("label.campaign"),
          thClass: "text-left",
          sortable: true,
        },
        {
          key: "status",
          label: this.$t("date_status"),
          thClass: "text-center",
          tdClass: "text-md-center",
        },
        {
          key: "actions",
          label: "",
          thClass: "text-center",
          tdClass: "text-center",
          thStyle: { width: "20%" },
        },
      ],
    };
  },

  mounted() {
    // this.$bvModal.show("modal-campaign");
    // this.$bvModal.show("modal-send-campaign");
  },

  created() {
    this.loadItems();
  },

  computed: {
    ...mapGetters({
      items: "campaigns/campaign_items",
      email_templates: "email_template/templates",
      status: "campaigns/campaign_status",
      campaign_email: "campaigns/campaign_email",
    }),

    showVariables() {
      if (this.form.template != null) {
        return false;

        if (this.form.template.variables.length == 0) {
          return false;
        }
      } else {
      }

      return true;
    },
  },

  methods: {
    ...mapActions("campaigns", [
      "get_campaigns",
      "delete_campaign",
      "get_campaign_email",
    ]),
    ...mapActions("email_template", ["get_templates", "delete_template"]),

    onSendComplete(){
      this.currentWindow = 0;
    },

    async loadItems() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
      };

      this.get_campaigns(params).then((resp) => {
        this.get_templates(params).then((resp) => {
          this.isLoading = false;
        });
      });
    },

    onReuseCampaign(item) {
      this.$refs.sendDialog.resetForms();
      this.selectedItem = item;

      this.form.action = "create";
      this.form.id = item.id;
      this.form.campaign = item.campaign_name;

      // load list of brands
      this.listbrands();

      this.modal.title = this.$t("button.send_email");
      this.sendDialog = true; //Reenable this when working on the new dialog
      //this.$bvModal.show("modal-send-campaign"); //Reenable this when pushing
    },

  
    onCreate() {
      /*
        let vm = this;
        item.isLoading = true;
        console.log(item.isLoading);
        this.getCampaign(item.id).then(function() {
          vm.tab = 1;
          item.isLoading = false;
          console.log(item.isLoading);
        });
        
        
        this.campaignDialog = true; //Reenable this when working on the new dialog
        //this.$bvModal.show("modal-create-campaign"); //Reenable this when pushing
        this.$refs.createDialog.resetForms();
        this.form.sender_name = this.$user.full_name;
        this.form.sender_email = this.$user.email;

        this.modal.title = `${this.$t("button.new")} ${this.$t(
          "label.campaign"
        )}`;
        this.modal.button.name = "Next";
        this.form.action = "create";
        
        let vm = this;
        vm.tab = 2;
      */

      let vm = this;
      vm.currentWindow = 1;
    
      //this.$bvModal.show("modal-create-campaign"); //Reenable this when pushing
      setTimeout(function(){
        vm.$refs.campaignForm.resetForms();

        vm.form.id = null;
        vm.form.sender_name = vm.$user.full_name;
        vm.form.sender_email = vm.$user.email;

        vm.modal.title = `${vm.$t("button.new")} ${vm.$t(
          "label.campaign"
        )}`;
        console.log("vm.modal");
        console.log(vm.modal);
        vm.modal.button.name = "Next";
        vm.form.action = "create";
      }, 10);
    },

    viewRecipient(items) {
      if (items == null) {
        this.makeToast(
          "danger",
          this.$t("errors.error"),
          this.$t("errors.no_recipients_to_be_shown")
        );
        return false;
      }

      this.isSendEmail = false;
      this.recipients = items;

      if (items.length > 0) {
        this.modal.title = this.$t("label.recipients");

        this.$bvModal.show("modal-recipient");
      }
    },

    onSendEmail(item) {
      this.selectedItem = item;

      this.form.id = item.id;
      this.form.campaign = item.campaign_name;
      this.form.action = "send";

      // load list of brands
      this.listbrands();

      this.modal.title = this.$t("button.send_email");
      this.sendDialog = true;
      //this.$bvModal.show("modal-send-campaign");
    },

    onViewCampaign(item) {
      let vm = this;
      item.isLoading = true;
      console.log(item.isLoading);
      this.getCampaign(item.id).then(function() {
        vm.tab = 1;
        item.isLoading = false;
        console.log(item.isLoading);
      });
    },



    setRecipient(recipients) {
      if (recipients.length < 1) {
        return "";
      }
      this.recipientDisabled = false;
      let items = [];
      recipients.forEach((recip) => {
        items.push(recip.profile);
      });
      return items;
    },

    onEdit(item) {
      this.selectedItem = item;

      this.form.id = item.id;
      this.form.campaign = item.campaign_name;
      this.form.action = "update";

      this.modal.title = this.$t("button.edit") + `: ${item.campaign_name}`;
      this.modal.button.name = this.$t("update");

      this.$bvModal.show("modal-campaign");
    },

    onDelete(item) {
      let template = item;
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
          `${item.campaign_name}` +
          vm.$t("from") +
          vm.$t("label.campaigns") +
          vm.$t("records"),
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
                item.is_loading = true;
                let params = {
                  api_token: vm.$user.api_token,
                  id: item.id,
                };

                vm.delete_campaign(params)
                  .then((resp) => {
                    item.is_loading = false;
                    if (vm.status) {
                      vm.loadItems();
                      vm.makeToast(
                        "danger",
                        vm.$t("delete_record"),
                        `${item.campaign_name}` +
                          vm.$t("delete_record_message") +
                          vm.$t("from") +
                          vm.$t("label.campaigns") +
                          vm.$t("records")
                      );
                    }
                  })
                  .catch((error) => {
                    vm.form.errors.record(error.response.data.errors);

                    vm.makeToast(
                      `danger`,
                      vm.$t("inused_alert"),
                      vm.$t("unable_message1") +
                        `${item.campaign_name}` +
                        vm.$t("unable_message2"),
                      `${item.campaign_name} ` +
                        vm.$t("delete_record_message") +
                        vm.$t("from") +
                        vm.$t("label.campaigns") +
                        vm.$t("records")
                    );

                    item.is_loading = false;
                  });
              } else {
                item.is_loading = false;
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
    },

    listDisplay(array) {
      let trans = "";
      let spliter = "";
      spliter = array.split("(")[0];
      trans = array.split("(")[1];
      if (trans !== undefined) {
        if (trans.length > 0) {
          spliter = spliter + '<small style="color:red">(' + trans + "</small>";
          array = spliter;
        }
      }
      return array;
    },

    viewEmail(email, canSend = false) {
      let vm = this;
      this.show_campaign_email(email.id).then(function() {
        vm.$refs.viewEmailModal.dialog = true;
        vm.$refs.viewEmailModal.dynamicPart = vm.campaign_email.preview;
        vm.$refs.viewEmailModal.canSend = canSend;
      });
    },

    async show_campaign_email(email_id) {
      let params = {
        api_token: this.$user.api_token,
        id: email_id,
      };

      await this.get_campaign_email(params);
    },

    noTemplateError() {
      let vm = this;

      vm.makeToast(
        "danger",
        vm.$t("errors.error"),
        vm.$t("errors.email_sent_without_template")
      );
    },
  },
};
</script>

<style>
.action__button button {
  border-radius: 0;
  text-transform: uppercase;
  font-size: 12px;
  margin-top: 13px;
}
.action__button button:nth-child(odd) {
  opacity: 0.8;
}
.action__button button:nth-child(even) {
}
</style>
