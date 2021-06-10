<template>
  <b-modal
    id="profile-modal"
    @show="onShow"
    @hide="onHide"
    @ok="handleOk"
    size="lg"
    hide-footer
    hide-header
  >
    <v-app id="profile__modal">
      <v-card>
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <span v-if="parent.form.action == 'Add'">
              {{ $t("label.add_profile_title").replace(
              /%variable%/g, brandName
            ) }}
            </span>
            <div v-else>
              {{ $t("label.update_profile_title") }}
              <span
                class="body-1 blue-grey lighten-4 text--disabled font-weight-light"
              >
                {{
                  "(" +
                    parent.form.surname +
                    ", " +
                    parent.form.first_name +
                    ")"
                }}
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
            <v-container>
              <v-row>
                <v-col cols="9">
                  <div>
                    <p
                      :class="
                        'text-left ' + name_alerts[name_alert_state].text_class
                      "
                      v-if="name_alert_state != 0"
                    >
                      <i
                        :class="'fas fa ' + name_alerts[name_alert_state].icon"
                      >
                      </i>
                      {{ name_alerts[name_alert_state].text }}
                      <u v-if="name_alert_state == 2">
                        {{ name_matched_profile.primary_email }}
                      </u>
                    </p>
                  </div>
                </v-col>
                <v-col cols="3" style="text-align: right">
                  <div>
                    <small style="color: red; ">
                      {{ $t("label.required_hint") }}
                    </small>
                  </div>
                </v-col>
              </v-row>
              <v-container>
                <v-row>
                  <v-col cols="12" md="4" class="modal__input-container">
                    <v-text-field
                      v-model="parent.form.surname"
                      :rules="[(v) => !!v || $t('label.sn_required')]"
                      :label="$t('label.surname')"
                      suffix="*"
                      class="modal__input"
                      autocomplete="off"
                      @input="checkNameMatch"
                      required
                    >
                    </v-text-field>
                  </v-col>
                  <v-col cols="12" md="4" class="modal__input-container">
                    <v-text-field
                      v-model="parent.form.first_name"
                      :rules="[(v) => !!v || $t('label.fn_required')]"
                      :label="$t('label.first_name')"
                      suffix="*"
                      class="modal__input"
                      autocomplete="off"
                      @input="checkNameMatch"
                      required
                    >
                    </v-text-field>
                  </v-col>
                  <v-col cols="12" md="4" class="modal__input-container">
                    <v-text-field
                      v-model="parent.form.middlename"
                      :label="$t('label.middle_name')"
                      autocomplete="off"
                      class="modal__input"
                    >
                    </v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" md="6" class="modal__input-container">
                    <v-text-field
                      v-model="parent.form.nickname"
                      :label="$t('label.nickname')"
                      autocomplete="off"
                      class="modal__input"
                    >
                    </v-text-field>
                  </v-col>
                  <v-col cols="12" md="6" class="modal__input-container">
                    <v-text-field
                      v-model="parent.form.primary_email"
                      :label="$t('label.primary_email')"
                      :rules="[
                        (v) =>
                          !!v
                            ? /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test(
                                v
                              ) || $t('label.email_valid')
                            : true,
                      ]"
                      autocomplete="off"
                      class="modal__input"
                    >
                    </v-text-field>
                  </v-col>
                </v-row>
              </v-container>
              <v-card color="gray lighten-3" outlined tile>
                <v-tabs
                  v-model="tabIndex"
                  show-arrows
                  center-active
                  grow
                  background-color="blue-grey lighten-5"
                  slider-color="blue-grey darken-2"
                  color="blue-grey darken-2"
                >
                  <v-tab class="caption font-weight-bold">
                    {{ $t("label.notes") }}
                  </v-tab>
                  <v-tab class="caption font-weight-bold">
                    {{ $t("label.contacts") }}
                  </v-tab>
                  <v-tab class="caption font-weight-bold">
                    {{ $t("label.location") }}
                  </v-tab>
                  <v-tab class="caption font-weight-bold">
                    {{ $t("label.jobs") }}
                  </v-tab>
                  <v-tab class="caption font-weight-bold">
                    {{ $t("label.engagements") }}
                  </v-tab>
                  <v-tab class="caption font-weight-bold">
                    {{ $t("label.attachments") }}
                  </v-tab>
                  <v-tab-item eager>
                    <div class="row">
                      <div class="col-md-12 text-left">
                        <input
                          type="checkbox"
                          v-model="parent.form.add_notes"
                          name="add_notes"
                          id="add_notes"
                          @click="updateAddNotesStatus"
                        />
                        <label
                          class="form-check-label ml-3"
                          for="add_notes"
                          style="cursor:pointer;"
                        >
                          {{ $t("label.add_notes_now") }}
                        </label>
                      </div>
                      <div
                        id="notes_container"
                        class="col-md-12"
                        v-if="parent.form.add_notes"
                      >
                        <noteDiv
                          v-for="(note, index) in parent.form.notes"
                          v-bind:key="'note_' + index"
                          :root_parent="parent"
                          :parent="vm"
                          :index="index"
                          :is_deletable="true"
                        ></noteDiv>

                        <div class="row">
                          <div class="col-md-12">
                            <v-btn
                              tile
                              block
                              color="success lighten-1"
                              @click.prevent="parent.addNote"
                            >
                              <v-icon>mdi-sticker-plus</v-icon>&nbsp;
                              {{ $t("label.add_note") }}
                            </v-btn>
                          </div>
                        </div>
                      </div>
                    </div>
                  </v-tab-item>
                  <v-tab-item eager>
                    <div class="row">
                      <div class="col-md-12 text-left">
                        <input
                          type="checkbox"
                          v-model="parent.form.add_contacts"
                          name="add_contacts"
                          id="add_contacts"
                          @click="updateAddContactsStatus"
                        />
                        <label
                          class="form-check-label ml-3"
                          for="add_contacts"
                          style="cursor:pointer;"
                          >{{ $t("label.add_contacts_now") }}</label
                        >
                      </div>

                      <div
                        id="contacts_container"
                        class="col-md-12"
                        v-if="parent.form.add_contacts"
                      >
                        <contactDiv
                          v-for="(contact, index) in parent.form.contacts"
                          v-bind:key="'contact_' + index"
                          ref="contacts"
                          :root_parent="parent"
                          :parent="vm"
                          :index="index"
                          :is_deletable="true"
                        ></contactDiv>

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
                      </div>
                    </div>
                  </v-tab-item>
                  <v-tab-item eager>
                    <div class="row">
                      <div class="col-md-3 text-left">
                        <label for="contact_type">{{
                          $t("label.country")
                        }}</label>
                      </div>
                      <div class="col-md-9 text-center">
                        <v-select
                          @input="
                            parent.fetchProvinces(
                              parent.form.location.country_id
                            )
                          "
                          v-model="parent.form.location.country_id"
                          id="country_id"
                          label="name"
                          :reduce="function(country){
                            parent.form.selectedCountry = country.name;
                            return country.id;
                          }"
                          :options="parent.countries"
                        >
                        </v-select>
                      </div>
                    </div>
                    <div
                      class="row mt-3"
                      v-if="parent.form.location.country_id != null"
                    >
                      <div class="col-md-3 text-left">
                        <label for="contact_type">{{
                          $t("label.province")
                        }}</label>
                      </div>
                      <div class="col-md-9 text-center">
                        <v-select
                          @input="
                            parent.fetchCities(parent.form.location.province_id)
                          "
                          v-model="parent.form.location.province_id"
                          id="province_id"
                          label="name"
                          
                          :reduce="function(province){
                            parent.form.selectedProvince = province.name;
                            return province.id;
                          }"
                          
                          :options="parent.provinces"
                        >
                          <template #list-header>
                            <li style="margin-left:20px;" class="mb-2">
                              <b-link
                                href="#"
                                @click="parent.modalAddNewProvince"
                              >
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                {{ $t("label.new_province") }}
                              </b-link>
                            </li>
                          </template>
                        </v-select>
                      </div>
                    </div>
                    <div
                      class="row mt-3"
                      v-if="parent.form.location.province_id != null"
                    >
                      <div class="col-md-3 text-left">
                        <label for="contact_type">{{ $t("label.city") }}</label>
                      </div>
                      <div class="col-md-9 text-center">
                        <v-select
                          v-model="parent.form.location.city_id"
                          id="city_id"
                          label="name"
                          :reduce="function(city){
                            parent.form.selectedCity = city.name;
                            return city.id;
                          }"
                          :options="parent.cities"
                        >
                          <template #list-header>
                            <li style="margin-left:20px;" class="mb-2">
                              <b-link href="#" @click="parent.modalAddNewCity">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                {{ $t("label.new_city") }}
                              </b-link>
                            </li>
                          </template>
                        </v-select>
                      </div>
                    </div>
                  </v-tab-item>
                  <v-tab-item eager>
                    <div class="row">
                      <div class="col-md-12 text-left">
                        <input
                          type="checkbox"
                          v-model="parent.form.add_jobs"
                          name="add_jobs"
                          id="add_jobs"
                          @click="updateAddJobsStatus"
                        />
                        <label
                          class="form-check-label ml-3"
                          for="add_jobs"
                          style="cursor:pointer;"
                          >{{ $t("label.add_jobs_now") }}</label
                        >
                      </div>

                      <div
                        id="jobs_container"
                        class="col-md-12"
                        v-if="parent.form.add_jobs"
                      >
                        <jobDiv
                          v-for="(job, index) in parent.form.jobs"
                          v-bind:key="'job_' + index"
                          ref="jobs"
                          :root_parent="parent"
                          :parent="vm"
                          :index="index"
                          :is_deletable="true"
                      ></jobDiv>

                        <div class="row">
                          <div class="col-md-12">
                            <v-btn
                              tile
                              block
                              color="success lighten-1"
                              @click.prevent="parent.addJob"
                            >
                              <v-icon>mdi-briefcase</v-icon>&nbsp;
                              {{ $t("label.add_job") }}
                            </v-btn>
                          </div>
                        </div>
                      </div>
                    </div>
                  </v-tab-item>
                  <v-tab-item eager>
                    <div class="row">
                      <div class="col-md-12 text-left">
                        <input
                          type="checkbox"
                          v-model="parent.form.add_engagements"
                          name="add_engagements"
                          id="add_engagements"
                          @click="updateAddClientEngagementsStatus"
                        />
                        <label
                          class="form-check-label ml-3"
                          for="add_engagements"
                          style="cursor:pointer;"
                          >{{ $t("label.add_client_engagements_now") }}</label
                        >
                      </div>
                    </div>

                    <div
                      id="engagements_container"
                      class="col-md-12"
                      v-if="parent.form.add_engagements"
                    >
                      <clientEngagementsDiv
                        v-for="(engagement, index) in parent.form
                          .client_engagements"
                        v-bind:key="'engagement_' + index"
                        :root_parent="parent"
                        :parent="vm"
                        :index="index"
                        :is_deletable="true"
                        @hide="parent.showProfileModal"
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
                    </div>
                  </v-tab-item>
                  <v-tab-item eager>
                    <div class="row">
                      <div class="col-md-12 text-left">
                        <input
                          type="checkbox"
                          v-model="parent.form.add_attachments"
                          name="add_attachments"
                          id="add_attachments"
                          @click="updateAddAttachmentsStatus"
                        />
                        <label
                          class="form-check-label ml-3"
                          for="add_attachments"
                          style="cursor:pointer;"
                          >{{ $t("label.add_attachments_now") }}</label
                        >
                      </div>
                    </div>

                    <div
                      id="attachments_container"
                      class="col-md-12"
                      v-if="parent.form.add_attachments"
                    >
                      <attachmentDiv
                        v-for="(attachment, index) in parent.form.attachments"
                        v-bind:key="'attachment_' + index"
                        :root_parent="parent"
                        :parent="vm"
                        :index="index"
                        :is_deletable="true"
                        @hide="parent.showProfileModal"
                        ref="attachments"
                      ></attachmentDiv>

                      <div class="row">
                        <div class="col-md-12">
                          <v-btn
                            tile
                            block
                            color="success lighten-1"
                            @click.prevent="parent.addAttachment"
                          >
                            <v-icon>mdi-paperclip</v-icon>&nbsp;
                            {{ $t("label.add_attachment") }}
                          </v-btn>
                        </div>
                      </div>
                    </div>
                  </v-tab-item>
                </v-tabs>
              </v-card>
            </v-container>
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
            <v-btn
              color="success"
              tile
              :dark="!preventSubmit"
              @click="onSave"
              :disabled="preventSubmit"
            >
              <div v-if="parent.btnloading" class="text-center">
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
                  {{ $t("label.add_profile") }}
                </div>
                <div v-else>
                  <v-icon left>mdi-account-edit</v-icon>
                  {{ $t("label.update_profile") }}
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

