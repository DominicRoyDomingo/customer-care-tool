<template>
  <div class="create">
    <b-modal
      id="actor-edit-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
    >
      <v-app id="create__container">
        <v-card>
          <v-form ref="form" v-model="valid" lazy-validation>
            <v-card-title class="title blue-grey lighten-4 text--secondary">
              <span>
                {{$t(modal.name)}}
              </span>
              <v-spacer></v-spacer>
              <v-btn
                icon
                color="error lighten-2"
                @click="modalClose"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-card-title>
            <v-card-text class="modal__content">
              <v-container>
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
                        required
                      >
                      </v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" class="modal__input-container">
                      <v-text-field
                        v-model="parent.form.firstname"
                        :label="$t('label.first_name')"
                        class="modal__input"
                        autocomplete="off"
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
                    <v-col cols="12" md="12" class="modal__input-container">
                      <label for="field_profession">{{ $t("label.field_of_profession") }}:</label>
                      <v-select
                          name="name"
                          label="base_name"
                          style="z-index: 1000000000000000;"
                          v-model="parent.form.fieldOfProfessions"
                          :options="parent.professionalTerms"
                          multiple
                          id="field_profession"
                      >
                          <template #list-header>
                                <li style="margin-left:20px;" class="mb-2">
                                    <b-link href="#" @click="openMedicalTermModal('profession')">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        {{ $t("button.new_field_profession") }}
                                    </b-link>
                                </li>
                            </template>
                      </v-select>
                    </v-col>
                  </v-row>
                  <!-- <v-row>
                    <v-col cols="12" md="12" class="modal__input-container">
                      <v-select
                          name="name"
                          label="description_name"
                          v-model="parent.form.specialization"
                          :options="parent.newSpecializations"
                          id="physical_code_type"
                      >
                          <template #list-header>
                              <li style="margin-left:20px;" class="mb-2">
                                  <b-link href="#" @click="openJobDescriptionModal">
                                      <i class="fa fa-plus" aria-hidden="true"></i>
                                      {{ $t("job_description_new_button") }}
                                  </b-link>
                              </li>
                          </template>
                      </v-select>
                    </v-col>
                  </v-row> -->
                </v-container>
                    <v-card color="gray lighten-3" outlined tile>
                      <v-overlay
                        v-if="parent.isSpecializationFocused"
                        absolute
                        color="#282828"
                        opacity="0"
                      >
                      </v-overlay>
                      <v-hover>
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
                              {{ $t("label.terminilogies") }}
                            </v-tab>
                            <v-tab class="caption font-weight-bold">
                              {{ $t("table.physical_code") }}
                            </v-tab>
                            <v-tab class="caption font-weight-bold">
                              {{ $t("label.other_info") }}
                            </v-tab>
                            <v-tab-item eager>
                              <v-container>
                                <v-card class="border-0">
                                  <!-- <v-card-title class="bg-light">
                                    {{ $t("label.linking_to") }} {{ $t("label.terminilogies") }}
                                    <small
                                      class="text-dark text-capitalize d-inline-block text-truncate mt-1 ml-1"
                                      style="max-width: 600px; letter-spacing: normal"
                                      v-html="`(${parent.itemSelected.term_name})`"
                                    />
                                    <v-spacer></v-spacer>
                                    <v-btn icon color="error lighten-2" @click="onCancel">
                                      <v-icon>mdi-close</v-icon>
                                    </v-btn>
                                  </v-card-title> -->

                                  <div class="row mt-3 pl-3 pr-3">
                                    <div class="col-sm-12 text-right">
                                      <v-btn color="success" tile @click="openMedicalTermModal">
                                          {{ $t('label.new_term') }}
                                      </v-btn>
                                    </div>
                                    <div class="col-sm-6">
                                      <b-form inline>
                                        <label class="mr-sm-2" for="inline-form-custom-select-pref">
                                          {{ $t("button.show") }}
                                        </label>
                                        <b-form-select
                                          id="inline-form-custom-select-pref"
                                          class="mb-2 mr-sm-2 mb-sm-0"
                                          :options="showEntriesOpt"
                                          v-model="parent.termsPerPage"
                                        />
                                        <label class="mb-2 mr-sm-2 mb-sm-0">
                                          {{ $t("label.entries") }}
                                        </label>
                                      </b-form>
                                    </div>
                                    <div class="col-sm-6">
                                      <b-form @submit.prevent="onSearchSubmit">
                                        <b-input-group class="mb-0 float-right">
                                          <b-input-group-prepend v-show="parent.termsFilter">
                                            <b-button variant="lignt" @click="parent.termsFilter = ''">
                                              <b-icon icon="x" class="text-danger" />
                                            </b-button>
                                          </b-input-group-prepend>

                                          <template #append>
                                            <b-button variant="light" type="submit" :disabled="!parent.termsFilter">
                                              <i class="fa fa-search" :class="{ 'text-primary': parent.termsFilter }" />
                                            </b-button>
                                          </template>
                                          <b-form-input
                                            v-model="parent.termsFilter"
                                            autocomplete="off"
                                            :placeholder="$t('type_and_enter')"
                                          />
                                        </b-input-group>
                                      </b-form>
                                    </div>
                                    <div class="col-md-12 mt-0" style="margin-top: -15px !important">
                                      <b-overlay
                                        id="overlay-background"
                                        :variant="'light'"
                                        opacity="0.85"
                                        :blur="'1px'"
                                        rounded="sm"
                                      >
                                        <b-card no-body>
                                          <template #header>
                                            <h6 class="mb-0 font-weight-bold text-info">
                                              {{ $t("label.terminilogies") }}
                                            </h6>
                                          </template>

                                          <!-- <div v-if="parent.tableTermLoading" class="p-4">
                                            <b-spinner small label="Small Spinner"></b-spinner>
                                            Fetching data...
                                          </div> -->

                                          <b-card-body
                                            id="nav-scroller"
                                            ref="content"
                                            class="p-0"
                                            :class="{ 'div-scroll': parent.termsTotalRow > 7 }"
                                          >
                                            <b-overlay
                                              id="overlay-background"
                                              :show="parent.isTermOverlay"
                                              :variant="'light'"
                                              opacity="0.85"
                                              :blur="'1px'"
                                              rounded="sm"
                                            >
                                              <b-table
                                                class="mb-0"
                                                show-empty
                                                ref="linkedTable"
                                                :sort-by.sync="sortBy"
                                                :sort-desc.sync="sortDesc"
                                                :empty-text="$t('no_record_found')"
                                                :fields="tableHeaders"
                                                :per-page="parent.termsPerPage"
                                                :busy="parent.tableTermLoading"
                                                :items="parent.terminologies"
                                                :tbody-tr-class="bgLightClass"
                                                @filtered="onFiltered"
                                              >
                                                <template v-slot:table-busy>
                                                  <div class="d-flex justify-content-center p-2">
                                                    <b-spinner label="Small Loading..."></b-spinner>
                                                  </div>
                                                </template>

                                                <template v-slot:cell(index)="data">
                                                  <div class="row mb-0 mt-0">
                                                    <div class="col-md-12">
                                                      <span
                                                        :id="`term-${data.index}`"
                                                        class="d-inline-block"
                                                        tabindex="0"
                                                      >
                                                        <b-form-checkbox
                                                          class="check-box"
                                                          v-model="parent.linkedMedicalTerm"
                                                          :disabled="
                                                            data.item.is_loading ||
                                                            !data.item.has_term_types
                                                          "
                                                          :id="`medterm-${data.item.id}`"
                                                          :name="`medterm-${data.item.id}`"
                                                          :value="data.item.id"
                                                        >
                                                          <strong
                                                            :for="`medterm-${data.item.id}`"
                                                            class="text-capitalize check-box"
                                                            v-html="data.item.term_name"
                                                          />

                                                          <b-spinner
                                                            label="Loading..."
                                                            class="ml-2"
                                                            style="position: absolute; margin-top: -2px"
                                                            small
                                                            v-if="data.item.is_loading"
                                                          />
                                                        </b-form-checkbox>
                                                      </span>

                                                      <b-tooltip
                                                        v-if="!data.item.has_term_types"
                                                        variant="danger"
                                                        :target="`term-${data.index}`"
                                                      >
                                                        <p class="mt-2">
                                                          <strong> {{ $t("errors.error") }}! </strong>
                                                          {{ $t("label.linking_to") }}
                                                          <strong> {{ data.item.base_name }} </strong>
                                                          {{ $t("errors.linking_error") }}
                                                        </p>
                                                      </b-tooltip>
                                                    </div>
                                                  </div>
                                                </template>
                                              </b-table>
                                            </b-overlay>
                                          </b-card-body>

                                          <b-card-footer v-if="parent.termsTotalRow > 0">
                                            <b-pagination
                                              class="mb-0"
                                              v-model="parent.termsCurrentPage"
                                              :total-rows="parent.termsTotalRow"
                                              :per-page="parent.termsPerPage"
                                              align="center"
                                              size="sm"
                                            />
                                          </b-card-footer>
                                        </b-card>
                                      </b-overlay>
                                    </div>
                                  </div>
                                  
                                <!-- <CreateTermDescription
                                  :parent="this"
                                  @done-success="create_description_success"
                                /> -->
                                </v-card>
                              </v-container>
                            </v-tab-item>
                            <v-tab-item eager>
                              <v-container>
                                <div class="note-div note-data" 
                                  v-for="(physical_code, index) in parent.form.physical_codes"
                                  v-bind:key="'physical_code_' + index"
                                >
                                  <div class="row no-gutters">
                                    <div class="col-md-11 text-left">
                                      <hr />
                                    </div>
                                    <div class="col-md-1 text-right">
                                      <v-btn fab dark small color="error" @click.prevent="deletePhysicalCode(index)">
                                        <v-icon dark>mdi-close</v-icon>
                                      </v-btn>
                                    </div>
                                  </div>

                                  <div class="form-group">
                                    <label for="physical_code_type">{{ $t("label.physical_code_type") }}</label>
                                      <v-select
                                          name="name"
                                          label="physical_code_type_name"
                                          v-model="parent.form.physical_codes[index].physical_code_type"
                                          :options="parent.newPhysicalCodeTypes"
                                          @input="parent.removeDuplicatePCT"
                                          id="physical_code_type"
                                      >
                                          <template #list-header>
                                              <li style="margin-left:20px;" class="mb-2">
                                                  <b-link href="#" @click="openPhysicalCodeTypeModal">
                                                      <i class="fa fa-plus" aria-hidden="true"></i>
                                                      {{ $t("physical_code_type_new_button") }}
                                                  </b-link>
                                              </li>
                                          </template>
                                      </v-select>
                                  </div>

                                  <div class="form-group">
                                    <label for="physical_code">{{ $t("table.physical_code") }}</label>
                                    <b-form-input 
                                      v-model="parent.form.physical_codes[index].code" 
                                      :placeholder="$t('table.physical_code')"
                                      id="physical_code"
                                    ></b-form-input>
                                  </div>
                                </div>
                                <!-- <physicalCodeDiv
                                    v-for="(physical_code, index) in parent.new_physical_codes"
                                    v-bind:key="'physical_code_' + index"
                                    :root_parent="parent"
                                    :parent="vm"
                                    :index="physical_code.index"
                                    :is_deletable="true"
                                    ref="physicalCodeType"
                                ></physicalCodeDiv> -->
                                <div class="row">
                                    <div class="col-md-12">
                                    <v-btn
                                        tile
                                        block
                                        color="success lighten-1"
                                        @click.prevent="parent.addPhysicalCode"
                                    >
                                        <v-icon>mdi-sticker-plus</v-icon>&nbsp;
                                        {{ $t("label.add_physical_code") }}
                                    </v-btn>
                                    </div>
                                </div>
                              </v-container>
                            </v-tab-item>
                            <v-tab-item eager>
                              <v-container>
                                <div class="note-div note-data" 
                                    v-for="(other_info, index) in parent.form.other_infos"
                                    v-bind:key="'other_info_' + index">
                                  <div class="row no-gutters">
                                    <div class="col-md-11 text-left">
                                      <hr />
                                    </div>
                                    <div class="col-md-1 text-right">
                                      <v-btn fab dark small color="error" @click.prevent="deleteOtherInfoDiv(index)">
                                        <v-icon dark>mdi-close</v-icon>
                                      </v-btn>
                                    </div>
                                  </div>

                                  <div class="form-group">
                                    <label for="information_type">{{ $t("label.information_type") }}</label>
                                      <v-select
                                          name="name"
                                          label="base_name"
                                          v-model="parent.form.other_infos[index].type"
                                          :options="parent.newInformationTypes"
                                          id="information_type"
                                      >
                                          <template #list-header>
                                              <li style="margin-left:20px;" class="mb-2">
                                                  <b-link href="#" @click="openInformationTypeModal">
                                                      <i class="fa fa-plus" aria-hidden="true"></i>
                                                      {{ $t("information_type_new_button") }}
                                                  </b-link>
                                              </li>
                                          </template>

                                          <template #option="{ information_type_name }">
                                            <span v-html="information_type_name" />
                                          </template>
                                      </v-select>
                                  </div>

                                  <div class="form-group" v-if="parent.form.other_infos[index].type != null">
                                    <div v-if="parent.form.other_infos[index].type.type_of_data == 'Short Text'">
                                      <v-text-field
                                        v-model="parent.form.other_infos[index].value"
                                        :rules="[parent.rules.required]"
                                        :label="$t('short_text')"
                                      ></v-text-field>
                                    </div>

                                    <div v-if="parent.form.other_infos[index].type.type_of_data == 'Email Format'">
                                      <v-text-field
                                        v-model="parent.form.other_infos[index].value"
                                        :rules="[parent.rules.required, parent.rules.email]"
                                        :label="$t('label.email')"
                                      ></v-text-field>
                                    </div>

                                    <div v-if="parent.form.other_infos[index].type.type_of_data == 'Numeric'">
                                      <v-text-field
                                        v-model="parent.form.other_infos[index].value"
                                        :rules="[parent.rules.required, parent.rules.number]"
                                        single-line
                                        type="number"
                                        :label="$t('numeric')"
                                      ></v-text-field>
                                    </div>

                                    <div v-if="parent.form.other_infos[index].type.type_of_data == 'Date'">
                                      <v-menu content-class="elevation-0" >
                                        <template v-slot:activator="{on}">
                                          <v-text-field
                                            v-model="parent.form.other_infos[index].value"
                                            v-on="on"
                                            :label="$t('date')"
                                            :rules="[parent.rules.required]"
                                          ></v-text-field>
                                        </template>
                                        <v-date-picker v-model="parent.form.other_infos[index].value" elevation="15"></v-date-picker>
                                      </v-menu>
                                    </div>

                                    <div v-if="parent.form.other_infos[index].type.type_of_data == 'Long Text'">
                                      <v-textarea
                                        :rules="[parent.rules.required]"
                                        v-model="parent.form.other_infos[index].value"
                                        :label="$t('long_text')"
                                      ></v-textarea>
                                    </div>
                                  </div>
                                </div>
                                <!-- <otherInfoDiv
                                    v-for="(other_info, index) in parent.new_other_infos"
                                    v-bind:key="'other_info_' + index"
                                    :root_parent="parent"
                                    :parent="vm"
                                    :index="other_info.index"
                                    :is_deletable="true"
                                    ref="otherInfoDiv"
                                ></otherInfoDiv> -->
                                <div class="row">
                                    <div class="col-md-12">
                                    <v-btn
                                        tile
                                        block
                                        color="success lighten-1"
                                        @click.prevent="parent.addOtherInfoDiv"
                                    >
                                        <v-icon>mdi-sticker-plus</v-icon>&nbsp;
                                        {{ $t("label.add_other_info") }}
                                    </v-btn>
                                    </div>
                                </div>
                              </v-container>
                            </v-tab-item>
                          </v-tabs>
                      </v-hover>
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
                  @click="modalClose"
                >
                  {{ $t("cancel") }}
                </v-btn>
                <v-btn
                  color="success"
                  tile
                  @click="onSubmit"
                >
                  <div v-if="this.modal.button.loading" class="text-center">
                    <v-progress-circular
                      size="20"
                      width="1"
                      color="white"
                      indeterminate
                    >
                    </v-progress-circular>
                  </div>
                  <div v-else>
                    <div>
                      {{ $t(modal.button.update) }}
                    </div>
                  </div>
                </v-btn>
              </v-card-actions>
          </v-form>
        </v-card>
      </v-app>
    </b-modal>
  </div>
