<template>
  <div>
    <v-row>
      <v-col>
        <div class="title font-weight-light text--secondary">
          {{ $t("label.client_profile") }}
        </div>
      </v-col>
    </v-row>
    <v-row>
      <v-col  cols="12" md="8" xs="12">
        <v-card outlined class="profile__cards">
          <div class="text-right">
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  icon
                  color="#8fea92"
                  @click.prevent="parent.onEdit(parent.profileViewed)"
                  v-bind="attrs"
                  v-on="on"
                >
                  <v-icon>mdi-account-edit</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("label.edit_profile") }}</span>
            </v-tooltip>
          </div>
          <v-row>
            <v-col cols="12" md="4" xs="12">
              <div class="text-center">
                <v-avatar color="#ebf4fe" size="180" style="margin: 5px">
                  <span
                    class="display-3"
                    style="margin-bottom: 0; color: #329ef4;"
                  >
                    {{
                      !!(parent.profileViewed.firstname + " " + parent.profileViewed.surname)
                        ? (parent.profileViewed.firstname + " " + parent.profileViewed.surname)
                            .split(/\s/)
                            .reduce(
                              (response, word) =>
                                (response += word.slice(0, 1)),
                              ""
                            )
                        : "AA"
                    }}
                  </span>
                </v-avatar>
              </div>
            </v-col>
            <v-col cols="12" md="8" xs="12" class="profile__info">
              <div style="margin-bottom: 5px">
                <span class="title font-weight-bold text--primary">
                  {{ parent.profileViewed.full_name }}
                </span>
              </div>
              <div v-if="parent.profileViewed.nickname != null">
                <span class="subtitle font-weight-bold text--secondary">
                  {{ $t("label.nickname") }}:
                </span>
                <span class="subtitle font-weight-light text--secondary">
                  {{ parent.profileViewed.nickname }}
                </span>
              </div>
              <div v-if="parent.profileViewed.primary_email != null">
                <span class="subtitle font-weight-bold text--secondary">
                  {{ $t("label.primary_email") }}:
                </span>
                <span class="subtitle font-weight-light text--secondary">
                  {{ parent.profileViewed.primary_email }}
                </span>
              </div>
              <div>
                <span class="subtitle font-weight-bold text--secondary">
                  {{ $t("label.location") }}:
                </span>
                <span class="subtitle font-weight-light text--secondary">
                  {{ parent.profileViewed.location_display }}
                </span>
              </div>
              <div>
                <span class="subtitle font-weight-bold text--secondary">
                  {{ $t("label.jobs") }}:
                </span>
                <span class="subtitle font-weight-light text--secondary">
                  <span v-if="parent.profileViewed.job_data.jobs_display.length">
                   <ul>
                    <li
                      v-for="(job_display, index) in parent.profileViewed.job_data.jobs_display"
                      :key="'job_display' + index"
                      style="margin: 0"
                    >
                      <u v-html="job_display"></u>
                    </li>
                  </ul>
                  </span>  
                   <span v-else>
                     {{ $t("label.no_jobs_found") }}                                
                  </span>
                </span>                 
              </div>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
      
      <v-col cols="12" xs="12" md="4">
        <v-card outlined class="profile__cards" max-height="290" height="290">
          <v-row>
            <v-col cols="10">
              <span class="title font-weight-light text--secondary">
                {{ $t("label.attachments") }}
              </span>
            </v-col>
            <v-col cols="2">
              <div class="text-right">
                <v-tooltip top>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      icon
                      color="#8fea92"
                      @click.prevent="parent.modalAddAttachments(parent.profileViewed)"
                      v-bind="attrs"
                      v-on="on"
                    >
                      <v-icon>mdi-file-link</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ $t("label.add_attachments") }}</span>
                </v-tooltip>
              </div>
            </v-col>
          </v-row>
          <v-divider></v-divider>
          <v-row>
            <v-col>
              <attachmentDisplay
                v-for="(attachment, index) in displayed_attachments"
                v-bind:key="'note_' + index"
                :root_parent="parent"
                :display_array="displayed_attachments"
                :parent="vm"
                :index="index"
                :is_editable="true"
                ref="attachmentsDisplay"
              ></attachmentDisplay>
              <div v-if="displayed_attachments.length == 0">
                {{ $t("label.no_attachments_found") }}
              </div>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" xs="12" md="4">
        <v-card outlined class="profile__cards" max-height="300" height="300">
          <v-row>
            <v-col cols="10">
              <span class="title font-weight-light text--secondary">
                {{ $t("label.notes") }}
              </span>
            </v-col>
            <v-col cols="2">
              <div class="text-right">
                <v-tooltip top>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      icon
                      color="#8fea92"
                      @click.prevent="parent.modalAddNotes"
                      v-bind="attrs"
                      v-on="on"
                    >
                      <v-icon>mdi-sticker-plus</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ $t("label.add_notes") }}</span>
                </v-tooltip>
              </div>
            </v-col>
          </v-row>
          <v-divider></v-divider>
          <v-row>
            <v-col>
              <noteDisplay
                v-for="(note, index) in displayed_notes"
                v-bind:key="'note_' + index"
                :root_parent="parent"
                :display_array="displayed_notes"
                :parent="vm"
                :index="index"
                :is_editable="true"
                ref="notesDisplay"
              ></noteDisplay>
              <div v-if="displayed_notes.length == 0">
                {{ $t("label.no_notes_found") }}
              </div>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
      <v-col cols="12" xs="12" md="4">
        <v-card outlined class="profile__cards" max-height="300" height="300">
          <v-row>
            <v-col cols="10">
              <span class="title font-weight-light text--secondary">
                {{ $t("label.contacts") }}
              </span>
            </v-col>
            <v-col cols="2">
              <div class="text-right">
                <v-tooltip top>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      icon
                      color="#8fea92"
                      @click.prevent="parent.modalAddContacts"
                      v-bind="attrs"
                      v-on="on"
                    >
                      <v-icon>mdi-book-account</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ $t("label.add_contacts") }}</span>
                </v-tooltip>
              </div>
            </v-col>
          </v-row>
          <v-divider></v-divider>
          <v-row>
            <v-col>
              <contactDisplay
                v-for="(contact, index) in displayed_contacts"
                v-bind:key="'contact_' + index"
                :root_parent="parent"
                :display_array="displayed_contacts"
                :parent="vm"
                :index="index"
                :is_editable="true"
                ref="contactsDisplay"
              ></contactDisplay>
              <div v-if="displayed_contacts.length == 0">
                {{ $t("label.no_contacts_found") }}
              </div>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
      <v-col cols="12" xs="12" md="4">
        <v-card outlined class="profile__cards" max-height="300" height="300">
          <v-row>
            <v-col cols="10">
              <span class="title font-weight-light text--secondary">
                {{ $t("label.client_engagements") }}
              </span>
            </v-col>
            <v-col cols="2">
              <div class="text-right">
                <v-tooltip top>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      icon
                      color="#8fea92"
                      @click.prevent="parent.modalAddClientEngagement"
                      v-bind="attrs"
                      v-on="on"
                    >
                      <v-icon>mdi-thumbs-up-down</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ $t("label.add_client_engagements") }}</span>
                </v-tooltip>
              </div>
            </v-col>
          </v-row>
          <v-divider></v-divider>
          <v-row>
            <v-col>
              <clientEngagementsDisplay
                v-for="(engagement, index) in displayed_client_engagements"
                v-bind:key="'engagement_' + index"
                :root_parent="parent"
                :display_array="displayed_client_engagements"
                :parent="vm"
                :index="index"
                :is_editable="true"
                ref="clientEngagementsDisplay"
              ></clientEngagementsDisplay>
              <div v-if="displayed_client_engagements.length == 0">
                {{ $t("label.no_client_engagements_found") }}
              </div>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
    <addPlatformType :parent="parent" />
  </div>
