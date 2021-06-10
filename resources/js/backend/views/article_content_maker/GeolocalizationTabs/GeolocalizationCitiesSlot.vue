<template>
	<v-card tile flat>
    <v-dialog
      v-model="dialog"
      fullscreen
      hide-overlay
      transition="dialog-bottom-transition"
    >
      <v-overlay
        v-if="dialog"
        absolute
        color="#000"
        opacity="1"
      >
      <div class="d-flex fill-height justify-space-between" style="flex-direction:column">
        <div class="d-flex justify-end">
          <div class="pa-1">
            <v-btn
              icon
              @click="dialog = false"
            >
              <v-icon>
                mdi-close
              </v-icon>
            </v-btn>
          </div>
        </div>
        <div>
          <v-hover v-slot="{ hover }">
            <v-card color="#000">
              <v-img
                :lazy-src="dialogImage"
                :src="dialogImage"
                height="90vh"
                contain
              ></v-img>
              <v-fade-transition>
                <v-overlay
                  v-if="hover || isDialogImageLoading"
                  absolute
                  color="#282828"
                  opacity="0.9"
                >
                <div class="d-flex fill-height justify-center" style="flex-direction:column">
                  <div class="d-flex justify-center" v-if="isDialogImageLoading">
                    <div>
                      <v-progress-circular
                        indeterminate
                      ></v-progress-circular>
                    </div>
                  </div>
                  <div class="d-flex justify-center" v-else>
                    <div class="mr-2">
                      <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            icon
                            v-bind="attrs"
                            v-on="on"
                            @click="changeImage()"
                          >
                            <v-icon large>
                              mdi-image-edit
                            </v-icon>
                          </v-btn>
                        </template>
                        <span>Change</span>
                      </v-tooltip>
                    </div>
                    <div class="mr-2">
                      <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            icon
                            v-bind="attrs"
                            v-on="on"
                            @click="removeImage()"
                          >
                            <v-icon large>
                              mdi-delete
                            </v-icon>
                          </v-btn>
                        </template>
                        <span>Delete</span>
                      </v-tooltip>
                    </div>
                  </div>
                </div>
                </v-overlay>
              </v-fade-transition>
            </v-card>
          </v-hover>
          
        </div>
      
        <div class="d-flex justify-center">
          <div class="pa-6">
          </div>
        </div>
      </div>
      </v-overlay>
    </v-dialog>
		<v-card-title>
			<v-row>
				<v-col>
					<span class="title font-weight-light text--secondary" v-html="parent.articleItem.article_title">
					</span><br>
					<span class="title font-weight-light text--secondary">
						<a href="#" class="text-sm-body-2" @click="goToArticles()">
							{{ $t("label.show_all_topics") }}
						</a>
					</span>
						
				</v-col>
				<v-spacer> </v-spacer>
				<v-col>
					<v-btn
						color="success"
						@click="createGeolocalization()"
						class="float-right"
						tile
					>
						<!-- <v-icon dark>mdi-hand-heart</v-icon>
						<v-icon small dark style="margin-top: 15px; margin-left: -3px;">
							mdi-plus
						</v-icon> -->
						&nbsp;
						{{ $t(modalGeolocalization.createGeolocalization.button.new) }}
					</v-btn>
				</v-col>
			</v-row>
		</v-card-title>
		<v-card-text>
			<v-row>
				<v-col cols="12" sm="12" md="2">
					<v-row no-gutters>
						<v-col
							cols="3"
							class="caption font-weight-regular text--secondary text-right"
							style="display:flex; justify-content: center; padding-top: 5px"
						>
							{{ $t("button.show") }}
						</v-col>
						<v-col cols="6">
							<b-form-select
								:options="showEntriesOpt"
								v-model="perPage"
								style="border-radius: 0"
							/>
						</v-col>
						<v-col
							cols="3"
							class="caption font-weight-regular text--secondary"
							style="display:flex; justify-content: center; padding: 5px 0 0 5px"
						>
							{{ $t("label.entries") }}
						</v-col>
					</v-row>
				</v-col>
				<v-spacer></v-spacer>
				<v-col cols="12" sm="12" md="4">
					<b-input-group class="float-right">
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
						:per-page="0"
						:busy="isLoading"
						:items="items.data"
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

						<template v-slot:cell(place)="data">
							<div class="d-flex" style="margin: 10px 0 0 0;">
								<a href="#" class="font-weight-bold" @click.prevent="showTemplate(data.item)">
									{{ data.item.place}}
								</a>
								<v-spacer></v-spacer>
                <div class="d-flex flex-row">
                  <div class="mr-1" v-for="(image,index) in data.item.geolocalize_images" :key="index">
                    <v-hover v-slot="{ hover }">
                      <v-card color="#fff">
                        <v-img
                          :lazy-src="image.image"
                          height="30"
                          max-width="40"
                          :src="image.image"
                        ></v-img>
                        <v-fade-transition>
                          <v-overlay
                            style="cursor: pointer;"
                            v-if="hover"
                            absolute
                            color="#fff"
                            opacity="0.7"
                            @click.native="setDialogImage(image)"
                          >
                          </v-overlay>
                        </v-fade-transition>
                      </v-card>
                    </v-hover>
                  </div>
                </div>
							</div>
						</template>

            <template v-slot:cell(count_providers)="data">
              <div style="margin-top: 10px">
                <span class="text--disabled">
                  <a href="#" @click.prevent="onShowProvider(data.item, data.index)">
                  	{{ data.item.count_providers}}
                  </a>
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
														{{ $t("change_place") }}
													</v-list-item-title>
												</v-list-item-content>
											</v-list-item>
                      <v-list-item @click="onAddImages(data.item, data.index)">
												<v-list-item-icon>
													<v-icon color="green darken-3">
														mdi-image-plus
													</v-icon>
												</v-list-item-icon>
												<v-list-item-content>
													<v-list-item-title>
														{{ $t("button.add_images") }}
													</v-list-item-title>
												</v-list-item-content>
											</v-list-item>
                      <v-list-item @click="changePublishStatus(data.item, data.index)">
                        <v-list-item-icon>
                          <v-icon color="green lighten-1">
                            mdi-checkbox-marked-circle
                          </v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                          <v-list-item-title>
                            {{ data.item.publish_status == "Unpublished" ?  $t("button.mark_as_published") : $t("button.mark_as_unpublished")}}
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
                      <v-list-item @click="onShowProvider(data.item, data.index)">
												<v-list-item-icon>
													<v-icon color="green lighten-1">
														mdi-office-building-marker
													</v-icon>
												</v-list-item-icon>
												<v-list-item-content>
													<v-list-item-title>
														{{ $t("button.show") }} {{ $t("label.providers") }}
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
				<v-spacer></v-spacer>
				<v-col>
					<b-pagination
						v-if="!this.isLoading"
						v-model="currentPage"
						:total-rows="tableTotalRows"
						:per-page="perPage"
					></b-pagination>
				</v-col>
			</v-row>
		</v-card-text>
		<Create :parent="this" @loadTable="loadItems"></Create>
		<CreateImages :parent="this" @loadTable="loadItems"></CreateImages>
		<ChangeImage :parent="this" @loadTable="loadItems"></ChangeImage>
		<CityModal :parent="this"></CityModal>
		<ProvinceModalCity :parent="this"></ProvinceModalCity>
		<ProvinceModalProvince :parent="this"></ProvinceModalProvince>
    <RegionModal :parent="this"></RegionModal>
    <ProviderModalShow :parent="this"></ProviderModalShow>
  
		<Edit :parent="this"></Edit>
	</v-card>
