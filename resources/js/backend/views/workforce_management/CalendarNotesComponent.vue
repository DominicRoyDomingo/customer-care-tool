<template>
<div class="animated fadeIn">
    <v-app id="tags__container">
      <v-card tile flat>
        <v-card-title>
          <v-row>
            <v-col>
              <span class="title font-weight-light text--secondary">
                {{ this.brand_session.name }}  {{ $t("calendar_notes_list") }}
              </span>
            </v-col>
            <v-spacer> </v-spacer>
            <v-col>
              <v-btn
                color="success"
                @click="createCalendarNotes()"
                class="float-right"
                tile
              >
                <v-icon dark>mdi-newspaper-plus</v-icon>
                <!-- <v-icon small dark style="margin-top: 15px; margin-left: -3px;">
                  mdi-plus
                </v-icon> -->
                &nbsp;
                {{ $t(modal.add.button.new) }}
              </v-btn>
            </v-col>
          </v-row>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col  cols="12" sm="6" md="2"
                class="caption font-weight-regular text--secondary text-right"
                style="display:flex; padding-top: 15px"
              >
                {{ $t("button.show") }}
                <b-form-select
                  :options="showEntriesOpt"
                  v-model="perPage"
                  style="border-radius: 0; width: 40% !important; margin-top: -8px; margin-left: 5px; margin-right: 5px"
                />{{ $t("label.entries") }}
            </v-col>
            <v-col cols="12" sm="6" md="2">
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
                      style="height: 33px; min-width: 64px; padding: 0 16px; margin-right: 7px; margin-top: -5px;"
                    >
                      <v-icon :size="18">
                        mdi-filter-menu
                      </v-icon>
                    </v-btn>
                  </template>
                  <v-list dense class="profile__row-menu">
                    <v-list-item-group color="primary">
                      <v-list-item v-for="(option, index) in noTranslationsOptions" :key="index">
                        <v-list-item-content @click="sortbyLang(option.value)">
                          <v-list-item-title>
                            {{ option.text }}
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list-item-group>
                  </v-list>
                </v-menu>
              </b-input-group-prepend>
            </v-col>
            <v-spacer></v-spacer>
            <v-col cols="12" sm="6" md="6">
              <div class="float-right">
                <b-form inline style="margin-right:-8px">
                  <b-input-group class="mb-2 mr-sm-2">
                    <template #append>
                      <b-input-group-text>
                        <i class="fa fa-search" aria-hidden="true"></i>
                      </b-input-group-text>
                    </template>
                    <b-form-input
                      v-model="filter"
                      id="inline-form-input-username"
                      :placeholder="$t('search_here')"
                      style="width: 300px"
                      type="search"
                    />
                  </b-input-group>
                </b-form>
              </div>
            </v-col>
          </v-row>
          <v-row>
            <v-container fluid>
              <b-table
                striped
                show-empty
                stacked="md"
                ref="table"
                :fields="tableHeaders"
                :current-page="currentPage"
                :per-page="perPage"       
                :filter="filter"
                :busy="isLoading"
                :items="items"
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc"
                @sort-changed="sortChanged"
                v-model="calendarNotesTable"
              >

                <template v-slot:cell(title)="data">
                  <div style="margin-top: 10px" class="title_show" @click="onShowDescription(data.item, data.index)">
                    <span
                      class="text-left text--secondary"
                    >
                      {{ data.item.title }}
                      <small
                        v-if="data.item.convertion == true"
                        style="color:red"
                      >
                        (No {{ data.item.language }} translation yet)</small
                      >
                    </span>
                  </div>
                </template>

                <template v-slot:cell(type_name)="data">
                  <div style="margin-top: 10px">
                    <span class="text-left text--secondary">
                      {{ data.item.type_name }}
                      <small
                        v-if="data.item.convertion_type == true"
                        style="color:red"
                      >
                        (No {{ data.item.language }} translation yet)</small
                      >
                    </span>
                  </div>
                </template>

                <template v-slot:cell(target_date_from_to)="data">
                  <div style="margin-top: 10px">
                    <span class="text-left text--secondary">
                      {{ data.item.target_date_from_to }}
                    </span>
                  </div>
                </template>

                <!-- <template v-slot:cell(target_date_to)="data">
                  <div style="margin-top: 10px">
                    <span class="text-left text--secondary">
                      {{ data.item.target_date_to | moment("DD/MM/YYYY") }}
                    </span>
                  </div>
                </template> -->

                <!-- <template v-slot:cell(target_location)="data">
                  <div style="margin-top: 10px">
                    <span class="text-left text--secondary">
                      {{ data.item.target_location }}
                    </span>
                  </div>
                </template> -->

                <template v-slot:cell(country)="data">
                  <div style="margin-top: 10px;">

                      <span class="text-left text--secondary" v-if="$i18n.locale === 'en' && data.item.target_location === 'All' 
                      || data.item.target_location === 'Alles' 
                      || data.item.target_location === 'Tutte' 
                      || data.item.target_location === 'Lahat' 
                      || data.item.target_location === 'Tanan' "> All </span>
                      <span class="text-left text--secondary" v-else-if="$i18n.locale === 'de' && data.item.target_location === 'All' 
                      || data.item.target_location === 'Alles' 
                      || data.item.target_location === 'Tutte' 
                      || data.item.target_location === 'Lahat' 
                      || data.item.target_location === 'Tanan' "> Alles </span>
                      <span class="text-left text--secondary" v-else-if="$i18n.locale === 'it' && data.item.target_location === 'All' 
                      || data.item.target_location === 'Alles' 
                      || data.item.target_location === 'Tutte' 
                      || data.item.target_location === 'Lahat' 
                      || data.item.target_location === 'Tanan' "> Tutte </span>
                      <span class="text-left text--secondary" v-else-if="$i18n.locale === 'ph-fil' && data.item.target_location === 'All' 
                      || data.item.target_location === 'Alles' 
                      || data.item.target_location === 'Tutte' 
                      || data.item.target_location === 'Lahat' 
                      || data.item.target_location === 'Tanan' "> Lahat </span>
                      <span class="text-left text--secondary" v-else-if="$i18n.locale === 'ph-bis' && data.item.target_location === 'All' 
                      || data.item.target_location === 'Alles' 
                      || data.item.target_location === 'Tutte' 
                      || data.item.target_location === 'Lahat' 
                      || data.item.target_location === 'Tanan' "> Tanan </span>
                      <span class="text-left text--secondary" v-else v-for="( country, index ) in data.item.country">
                          {{ country.name }}
                          <span v-if="data.item.country.length > index+1">,</span>
                      </span>
                  </div>
                </template>

                <template v-slot:cell(actions)="data">
                  <div style="margin-top: 10px">
                    <v-menu offset-y>
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
                          <v-list-item @click="onEdit(data.item, data.index)">
                            <v-list-item-icon>
                              <v-icon color="yellow darken-3">
                                mdi-map-marker-check
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("button.edit") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="onDelete(data.item, data.index)">
                            <v-list-item-icon>
                              <v-icon color="red lighten-1">
                                mdi-map-marker-remove
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("button.delete") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="onLinkBrand(data.item, data.index)">
                            <v-list-item-icon>
                              <v-icon color="blue lighten-1">
                                mdi-briefcase-plus
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("label.link_to_brand") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="onShowDescription(data.item, data.index)">
                            <v-list-item-icon>
                              <v-icon color="blue lighten-1">
                                mdi-eye
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("show_description") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                        </v-list-item-group>
                      </v-list>
                    </v-menu>
                  </div>
                </template>
                
                <template slot="empty">
                  <section class="section">
                    <div class="content has-text-grey has-text-centered">
                      <center>{{ $t("table.empty") }}</center>
                    </div>
                  </section>
                </template>
              </b-table>
            </v-container>
          </v-row>
          <v-row>
            <v-spacer>
              <v-col cols="12" sm="6" md="4">
                <span>
                  Showing
                  <strong v-text="calendarNotesTable.length" />
                  of
                  <strong v-text="totalRows" />
                  {{ $t("label.entries") }}
                </span>
              </v-col>
            </v-spacer>
            <v-col>
              <v-spacer>
                <b-pagination
                  v-if="!this.isLoading"
                  v-model="currentPage"
                  :total-rows="totalRows"
                  :per-page="perPage"
                  style="float: right"
                ></b-pagination>
              </v-spacer>
            </v-col>
          </v-row>
        </v-card-text>

      <brandModal :parent="this"  @loadTable="loadBrand" @hide="closeBrandModal"></brandModal>
      <CalendarNotesType :parent="this"  @loadTable="loadCalendarNotesType" @hide="closeCalendarNotesTypeModal"></CalendarNotesType>
      <LinkBrandModal :$this="this"></LinkBrandModal>

      </v-card>

      <CalendarNotesDescription :$this="this" :title="title" :description="description"></CalendarNotesDescription>

      <Create :$this="this" @loadTable="loadItems"></Create>

      <Edit :$this="this" @loadTable="loadItems"></Edit>

      
    </v-app>
  </div>
  
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import Create from "./Calendar-notes/CalendarNotesCreateComponent.vue";