import noteDiv from "./../components/note_div.vue";
import contactDiv from "./../components/contact_div.vue";
import jobDiv from "./../components/job_div.vue";
import clientEngagementsDiv from "./../components/client_engagement_div.vue";
import attachmentDiv from "./../components/attachment_div.vue";

function default_matched_profile_state() {
  return {
    profile: {
      full_name: "",
    },
  };
}

export default {
  props: ["parent", "isOpen", "brandName"],
  components: {
    noteDiv,
    contactDiv,
    jobDiv,
    clientEngagementsDiv,
    attachmentDiv,
  },
  data() {
    return {
      isHideBtn : false,
      loading: true,
      vm: this,
      valid: true,
      surname: null,
      firstname: null,
      middlename: null,
      nickname: null,
      email: null,
      tabIndex: 0,
      preventSubmit: false,
      is_name_unique: true,

      //name matching
      name_alerts: [
        {
          icon: "",
          text: "",
        },
        {
          icon: "fa-spin fa-spinner",
          text: "",
          text_class: "text-primary",
        },
        {
          icon: "fa-exclamation-triangle",
          text: "", //Match found
          text_class: "text-danger",
        },
        {
          icon: "fa-check",
          text: "",
          text_class: "text-success",
        },
      ],
      name_alert_state: 0,
      name_matched_profile: default_matched_profile_state(),

      //primary email matching
      primary_email_alerts: [
        {
          icon: "",
          text: "",
        },
        {
          icon: "fa-spin fa-spinner",
          text: "",
          text_class: "text-primary",
        },
        {
          icon: "fa-exclamation-triangle",
          text: "", //Match found
          text_class: "text-danger",
        },
        {
          icon: "fa-check",
          text: "",
          text_class: "text-success",
        },
      ],
      primary_email_alert_state: 0,
      primary_email_matched_profile: default_matched_profile_state(),
    };
  },
  mounted() {
    console.log(this.loadedBrand);

    let vm = this;
    vm.name_alerts[1].text = vm.$t("alerts.checking_database_for_matches");
    vm.name_alerts[2].text = vm.$t("alerts.existing_name"); //Match found
    vm.name_alerts[3].text = vm.$t("alerts.no_profile_match_found_for_name");

    vm.primary_email_alerts[1].text = vm.$t(
      "alerts.checking_database_for_matches"
    );
    vm.primary_email_alerts[2].text = vm.$t("alerts.existing_primary_email"); //Match found
    vm.primary_email_alerts[3].text = vm.$t(
      "alerts.no_profile_match_found_for_primary_email"
    );
  },
  computed: {
    ...mapGetters("profile", {
      response: "get_response",
    }),

    contacts_have_matches() {
      let has_matches = false;
      let vm = this;
      if (vm.parent.form.contacts.length == 0) {
        return has_matches;
      }

      vm.$refs.contacts.forEach(function(contact) {
        if (contact.alert_state == 2) {
          has_matches = true;
        }
      });

      return has_matches;
    },
  },

  methods: {
    ...mapActions("profile", ["post_profile", "add_profile", "update_profile"]),
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
        this.$bvModal.hide('profile-modal')
    },
    //Notes
    updateAddNotesStatus() {
      if (!this.parent.form.add_notes) {
        //Check if not ticked because it will check first before switching to the new state
        if (this.parent.form.notes.length == 0) {
          this.parent.addNote();
        }
      } else {
        let vm = this;
        this.parent.$bvModal
          .msgBoxConfirm(vm.$t("toasts.delete_notes_input"), {
            title: vm.$t("confirmation_record_delete"),
            okVariant: "danger",
            okTitle: vm.$t("yes"),
            cancelTitle: vm.$t("no"),
            hideHeaderClose: false,
            centered: true,
          })
          .then((value) => {
            if (value) {
              vm.parent.form.notes = [];
            } else {
              vm.parent.form.add_notes = true;
            }
          })
          .catch((err) => {
            vm.parent.form.add_notes = true;
          });
      }
    },

    //Contacts
    updateAddContactsStatus() {
      if (!this.parent.form.add_contacts) {
        //Check if not ticked because it will check first before switching to the new state
        if (this.parent.form.contacts.length == 0) {
          this.parent.addContact();
        }
      } else {
        let vm = this;
        this.parent.$bvModal
          .msgBoxConfirm(vm.$t("toasts.contact_input"), {
            title: vm.$t("confirmation_record_delete"),
            okVariant: "danger",
            okTitle: vm.$t("yes"),
            cancelTitle: vm.$t("no"),
            hideHeaderClose: false,
            centered: true,
          })
          .then((value) => {
            if (value) {
              vm.parent.form.contacts = [];
            } else {
              vm.parent.form.add_contacts = true;
            }
          })
          .catch((err) => {
            vm.parent.form.add_contacts = true;
          });
      }
    },

    //Jobs
    updateAddJobsStatus() {
      if (!this.parent.form.add_jobs) {
        //Check if not ticked because it will check first before switching to the new state
        if (this.parent.form.jobs.length == 0) {
          this.parent.addJob();
        }
      } else {
        let vm = this;
        this.parent.$bvModal
          .msgBoxConfirm(vm.$t("toasts.job_input"), {
            title: vm.$t("confirmation_record_delete"),
            okVariant: "danger",
            okTitle: vm.$t("yes"),
            cancelTitle: vm.$t("no"),
            hideHeaderClose: false,
            centered: true,
          })
          .then((value) => {
            if (value) {
              vm.parent.form.jobs = [];
            } else {
              vm.parent.form.add_jobs = true;
              setTimeout(() => {
                vm.syncJobs();
              }, 100);
            }
          })
          .catch((err) => {
            vm.parent.form.add_jobs = true;
            setTimeout(() => {
              vm.syncJobs();
            }, 100);
          });
      }
    },

    //ClientEngagementsPart
    updateAddClientEngagementsStatus() {
      if (!this.parent.form.add_engagements) {
        //Check if not ticked because it will check first before switching to the new state
        if (this.parent.form.client_engagements.length == 0) {
          this.parent.addClientEngagement();
        }
      } else {
        let vm = this;
        this.parent.$bvModal
          .msgBoxConfirm(vm.$t("toasts.delete_client_engagement_input"), {
            title: vm.$t("confirmation_record_delete"),
            okVariant: "danger",
            okTitle: vm.$t("yes"),
            cancelTitle: vm.$t("no"),
            hideHeaderClose: false,
            centered: true,
          })
          .then((value) => {
            if (value) {
              vm.parent.form.client_engagements = [];
            } else {
              vm.parent.form.add_engagements = true;
            }
          })
          .catch((err) => {
            vm.parent.form.add_engagements = true;
          });
      }
    },

    //Attachments Part
    updateAddAttachmentsStatus() {
      if (!this.parent.form.add_attachments) {
        //Check if not ticked because it will check first before switching to the new state
        if (this.parent.form.attachments.length == 0) {
          this.parent.addAttachment();
        }
      } else {
        let vm = this;
        this.parent.$bvModal
          .msgBoxConfirm(vm.$t("toasts.delete_attachment_input"), {
            title: vm.$t("confirmation_record_delete"),
            okVariant: "danger",
            okTitle: vm.$t("yes"),
            cancelTitle: vm.$t("no"),
            hideHeaderClose: false,
            centered: true,
          })
          .then((value) => {
            if (value) {
              vm.parent.form.attachments = [];
            } else {
              vm.parent.form.add_attachments = true;
            }
          })
          .catch((err) => {
            vm.parent.form.add_attachments = true;
          });
      }
    },

    resetModalData() {
      let vm = this;
      vm.name_alert_state = 0;
      vm.name_matched_profile = default_matched_profile_state();
      vm.primary_email_alert_state = 0;
      vm.primary_email_matched_profile = default_matched_profile_state();
    },

    show() {
      let vm = this;
      resetModalData();
    },

    hide() {
      let vm = this;
      
      vm.name_alert_state = 3;
      vm.name_matched_profile = default_matched_profile_state();
      vm.preventSubmit = false;

      this.$bvModal.hide("profile-modal");
      this.$emit("hide");
    },

    checkNameMatch() {
      let vm = this;
      let firstname = vm.parent.form.first_name;
      let surname = vm.parent.form.surname;
      vm.name_alert_state = 0;

      if (firstname == "" || surname == "" || vm.parent.form.id != null) return;

      let params = {
        api_token: vm.$user.api_token,
        filter: "names",
        firstname: firstname,
        surname: surname,
      };
      
      vm.preventSubmit = true;
      vm.name_alert_state = 1; //Loading state
      axios
        .get("/api/profile/find_match", {
          params,
        })
        .then((resp) => {
          let data = resp.data;
          if (data.length > 0) {
            vm.name_alert_state = 2;
            vm.name_matched_profile = data[0];
            vm.preventSubmit = true;
            vm.is_name_unique = false;
          } else {
            vm.name_alert_state = 3;
            vm.name_matched_profile = default_matched_profile_state();
            vm.preventSubmit = false;
            vm.is_name_unique = true;
          }
        });
    },

    checkPrimaryEmailMatch() {
      let vm = this;
      let primary_email = vm.parent.form.primary_email;
      vm.primary_email_alert_state = 0;

      if (primary_email == "" || vm.parent.form.id != null) return;

      let params = {
        api_token: vm.$user.api_token,
        filter: "email",
        email: primary_email,
      };

      vm.primary_email_alert_state = 1; //Loading state
      axios
        .get("/api/profile/find_match", {
          params,
        })
        .then((resp) => {
          let data = resp.data;
          if (data.length > 0) {
            vm.primary_email_alert_state = 2;
            vm.primary_email_matched_profile = data[0];
          } else {
            vm.primary_email_alert_state = 3;
            vm.primary_email_matched_profile = default_matched_profile_state();
          }
        });
    },

    syncLocation() {
      let vm = this;
      vm.parent.fetchProvinces(parent.form.location.country_id);
      vm.parent.fetchCities(parent.form.location.province_id);
    },

    syncJobs() {
      let vm = this;
      if (vm.parent.form.jobs.length > 0) {
        vm.$refs.jobs.forEach(function(job) {
          job.syncJob();
        });
      }
    },

    onSave() {
      let vm = this;

      this.parent.btnloading = true;

      if (this.contacts_have_matches) {
        this.$bvModal
          .msgBoxConfirm(
            vm.$t("errors.cannot_save_profile_with_same_contact_information"),
            {
              title: vm.$t("errors.error"),
              okVariant: "danger",
              okTitle: "Ok",
              OkOnly: true,
              hideHeaderClose: false,
              centered: true,
            }
          )
          .then((value) => {
            vm.parent.btnloading = false;
          })
          .catch((err) => {
            vm.parent.btnloading = false;
          });

        return false;
      }

      if (this.parent.form.action == "Add") {
        this.checkNameMatch();
      }

      if (!this.is_name_unique) {
        this.$bvModal
          .msgBoxConfirm(
            vm.$t("alerts.existing_name"),
            {
              title: vm.$t("errors.error"),
              okVariant: "danger",
              okTitle: "Ok",
              OkOnly: true,
              hideHeaderClose: false,
              centered: true,
            }
          )
          .then((value) => {
            vm.parent.btnloading = false;
          })
          .catch((err) => {
            vm.parent.btnloading = false;
          });

        return false;
      }

      if (this.parent.form.action == "Add") {
        this.parent.form.brands = [];
        this.parent.form.brands.push(this.$brand.id);
      }

      let formData = new FormData();

      if (this.parent.form.id != null)
        formData.append("id", this.parent.form.id);

      formData.append("api_token", this.$user.api_token);
      formData.append("action", this.parent.form.action);
      formData.append("firstname", this.parent.form.first_name);
      formData.append("middlename", this.parent.form.middlename);
      formData.append("surname", this.parent.form.surname);
      formData.append("nickname", this.parent.form.nickname);
      formData.append("primary_email", this.parent.form.primary_email);

      if (this.parent.form.brands != null)
        formData.append("brands", JSON.stringify(this.parent.form.brands));

      if (this.parent.form.notes != null)
        formData.append("notes", JSON.stringify(this.parent.form.notes));

      if (this.parent.form.contacts != null)
        formData.append("contacts", JSON.stringify(this.parent.form.contacts));

      if (this.parent.form.location != null)
        formData.append("location", JSON.stringify(this.parent.form.location));

      if (this.parent.form.jobs != null)
        formData.append("jobs", JSON.stringify(this.parent.form.jobs));

      if (this.parent.form.client_engagements != null)
        formData.append(
          "client_engagements",
          JSON.stringify(this.parent.form.client_engagements)
        );

      if (this.parent.form.attachments_info.length > 0)
        formData.append(
          "attachments_info",
          JSON.stringify(this.parent.form.attachments_info)
        );

      if (this.parent.form.attachments.length > 0) {
        this.parent.form.attachments.forEach(function(item, index) {
          formData.append("attachments_" + index, item);
        });
      }
      
      this.onHideClick()
      vm.post_profile(formData)
        .then((resp) => {
          this.parent.btnloading = false;
          
          if (this.response.responseStatus) {
            let message_text = "";

            let message = {};

            if (this.parent.form.action === "Add") {
              message_text = vm.$t("toasts.added_profile");
              message_text = message_text.replace(
                /%variable%/g,
                this.parent.form.first_name
              );

              message = {
                text: message_text,
                title: vm.$t("new_record_created"),
                variant: "success",
              };

              vm.add_profile(vm.response);
            } else {
              message_text = vm.$t("toasts.updated_profile");
              message_text = message_text.replace(
                /%variable%/g,
                this.parent.form.id
              );

              message = {
                text: message_text,
                title: vm.$t("record_updated"),
                variant: "warning",
              };

              let profile = vm.response;
              profile.profile_index = vm.parent.editingIndex;
              vm.update_profile(profile);
              this.parent.loadProfiles()
            }

            this.parent.makeToast(message.variant, message.title, message.text);

            vm.parent.clearErrors();
            this.parent.onSuccessfulSaveProfile(vm.response);
            vm.resetModalData();
          }
        })
        .catch((error) => {
          console.log("Error");
          this.toastError(error.response.data.errors);
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnloading = false;
        });
    },

    toastError(error){
      console.log(error);
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        this.parent.makeToast("danger", vm.$t('errors.error'), value);
      }
    }
  },
};
</script>
<style>
.v-window {
  overflow: inherit !important;
}
</style>