</template>

<script>
// import Api from "./../../../shared/api.js";

import { mapActions, mapGetters } from "vuex";

import TermDescriptionIndex from "../../medical_stuff/snippets/term_descriptions/index";

// Modals
import CreateTermDescription from "../../medical_stuff/modals/create-termDescription.modal";

import medicalMixin from "../../medical_stuff/mixins/medicalMixin";
import termMixin from "../../medical_stuff/mixins/termMixin";

export default {
  props: ["parent"],

  components: {
    CreateTermDescription,
    TermDescriptionIndex,
  },

  mixins: [medicalMixin, termMixin],

  data: function() {
    return {
      modal: this.parent.modal.edit,

      submitted: false,

      keep_open: false,

      valid: true,

      vm: this,

      form: this.parent.form,

      formData: this.parent.formData,

      tabIndex: 0,

      formGroups: [],

      files: [],

      modalServiceType: {
        add: {

        },
        
        createService: {
          name: "service_type_add_new_button",

          id: "service-type-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "service_type_add_new_button",

            loading: false,
          },
        },
      },

      langs: [
        { id: "en", value: "English" },

        { id: "it", value: "Italian" },

        { id: "de", value: "German" },
      ],

      sortBy: "is_linked_term",
      sortDesc: true,

      isPaginate: false,
      tableTermLoading: true,
      currentPage: 1,
      perPage: 10,
      totalRow: 1,
      filter: null,

      parent_id: this.parent.form.id,
      linked_medical_terms: this.parent.form.medical_terms,

      selectedTerm: "",
      term: "",
      isOverlay: false,

      description: "",
      medtermId: [],
      termName: "",
      selected: "",
      withValue: "",
      terms: [],

      tableHeaders: [
        {
          key: "index",
          thClass: "text-left",
          label: this.$t("label.linking_to") + " ?",
          filterByFormatted: true,
        },
      ],
    };
  },

  computed: {
    ...mapGetters("categ_terms", {
      descriptions: "get_term_descriptions_items",
      termItems: "get_terms_items",
      termtypes: "get_type_terms_items",
    }),

    terms_types_id() {
      let items = [];
      this.terms.forEach((ele) => {
        if (ele.has_term_types) {
          ele.has_term_types.forEach((type) => {
            items.push(type.id);
          });
        }
      });

      return items;
    },
  },

  methods: {
    ...mapActions("actor", ["post_actor", "update_actor"]),
    ...mapActions("categ_terms", ["get_terms_descriptions", "get_terms"]),
    
    modalClose(done) {

      $(".error-provider").remove();

      $("#name, #service_type").removeAttr(
        "style"
      );
      
      this.file = "";

      this.parent.$bvModal.hide("actor-edit-modal");
      this.parent.termsFilter = "";
      this.form.reset();

      this.keep_open = false;
    },

    deleteOtherInfoDiv: function(index) {
      let vm = this;

      $.confirm({
        title: this.$t("confirm_action"),
        content: vm.$t("label.delete_other_info_confirmation"),
        type: "red",
        typeAnimated: true,
        buttons: {
          tryAgain: {
            text: this.$t("confirm"),
            btnClass: "btn-red",
            action: function() {
              vm.parent.deleteOtherInfoDiv(index);
            },
          },
          cancel: function() {},
        },
      });
    },

    openInformationTypeModal() {
      // this.modal.isVisible = false;
      this.parent.$bvModal.hide(this.parent.modalId);

      this.parent.$bvModal.show("information-type-add-modal");
      // this.$this.modal.itemType.isVisible = true;
    },

    deletePhysicalCode: function(index) {
      let vm = this;

      $.confirm({
        title: this.$t("confirm_action"),
        content: vm.$t("label.delete_phsyical_code_confirmation"),
        type: "red",
        typeAnimated: true,
        buttons: {
          tryAgain: {
            text: this.$t("confirm"),
            btnClass: "btn-red",
            action: function() {
              vm.parent.deletePhysicalCode(index);
            },
          },
          cancel: function() {},
        },
      });
    },

    openPhysicalCodeTypeModal() {
      // this.modal.isVisible = false;
      this.parent.$bvModal.hide(this.parent.modalId);

      this.parent.$bvModal.show("physical-code-type-add-modal");
      // this.$this.modal.itemType.isVisible = true;
    },

    deleteSpecializationDiv: function(index) {
      let vm = this;

      $.confirm({
        title: this.$t("confirm_action"),
        content: vm.$t("label.delete_specialization_confirmation"),
        type: "red",
        typeAnimated: true,
        buttons: {
          tryAgain: {
            text: this.$t("confirm"),
            btnClass: "btn-red",
            action: function() {
              vm.parent.deleteSpecializationDiv(index);
            },
          },
          cancel: function() {},
        },
      });
    },

    openJobDescriptionModal() {
      // this.modal.isVisible = false;
      this.parent.$bvModal.hide(this.parent.modalId);

      this.parent.loadProfessions()
      // this.$this.modal.itemType.isVisible = true;
    },

    openJobCategoryModal() {
      // this.modal.isVisible = false;
      this.parent.$bvModal.hide(this.parent.modalId);

      this.parent.$bvModal.show('job-category-modal')
      // this.$this.modal.itemType.isVisible = true;
    },

    openMedicalTermModal(hasProfession = null) {
      this.parent.mtForm.action = "Add";
      this.parent.mtForm.term_types = [];
      if(hasProfession == 'profession') {
        this.parent.mtForm.term_types = [this.parent.searchProfession(this.termtypes)]
      }
      this.$bvModal.hide(this.parent.modalId);
      this.$bvModal.show("term-modal");
    },

    onSubmit() {
      let fieldOfProfessions = [];
      let physicalCodes = [];
      let otherInfos = [];

      this.parent.form.fieldOfProfessions.forEach(function (data) {
        if (data.length == 0) return;
        fieldOfProfessions.push(data.id);
      });

      if (fieldOfProfessions.length == 0) fieldOfProfessions = "";

      this.parent.form.physical_codes.forEach(function(data) {
        if(data.code == "" || data.physical_code_type == undefined) return;
        physicalCodes.push({
          index: data.index,
          code: data.code,
          physical_code_type: data.physical_code_type.id,
        })
      });

      if(physicalCodes.length == 0) physicalCodes = ""

      this.parent.form.other_infos.forEach(function(data) {
        if(data.type == "" || data.value == "") return;
        otherInfos.push({
          index: data.index,
          type: data.type.id,
          value: data.value,
        })
      });

      if(otherInfos.length == 0) otherInfos = ""
      
      let params = {
        api_token: this.$user.api_token,
        id: this.form.id,
        locale: this.$i18n.locale,
        lang: this.form.language,
        firstname: this.form.firstname,
        surname: this.form.surname,
        middlename: this.form.middlename,
        field_of_professions: fieldOfProfessions,
        physical_code: physicalCodes,
        brand_id: this.$brand.id,
        other_info: otherInfos,
        linked_medical_terms: this.parent.linkedMedicalTerm
      };
      this.post_actor(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("actor-edit-modal");
          if (this.parent.actorsResponseStatus) {
            let response = this.parent.actorsResponseStatus;
            response.index = this.parent.editingIndex;
            this.update_actor(response);

            let message = {
              update:
                this.$t("updated_message1") +
                this.$t("label.actors") +
                ` ID: ${this.parent.form.id} ` +
                this.$t("updated_message2"),
              title: {
                update: this.$t("record_updated"),
              },
            };

            this.parent.makeToast(
              "success",
              message.title.update,
              message.update
            );

            this.parent.successfullySavedActor();
          }
        })
        .catch((error) => {
          console.log(error);
          let errors = error.response.data.errors;
          this.toastError(errors);
        });
    },

    toastError(error){
      console.log(error);
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        this.parent.makeToast("danger", vm.$t('errors.error'), value);
      }
    },

    async loadTermDescriptions() {
      let params = {
        ...this.defaultParams(),
        filter: "",
      };

      this.get_terms_descriptions(params).then((_) => {
        if (this.term) {
          this.$nextTick(() => {
            this.term.is_rendered = true;
          });
        }
      });
    },

    onFiltered(items) {
      this.totalRow = items.length;
      this.currentPage = 1;
    },

    bgLightClass(item, type) {
      let key = false;
      if (item) {
        this.parent.linkedMedicalTerm.forEach((id) => {
          if (id === item.id) {
            key = true;
          }
        });
      }
      return key ? "bg-light" : "";
    },

    onSearchSubmit() {
      this.isOverlay = true;
      this.linked_medical_terms = JSON.stringify(this.parent.linkedMedicalTerm);
      this.parent.loadTerms().catch((error) => {
        console.log(error);
      });
    },

    create_description_success(item) {
      const records = this.$t("label.descriptions");

      if (this.term) {
        this.term.is_rendered = false;
      }

      if (this.termDescForm.action === "Add") {
        this.storeToast(item.description_name, records);
      }

      this.loadTermDescriptions().catch((error) => {
        console.log(error);
      });
    },

    delete_description_success(item) {
      this.$emit("link-success");
    },

    create_term_connection_success(item) {
      this.$emit("link-success");
    },

    onAddDescription() {
      this.termConform.reset();
      this.termConform.action = "Add";
      this.$root.$emit("bv::hide::popover");
    },

    onCancel() {
      this.parent.mtForm.reset();
      this.parent.articleForm.reset();
      this.parent.itemSelected = "";
      // this.parent.filter = "";
      this.$bvModal.hide("link-term-modal");
    },

    onLinkMessage(id) {
      let key = false;
      this.parent.linkedMedicalTerm.forEach((ele) => {
        if (ele === id) {
          key = true;
        }
      });
      return key;
    },

    onLinked(term, index) {
      let checked = $(`#medterm-${term.id}`).prop("checked");
      this.termName = term.base_name;
      term.is_loading = true;

      if (!checked) {
        this.unLinkConfirm()
          .then((resp) => {
            term.is_rendered = false;
            this.postLink(term, checked);
          })
          .catch((error) => {
            term.is_loading = false;
            this.parent.linkedMedicalTerm.push(term.id);
          });

        return false;
      }

      term.is_rendered = false;
      this.postLink(term, checked);
    },

    linkQuery(data) {
      let params = {
        id: this.parent_id ? this.parent_id : this.parent.id,
        child_id: data.term.id,
        term_types: data.term.has_term_types,
        api_token: this.$user.api_token,
        key: data.key ? "link" : "unlink",
      };

      return new Promise((resolve, reject) => {
        axios
          .post("/api/links/term", params)
          .then((response) => resolve(response))
          .catch((error) => reject(error));
      });
    },

    postLink(term, checked) {
      this.linkQuery({ term: term, key: checked })
        .then((resp) => {
          if (checked) {
            // term.is_linked_term = true;
            this.parent.linkedToast(
              term.base_name,
              this.$t("label.terminilogies")
            );
          } else {
            this.parent.unlinkedToast(
              term.base_name,
              this.$t("label.terminilogies")
            );
            term.is_linked_term = false;
            term.has_descriptions = [];
          }
          this.$emit("link-success");
        })
        .catch((error) => {
          const index = this.parent.linkedMedicalTerm.indexOf(term.id);
          if (index > -1) {
            this.parent.linkedMedicalTerm.splice(index, 1);
          }
          this.$bvToast.show("link-error-toast");
        })
        .finally(() => {
          term.is_loading = false;
          this.$nextTick(() => {
            term.is_rendered = true;
          });
        });
    },

    show_modal(term, modalname) {
      this.term = term;
      this.termDescForm.reset();
      this.termDescForm.action = "Add";
      this.$bvModal.show(modalname);
    },
  },
};
</script>

<style></style>
