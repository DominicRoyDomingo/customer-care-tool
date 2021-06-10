<template>
<div class="animated fadeIn">
    <v-app id="tags__container">
      <v-card tile flat>
        <v-card-title>
          <v-row>
            <v-col>
              <span class="title font-weight-light text--secondary">
                {{ $t("geolocalization_list") }}
              </span>
            </v-col>
            <v-spacer> </v-spacer>
            <v-col>
              <v-btn
                color="success"
                @click="createTopic()"
                class="float-right"
                tile
              >
                <!-- <v-icon dark>mdi-hand-heart</v-icon> -->
                <v-icon
                >
                  mdi-text-box-plus
                </v-icon>
                <!-- <v-icon small dark style="margin-top: 15px; margin-left: -3px;">
                  mdi-plus
                </v-icon> -->
                &nbsp;
                {{ $t('button.new_topic') }}
              </v-btn>
            </v-col>
          </v-row>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" sm="6" md="6">
              <!-- <div class="algolia-toast">
                <b-toast 
                  id="algolia-toast" 
                  variant="success" 
                  :title="`GEOLOCALIZATIONS ${this.$t('synced')}`" 
                  static 
                  no-auto-hide
                >
                  {{ `${algoliaSummary.synced} Geolocalized Pages are successfully synced.`}}<br>
                  {{ `${algoliaSummary.unsynced} Geolocalized Pages are unsynchronized due to lack of template use.`}}<br>
                </b-toast>
              </div> -->
                <v-col
                  class="caption font-weight-regular text--secondary text-right"
                  style="display: flex; position: absolute; margin-left: -10px;"
                >
                  {{ $t("button.show") }}
                  <b-form-select
                    :options="showEntriesOpt"
                    v-model="perPage"
                    style="
                      border-radius: 0;
                      width: 13% !important;
                      margin-top: -8px;
                      margin-left: 5px;
                      margin-right: 5px;
                    "
                  />{{ $t("label.entries") }}
                  <b-form inline>
                    <b-input-group-prepend style="margin-left: 10px; margin-top: -10px;">
                      <v-menu offset-y :rounded="false">
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            title="Filter by no Translations."
                            color="info"
                            dark
                            v-bind="attrs"
                            v-on="on"
                            tile
                            depressed
                            style="height: 33px; min-width: 64px; padding: 0 16px"
                          >
                            <v-icon :size="18"> mdi-filter-menu </v-icon>
                          </v-btn>
                        </template>
                        <v-list dense class="profile__row-menu">
                          <v-list-item-group color="primary">
                            <v-list-item
                              v-for="(option, index) in sortOptions"
                              :key="index"
                            >
                              <v-list-item-content
                                @click="sortByGeo(option.value)"
                              >
                                <v-list-item-title>
                                  {{ option.text }}
                                </v-list-item-title>
                              </v-list-item-content>
                            </v-list-item>
                          </v-list-item-group>
                        </v-list>
                      </v-menu>
                      <v-btn
                        id="sync-to-algolia"
                        color="warning"
                        @click="syncToAlgolia()"
                        block
                        x-small
                        depressed
                        tile
                        height="33"
                        class="ml-3"
                      >
                      <span v-if="isAlgoliaSyncing" class="text-center">
                          <v-progress-circular
                            size="20"
                            width="1"
                            color="white"
                            indeterminate
                          />
                        </span>
                        <span v-else>{{ $t("button.sync_to_algolia") }}</span>
                        
                        
                      </v-btn>
                      <b-popover class="geolocalization-popover" target="sync-to-algolia" triggers="hover focus" >
                        <template #title>{{ $t('geolocalization_popover_queue') }}</template>
                        <span class="geolocalization-popover-text">{{ algoliaSummary.new }} {{ $t('geolocalization_popover_new_geolocalizations') }}</span><br>
                        <span class="geolocalization-popover-text">{{ algoliaSummary.update }} {{ $t('geolocalization_popover_geolocalizations_to_be_updated') }}</span>
                      </b-popover>
                    </b-input-group-prepend>
                  </b-form>
              </v-col>
            </v-col>
            <v-spacer></v-spacer>
            <v-col cols="12" sm="12" md="4">
              <b-input-group class="float-right">
                <b-form-input
                  v-model="searchTopic"
                  :placeholder="$t('search_here')"
                  type="search"
                  style="border-radius: 0; border-left: 0"
                ></b-form-input>
                <template v-slot:prepend>
                  <b-input-group-text
                    style="background-color: rgba(0,0,0,0.05); 
                      border-radius: 0;"
                  >
                    <v-icon size="21">mdi-magnify</v-icon>
                  </b-input-group-text>
                </template>
              </b-input-group>
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
                :per-page="perPage"
                :busy="isLoading"
                :items="items"
                v-model="geolocalizationTable"
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc"
                @sort-changed="sortChanged"
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

                <template v-slot:cell(topic)="data">
                  <div style="margin: 10px 0 0 0px;">
                    <!-- <span
                      class="text-subtitle-1 text--secondary font-weight-bold"
                      v-html="data.item.article_title"
                    /> -->
                    <a class="font-weight-bold" href="#" @click.prevent="viewGeolocalizations(data.item, filterByGeolocalization)" v-html="data.item.article_title">
                    </a>
                  </div>
                </template>

                <template v-slot:cell(number_of_geolocalizations)="data">
                  <div style="margin-top: 10px">
                    <span class="text--disabled">
                      {{ data.item.geolocalizations_count }}
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
                          <v-list-item @click="viewGeolocalizations(data.item, filterByGeolocalization)">
                            <v-list-item-icon>
                              <v-icon color="green lighten-1">
                                mdi-map-marker
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("geolocalizations") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="createGeolocalizationTemplate(data.item, data.index)">
                            <v-list-item-icon>
                              <v-icon color="green lighten-1">
                                mdi-map-marker
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("button.template") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                        </v-list-item-group>
                      </v-list>
                    </v-menu>
                  </div>
                </template>
              </b-table>
            </v-container>
          </v-row>
          <v-row>
            <v-col sm="6" v-if="total_rows > 0">
              <v-spacer>
                <v-col cols="12" sm="6" md="4">
                  <span>
                    Showing
                    <strong v-text="showing.from" />
                    to
                    <strong v-text="showing.to" />
                    of
                    <strong v-text="showing.total" />
                    {{ $t("label.entries") }}
                  </span>
                </v-col>
              </v-spacer>
            </v-col>
            <v-col sm="6">
              <b-pagination
                v-if="!this.isLoading"
                v-model="currentPage"
                :total-rows="total_rows"
                :per-page="perPage"
                align="right"
              ></b-pagination>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
      <CreateArticleModal :parent="this" @done-success="create_success" />
      <CreateGeolocalizationTemplateModal :parent="this" @done-success="loadItems" />
      <!-- <Create :$this="this" @loadTable="successfullySavedServiceType"></Create>
      

      <Edit :$this="this"></Edit> -->
    </v-app>
  
  </div>
  

