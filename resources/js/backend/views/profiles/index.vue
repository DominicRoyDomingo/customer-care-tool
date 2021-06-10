<template>
  <div class="animated fadeIn">
    <v-app id="profile__container" light>
      <v-tabs
        show-arrows
        background-color="#F0F3F5"
        color="#2F353A"
        v-model="tab"
        center-active
        grow
      >
        <v-tab>
          <strong>{{ this.$brand.brand_name }}</strong>
          &nbsp;
          {{ $t("table.clients") }}
        </v-tab>
        <v-divider vertical inset></v-divider>
        <v-tab> {{ $t("label.statistics") }} </v-tab>
        <v-divider vertical inset></v-divider>
        <v-tab> {{ $t("label.see_graph") }} </v-tab>
        <v-divider vertical inset v-if="tab_length > 0"></v-divider>
        <v-tab
          key="showProfile"
          @click.prevent="
            [
              (editingIndex = profileViewed.index),
              goToProfileTab(profileViewed),
            ]
          "
          v-if="viewing_profile"
          style="background-color: #ebf4fe; color: #329ef4;"
        >
          {{ profile_name }}'s Profile &nbsp;
          <v-btn
            icon
            @click="
              [
                (tab_length = 0),
                (tab = 0),
                (viewing_profile = false),
                (profileViewed = {}),
              ]
            "
            color="error"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-tab>
        <v-tab-item>
          <v-card flat>
            <div class="row">
              <v-row justify="space-between">
                <v-col md="4" style="padding-left: 25px">
                  <div class="title font-weight-light text--secondary">
                    {{ $t("table.profiles") }}
                  </div>
                  <div class="subheading font-weight-medium text--disabled">
                    {{ this.$brand.brand_name }}
                  </div>
                </v-col>
                <v-col md="4">
                  <v-btn
                    tile
                    dark
                    color="success"
                    v-b-modal.profile-modal
                    class="float-right ma-2"
                    @click="onAdd"
                  >
                    <v-icon dark left>mdi-account-plus</v-icon>
                    {{ $t("button.new") }}
                  </v-btn>
                </v-col>
              </v-row>
            </div>
            <div class="row mt-2">
              <div class="col-md-4">
                <div class="form-inline">
                  <div
                    class="caption font-weight-regular text--secondary"
                    style="margin-right: 10px"
                  >
                    {{ $t("button.show") }}
                  </div>
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
              </div>
              <div class="col-md-4"></div>
              <div class="col-md-4">
                <b-input-group>
                  <b-form-input
                    v-model="query_text"
                    type="search"
                    :placeholder="$t('search_here')"
                    style="border-radius: 0; height: 36px"
                  >
                  </b-form-input>
                  <b-input-group-prepend>
                    <v-menu offset-y :rounded="false">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          color="info"
                          dark
                          v-bind="attrs"
                          v-on="on"
                          tile
                          depressed
                        >
                          <v-icon :size="18">
                            mdi-filter-menu
                          </v-icon>
                        </v-btn>
                      </template>
                      <v-list dense class="profile__row-menu">
                        <v-list-item-group color="primary">
                          <v-list-item @click="setFilter('Name')">
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("table.filter_by_name") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="setFilter('Jobs')">
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("table.filter_by_jobs") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="setFilter('Locations')">
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("table.filter_by_locations") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="setFilter('Engagements')">
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("table.filter_by_engagements") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="resetFilter()">
                            <v-list-item-content>
                              <v-list-item-title>
                                {{
                                  $t("table.see_all_brands_list").replace(
                                    "%variable%",
                                    this.$brand.brand_name
                                  )
                                }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                        </v-list-item-group>
                      </v-list>
                    </v-menu>
                  </b-input-group-prepend>
                </b-input-group>
              </div>
              <div class="col-md-12 mt-3">
                <b-table
                  ref="table"
                  striped
                  stacked="md"
                  show-empty
                  primary-key="id"
                  :fields="tableHeaders"
                  :current-page="currentPage"
                  :per-page="perPage"
                  :busy="isLoading"
                  :items="filtered"
                >
                  <template v-slot:table-busy>
                    <v-fade-transition>
                      <v-overlay
                        opacity="0.8"
                        style="z-index: 999999 !important"
                      >
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

                  <template v-slot:cell(full_name)="data">
                    <a href="#" @click.prevent="goToProfileTab(data.item)">
                      {{ data.item.full_name }}
                    </a>
                  </template>
                  <template v-slot:cell(actions)="data">
                    <v-menu offset-y :rounded="false">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          color="#c8ced3"
                          light
                          v-bind="attrs"
                          v-on="on"
                          tile
                          depressed
                          small
                        >
                          {{ $t("button.more_actions") }}
                          <v-icon small right>
                            mdi-chevron-down
                          </v-icon>
                        </v-btn>
                      </template>
                      <v-list dense class="profile__row-menu">
                        <v-list-item-group color="primary">
                          <v-list-item @click="onEdit(data.item)">
                            <v-list-item-icon>
                              <v-icon color="yellow darken-3">
                                mdi-account-edit
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("button.edit") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="onDelete(data.item)">
                            <v-list-item-icon>
                              <v-icon color="red lighten-1">
                                mdi-account-remove
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("button.delete") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-divider></v-divider>
                          <v-list-item
                            @click="modalAddClientEngagement(data.item)"
                          >
                            <v-list-item-icon>
                              <v-icon color="light-blue lighten-3">
                                mdi-thumbs-up-down
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("label.add_client_engagements") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="modalLinkToBrand(data.item)">
                            <v-list-item-icon>
                              <v-icon color="light-blue lighten-3">
                                mdi-briefcase-plus
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("label.link_to_brand") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="modalAddContacts(data.item)">
                            <v-list-item-icon>
                              <v-icon color="light-blue lighten-3">
                                mdi-book-account
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("label.add_contacts") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="modalAddNotes(data.item)">
                            <v-list-item-icon>
                              <v-icon color="light-blue lighten-3">
                                mdi-note-plus
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("label.add_notes") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="modalAddAttachments(data.item)">
                            <v-list-item-icon>
                              <v-icon color="light-blue lighten-3">
                                mdi-file-link
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("label.add_attachments") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                        </v-list-item-group>
                      </v-list>
                    </v-menu>
                  </template>
                </b-table>
              </div>
              <div class="col-md-8"></div>
              <div class="col-md-4">
                <v-pagination
                  v-if="!isLoading"
                  v-model="currentPage"
                  :length="Math.ceil(totalRows / perPage)"
                  :total-visible="5"
                  color="secondary"
                  circle
                  class="profile__table-pagination float-right"
                >
                </v-pagination>
              </div>
            </div>
          </v-card>
        </v-tab-item>
        <v-tab-item>
          <v-card flat>
            <statsPanel
              :brand="this.$brand"
              :brandName="this.$brand.brand_name"
            />
          </v-card>
        </v-tab-item>
        <v-tab-item>
          <v-card flat>
            <chartsGraph
              :brand="this.$brand"
              :brandName="this.$brand.brand_name"
              :label="{
                brand_label: $t('label.brand'),
                location_statistics: $t('label.location_statistics'),
                by_country: $t('label.by_country'),
                by_province: $t('label.by_province'),
                by_city: $t('label.by_city'),
                country_province_city: $t('label.country_province_city'),
                job_statistics: $t('label.job_statistics'),
                by_category: $t('label.by_category'),
                by_profession: $t('label.by_profession'),
                by_specialization: $t('label.by_specialization'),
                category_profession_specialization: $t(
                  'label.category_profession_specialization'
                ),
              }"
            />
          </v-card>
        </v-tab-item>
        <v-tab-item key="showProfile">
          <profiles :profile_id="profile_id" :parent="vm"> </profiles>
        </v-tab-item>
      </v-tabs>

      <!-- Profile Modal Here -->
      <profileModal
        :parent="this"
        ref="profileModal"
        :brandName="this.$brand.brand_name"
      ></profileModal>

      <!-- client engagements Modal Here -->
      <clientEngagementsModal
        @hide="showProfileModal(4)"
        :parent="vm"
      ></clientEngagementsModal>

      <!-- client engagements Modal Here -->
      <linkToBrandModal :parent="vm"></linkToBrandModal>

      <!-- add contacts Modal Here -->
      <addContactsModal :parent="vm"></addContactsModal>

      <!-- add notes Modal Here -->
      <addNotesModal :parent="vm"></addNotesModal>

      <!-- add attachments Modal Here -->
      <addAttachmentsModal :parent="vm"></addAttachmentsModal>
      <!-- Contact Type Modal Here -->
      <contactTypeModal
        @hide="showModalOnHideContactType"
        :parent="vm"
      ></contactTypeModal>

      <!-- Brand Modal Here -->
      <brandModal @hide="showModalOnHideBrand" :parent="vm"></brandModal>

      <!-- Engagement Modal Here -->
      <engagementModal
        @hide="showModalOnHideEngagement"
        :parent="vm"
      ></engagementModal>

      <!-- Platform Modal Here -->
      <platformModal
        @hide="showModalOnHidePlatform"
        :parent="vm"
      ></platformModal>

      <!-- category Modal Here -->
      <jobCategoryModal
        @hide="showProfileModal(3)"
        :parent="vm"
      ></jobCategoryModal>

      <!-- profession Modal Here -->
      <jobProfessionModal
        @hide="showProfileModal(3)"
        :parent="vm"
      ></jobProfessionModal>

      <!-- profession Modal Here -->
      <jobDescriptionModal
        @hide="showProfileModal(3)"
        :parent="vm"
      ></jobDescriptionModal>

      <!-- Province Modal Here -->
      <provinceModal @hide="showProfileModal(2)" :parent="vm"></provinceModal>

      <!-- Province Modal Here -->
      <cityModal @hide="showProfileModal(2)" :parent="vm"></cityModal>

      <!-- Edit Client Engagement Modal Here -->
      <editClientEngagementModal :parent="vm"></editClientEngagementModal>

      <!-- Edit Contact Modal Here -->
      <editContactModal :parent="vm"></editContactModal>

      <!-- Edit Attachment Modal Here -->
      <editAttachmentModal :parent="vm"></editAttachmentModal>

      <!-- Edit Note Modal Here -->
      <editNoteModal :parent="vm"></editNoteModal>

      <documentType :parent="vm"></documentType>

      <documentCategory :parent="vm"></documentCategory>
    </v-app>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import { API_BRAND, API_ORGANIZATION } from "./../../common/API.service";