</template>

<style>
/*Select2 ReadOnly Start*/
select[readonly].select2-hidden-accessible + .select2-container {
  pointer-events: none;
  touch-action: none;
}

select[readonly].select2-hidden-accessible
  + .select2-container
  .select2-selection {
  background: #eee;
  box-shadow: none;
}

select[readonly].select2-hidden-accessible
  + .select2-container
  .select2-selection__arrow,
select[readonly].select2-hidden-accessible
  + .select2-container
  .select2-selection__clear {
  display: none;
}

/*Select2 ReadOnly End*/
</style>

<script>
import { mapActions, mapGetters } from "vuex";
import axios from "axios";

import { API_LOCATIONS } from "./../../common/API.service";
import { API_BRAND } from "./../../common/API.service";

import profileMixins from "./mixins/profileMixins.js";

import profileModal from "./modals/profile.modal.vue";
import brandModal from "./modals/brand.modal.vue";
import addPlatformType from "./modals/add-platform-type.vue";
import engagementModal from "./modals/engagement.modal.vue";
import platformModal from "./modals/platform.modal.vue";
import provinceModal from "./modals/province.modal.vue";
import cityModal from "./modals/city.modal.vue";
import jobProfessionModal from "./modals/job-profession.modal.vue";
import jobCategoryModal from "./modals/job-category.modal.vue";
import jobDescriptionModal from "./modals/job-description.modal.vue";
import clientEngagementsModal from "./modals/add-client-engagements.modal.vue";
import linkToBrandModal from "./modals/link-to-brand.modal.vue";
import contactTypeModal from "./modals/contact-type.modal.vue";
import addContactsModal from "./modals/add-contacts.modal.vue";
import addNotesModal from "./modals/add-notes.modal.vue";
import clientEngagementsDisplay from "./components/client_engagement_display.vue";
import noteDisplay from "./components/note_display.vue";
import contactDisplay from "./components/contact_display.vue";
import attachmentDisplay from "./components/attachment_display.vue";