</template>

<script>


import { mapActions, mapGetters } from "vuex";

// import Create from "../../includes/geolocalization/cities/CreateComponent.vue";

import Create from "../../includes/geolocalization/CreateComponent.vue";

import CreateImages from "../../includes/geolocalization/CreateImagesComponent.vue";

import ChangeImage from "../../includes/geolocalization/ChangeImageComponent.vue";

import Edit from "../../includes/geolocalization/cities/UpdateComponent.vue";

import CityModal from "../../includes/geolocalization/cities/CreateCityComponent.vue";

import ProviderModalShow from "../../includes/geolocalization/NumberOfProvidersModal.vue";

import ProvinceModalCity from "../../includes/geolocalization/cities/CreateDivisionComponent.vue";

import ProvinceModalProvince from "../../includes/geolocalization/provinces/CreateDivisionComponent.vue";

import RegionModal from "../../includes/geolocalization/regions/CreateRegionComponent.vue";

import geolocalizationMixins from "../../includes/geolocalization/mixins/geolocalizationMixins";

import Loading from "vue-loading-overlay";

import Form from "./../../../shared/form.js";

import toast from "./../../../helpers/toast.js";

export default {

	props: ['parent'],

  mixins: [toast,geolocalizationMixins],
  
  components: {
    
    Create,

    CreateImages,

    ChangeImage,

    Loading,

    Edit,

    CityModal,

    ProvinceModalCity,

    ProvinceModalProvince,

    RegionModal,

    ProviderModalShow
  
  },
  
  data: function() {
    
    return {

      isLoading: true,

      btnloading: false,

      viewing_template: false,

      places: [],

      template: [],

      tableTotalRows: "",
      
      filter: "",

      sortBy: 'name',
      
      ul: "",

      scrollTop: "",

      searched: "",

      searchPlace: "",

      searchedPlace: "",

      place: "",

      placePage: 1,

      placesLength: 0,

      sortDesc: false,

      nameTranslation: "",

      currentLanguage: this.$i18n.locale,
      
      currentPage: 1,
      
      perPage: 10,

      selected_country: null,

      showEntriesOpt: [

        { value: 10, text: "10" },
      
        { value: 30, text: "30" },
        
        { value: 50, text: "50" },
        
        { value: 100, text: "100" },
      
      ],

      tableHeaders: [
        
        { key: "place", label: this.$t('label.places'), thClass: "text-left", sortable: true },
        
        { key: "count_providers", label: this.$t("table.number_of_providers"), thClass: "text-center", tdClass: "text-center", sortable: true },

        // { key: "created_at", label: this.$t("table.created_at"), thClass: "text-center", thStyle: { width: "15%" }, tdClass: "text-center", sortable: true },
        
        // { key: "updated_at", label: this.$t("table.updated_at"), thClass: "text-center", thStyle: { width: "15%" }, tdClass: "text-center", sortable: true },
        
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

      modalGeolocalization: {
        
        createGeolocalization: {
          
          name: "geolocalization_add_new_button",
          
          isVisible: false,
          
          button: {
            
            save: "save_button",
            
            cancel: "cancel",
            
            new: "geolocalization_add_new_button",
            
            loading: false
          
          }

        },
        
        edit: {
          
          name: "geolocalization_add_edit_button",
          
          isVisible: false,
          
          button: {
            
            update: "button.save_change",
            
            cancel: "cancel",
            
            new: "geolocalization_add_new_button",
            
            loading: false
          
          }

        },

      },

      templateForm: new Form({

        id: "",
        alt_tag_image: "",
        body: "",
        img_description: "",
        meta_description: "",
        page_title: "",
        slug: "",
        place: "",

      }),
      
      form: new Form({
        
        id: "",
        service_type_name: "",
        places: "",
        language: this.$i18n.locale
      
      }),

      formData: new FormData(),
      providers: [],
      article_title: ''
      // cityPlaces: [],

      // cityPlacePage: 1,

      // cityPlacesLength: 0,

      // citySearchedPlace: "",

      // provincePlaces: [],

      // provincePlacePage: 1,

      // provincePlacesLength: 0,

      // provinceSearchedPlace: "",
    
    };
  
  },

  mounted() {
    this.loadItems();
  },

  watch: {

    filter(query) {
      this.performSearch(query)
    },

    searchPlace(query) {
      this.performSearchPlace(query)
    },

    searchedPlace: {
      handler: function(value) {
        this.placePage = 1
        this.loadPlaces(true)
      }
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

    places: {
      handler: function(value) {
        if(this.ul != "") {
          setTimeout(() => {
            this.ul.scrollTop = this.scrollTop;
          }, 10);
        }
      }
    },
  },
  
  methods: {
    ...mapActions("geolocalization", ["get_geolocalization_cities", "delete_geolocalization", "remove_from_geolocalization_array"]),
    ...mapActions("geolocalization_template", ["get_geolocalization_template"]),
    ...mapActions("division", ["get_divisions"]),
    ...mapActions("city", ["get_cities"]),

    async loadItems() {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        entries: this.perPage,
        page: this.currentPage,
        sort_name: this.sortBy,
        locale: this.$i18n.locale,
        sort_desc: this.sortDesc,
				search: this.searched,
        article_id: this.parent.article.id,
        filter_by: this.filterBy
      };
      this.get_geolocalization_cities(params).then(_ => {
        this.tableTotalRows = this.items.total
        this.isLoading = false;
      });
    },

    async loadPlaces(isSearched = false) {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        page: this.placePage,
        search: this.searchedPlace,
      };
      this.get_all_city(params).then((data) => {
        this.placesLength = data.length;
        if(this.placePage == 1 || isSearched == true) {
          this.places = data;
          return;
        }
        this.places = this.places.concat(data);
      });
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

    goToArticles() {
      window.location.href = `/admin/article-content-maker/geolocalizations`;
    },

    showTemplate(item) {
      let place = item.place.replace(/,|\s/gi, "-")
      let altTagImage,body,imageDescription,metaDescription,title,slug;
      place = place.replace(/-{2,}/g, '-');
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
				article_id: this.parent.article.id
      };
      this.get_geolocalization_template(params).then(data => {
        if(Object.entries(data).length == 0) {
          let message = {
            warning: this.$t( 'errors.no_template_exists' ),
            title: {
              warning: this.$t( 'no_template_exists' )
            }
          };

          this.makeToast(
            "danger",
            message.title.warning,
            message.warning
          );
          return;
				}
        data['page_title'] = item.article;
        altTagImage = data.alt_tag_image != null ? data.alt_tag_image.replace('[place]', item.place) : null;
        body = data.content != null ? data.content.replaceAll('[place]', item.place) : null;
        imageDescription = data.img_description != null ? data.img_description.replace('[place]', item.place) : null;
        metaDescription = data.meta_description != null ? data.meta_description.replace('[place]', item.place) : null;
        title = item.article != null ? item.article.replace('[place]', item.place) : null;
        slug = data.slug != null ? data.slug.replace('[place]', place) : null;
        this.parent.templateForm.id = data.id
        this.parent.templateForm.alt_tag_image = altTagImage
        this.parent.templateForm.body = body
        this.parent.templateForm.img_description = imageDescription
        this.parent.templateForm.meta_description = metaDescription
        this.parent.templateForm.page_title = title
        this.parent.templateForm.slug = slug
        this.parent.templateForm.place = item.place,
        this.parent.template = data;
        this.parent.tab_length = 3;
        this.parent.tab = 3;
        this.parent.viewing_template = true;
      });
    },
    
    onEdit(item,index) {
      this.placePage = 1;
      this.editingIndex = index;
      this.form.reset();
      this.form.language = this.$i18n.locale;
      this.form.id = item.geolocalization_id;
      delete item.geolocalization_index;
      delete item.id;
      this.place = item
      this.loadPlaces();
      this.$bvModal.show("geolocalization-city-edit-modal");
    },

    onDelete(item,index) {
      let vm = this;
      $.confirm({
        title:
          vm.$t("confirmation_record_delete").charAt(0).toUpperCase() +
          vm
            .$t("confirmation_record_delete")
            .slice(1)
            .toUpperCase(),
        content:
          vm.$t("question_record_delete") +
          `${item.place}` +
          vm.$t("from") +
          vm.$t("geolocalization") +
          vm.$t("records")+"?",
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
                  id: item.geolocalization_id,
                };
                vm.delete_geolocalization(params)
                  .then((resp) => {
                    item.is_loading = false;
                    if(resp.data == false) {
                      vm.makeToast(
                        "danger",
                        vm.$t("data_used"),
                        vm.$t("used_data_alert")
                      );
                      return false;
                    }
                    if (vm.geolocalizationResponseStatus) {
                      vm.loadItems();
                      vm.makeToast(
                        "danger",
                        vm.$t("delete_record"),
                        `${item.place}` +
                          vm.$t("delete_record_message") +
                          vm.$t("geolocalization") +
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

    onShowProvider( item, index ) {
      console.log(item);
      this.place = item.place;
      this.providers = item.providers;
      this.article_title = item.article;
      this.$bvModal.show("show-providers-list");
    },

    sortChanged(e) {
      this.sortBy = e.sortBy
      this.sortDesc = e.sortDesc
      this.loadItems()
    },

    performSearch: _.debounce(function(query) {
        this.searched = query
    }, 1000),

    performSearchPlace: _.debounce(function(query,name) {
      this.searchedPlace = query
    }, 1000)

  },

  computed: {
    ...mapGetters("geolocalization", {
      items: "geolocalization_cities",
      // categories: "categories",
      geolocalizationResponseStatus: "get_response_status"
    }),

    ...mapGetters("geolocalization_template", {
      geolocalizationTemplateResponseStatus: "get_response_status"
    }),

    ...mapGetters("location", {
        countries: "countries",
        provinces: "provinces",
        cities: "cities",
    }),

  }
};

</script>