import profileMixins from "./mixins/profileMixins.js";
import profileModal from "./modals/profile.modal.vue";
import brandModal from "./modals/brand.modal.vue";
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
import addAttachmentsModal from "./modals/add-attachments.modal.vue";
import chartsGraph from "./components/ChartsGraph.vue";
import statsPanel from "./components/StatsPanel.vue";
import profiles from "./show.vue";

import editClientEngagementModal from "./modals/edit-client-engagement.modal.vue";
import editNoteModal from "./modals/edit-note.modal.vue";
import editContactModal from "./modals/edit-contact.modal.vue";
import editAttachmentModal from "./modals/edit-attachment.modal.vue";

import attachmentsMixins from "../attachments/mixins/attachmentsMixins";
import documentCategory from "../attachments/modals/category/formModal";
import documentType from "../attachments/modals/type/formModal";

export default {
  name: "profiles-index",
  components: {
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
    addAttachmentsModal,
    chartsGraph,
    statsPanel,
    profiles,
    editNoteModal,
    editContactModal,
    editAttachmentModal,
    editClientEngagementModal,
    documentType,
    documentCategory,
  },
  props: ["loaded_brand"],
  mixins: [profileMixins, attachmentsMixins],
  data() {
    return {
      vm: this,
      tab: null,
      tab_length: 0,
      profile_id: 0,
      organization_id : 0,
      profile_name: null,
      viewing_profile: false,
      query_text: "",
      filters: {
        full_name: "",
        brand_names: "",
        location_display: "",
        job_display_name: "",
        engagement_names: "",
      },
      tableHeaders: [
        {
          key: "full_name",
          label: this.$t("table.full_name"),
          thClass: "text-left profile__table-header",
          thStyle: { width: "50%" },
          sortable: true,
          tdClass: "profile__table-row",
        },
        {
          key: "brand_names",
          label: this.$t("table.for_what_brand"),
          thClass: "text-left profile__table-header",
          thStyle: { width: "30%" },
          sortable: true,
          tdClass: "profile__table-row",
        },
        {
          key: "actions",
          label: this.$t("table.action"),
          thClass: "text-center profile__table-header",
          thStyle: { width: "20%" },
          tdClass: "text-center profile__table-row",
        },
      ],
    };
  },

  created() {
    
    this.$bvModal.show("brand-modal");
    this.$eventBus.$on("change-brand", (_) => {
      this.initBrand(this.$brand.id);
    });
  },

  mounted() {
    this.initBrand();
  },

  computed: {
    ...mapGetters("profile", {
      items: "profiles",
      profile: "profile",
      responseStatus: "get_response_status",
    }),
    filtered() {
      const filtered = this.items.filter((item) => {
        return Object.keys(this.filters).every((key) =>
          String(item[key]).includes(this.filters[key])
        );
      });

      return filtered.length > 0 ? filtered : [];
    },
  },

  methods: {
    ...mapActions("profile", [
      "get_profiles",
      "get_filtered_profiles",
      "update_profile",
      "get_profile",
      "delete_profile",
      "remove_from_profiles_array",
    ]),

    initProfile() {
      this.loadProfiles().catch((errors) => {
        console.log(errors);
      });
    },

    async loadBrand(brand) {
      let params = {
        api_token: this.$user.api_token,
      };

      let vm = this;
      await axios.get(API_BRAND + "/" + brand, { params }).then((resp) => {
        vm.$brand = resp.data;
      });
    },

    async load_organization(){
      await axios.get(API_ORGANIZATION).then((resp) => {
          console.log(resp)
      });
    },

    modalAddClientEngagement(item) {
      let vm = this;
      let id = item.id;

      item.is_loading = true;

      vm.form.client_engagements = [];

      vm.meta.page = "client-engagements-modal";
      //vm.resetProfileForm();
      vm.resetClientEngagementsForm();

      if (
        vm.engagements.length == 0 &&
        vm.platform_items.length == 0 &&
        vm.platform_types.length == 0
      ) {
        vm.InitializeProfileResources("client_engagements");
      }

      vm.loadProfile(id).then(() => {
        vm.$nextTick(() => {
          vm.form.id = vm.profile.id;
          vm.form.profile_id = vm.profile.id;
          vm.form.client_engagements = vm.deepCopy(
            vm.profile.client_engagements
          );
          vm.addClientEngagement();
          vm.form.profile_name = vm.profile.full_name;
          vm.itemSelected = vm.profile;
          item.is_loading = false;

          // open category modal
          vm.$bvModal.show("client-engagements-modal");
        });
      });
    },

    addedNewClientEngagementSuccessfully(client_engagements) {
      let vm = this;
      if (vm.profileViewed.id != undefined)
        Object.assign(vm.profileViewed, {client_engagements : client_engagements})
        Object.assign(vm.form, {client_engagements : client_engagements })
        // vm.profileViewed.client_engagements = client_engagements;
    },

    addedNotesSuccessfully(notes) {
      let vm = this;
      console.log(notes);
      if (vm.profileViewed.id != undefined) vm.profileViewed.notes = notes;
    },

    addedContactsSuccessfully(contacts) {
      let vm = this;
      if (vm.profileViewed.id != undefined)
        Object.assign(vm.profileViewed, {contacts : contacts })
        Object.assign(vm.form, {contacts : contacts })

        // vm.profileViewed.contacts = contacts;
    },

    addedAttachmentsSuccessfully(attachments) {
      let vm = this;
      console.log(attachments);
      vm.form.attachments_info = vm.deepCopy(attachments);

      if (vm.profileViewed.id != undefined)
        vm.profileViewed.attachments = attachments;

      vm.form.attachments = [];

      //add attachments per attachment info
      vm.form.attachments_info.forEach(function(item) {
        vm.form.attachments.push(null);
      });
    },

    updatedClientEngagementSuccessfully(client_engagements) {
      this.profileViewed.client_engagements = client_engagements;
      this.form.client_engagements = client_engagements;
    },

    updatedNoteSuccessfully(notes) {
      this.profileViewed.notes = notes;
      this.form.notes = notes;
    },

    updatedContactSuccessfully(contacts) {
      console.log(contacts);
      this.profileViewed.contacts = contacts;
      this.form.contacts = contacts;
    },

    updatedAttachmentSuccessfully(attachments) {
      let vm = this;
      console.log(attachments);
      vm.form.attachments_info = vm.deepCopy(attachments);
      vm.profileViewed.attachments = vm.deepCopy(attachments);

      vm.form.attachments = [];

      //add attachments per attachment info
      vm.form.attachments_info.forEach(function(item) {
        vm.form.attachments.push(null);
      });
    },

    deletedNoteSuccessfully(notes) {
      this.profileViewed.notes = notes;
      this.form.notes = notes;
    },

    deletedContactSuccessfully(contacts) {
      Object.assign(this.profileViewed, {contacts : contacts })
      Object.assign(this.form, {contacts : contacts })
    },

    deletedAttachmentSuccessfully(attachments) {
      let vm = this;
      console.log("New attachments");
      console.log(attachments);
      vm.form.attachments_info = vm.deepCopy(attachments);
      vm.profileViewed.attachments = vm.deepCopy(attachments);

      vm.form.attachments = [];

      //add attachments per attachment info
      vm.form.attachments_info.forEach(function(item) {
        vm.form.attachments.push(null);
      });
    },

    deletedClientEngagementSuccessfully(client_engagements) {
      Object.assign(this.profileViewed, {client_engagements : client_engagements})
      Object.assign(this.form, {client_engagements : client_engagements })
    },

    modalLinkToBrand(item) {
      let vm = this;
      let id = item.id;

      vm.form.action = "Add";
      vm.editingIndex = item.profile_index;
      item.is_loading = true;

      vm.resetProfileForm();
      vm.meta.page = "link-to-brand-modal";

      if (vm.brands.length == 0) {
        vm.InitializeProfileResources("brands");
      }

      vm.loadProfile(id).then(() => {
        vm.$nextTick(() => {
          vm.form.id = vm.profile.id;
          vm.form.profile_id = vm.profile.id;
          vm.form.brands = vm.profile.brand_ids;
          vm.form.profile_name = vm.profile.full_name;
          vm.itemSelected = vm.profile;
          item.is_loading = false;

          // open category modal
          vm.$bvModal.show("link-to-brand-modal");
        });
      });
    },

    linkedBrandsSuccessfully(profile) {
      let vm = this;

      profile.profile_index = this.editingIndex;
      vm.update_profile(profile).then(() => {
        this.loadProfiles().catch((errors) => {
          console.log(errors);
        });
        /*if (vm.loadedBrand.id == 0) {
          if (profile.brand_names != "") {
            console.log("No Brand");

            vm.remove_from_profiles_array(profile).then(() => {
              vm.$refs.table.refresh();
            });
          }
        } else {
          if (!profile.brand_names.includes(vm.$brand.brand_name)) {
            console.log(vm.$brand.brand_name);

            vm.remove_from_profiles_array(profile).then(() => {
              vm.$refs.table.refresh();
            });
          } else {
            vm.$refs.table.refresh();
          }
        }*/
      });

      //this.loadProfiles();
    },

    modalAddContacts(item) {
      let vm = this;
      let id = item.id;

      item.is_loading = true;
      //vm.resetProfileForm();
      vm.form.contacts = [];
      vm.meta.page = "add-contacts-modal";

      if (vm.contact_types.length == 0) {
        vm.InitializeProfileResources("contacts");
      }

      vm.loadProfile(id).then(() => {
        vm.$nextTick(() => {
          vm.form.id = vm.profile.id;
          vm.form.profile_id = vm.profile.id;
          vm.form.contacts = vm.deepCopy(vm.profile.contacts);
          vm.addContact();
          vm.form.profile_name = vm.profile.full_name;

          vm.itemSelected = vm.profile;

          item.is_loading = false;
          // open category modal
          vm.$bvModal.show("add-contacts-modal");
        });
      });
    },

    modalAddAttachments(item) {
      let vm = this;
      let id = item.id;

      item.is_loading = true;

      if (vm.contact_types.length == 0) {
        vm.InitializeProfileResources("attachments");
      }

      //vm.resetProfileForm();
      vm.form.notes = [];
      vm.meta.page = "add-attachments-modal";

      console.log("id ==> " + id);

      vm.loadProfile(id).then(() => {
        vm.$nextTick(() => {
          vm.form.id = vm.profile.id;
          vm.form.profile_id = vm.profile.id;

          vm.form.attachments_info = vm.deepCopy(vm.profile.attachments);

          //add attachments per attachment info
          vm.form.attachments_info.forEach(function(item) {
            vm.form.attachments.push(null);
          });

          vm.addAttachment();
          vm.form.profile_name = vm.profile.full_name;

          vm.itemSelected = vm.profile;

          item.is_loading = false;
          // open category modal
          vm.$bvModal.show("add-attachments-modal");
        });
      });
    },

    modalAddNotes(item) {
      let vm = this;
      let id = item.id;

      item.is_loading = true;

      //vm.resetProfileForm();
      vm.form.notes = [];
      vm.meta.page = "add-notes-modal";

      vm.loadProfile(id).then(() => {
        vm.$nextTick(() => {
          vm.form.id = vm.profile.id;
          vm.form.profile_id = vm.profile.id;
          vm.form.notes = vm.deepCopy(vm.profile.notes);
          vm.addNote();
          vm.form.profile_name = vm.profile.full_name;

          vm.itemSelected = vm.profile;

          item.is_loading = false;
          // open category modal
          vm.$bvModal.show("add-notes-modal");
        });
      });
    },

    onSuccessfulSaveProfile(profile) {
      let vm = this;

      vm.$refs.table.refresh();
      console.log(profile);
      if (vm.profileViewed.id != undefined) vm.profileViewed = profile;
    },

    async loadProfiles() {
      this.isLoading = true;

      let params = {
        api_token: this.$user.api_token,
        brand: this.$brand.id,
      };

      await this.get_profiles(params).then((_) => {
        this.isLoading = false;
      });
    },

    async loadProfile(id) {
      let params = {
        api_token: this.$user.api_token,
        id: id,
      };

      await this.get_profile(params);
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

    modalAddNewBrand(from) {
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
      if (this.meta.page == "link-to-brand-modal") {
        this.$bvModal.show("link-to-brand-modal");
      } else {
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

    openAddTypeModal() {
      this.typeReferrer = "profile";
      this.form.action = 'Add'
      this.setCategories();

      if (this.meta.page == "add-attachments-modal")
        this.$bvModal.hide("add-attachments-modal");
      else this.$bvModal.hide("profile-modal");

      this.isTypeFormModalOpen = true;
    },
    openAddCategoryModal() {
      this.categoryReferrer = "type";
      this.form.action = 'Add'
      this.form.category_name = null;
      this.isTypeFormModalOpen = false;
      this.isCategoryFormModalOpen = true;
    },
    successfullySavedDocType() {
      if (this.meta.page == "add-attachments-modal")
        this.$bvModal.show("add-attachments-modal");
      else this.$bvModal.show("profile-modal");

      this.isTypeFormModalOpen = false;
      this.fetchDocumentTypes();
      this.loadTypes();
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
      }
      this.$bvModal.show("platform-modal");
    },

    showModalOnHidePlatform() {
      if (this.meta.page == "profile-modal") {
        this.showProfileModal(4);
      } else {
        this.$bvModal.show("client-engagements-modal");
      }
    },

    addedNewPlatformItemSuccessfully() {
      this.resetPlatformItemForm();
      this.fetchPlatformItems();
      if (this.meta.page == "profile-modal") {
        this.showProfileModal(4);
      } else {
        this.$bvModal.show("client-engagements-modal");
      }
    },

    onAdd() {
      let vm = this;
      this.resetProfileForm();

      //Initialize necessary resources
      if (
        vm.categories.length == 0 &&
        vm.professions.length == 0 &&
        vm.descriptions.length == 0 && //Jobs
        vm.engagements.length == 0 &&
        vm.platform_items.length == 0 &&
        vm.platform_types.length == 0 && //Client Engagements
        vm.contact_types.length == 0 && //Contacts
        vm.brands.length == 0 && //Brands
        vm.countries.length == 0 //Location
      ) {
        vm.InitializeProfileResources();
      } else {
        if (
          vm.categories.length == 0 &&
          vm.professions.length == 0 &&
          vm.descriptions.length == 0
        ) {
          vm.InitializeProfileResources("jobs");
        }

        if (
          vm.engagements.length == 0 &&
          vm.platform_items.length == 0 &&
          vm.platform_types.length == 0
        ) {
          vm.InitializeProfileResources("client_engagements");
        }

        if (vm.contact_types.length == 0) {
          vm.InitializeProfileResources("contacts");
        }

        if (vm.brands.length == 0) {
          vm.InitializeProfileResources("brands");
        }

        if (vm.countries.length == 0) {
          vm.InitializeProfileResources("location");
        }
      }

      this.meta.page = "profile-modal";
      this.btnloading = false;
      this.form.action = "Add";

      // open modal
      this.$bvModal.show("profile-modal");
    },

    onEdit(item) {
      let vm = this;
      let id = item.id;
      let index = item.profile_index;
      vm.editingIndex = index;
      console.log(item);
      item.is_loading = true;
      vm.resetProfileForm();
      vm.meta.page = "profile-modal";

      if (
        vm.categories.length == 0 &&
        vm.professions.length == 0 &&
        vm.descriptions.length == 0 && //Jobs
        vm.engagements.length == 0 &&
        vm.platform_items.length == 0 &&
        vm.platform_types.length == 0 && //Client Engagements
        vm.contact_types.length == 0 && //Contacts
        vm.brands.length == 0 && //Brands
        vm.document_types.length == 0 && //Attachments
        vm.countries.length == 0 //Location
      ) {
        vm.InitializeProfileResources();
      } else {
        if (
          vm.categories.length == 0 &&
          vm.professions.length == 0 &&
          vm.descriptions.length == 0
        ) {
          vm.InitializeProfileResources("jobs");
        }

        if (
          vm.engagements.length == 0 &&
          vm.platform_items.length == 0 &&
          vm.platform_types.length == 0
        ) {
          vm.InitializeProfileResources("client_engagements");
        }

        if (vm.contact_types.length == 0) {
          vm.InitializeProfileResources("contacts");
        }

        if (vm.brands.length == 0) {
          vm.InitializeProfileResources("brands");
        }

        if (vm.countries.length == 0) {
          vm.InitializeProfileResources("location");
        }

        if (vm.document_types.length == 0)
          vm.InitializeProfileResources("attachments");
      }

      vm.loadProfile(id).then(() => {
        vm.$nextTick(() => {
          vm.form.id = vm.profile.id;
          vm.form.profile_id = vm.profile.id;
          vm.form.first_name = vm.deepCopy(vm.profile.firstname);
          vm.form.middlename = vm.deepCopy(vm.profile.middlename);
          vm.form.surname = vm.deepCopy(vm.profile.surname);
          vm.form.nickname = vm.deepCopy(vm.profile.nickname);
          vm.form.primary_email = vm.deepCopy(vm.profile.primary_email);

          vm.form.notes = vm.deepCopy(vm.profile.notes);

          if (vm.form.notes.length > 0) {
            vm.form.add_notes = true;
          } else {
            vm.form.add_notes = false;
          }

          vm.form.contacts = vm.deepCopy(vm.profile.contacts);

          if (vm.form.contacts.length > 0) {
            vm.form.add_contacts = true;
          } else {
            vm.form.add_contacts = false;
          }

          if (vm.profile.client_location != null) {
            if (vm.profile.client_location.world_countries_id != null) {
              vm.form.location.country_id = vm.deepCopy(
                vm.profile.client_location.world_countries_id
              );
              vm.$nextTick(() => {
                vm.fetchProvinces(vm.form.location.country_id).then(() => {
                  vm.$nextTick(() => {
                    if (vm.profile.client_location.world_provinces_id != null) {
                      vm.form.location.province_id = vm.deepCopy(
                        vm.profile.client_location.world_provinces_id
                      );

                      vm.fetchCities(vm.form.location.province_id).then(() => {
                        vm.$nextTick(() => {
                          if (
                            vm.profile.client_location.world_cities_id != null
                          ) {
                            vm.form.location.city_id = vm.deepCopy(
                              vm.profile.client_location.world_cities_id
                            );
                          }
                        });
                      });
                    }
                  });
                });
              });
            }
          }

          vm.form.jobs = vm.deepCopy(vm.profile.jobs);

          if (vm.form.jobs.length > 0) {
            vm.form.add_jobs = true;
            /*
              //Un comment this if jobs does not sync on edit sync
              vm.$nextTick(() => {
                vm.$refs.profileModal.syncJobs();
              });
            */
          } else {
            vm.form.add_jobs = false;
          }

          vm.form.client_engagements = vm.deepCopy(
            vm.profile.client_engagements
          );

          if (vm.form.client_engagements.length > 0) {
            vm.form.add_engagements = true;
          } else {
            vm.form.add_engagements = false;
          }

          vm.form.attachments_info = vm.deepCopy(vm.profile.attachments);

          //add attachments per attachment info
          vm.form.attachments_info.forEach(function(item) {
            vm.form.attachments.push(null);
          });

          if (vm.form.attachments_info.length > 0) {
            vm.form.add_attachments = true;
          } else {
            vm.form.add_attachments = false;
          }

          vm.form.action = "Update";
          vm.profileSelected = vm.profile;

          item.is_loading = false;

          // Open Modal
          vm.$bvModal.show("profile-modal");
        });
      });
    },

    onDelete(item) {
      let vm = this;
      let id = item.id;
      let profile = item;

      item.is_loading = true;

      if (this.profile_id === id) {
        this.tab_length = this.tab = 0;
        this.viewing_profile = false;
      }

      let confirmation_message = vm.$t("label.profile_delete_confirmation");
      confirmation_message = confirmation_message.replace(
        /%variable%/g,
        "<strong>" + item.full_name + "</strong>"
      );

      $.confirm({
        title:
          vm.$t("confirmation_record_delete").charAt(0) +
          vm
            .$t("confirmation_record_delete")
            .slice(1)
            .toLowerCase(),
        content: confirmation_message,
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

                let notification_message = vm.$t("toasts.deleted_profile");
                notification_message = notification_message.replace(
                  /%variable%/g,
                  item.full_name
                );

                vm.delete_profile(params)
                  .then((resp) => {
                    item.is_loading = false;
                    if (vm.responseStatus) {
                      vm.remove_from_profiles_array(profile);
                      vm.makeToast(
                        "danger",
                        vm.$t("delete_record"),
                        notification_message
                      );
                    }
                  })
                  .catch((error) => {
                    vm.form.errors.record(error.response.data.errors);
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
    setFilter(filter_by) {
      this.isLoading = true;
      let vm = this;

      let params = {
        api_token: vm.$user.api_token,
        brand: vm.$brand.id,
        filter: filter_by,
        filter_value: vm.query_text,
      };

      vm.get_filtered_profiles(params).then(() => {
        vm.$refs.table.refresh();
        vm.isLoading = false;
      });
    },
    resetFilter() {
      this.query_text = null;
      this.isLoading = true;
      this.loadProfiles().then(() => {
        this.$refs.table.refresh();
      });
    },
    goToProfileTab(profile) {
      let vm = this;
      let id = profile.id;
      vm.profile_id = profile.id
      vm.isLoading = true;
      vm.tab_length = 0;
      vm.tab = 0;
      let index = profile.profile_index;
      vm.editingIndex = index;
      profile.is_loading = true;
      vm.resetProfileForm();
      vm.meta.page = "profile-modal";

      if (
        vm.categories.length == 0 &&
        vm.professions.length == 0 &&
        vm.descriptions.length == 0 && //Jobs
        vm.engagements.length == 0 &&
        vm.platform_items.length == 0 &&
        vm.platform_types.length == 0 && //Client Engagements
        vm.contact_types.length == 0 && //Contacts
        vm.brands.length == 0 && //Brands
        vm.document_types.length == 0 && //Attachments
        vm.countries.length == 0 //Location
      ) {
        vm.InitializeProfileResources();
      } else {
        if (
          vm.categories.length == 0 &&
          vm.professions.length == 0 &&
          vm.descriptions.length == 0
        ) {
          vm.InitializeProfileResources("jobs");
        }

        if (
          vm.engagements.length == 0 &&
          vm.platform_items.length == 0 &&
          vm.platform_types.length == 0
        ) {
          vm.InitializeProfileResources("client_engagements");
        }

        if (vm.contact_types.length == 0) {
          vm.InitializeProfileResources("contacts");
        }

        if (vm.brands.length == 0) {
          vm.InitializeProfileResources("brands");
        }

        if (vm.countries.length == 0) {
          vm.InitializeProfileResources("location");
        }

        if (vm.document_types.length == 0)
          vm.InitializeProfileResources("attachments");
      }

      vm.loadProfile(id).then(() => {
        vm.$nextTick(() => {
          vm.form.id = vm.profile.id;
          vm.form.profile_id = vm.profile.id;
          vm.form.first_name = vm.deepCopy(vm.profile.firstname);
          vm.form.middlename = vm.deepCopy(vm.profile.middlename);
          vm.form.surname = vm.deepCopy(vm.profile.surname);
          vm.form.nickname = vm.deepCopy(vm.profile.nickname);
          vm.form.primary_email = vm.deepCopy(vm.profile.primary_email);

          vm.form.notes = vm.deepCopy(vm.profile.notes);
          vm.form.contacts = vm.deepCopy(vm.profile.contacts);

          vm.form.client_engagements = vm.deepCopy(
            vm.profile.client_engagements
          );

          vm.form.attachments_info = vm.deepCopy(vm.profile.attachments);

          //add attachments per attachment info
          vm.form.attachments_info.forEach(function(item) {
            vm.form.attachments.push(null);
          });

          if (vm.form.attachments_info.length > 0) {
            vm.form.add_attachments = true;
          } else {
            vm.form.add_attachments = false;
          }

          vm.form.action = "Update";
          vm.profileViewed = vm.profile;
          vm.profileViewed.index = index;
          console.log(vm.profileViewed);
          profile.is_loading = false;

          vm.tab_length = 1;
          vm.profile_id = profile.id;
          vm.profile_name = profile.full_name;
          vm.tab = 3;
          vm.viewing_profile = true;

          vm.isLoading = false;
        });
      });
    },
  },
};
</script>
