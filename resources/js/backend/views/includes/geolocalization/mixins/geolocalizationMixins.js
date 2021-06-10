import axios from "axios";
import Form from "../../../../helpers/form.js";

import { mapActions, mapGetters } from "vuex";
import toast from "./../../../../helpers/toast.js";
export default {
  mixins: [toast],
  data() {
    return {

      dialog: false,

      isDialogImageLoading: false,

      cityPlaces: [],

      cityPlacePage: 1,

      cityPlacesLength: 0,

      citySearchPlace: "",

      citySearchedPlace: "",

      provincePlaces: [],

      provincePlacePage: 1,

      provincePlacesLength: 0,

      provinceSearchPlace: "",

			provinceSearchedPlace: "",
			
			regionPlaces: [],

      regionPlacePage: 1,

      regionPlacesLength: 0,

      regionSearchPlace: "",

      regionSearchedPlace: "",

      modalImages: {
        
        createImages: {
          
          name: "button.add_images",
          
          isVisible: false,
          
          button: {
            
            save: "save_button",
            
            cancel: "cancel",
            
            new: "button.add_images",
            
            loading: false
          
          }

        },

        changeImage: {
          
          name: "button.change_image",
          
          isVisible: false,
          
          button: {
            
            save: "button.save_change",
            
            cancel: "cancel",
            
            new: "button.change_image",
            
            loading: false
          
          }

        },

      },

      currentImage: [],

      imagesForm: new Form({
        
        id: "",
        geolocalize_image_id: "",
        images: [],
        imagesPlaceholder: [],
        place: "",
        language: this.$i18n.locale
      
      }),

      imageUpdateForm: new Form({

        geolocalize_image_id: "",
        image: null,
        imagePlaceholder: '',
        language: this.$i18n.locale
      
      }),
      filterBy: "",
      dialogImage: "",
      imageId: ""
    };
  },
  created() {
    this.getFilterByURLParameter();
    // this.listProfession();
  },
  methods: {
    ...mapActions("country", ["get_all_city", "get_all_province", "get_all_region"]),
    ...mapActions("geolocalization", [ "delete_geolocalize_image", "post_change_publish_status"]),
    createGeolocalization() {
      this.cityPlacePage = 1;
			this.provincePlacePage = 1;
			this.regionPlacePage = 1;
      this.loadCityPlaces();
			this.loadProvincePlaces();
      this.loadRegionPlaces()
      this.$bvModal.show("geolocalization-add-modal");
    },

		async loadCityPlaces(isSearched = false) {
      let params = {
        api_token: this.$user.api_token,
        lang: this.$i18n.locale,
        page: this.cityPlacePage,
        search: this.citySearchedPlace,
      };
      this.get_all_city(params).then((data) => {
        this.cityPlacesLength = data.length;
        if(this.cityPlacePage == 1 || isSearched == true) {
          this.cityPlaces = data;
          return;
        }
        this.cityPlaces = this.cityPlaces.concat(data);
      });
    },

    async loadProvincePlaces(isSearched = false) {
      let params = {
        api_token: this.$user.api_token,
        lang: this.$i18n.locale,
        page: this.provincePlacePage,
        search: this.provinceSearchedPlace,
      };
      this.get_all_province(params).then((data) => {
        this.provincePlacesLength = data.length;
        if(this.provincePlacePage == 1 || isSearched == true) {
          this.provincePlaces = data;
          return;
        }
        this.provincePlaces = this.provincePlaces.concat(data);
      });
		},
		
		async loadRegionPlaces(isSearched = false) {
      let params = {
        api_token: this.$user.api_token,
        lang: this.$i18n.locale,
        page: this.regionPlacePage,
        search: this.regionSearchedPlace,
      };
      this.get_all_region(params).then((data) => {
        this.regionPlacesLength = data.length;
        if(this.regionPlacePage == 1 || isSearched == true) {
          this.regionPlaces = data;
          return;
        }
        this.regionPlaces = this.regionPlaces.concat(data);
      });
    },

    changePublishStatus(geolocalization, index) {

      let publish_status = geolocalization.publish_status == "Unpublished" ? "Published" : "Unpublished";

      let formData = new FormData();
      formData.append("api_token", this.$user.api_token);
      formData.append("id", geolocalization.geolocalization_id);
      formData.append("article_id", geolocalization.article_id)
      formData.append("publish_status", publish_status);

      this.post_change_publish_status(formData)
        .then((resp) => {
          if(resp.data) {
            let message = {
              create:
                `${geolocalization.place} ${this.$t("publish_status_changed_message")} ${publish_status}`,
              title: {
                create: this.$t("publish_status_changed"),
              },
            };
  
            this.makeToast(
              "success",
              message.title.create,
              message.create
            );

            this.loadItems()
          }
        })
        .catch((error) => {
          let errors = error.response.data.errors;
          console.log(errors);
          this.toastError(errors);
        });
    },

    toastError(error){
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        value.forEach(function(message) {
          vm.makeToast("danger", vm.$t('errors.error'), message);
        })
        
      }
    },

    setDialogImage(image) {
      this.dialogImage = image.image
      this.imageUpdateForm.geolocalize_image_id = image.id
      this.dialog = true
    },

    resetImagesForm() {
      this.imagesForm.images = [];
      this.imagesForm.imagesPlaceholder = [];
      this.imagesForm.id = "";
      this.imagesForm.language = this.$i18n.locale
      this.currentImage = [];
    },

    resetImageUpdateForm() {
      this.imageUpdateForm.image = null
      this.imageUpdateForm.geolocalize_image_id = ""
      this.imageUpdateForm.imagePlaceholder = ""
      this.imagesForm.language = this.$i18n.locale
    },

    onAddImages(item) {
      this.imagesForm.id = item.geolocalization_id
      this.imagesForm.place = item.place;
      this.$bvModal.show("images-add-modal");
    },

    changeImage(){
      this.$bvModal.show("update-image-modal");
    },

    removeImage(){
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
          vm.$t("delete_image") +"?",
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
                vm.isDialogImageLoading = true;
                let params = {
                  api_token: vm.$user.api_token,
                  id: vm.imageUpdateForm.geolocalize_image_id,
                };
                vm.delete_geolocalize_image(params)
                  .then((resp) => {
                    if (resp) {
                      vm.imageUpdateForm.geolocalize_image_id = '';
                      vm.isDialogImageLoading = false;
                      vm.dialog = false;
                      vm.loadItems();
                      vm.makeToast(
                        "danger",
                        vm.$t("delete_image_title"),
                          vm.$t("brands_image")+
                          vm.$t("delete_record_message") +
                          vm.$t("geolocalization_geolocalize_images") +
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

    getFilterByURLParameter() {
      var urlString = window.location.href
      var url = new URL(urlString);
      var filterBy = url.searchParams.get("filterBy");
      if(filterBy != null) {
        this.filterBy = filterBy
      }
    },

    performSearchVariousPlace: _.debounce(function(query,name) {
      switch (name) {
        case 'city':
          this.citySearchedPlace = query
          break;
        case 'province':
          this.provinceSearchedPlace = query
          break;
        case 'region':
          this.regionSearchedPlace = query
          break;
      }
    }, 1000)

  },

  computed: {
  },

  watch: {
    citySearchPlace(query) {
      this.performSearchVariousPlace(query,'city')
    },

    provinceSearchPlace(query) {
      this.performSearchVariousPlace(query,'province')
    },

    regionSearchPlace(query) {
      this.performSearchVariousPlace(query,'region')
    },

    citySearchedPlace: {
      handler: function(value) {
        this.cityPlacePage = 1
        this.loadCityPlaces(true)
      }
    },

    provinceSearchedPlace: {
      handler: function(value) {
        this.provincePlacePage = 1
        this.loadProvincePlaces(true)
      }
    },

    regionSearchedPlace: {
      handler: function(value) {
        this.regionPlacePage = 1
        this.loadRegionPlaces(true)
      }
    },
  },
};