import Edit from "./Calendar-notes/CalendarNotesUpdateComponent.vue";

import CalendarNotesType from "./modals/CalendarNotesType.modal.vue";

import brandModal from "./modals/brand.modal.vue";

import LinkBrandModal from "./modals/LinkBrandComponent.vue";

import CalendarNotesDescription from "./Calendar-notes/CalendarNotesContent.vue";

import Loading from "vue-loading-overlay";

import Form from "./../../shared/form.js";

import toast from "./../../helpers/toast.js";

export default {
  mixins: [toast],
  props: [ 'brand_session' ],
  components: {
    Create,

    Loading,

    Edit,

    brandModal,

    LinkBrandModal,

    CalendarNotesDescription,

    CalendarNotesType
    
  },

  data: function() {
    return {
      isLoading: true,

      btnloading: false,

      filter: "",

      tableTotalRows: "",

      searched: "",

      currentPage: 1,

      perPage: 10,

      sortBy: 'name',

      files: [],

      sortDesc: false,

      showEntriesOpt: [
        { value: 10, text: "10" },

        { value: 30, text: "30" },

        { value: 50, text: "50" },

        { value: 100, text: "100" },
      ],
      calendarNotesTable: [],
      tableHeaders: [
        {
          key: "title",
          label: this.$t("label.title"),
          thClass: "text-left",
          sortable: true,
        },

        {
          key: "type_name",
          label: this.$t("calendar_type"),
          thClass: "text-left",
          sortable: true,
        },

        {
          key: "target_date_from_to",
          label: this.$t("table.target_date_from"),
          thClass: "text-center",
          tdClass: "text-center",
          thStyle: { width: "25%" },
          sortable: true,
        },

        // {
        //   key: "target_date_to",
        //   label: this.$t("table.target_date_to"),
        //   thClass: "text-center",
        //   tdClass: "text-center",
        //   sortable: true,
        // },

        {
          key: "country",
          label: this.$t("table.target_location"),
          thClass: "text-center",
          tdClass: "text-center",
          thStyle: { width: "20%" },
          sortable: true,
        },

        // {
        //   key: "country",
        //   label: this.$t("table.country"),
        //   thClass: "text-center",
        //   tdClass: "text-center",
        //   sortable: true,
        // },

        {
          key: "actions",
          label: this.$t("table.action"),
          thClass: "text-center",
          thStyle: { width: "20%" },
          tdClass: "text-center",
        },
      ],

      modal: {
        add: {
          name: "calendar_notes_add_new_button",

          id: "calendar-notes-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "calendar_notes_add_new_button",

            loading: false,
          },
        },

        brand: {
          isVisible: false,
        },

        calendar: {
          isVisible: false,
        },
        
        edit: {
          name: "calendar_notes_edit_button",

          id: "calendar-notes-edit-modal",

          isVisible: false,

          button: {
            update: "button.save_change",

            cancel: "cancel",

            new: "calendar_notes_add_new_button",

            loading: false,
          },
        },

        createCalendarNotes: {
          name: "calendar_notes_add_new_button",

          id: "calendar-notes-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "calendar_notes_add_new_button",

            loading: false,
          },
        },
      },

      form: new Form({

        id: "",

        title: "",

        content: "",

        type: "",

        target_date_from: new Date().toISOString().substr(0, 10),

        target_date_to: new Date().toISOString().substr(0, 10),

        target_location: "",

        country: [],

        brand: [],

        default: '',

        language: this.$i18n.locale,
      }),

      formData: new FormData(),

      link_option: '',

      target_location: [ this.$t('all'), this.$t('selected_country') ],

      title: '',

      description: '',

      default: '',

      convertion: '',

      language: '',

      sortbyLangId: '',

      noTranslationsOptions: [
        { value: 'all', text: "All", disabled: true },
        { value: "en", text: "No English Translation" },
        { value: "de", text: "No German Translation" },
        { value: "it", text: "No Italian Translation" },
        { value: "ph-fil", text: "No Filipino Translation" },
        { value: "ph-bis", text: "No Visayan Translation" },
      ],
        
    };

  },

  mounted() {
    this.loadItems();
  },

  methods: {
    ...mapActions("calendar_notes", [
      "get_calendar_notes",
      "delete_calendar_notes",
      "remove_from_calendar_notes_array",
    ]),
    ...mapActions("location", ["get_countries"]),

    ...mapActions("brand", ["get_brands"]),

    ...mapActions("calendar_notes_type", [
      "get_calendar_notes_type"
    ]),

    async loadItems() {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,
        lang: this.$i18n.locale,
        entries: this.perPage,
        page: this.currentPage,
        sort_name: this.sortBy,
        sort_desc: this.sortDesc,
        search: this.searched,
        org_id: this.$org_id,
        sortbyLang: this.sortbyLangId,
        brand: ( this.brand_session.id !== undefined || this.brand_session.id !== '' ? this.brand_session.id : null )
      };
      this.get_calendar_notes(params).then((_) => {
        this.tableTotalRows = this.items.total
        this.isLoading = false;
      });
      this.get_countries(params).then((_) => {
      });
      this.get_calendar_notes_type(params).then((_) => {
      });
    },

    sortbyLang(value) {
      this.sortbyLangId = value;
      this.loadItems();
    },
    
    successfullySavedRequestType() {
      this.$refs.table.refresh();
    },

    createCalendarNotes() {
      this.form.reset();
      this.modalId = this.modal.add.id;
      this.form.language = this.$i18n.locale;
      this.form.brand = JSON.parse(JSON.stringify( this.brand_session.id !== undefined || this.brand_session.id !== '' ? this.brand_session.id : null ) );
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
      };
      // this.get_calendar_notes(params).then((_) => {
          this.form.target_date_from = new Date().toISOString().substr(0, 10);
          this.form.target_date_to = new Date().toISOString().substr(0, 10);
        this.$bvModal.show("calendar-notes-add-modal");
      // });
    },

    onShowDescription(item, index) {
        this.title = item.title;
        let descript = item.content;
        if( item.content === '' || item.content === null || item.content === 'null' ) {
          descript = '<i><small>No Description</small></i>';
        }
        this.description = descript;
        this.default = item.title;
        this.language = item.language;
        this.convertion = item.convertion;
        this.$bvModal.show("calendar-notes-description-modal");
    },

    openBrandModal() {
      // this.form.reset();
      this.form.action = "Add";
      this.form.actual_action = "Add";
      this.modal.brand.isVisible = false;
      this.$bvModal.show("brand-modal");
    },

    closeBrandModal() {
      this.$bvModal.hide("brand-modal");
    },

    openCalendarNotesTypeModal() {
      // this.form.reset();
      this.form.action = "Add";
      this.form.actual_action = "Add";
      this.modal.calendar.isVisible = false;
      this.$bvModal.show("calendar-modal");
    },

    closeCalendarNotesTypeModal() {
      this.$bvModal.hide("calendar-modal");
    },

    onLinkBrand(item, index) {
        //   this.form.reset();
      this.editingIndex = index;
      this.form.language = this.$i18n.locale;
      this.form.id = item.id;
      this.form.brand = JSON.parse( item.brands );
      this.modalId = this.modal.brand.id;
      this.link_option = 'post_link_brand_calendar_notes';
      this.$bvModal.show("link-brand-modal");
    },

    loadBrand() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        entries: this.perPage,
        page: this.currentPage,
        sort_name: this.sortBy,
        sort_desc: this.sortDesc,
        search: this.searched,
        org_id: this.$org_id
      };
      this.get_brands(params).then((_) => {
      });
    },

    loadCalendarNotesType() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        entries: this.perPage,
        page: this.currentPage,
        sort_name: this.sortBy,
        sort_desc: this.sortDesc,
        search: this.searched,
        org_id: this.$org_id
      };
      this.get_calendar_notes_type(params).then((_) => {
      });
    },

    loadCalendarNotes() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        entries: this.perPage,
        page: this.currentPage,
        sort_name: this.sortBy,
        sort_desc: this.sortDesc,
        search: this.searched,
        org_id: this.$org_id,
        brand: ( this.brand_session.id !== undefined || this.brand_session.id !== '' ? this.brand_session.id : null )
      };
      this.get_calendar_notes(params).then((_) => {
        this.$bvModal.hide("calendar-notes-add-modal");
        this.$bvModal.show(this.modalId);
      });
    },

    onEdit(item, index) {
        // var date = new Date();
        console.log(item);

        this.editingIndex = index;
        this.form.reset();
        this.form.language = this.$i18n.locale;
        this.loadCalendarNotes();
        this.form.id = item.id;
        this.default = item.title;
        this.language = item.language;
        this.convertion = item.convertion;
        this.form.title = ( item.convertion === true ? '' : item.title );
        this.form.content = item.content;
        this.form.type = item.type;
        var date = new Date();
        this.form.target_date_from = date.toISOString().substr(0, 10);
        this.form.target_date_to = date.toISOString().substr(0, 10);
        // this.form.target_date_from = new Date( new Date( item.target_date_from ) - (date.getTimezoneOffset() * 60000  )).toISOString().substr(0, 10);
        // this.form.target_date_to = new Date( new Date( item.target_date_to ) - (date.getTimezoneOffset() * 60000  )).toISOString().substr(0, 10) ;
          this.form.country = item.id;
        if( item.target_location === 'Selected Location' ||  
            item.target_location === 'Ausgewählter Ort' ||  
            item.target_location === 'Posizione selezionata' ||  
            item.target_location === 'Pinili nga Lokasyon'  ||  
            item.target_location === 'Napiling Lokasyon' ){
            if( this.$i18n.locale === 'en' ) { this.form.target_location = 'Selected Location'; }
            if( this.$i18n.locale === 'de' ) { this.form.target_location = 'Ausgewählter Ort'; }
            if( this.$i18n.locale === 'it' ) { this.form.target_location = 'Posizione selezionata'; }
            if( this.$i18n.locale === 'ph-fil' ) { this.form.target_location = 'Napiling Lokasyon'; }
            if( this.$i18n.locale === 'ph-bis' ) { this.form.target_location = 'Pinili nga Lokasyon'; }

            // this.form.target_location = item.target_location;
            
            var country_array = [];
            $.each( item.country, function(i,k){
                country_array.push(k.id);
            })
            this.form.country = country_array;
        } else {
          if( this.form.target_location !== '' ) {
            if( this.$i18n.locale === 'en' ) { this.form.target_location = 'All'; }
            if( this.$i18n.locale === 'de' ) { this.form.target_location = 'Alles'; }
            if( this.$i18n.locale === 'it' ) { this.form.target_location = 'Tutte'; }
            if( this.$i18n.locale === 'ph-fil' ) { this.form.target_location = 'Lahat'; }
            if( this.$i18n.locale === 'ph-bis' ) { this.form.target_location = 'Tanan'; }
          }
        }
        this.form.brand = JSON.parse( item.brands );
        this.modalId = this.modal.edit.id;
    },

    sortChanged(e) {
      console.log(e.sortBy)
      this.sortBy = e.sortBy
      this.sortDesc = e.sortDesc
      this.loadItems()
    },

    onDelete(item, index) {
      let calendar_notes = item;
      let vm = this;

      $.confirm({
        title:
          vm.$t("confirmation_record_delete"),
        content:
          vm.$t("question_record_delete") +
          `${item.title}` +
          vm.$t("from") +
          vm.$t("label.calendar_notes") +
          vm.$t("records") + '?',
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
                  lang: vm.form.language
                };
                vm.delete_calendar_notes(params)
                  .then((resp) => {
                    item.is_loading = false;
                    if( resp.data.inUse == true ) {
                      vm.makeToast(
                        "danger",
                        vm.$t("failed_to_delete"),
                        vm.$t("unable_message1") + resp.data.name + vm.$t("unable_message2")
                      );
                      return false;
                    }
                    if (vm.requestTypeResponseStatus) {
                      vm.loadItems();
                      vm.makeToast(
                        "danger",
                        vm.$t("delete_record"),
                        `${item.title}` +
                          vm.$t("delete_record_message") +
                          vm.$t("label.calendar_notes") +
                          vm.$t("records")
                      );
                    }
                  })
                  .catch((error) => {
                    console.log(error);
                  });
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

    performSearch: _.debounce(function(query) {
        this.searched = query
    }, 1000)

  },

  watch: {

    filter(query) {
      this.performSearch(query)
    },

    // currentPage: {
    //   handler: function(value) {
    //     this.loadItems()
    //   }
    // },

    // perPage: {
    //   handler: function(value) {
    //     this.loadItems()
    //   }
    // },

    searched: {
      handler: function(value) {
        this.loadItems()
      }
    },
  },

  computed: {
    ...mapGetters("calendar_notes", {
      items: "calendar_notes",
      // categories: "categories",
      requestTypeResponseStatus: "get_response_status",
    }),

    ...mapGetters("brand", {
      brands: "brands",
    }),

    ...mapGetters("location", {
      countries: "countries",
    }),

    ...mapGetters("calendar_notes_type", {
      calendar_notes_type: "calendar_notes_type",
    }),

    totalRows() {
      return this.items.length;
    },
  },
};
</script>

<style lang="scss">
th:nth-last-child(2) {
  text-align: center;
}
td:nth-last-child(1):hover {
  cursor: pointer;
}
th:nth-last-child(3) {
  text-align: center;
}
.title_show{
  cursor: pointer;
}
</style>
