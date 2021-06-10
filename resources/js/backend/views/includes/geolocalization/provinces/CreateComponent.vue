<template>
  <div class="create">
      <v-app>
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-card-text class="modal__content">
            <v-container style="height: 350px;">
              <v-container>
                <v-row>
                  <v-col cols="12" md="12" class="modal__input-container">
                    <label :for="'brands'">
                        {{ $t("geolocalization_geolocalized_in_provinces") }}
                        <strong class="text-danger">*</strong>
                    </label>
										<v-select
											label="place"
											:options="parent.parent.provincePlaces"
											:filterable="false"
											v-model="form.places"
											multiple
											@open="onOpen"
											@close="onClose"
											@search="query => parent.parent.provinceSearchPlace = query"
										>
											<template #list-footer>
												<li ref="load" class="loader" v-show="hasNextPage">
													Loading more options...
												</li>
											</template>
                      <template #list-header>
                        <li style="margin-left:20px;" class="mb-2">
                            <b-link href="#" @click="openProvinceModal">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            {{ $t("label.new_province") }}
                            </b-link>
                        </li>
                      </template> 
										</v-select>
                    <!-- <v-select
                        :id="'brands'"
                        label="place"
                        @change="parent.form.errors.clear('brands')"
                        v-model="form.places"
                        multiple
                        :options="parent.places"
                    >
                        
                    </v-select> -->
                  </v-col>
                </v-row>
              </v-container>
            </v-container>
          </v-card-text>
          <v-divider style="margin-bottom: 0"></v-divider>
            <v-card-actions class="modal__footer blue-grey lighten-5">
              <div v-if="this.modal.id != undefined">
                <v-btn
                  color="error"
                  text
                  tile
                  link
                  @click.stop="parent.informationTypePage"
                >
                  <v-icon>
                    mdi-open-in-new
                  </v-icon>
                  {{ $t("label.go_to_information_type_page") }}
                </v-btn>
              </div>
              <v-spacer></v-spacer>
              <v-btn
                color="error"
                text
                tile
                @click="parent.modalClose"
              >
                {{ $t(modal.button.cancel) }}
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
                    {{ $t(modal.button.save) }}
                  </div>
                </div>
              </v-btn>
            </v-card-actions>
        </v-form>
      </v-app>
  </div>
</template>

<script>

import { mapActions, mapGetters } from "vuex";

export default {
  props: ["parent"],

  name: "InfiniteScroll",

  data: function() {
    return {
      modal: this.parent.geolocalizationProvince.createGeolocalization,

      submitted: false,

      keep_open: false,

      valid: true,

      form: this.parent.provinceForm,

      observer: null,
			limit: 10,
			search: ''
    };
	},

	mounted () {
    this.observer = new IntersectionObserver(this.infiniteScroll);
  },
	
	
		
	computed: {
		filtered () {
			// console.log(parent.places)
			return this.parent.parent.provincePlaces.filter(place => place.place.toLowerCase().includes(this.search.toLowerCase()));
		},
		paginated () {
			return this.filtered.slice(0, this.limit);
		},
		hasNextPage () {
			return this.parent.parent.provincePlacesLength != 0;
		},
	},

  methods: {
    ...mapActions("geolocalization", [
      "post_geolocalization",
      "add_geolocalization"
    ]),

    ...mapActions("location", [
        "get_countries",
        "get_provinces",
        "get_cities",
    ]),

    modalClose(done) {

      this.parent.$bvModal.hide("geolocalization-province-add-modal");

      this.form.reset();

		},

		async onOpen () {
      if (this.hasNextPage) {
        await this.$nextTick();
        this.observer.observe(this.$refs.load)
      }
    },
    
    onClose () {
      this.observer.disconnect();
    },

    async infiniteScroll ([{isIntersecting, target}]) {
      if (isIntersecting) {
        setTimeout(() => {
          this.parent.ul = target.offsetParent;
          this.parent.scrollTop = target.offsetParent.scrollTop;
          this.parent.parent.provincePlacePage = this.parent.parent.provincePlacePage+1;
          let params = {
            api_token: this.$user.api_token,
            lang: this.$i18n.locale,
            page: this.parent.parent.provincePlacePage,
            search: this.parent.parent.provinceSearchedPlace,
          };
          this.parent.parent.get_all_province(params).then((data) => {
            this.parent.parent.provincePlacesLength = data.length
            this.parent.parent.provincePlaces = this.parent.parent.provincePlaces.concat(data);
          });
        }, 1000);
      }
    },

    openProvinceModal() {
      let params = {
        api_token: this.$user.api_token,
      };
      this.get_countries(params)
      .then((resp) => {
      });
      this.parent.$bvModal.show('geolocalization-province-province-modal');
    },

    onSubmit() {
			this.$refs.form.validate();
			this.modal.button.loading = true;
			let formData = new FormData();
			let places = ""
      if(this.form.places.length != 0) places = JSON.stringify(this.form.places)
      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.$i18n.locale);
      formData.append("article_id", this.parent.parent.parent.article.id);
      formData.append("places", places);
      formData.append("area", 'Province');

      this.post_geolocalization(formData)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("geolocalization-add-modal");

          if (this.parent.parent.geolocalizationResponseStatus) {
            
            let message = {
              create:
                `${this.parent.parent.geolocalizationResponseStatus} ${this.$t("label.province_2").toLowerCase()} ${this.$t("brand_linked1")} '${this.parent.parent.parent.article.base_name}'`,
              title: {
                create: this.$t("new_record_created"),
              },
            };

            this.parent.parent.makeToast(
              "success",
              message.title.create,
              message.create
            );
            this.form.reset();
            this.modal.button.loading = false;
            this.$emit("loadTable");
          }
        })
        .catch((error) => {
          console.log(error)
          this.modal.button.loading = false;
          let errors = error.response.data.errors;
          this.toastError(errors);
        });
    },

    toastError(error){
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        this.parent.parent.makeToast("danger", vm.$t('errors.error'), value);
      }
    },
  },
};
</script>

<style></style>