export default {
  name: "profile_page",
  components: {
    clientEngagementsDisplay,
    noteDisplay,
    contactDisplay,
    attachmentDisplay,
    profileModal,
    brandModal,
    contactTypeModal,
    jobProfessionModal,
    jobCategoryModal,
    jobDescriptionModal,
    engagementModal,
    platformModal,
    provinceModal,
    cityModal,
    clientEngagementsModal,
    linkToBrandModal,
    addContactsModal,
    addNotesModal,
    addPlatformType
  },
  mixins: [profileMixins],
  props: ["parent", "profile_id"],
  data() {
    return {
      loading: true,
      vm: this,
    };
  },
  computed: {
    old_client_engagements() {
      let filtered = [];
      this.parent.form.client_engagements.forEach((data, index) => {
        data.index = index;
        if (data.id != undefined) filtered.push(data);
      });

      return filtered;
    },
    displayed_client_engagements() {
      return this.parent.profileViewed.client_engagements;
    },
    old_notes() {
      let filtered = [];
      this.form.notes.forEach((data, index) => {
        data.index = index;
        if (data.added_by != undefined) filtered.push(data);
      });

      return filtered;
    },
    displayed_notes() {
      return this.parent.profileViewed.notes;
    },
    old_contacts() {
      let filtered = [];
      console.log(this.parent.form.contacts);

      this.parent.form.contacts.forEach((data, index) => {
        data.index = index;
        if (data.id != undefined) filtered.push(data);
      });

      return filtered;
    },
    displayed_contacts() {
      return this.parent.profileViewed.contacts;
    },
    old_attachments() {
      let filtered = [];
      console.log(this.parent.form.attachments);

      this.parent.form.attachments.forEach((data, index) => {
        data.index = index;
        if (data.id != undefined) filtered.push(data);
      });

      return filtered;
    },
    displayed_attachments() {
      return this.parent.profileViewed.attachments;
    }
  },
  methods: {
    ...mapActions("actions", ["get_engagaments", "delete_engagement"]),
    ...mapActions("jobs", ["get_filtered_jobs", "get_jobs"]),

    initProfile() {
      this.InitializeProfileResources();
    },

    showProfileModal(tabIndex) {
      if (this.meta.page == "add engagement form") {
        return false;
      }
      let vm = this;
      vm.$bvModal.show("profile-modal");

      setTimeout(function() {
        vm.profileModalTabIndex = tabIndex;
      }, 100);
    },

    onSuccessfulSaveProfile() {
      this.LoadProfile(this.profile_id);
    },

    async loadBrand(brand) {
      let params = {
        api_token: this.$user.api_token,
      };

      let vm = this;
      await axios.get(API_BRAND + "/" + brand, { params }).then((resp) => {
        vm.loadedBrand = resp.data;
      });
    },

    async LoadProfile(id) {
      this.form.profile_id = id;
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
      };

      await axios
        .get("/api/profile/" + id, {
          params,
        })
        .then((resp) => {
          let data = resp.data;

          this.loadedProfile = this.deepCopy(data);
          this.parent.profileViewed = this.deepCopy(data);

          this.form.id = data.id;
          this.form.profile_id = data.id;
          this.form.profile_name = data.profile_name;

          this.form.notes = this.deepCopy(data.notes);
          this.form.contacts = this.deepCopy(data.contacts);
          this.form.jobs = this.deepCopy(data.jobs);
          this.form.client_engagements = this.deepCopy(data.client_engagements);
        });
    },

    modalAddContacts() {
      this.meta.page = "add-contacts-modal";
      let vm = this;
      if (vm.contact_types.length == 0) {
        vm.InitializeProfileResources("contacts");
      }

      this.LoadProfile(this.profile_id).then(() => {
        this.addContact();
        // open category modal
        this.$bvModal.show("add-contacts-modal");
      });
    },

    modalEditContact(index) {
      let vm = this;
      if (vm.contact_types.length == 0) {
        vm.InitializeProfileResources("contacts");
      }

      vm.parent.meta.page = "edit-contact-modal";
      vm.parent.form.action = "Edit";
      vm.parent.editingIndex = index;

      // open category modal
      vm.$bvModal.show("edit-contact-modal");
    },

    modalEditAttachment(index) {
      let vm = this;
      console.log("Editing index: " + index);
      vm.parent.meta.page = "edit-attachment-modal";
      vm.parent.form.action = "Edit";
      vm.parent.editingIndex = index;

      // open category modal
      vm.$bvModal.show("edit-attachment-modal");
    },

    modalAddNotes(item) {
      this.meta.page = "add-notes-modal";
      this.LoadProfile(this.profile_id).then(() => {
        this.addNote();
        // open category modal
        this.$bvModal.show("add-notes-modal");
      });
    },

    modalEditNote(index) {
      console.log("Editing Note");
      this.parent.meta.page = "edit-note-modal";
      this.parent.form.action = "Edit";
      this.parent.editingIndex = index;
      this.$bvModal.show("edit-note-modal");
    },

    async loadCategories() {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,
        locale: this.$user.locale,
        type: "profession",
      };

      this.get_filtered_jobs(params).then((_) => {
        this.isLoading = false;
      });
    },

    modalAddNewBrand(from) {
      let vm = this;
      if (vm.brands.length == 0) {
        vm.InitializeProfileResources("brands");
      }

      this.form.sub_action = "Add";
      this.resetBrandForm();
      this.btnloading = false;

      if (this.meta.page == "link-to-brand-modal") {
        this.$bvModal.hide("link-to-brand-modal");
      } else {
        this.$bvModal.hide("engagement-modal");
      }

      this.$bvModal.show("brand-modal");
    },

    addedNewBrandSuccessfully() {
      this.fetchBrands();
      this.resetBrandForm();
      console.log(this.meta.page)
      if (this.meta.page == "link-to-brand-modal") {
        this.$bvModal.show("link-to-brand-modal");
      } 
      else if(this.meta.page == "edit-client-engagement-modal"){
        this.$bvModal.show("edit-client-engagement-modal");
      }
      else {
        this.$bvModal.show("engagement-modal");
      }
    },

    showModalOnHideBrand() {
      if (this.meta.page == "link-to-brand-modal") {
        this.$bvModal.show("link-to-brand-modal");
      } else {
        this.$bvModal.show("engagement-modal");
      }
    },

    showModalOnHideContactType() {
      if (this.meta.page == "profile-modal") {
        this.showProfileModal(1);
      } else {
        this.$bvModal.show("add-contacts-modal");
      }
    },

    showModalOnHideEngagement() {
      if (this.meta.page == "profile-modal") {
        this.showProfileModal(4);
      } else {
        this.$bvModal.show("client-engagements-modal");
      }
    },

    showModalOnHidePlatform() {
      if (this.meta.page == "profile-modal") {
        this.showProfileModal(4);
      } else {
        this.$bvModal.show("client-engagements-modal");
      }
    },

    modalAddNewContactType() {
      this.form.sub_action = "Add";
      this.resetContactTypeForm();
      this.btnloading = false;
      if (this.meta.page == "profile-modal") {
        this.$bvModal.hide("profile-modal");
      } else {
        this.$bvModal.hide("add-contacts-modal");
      }
      this.$bvModal.show("contact-type-modal");
    },

    addedNewContactTypeSuccessfully() {
      this.fetchContactTypes();
      this.resetContactTypeForm();
      if (this.meta.page == "profile-modal") {
        this.showProfileModal(1);
      } else {
        this.$bvModal.show("add-contacts-modal");
      }
    },

    modalAddNewCategory() {
      this.form.sub_action = "Add";
      this.resetCategoryForm();
      this.btnloading = false;
      this.$bvModal.hide("profile-modal");
      this.$bvModal.show("job-category-modal");
    },

    addedNewCategorySuccessfully() {
      this.fetchCategories();
      this.$nextTick(() => {
        this.$refs.profileModal.syncJobs();
      });
      this.resetCategoryForm();
      this.showProfileModal(3);
    },

    modalAddNewProfession(job_category_id) {
      this.form.sub_action = "Add";
      this.resetProfessionForm();
      this.btnloading = false;
      this.$bvModal.hide("profile-modal");
      this.$bvModal.show("job-profession-modal");
    },

    addedNewProfessionSuccessfully() {
      this.fetchProfessions();
      this.$nextTick(() => {
        this.$refs.profileModal.syncJobs();
      });
      this.resetProfessionForm();
      this.showProfileModal(3);
    },

    modalAddNewDescription() {
      this.form.sub_action = "Add";
      this.resetDescriptionForm();
      this.btnloading = false;
      this.$bvModal.hide("profile-modal");
      this.$bvModal.show("job-description-modal");
    },

    addedNewDescriptionSuccessfully() {
      this.fetchDescriptions();
      this.$nextTick(() => {
        this.$refs.profileModal.syncJobs();
      });
      this.resetDescriptionForm();
      this.showProfileModal(3);
    },

    //Province Modal Events
    modalAddNewProvince() {
      this.form.sub_action = "Add";
      this.resetProvinceForm();
      this.btnloading = false;
      this.$bvModal.hide("profile-modal");
      this.$bvModal.show("province-modal");
    },

    addedNewProvinceSuccessfully() {
      this.fetchProvinces(this.form.location.country_id);
      this.resetProvinceForm();
      this.showProfileModal(2);
    },

    //City Modal Events
    modalAddNewCity() {
      this.form.sub_action = "Add";
      this.resetCityForm();
      this.btnloading = false;
      this.$bvModal.hide("profile-modal");
      this.$bvModal.show("city-modal");
    },

    addedNewCitySuccessfully() {
      this.fetchCities(this.form.location.province_id);
      this.resetCityForm();
      this.showProfileModal(2);
    },

    //Action Modal Events
    modalAddNewEngagement() {
      this.form.sub_action = "Add";
      this.resetEngagementForm();
      this.btnloading = false;
      if (this.meta.page == "profile-modal") {
        this.$bvModal.hide("profile-modal");
      } else if (this.meta.page == "client-engagements-modal") {
        this.$bvModal.hide("client-engagements-modal");
      }
      this.$bvModal.show("engagement-modal");
    },

    showModalOnHideEngagement() {
      if (this.meta.page == "profile-modal") {
        this.showProfileModal(4);
      } else {
        this.$bvModal.show("client-engagements-modal");
      }
    },

    addedNewEngagementSuccessfully() {
      this.fetchEngagements();
      this.resetEngagementForm();
      if (this.meta.page == "profile-modal") {
        this.showProfileModal(4);
      } else {
        this.$bvModal.show("client-engagements-modal");
      }
    },

    modalAddNewPlatformItem() {
      this.form.sub_action = "Add";
      this.resetPlatformItemForm();
      this.btnloading = false;
      if (this.meta.page == "profile-modal") {
        this.$bvModal.hide("profile-modal");
      } else if (this.meta.page == "client-engagements-modal") {
        this.$bvModal.hide("client-engagements-modal");
      } else if (this.meta.page == "edit-client-engagement-modal") {
        this.$bvModal.hide("edit-client-engagement-modal");
      }
      this.$bvModal.show("platform-modal");
    },

    showModalOnHidePlatform() {
      if (this.meta.page == "profile-modal") {
        this.showProfileModal(4);
      } else if (this.meta.page == "client-engagements-modal") {
        this.$bvModal.show("client-engagements-modal");
      } else if (this.meta.page == "edit-client-engagement-modal") {
        this.$bvModal.show("edit-client-engagement-modal");
      }
    },

    addedNewPlatformItemSuccessfully() {
      this.resetPlatformItemForm();
      this.fetchPlatformItems();
      if (this.meta.page == "profile-modal") {
        this.showProfileModal(4);
      } else if (this.meta.page == "client-engagements-modal") {
        this.$bvModal.show("client-engagements-modal");
      } else if (this.meta.page == "edit-client-engagement-modal") {
        this.$bvModal.show("edit-client-engagement-modal");
      }
    },

    modalAddClientEngagement() {
      let vm = this;
      if (
        vm.engagements.length == 0 &&
        vm.platform_items.length == 0 &&
        vm.platform_types.length == 0
      ) {
        vm.InitializeProfileResources("client_engagements");
      }

      this.meta.page = "client-engagements-modal";
      this.form.action = "Add";
      this.LoadProfile(this.profile_id).then(() => {
        this.addClientEngagement();
        // open category modal
        this.$bvModal.show("client-engagements-modal");
      });
    },

    modalEditClientEngagement(index) {
      this.parent.meta.page = "edit-client-engagement-modal";
      this.parent.form.action = "Edit";
      //this.form.client_engagements = [];
      this.parent.editingIndex = index;
      // open category modal
      this.$bvModal.show("edit-client-engagement-modal");
    },

    modalAddCategory() {
      this.form.sub_action = "Add";
      this.resetJobsForm();

      // Close job profession modal
      $("#profileModal").modal("hide");

      // open category modal
      this.$bvModal.show("job-category-modal");
    },
    modalAddProfession() {
      this.form.sub_action = "Add";
      this.resetJobsForm();
      this.fetchCategories();
      // Close job profession modal
      $("#profileModal").modal("hide");

      // open category modal
      this.$bvModal.show("job-profession-modal");
    },
    modalAddDescription() {
      this.form.sub_action = "Add";
      this.resetJobsForm();
      this.fetchCategories();
      this.fetchProfessions();

      // Close job profession modal
      $("#profileModal").modal("hide");

      // open category modal
      this.$bvModal.show("job-description-modal");
    },
  },
};
</script>