</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CreateArticleModal from "../includes/geolocalization/CreateArticleComponent.vue";
import CreateGeolocalizationTemplateModal from "../includes/geolocalization/CreateTemplateComponent.vue";
import Loading from "vue-loading-overlay";
import Form from "./../../shared/form.js";
import toast from "./../../helpers/toast.js";
import prefilledSectionMixin from "../medical_stuff/mixins/prefilledSectionMixin";


export default {

  mixins: [toast, prefilledSectionMixin],
  
  components: {
    
    CreateArticleModal,

    CreateGeolocalizationTemplateModal,

    Loading,
  
  },
  
  data: function() {
    
    return {
      isLoading: true,
      btnloading: false,
      isAlgoliaSyncing: false,
      isLoadingSelect: true,
      tableTotalRows: "",
      htmlTagPriorityName: "",
      filter: "",
      searchTopic: "",
      sortBy: 'name',
      filterByGeolocalization: "",
      searched: "",
      sortDesc: false,
      article: "",
      nameTranslation: "",
      currentLanguage: this.$i18n.locale,
      currentPage: 1,
      itemSelected: "",
      actors_list: "",
      author_type_list: "",
      isAddTypeDate: false,
      perPage: 10,
      total_rows: 0,
      typedatesForm: [],
      authorSlotForm: [],
      imageSlotForm: [],
      itemTypes: [],
      geolocalizationTable: [],
      algoliaSummary: [],
      items: [],
      isAddTypeDate: false,
      isAddAuthor: false,
      isAddImage: false,
      languageOptions: [
        {
            value: "en",
            text: "English",
        },
        {
            value: "de",
            text: "German",
        },
        {
            value: "it",
            text: "Italian",
        },
        {
            value: "ph-fil",
            text: "Filipino",
        },
        {
            value: "ph-bis",
            text: "Visayan",
        },
      ],

      showing: {
        to: 0,
        from: 0,
        total: 0,
      },

      language: this.$i18n.locale,

      showEntriesOpt: [

        { value: 10, text: "10" },
        { value: 30, text: "30" },
        { value: 50, text: "50" },
        { value: 100, text: "100" },
      
      ],

      tableHeaders: [
        { key: "topic", label: this.$t('content_update_name'), thClass: "text-left", sortable: true },
        { key: "number_of_geolocalizations", label: this.$t("table.number_of_geolocalizations"), thClass: "text-center", tdClass: "text-center", sortable: true },
        { key: "actions", label: this.$t("table.action"), thClass: "text-center", thStyle: { width: "20%" }, tdClass: "text-center" }
      
      ],
      translations: {
        en: {
          title: "Edit Service Type",
          name: "Name",
          button: "Save Changes",
          cancel: "Cancel",
          image: "Image",
        },

        it: {
          title: "Modifica tipo di servizio",
          name: "Nome",
          button: "Salva il resto",
          cancel: "Annulla",
          image: "Immagine",
        },

        de: {
          title: "Servicetyp bearbeiten",
          name: "Name",
          button: "Änderungen speichern",
          cancel: "Stornieren",
          image: "Bild",
        },

        ph_fil: {
          title: "I-edit ang Uri ng Serbisyo",
          name: "Pangalan",
          button: "I-save ang mga pagbabago",
          cancel: "Kanselahin",
          image: "Larawan",
        },

        ph_bis: {
          title: "Pag-edit sa Matang sa Serbisyo",
          name: "Ngalan",
          button: "E-save ang kausaban",
          cancel: "Kanselahon",
          image: "Litrato",
        }
      },
      modalGeolocalizationTemplate: {
        createGeolocalizationTemplate: {
          name: "service_type_add_new_button",
          isVisible: false,
          button: {
            save: "save_button",
            cancel: "cancel",
            new: "service_type_add_new_button",
            loading: false
          }
        },
        edit: {
          name: "service_type_add_edit_button",
          isVisible: false,
          button: {
            update: "save_change",
            cancel: "cancel",
            new: "service_type_add_new_button",
            loading: false
          }
        },
      },
      
      form: new Form({
        id: "",
        service_type_name: "",
        language: this.$i18n.locale
      }),

      typeDateForm: new Form({
        id: "",
        name: "",
        action: ""
      }),

      articleForm: new Form({
        id: "",
        title: "",
        link: "",
        actors: "",
        item_type: '',
        actor_type: "",
        type_dates: "",
        publish_date: "",
        action: "",
      }),

      typeForm: new Form({
        id: "",
        name: "",
        term_type: '',
        action: "",
        term_types: '',
        file: ""
      }),

      mtForm: new Form({
        id: "",
        name: "",
        term_types: "",
        specializations: "",
        action: "",
        file: "",
      }),

      htmlTagForm: new Form({
        id: "",
        description: "",
        action: ""
      }),

      typeAuthor: new Form({
            id: "",
            name: "",
            action: "Add",
            default: "",
            convertion: "",
            language: ""
         }),

      geolocalizationTemplateForm: new Form({
        id: "",
        title: "",
        body: "",
        meta_description: "",
        slug: "",
        alt_tag_image: "",
        image_description: "",
        prefilledsectionOptions: "",
        language: this.$i18n.locale
      }),

      sortOptions: [
        { value: "all", text: "All", disabled: true },
        { value: "en", text: "No English Translation" },
        { value: "de", text: "No German Translation" },
        { value: "it", text: "No Italian Translation" },
        { value: "ph-fil", text: "No Filipino Translation" },
        { value: "ph-bis", text: "No Visayan Translation" },
        { value: "no-gc", text: "No Geolocalized Cities" },
        { value: "no-gp", text: "No Geolocalized Provinces" },
        { value: "no-gr", text: "No Geolocalized Regions" },
        { value: "no-t", text: "No Template" },
        { value: "wuga", text: "With Unpublished Geolocalized Article" },
        { value: "wpga", text: "With Published Geolocalized Article" },
      ],

      formData: new FormData(),
      isShowLink: true,
      isShowHtmlTag: true,
    };
  
  },

  mounted() {
    this.loadAlgoliaSummary()
    this.loadItems();
    this.loadItemType();
    this.loadActors();
    this.loadAutorType();
  },

  watch: {

    searchTopic(query) {
      this.performSearch(query)
    },

    currentPage: {
      handler: function(value) {
        this.loadItems()
      }
    },

    perPage: {
      handler: function(value) {
        this.loadItems()
      }
    },

    searched: {
      handler: function(value) {
        this.loadItems()
      }
    },

    language(value) {
        switch (value) {
          case 'en':
              this.mtForm.name = this.itemSelected.is_english;
              this.articleForm.title = this.itemSelected.is_english;
              this.articleForm.link = this.itemSelected.english_link;
              this.typeForm.term_type = this.itemSelected.is_english;
              this.typeDateForm.name = this.itemSelected.is_english;
              break;
          case 'it':
              this.mtForm.name = this.itemSelected.is_italian;
              this.articleForm.title = this.itemSelected.is_italian;
              this.articleForm.link = this.itemSelected.italian_link;
              this.typeForm.term_type = this.itemSelected.is_italian;
              this.typeDateForm.name = this.itemSelected.is_italian;
              break;
          case 'de':
              this.mtForm.name = this.itemSelected.is_german;
              this.articleForm.title = this.itemSelected.is_german;
              this.articleForm.link = this.itemSelected.german_link;
              this.typeForm.term_type = this.itemSelected.is_german;
              this.typeDateForm.name = this.itemSelected.is_german;
              break;
          case 'ph-bis':
              this.mtForm.name = this.itemSelected.is_bisaya;
              this.articleForm.title = this.itemSelected.is_bisaya;
              this.articleForm.link = this.itemSelected.bisaya_link;
              this.typeForm.term_type = this.itemSelected.is_bisaya;
              this.typeDateForm.name = this.itemSelected.is_bisaya;
              break;
          case 'ph-fil':
              this.mtForm.name = this.itemSelected.is_filipino;
              this.articleForm.title = this.itemSelected.is_filipino;
              this.articleForm.link = this.itemSelected.filipino_link;
              this.typeForm.term_type = this.itemSelected.is_filipino;
              this.typeDateForm.name = this.itemSelected.is_filipino;
              break;
        }
    },
  },
  
  methods: {
    ...mapActions("service_type", ["get_service_types", "delete_service_type", "remove_from_service_type_array"]),
    ...mapActions("categ_terms", [
      "get_articles",
      "delete_article",
      "get_terms",
      "get_type_authors",
    ]),
    
    ...mapActions("prefilledsection", [
      "get_specific_prefilledsections"
    ]),

    ...mapActions("actor", ["get_actors"]),

    ...mapActions("geolocalization", [
      "post_sync_to_algolia",
      "get_algolia_summary"
    ]),

    loadItems() {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        searchTopic: this.searchTopic,
        brand_id: this.$brand.id,
        filterByGeolocalization: this.filterByGeolocalization,
        perPage: this.perPage,
        sortName: this.sortBy,
        sortDesc: this.sortDesc,
        page: this.currentPage,
      };
      
      axios
        .get("/api/articles/active-paginated-articles", { params })
        .then((resp) => {
          let data = resp.data;
          this.items = data.data;
          this.total_rows = data.total;
          // this.items = this.article_list;

          this.showing.to = data.to;
          this.showing.from = data.from;
          this.showing.total = data.total;
        })
        .finally(() => {
          this.isLoading = false;
        });

      // this.get_articles(params).then((_) => {
      //   this.isLinked = false;
      //   this.isLoading = false;
      //   this.items = this.article_list;
      // });
    // loadItems() {
    //   let params = {
    //     api_token: this.$user.api_token,
    //     locale: this.$i18n.locale,
    //     filterByGeolocalization: this.filter,
    //     brand_id: this.$brand.id,
    //     articles: true,
    //     filterByGeolocalization: this.filterByGeolocalization,
    //   };
    //   this.get_articles(params).then((_) => {
    //     this.isLoading = false;
    //   });
    },

    getAuthorFullName(id) {
      var act = this.actors_list.find((x) => x.id === id);
      return act.id;
    },

    getAuthorName(data) {
      let article_id = [];
      if (data.length > 0) {
        data.forEach((ele) => {
          let id = [];
          let authors_ = jQuery.parseJSON(ele.authors);
          authors_.forEach((ele1) => {
            var act = this.actors_list.find((x) => x.id === ele1);
            id.push(act.fullname);
          });

          var type = this.author_type_list.find(
            (x) => x.id === ele.author_type
          );
          article_id.push({
            type: type.name,
            authors: id,
          });
        });
        return article_id;
      }
    },

    successfullySavedServiceType(){
      this.$refs.table.refresh();
    },

    listDisplay(array) {
      let trans = '';
      let spliter = '';
        spliter = array.split('(')[0];
        trans = array.split('(')[1];
        if( trans !== undefined ){
          if( trans.length > 0 ) {
            spliter = spliter + '<small style="color:red">(' + trans + '</small>';
            array = spliter;
          }
        }
      return array;
    },

    checkIfHasTranslation(name) {
      let trans = '';
        trans = name.split('(')[1];
        if( trans !== undefined ){
          if( trans.length > 0 ) {
            return '(' +trans;
          }
        }
      return "";
    },

    createTopic() {
      this.form.reset();
      this.form.language = this.$i18n.locale;
      this.articleForm.item_type = this.searchArticle(this.itemTypes)
      this.btnloading = false;
      this.articleForm.action = "Add";
      this.authorSlotForm = [];
      this.imageSlotForm = [];
      this.$bvModal.show("article-modal");

    },

    createGeolocalizationTemplate(item,index) {
      this.geolocalizationTemplateForm.reset();
      this.geolocalizationTemplateForm.language = this.$i18n.locale;
      this.article = item;
      this.geolocalizationTemplateForm.title = this.article.base_name
      if(this.article.geolocalization_template != null) {
        this.geolocalizationTemplateForm.id = this.article.geolocalization_template.id
        this.geolocalizationTemplateForm.body = this.article.geolocalization_template.content
        this.geolocalizationTemplateForm.meta_description = this.article.geolocalization_template.meta_description
        this.geolocalizationTemplateForm.slug = this.article.geolocalization_template.slug
        this.geolocalizationTemplateForm.alt_tag_image = this.article.geolocalization_template.alt_tag_image
        this.geolocalizationTemplateForm.image_description = this.article.geolocalization_template.img_description
      }

      let params = {
           api_token: this.$user.api_token,
           articleTypeID: this.article.publishing_item_type_articles[0].id,
           locale: this.$i18n.locale,
      };
   
      this.get_specific_prefilledsections(params).then((_) => {
        this.geolocalizationTemplateForm.prefilledsectionOptions = this.specific_sections
      });
   
      this.btnloading = false;
      this.$bvModal.show("geolocalization-template-add-modal");
    },

    viewGeolocalizations(item,filter){
      let filterBy = "";
      if(filter == "wpga" || filter == "wuga") {
        filterBy = `?filterBy=${filter}`;
      }
      window.location.href = `/admin/article-content-maker/geolocalizations/${item.id}${filterBy}`;
    },
    
    onEdit(item,index) {
      this.typedatesForm = [];
      this.authorSlotForm = [];
      this.imageSlotForm = [];
      console.log()
      this.articleForm.reset();
      this.language = this.$i18n.locale;
      this.articleForm.id = item.id;
      this.articleForm.title = item.base_name;
      this.articleForm.type_dates = item.type_dates;
      this.articleForm.link = item.base_link !== "null" ? item.base_link : "";
      this.articleForm.publish_date = item.publish_date;
      this.articleForm.item_type = item.publishing_item_type_articles;
      // this.articleForm.actors = item.has_authors;
      this.articleForm.action = "Update";
      this.itemSelected = item;
      if (item.type_dates.length > 0) {
        this.typedatesForm = this.setTypeDates(item.type_dates);
        this.isAddTypeDate = true;
      }
      if (item.author_slot.length > 0) {
        this.authorSlotForm = this.setAuthorSlot(item.author_slot);
        this.isAddAuthor = true;
      }
      if (item.image_slot.length > 0) {
        this.imageSlotForm = this.setImageSlot(item.image_slot);
        this.isAddImage = true;
      }

      // Open Modal
      this.$bvModal.show("article-modal");
    },

    onDelete(item,index) {
      let baseName = item.base_name;

      this.deleteConfirm(baseName, this.$t("label.articles")).then((resp) => {
        if (resp) {
          item.is_loading = true;
          let params = {
            api_token: this.$user.api_token,
            locale: this.language,
            brand_id: this.$brand.id,
            id: item.id,
          };

          axios
            .delete("/api/articles", { params })
            .then((resp) => {
              let message = `${baseName} ${this.$t(
                "delete_record_message"
              )} ${this.$t("label.articles")} ${this.$t("records")}`;

              this.errorToast(this.$t("delete_record"), message);
 
              this.loadItems();
            })
            .catch((error) => {
              let message = error.response.data.errors.title[0];
              this.makeToast(
                `danger`,
                this.$t("inused_alert"),
                this.$t("unable_message1") +
                  `${baseName}` +
                  this.$t("unable_message2"),
                `${baseName} ` +
                  this.$t("delete_record_message") +
                  this.$t("from") +
                  this.$t("label.articles") +
                  this.$t("records")
              );
              item.is_loading = false;
            });
        }
      });
    },

    syncToAlgolia() {

      let formData = new FormData();
      this.isAlgoliaSyncing = true;
      formData.append("api_token", this.$user.api_token);

      this.post_sync_to_algolia(formData)
        .then((resp) => {
          if (resp) {
            this.isAlgoliaSyncing = false;
            this.loadAlgoliaSummary();
            let message = {
              create:
                `Geolocalizations ${this.$t("toasts.synced_to_algolia")}`,
              title: {
                create: `GEOLOCALIZATIONS ${this.$t("synced")}`,
              },
            };

            this.makeToast(
              "success",
              message.title.create,
              message.create
            );
            
            // this.algoliaSummary.synced = resp.data.synced;
            // this.algoliaSummary.unsynced = resp.data.unsynced;


            // this.$bvToast.show('algolia-toast')
          }
        })
        .catch((error) => {
          console.log(error)
          let errors = error.response.data.errors;
        });
    },

    async loadAlgoliaSummary(isSearched = false) {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        brand_id: this.$brand.id,
      };
    
      this.get_algolia_summary(params).then((data) => {
        this.algoliaSummary = data;
        // this.isProviderLoaded = true;
      });
    },

    setTypeDates(data) {
      let items = [];
      data.forEach((ele) => {
        items.push({
          name: ele,
          date: ele.pivot.article_date,
        });
      });
      return items;
    },

    setImageSlot(data) {
      let items = [];
      data.forEach((ele) => {
        items.push({
          image: null,
          imagePlaceholder:
            ele.image !== null
              ? JSON.parse(ele.image)[Object.keys(JSON.parse(ele.image))[0]]
              : "",
          htmlTags: ele.html_tag_items,
          id: ele.id,
        });
      });
      return items;
    },

    setAuthorSlot(data) {
      let items = [];
      data.forEach((ele) => {
        let id = [];
        let authors = jQuery.parseJSON(ele.authors);
        authors.forEach((ele1) => {
          var act = this.actors_list.find((x) => x.id === ele1);
          id.push({ id: ele1, full_name: act.fullname });
        });
        var type = this.author_type_list.find((x) => x.id === ele.author_type);
        items.push({
          actors: id,
          actor_type: { id: ele.author_type, name: type.name },
        });
      });
      return items;
    },

    searchArticle(itemTypes, providerType = false){
        let articleKeywords = ['articolo geolocalizzato', 'articoli geolocalizzati', 'geolocalized articles'];
        let itemTypesArr = [];
        // for (var i=0; i < itemTypes.length; i++) {
        //     for (const [key, value] of Object.entries(JSON.parse(itemTypes[i].type_name))) {
        //       console.log(value);
        //       if (articleKeywords.includes(value.toLowerCase())) {
        //         itemTypesArr.push(itemTypes[i]);
        //       }
        //     }
        // }
        for (var i=0; i < itemTypes.length; i++) {
          if (articleKeywords.includes(itemTypes[i].base_name.toLowerCase())) {
            itemTypesArr.push(itemTypes[i]);
          }
        }
        return itemTypesArr;
    },

    create_success(item) {
      let recordName = this.$t("label.articles");
      if (this.articleForm.action === "Add") {
        this.storeToast(item.article_title, recordName);
      } else {
        this.updateToast(item.id, recordName);
      }

      this.typedatesForm = [];
      this.authorSlotForm = [];
      this.imageSlotForm = [];
      this.isAddTypeDate = false;
      this.isAddAuthor = false;
      this.isAddImage = false;
      this.articleForm.reset();
      this.isLoading = true;
      this.loadAutorType();
      this.loadItems();
    },

    loadAutorType() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
      };
      this.get_type_authors(params).then((_) => {
        this.author_type_list = this.type_authors;
      });
    },

    loadItemType() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        // brand_id: this.$brand.id,
      };
      axios.get("/api/select/item_types/all", { params }).then((resp) => {
        this.isLoadingSelect = false;
        this.itemTypes = resp.data;
      });
    },

    loadActors() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        brand_id: this.$brand.id,
      };
      this.get_actors(params).then((_) => {
        this.actors_list = this.actors;
      });
    },

    deleteConfirm(name, records) {

         let message = `${this.$t("question_record_delete")} ${name} ${this.$t("from")} ${records} ${this.$t("records")}?`;

         return new Promise((resolve, reject) => {
            $.confirm({
               title: this.$t("confirmation_record_delete"),
               content: message,
               type: "red",
               typeAnimated: true,
               columnClass: "medium",
               buttons: {
                  yes: {
                     text: this.$t("yes"),
                     btnClass:
                        "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
                     action: function (value) {
                        resolve(value);
                     },
                  },
                  no: {
                     text: this.$t("no"),
                     btnClass:
                        "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
                     action: function () {
                     },
                  },
               },
            });
         });

      },
    sortByGeo(value) {
      this.filterByGeolocalization = value
      this.loadItems()
    },

    sortChanged(e) {
      this.sortBy = e.sortBy
      this.sortDesc = e.sortDesc
      this.loadItems()
    },

    performSearch: _.debounce(function(query) {
        this.searched = query
    }, 1000)

  },

  computed: {
    ...mapGetters("geolocalization_template", {
      geolocalizationTemplateResponseStatus: "get_response_status"
    }),

    ...mapGetters({
      specific_sections: "prefilledsection/get_specific_prefilledsections",
    }),

    ...mapGetters("actor", {
      actors: "actors",
    }),

    ...mapGetters("categ_terms", {
      medicalTerms: "get_terms_items",
      notes_terms_items: "get_notes_terms_items",
      type_authors: "get_type_author_items",
    }),

    totalRows() {
      return this.items.length;
    },


    selectedLanguage() {
      return this.currentLanguage.replace("-", "_")
    }

  }

};

</script>

<style>
  /* .algolia-toast {
    position: absolute;
    top: 32px;
    left: 460px;
  } */
.provider-expansion-panel-header {
  padding: 0px 6px !important;
  min-height: 48px !important;
}

.provider-expansion-panel-header .expansion-panel-title {
  font-size: 0.700rem !important;
}
.provider-expansion-panel-content .v-expansion-panel-content__wrap {
  padding: 0px 7px 7px !important;
}

.geolocalization-popover-text {
  font-size: 0.825rem !important;
  color: #fff !important;
}

.popover-header {
  color: #fff;
  background-color: #fb8c00;
  border-bottom-color: #fb8c00;
}

.popover-body {
  color: #fb8c00;
}

.popover {
  background-color: #ff8e00;
  border-bottom-color: #ff9917;
}

#__bv_popover_97__ .arrow::before {
    border-right-color: #fb8c00 !important;
}
#__bv_popover_97__ .arrow::after {
    border-right-color: #fb8c00 !important;
}
</style>