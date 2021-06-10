<template>
  <div class="create">
    <b-modal
      id="geolocalization-region-edit-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
      :title="$t(modal.name)"
    >
      <v-app>
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <span>
              {{ $t(modal.name) }}
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
                  <v-col cols="12" md="12" class="modal__input-container">
                    <label :for="'brands'">
                        {{ $t("geolocalization_geolocalized_in_regions") }}
                        <strong class="text-danger">*</strong>
                    </label>
										<v-select
											label="place"
											:options="parent.places"
											:filterable="false"
											v-model="parent.place"
											@open="onOpen"
											@close="onClose"
											@search="query => parent.searchPlace = query"
										>
											<template #list-footer>
												<li ref="load" class="loader" v-show="hasNextPage">
													Loading more options...
												</li>
											</template>
                      <template #list-header>
                        <li style="margin-left:20px;" class="mb-2">
                            <b-link href="#" @click="openRegionModal">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            {{ $t("label.new_region") }}
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
                         <template #list-header>
                        <li style="margin-left:20px;" class="mb-2">
                            <b-link href="#" @click="openBrandModal">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            {{ $t("geolocalization_add_new_button") }}
                            </b-link>
                        </li>
                        </template> 
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
                @click="modalClose"
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
                    {{ $t(modal.button.update) }}
                  </div>
                </div>
              </v-btn>
            </v-card-actions>
        </v-form>
      </v-app>
    </b-modal>
  </div>
</template>

<script>

import { mapActions, mapGetters } from "vuex";

export default {
  props: ["parent"],

  name: "InfiniteScroll",

  data: function() {
    return {
      modal: this.parent.modalGeolocalization.edit,

      submitted: false,

      keep_open: false,

      valid: true,

      form: this.parent.form,

      formData: this.parent.formData,

      formGroups: [],

      observer: null,
			limit: 10,
			search: ''
    };
	},

	mounted () {
    this.observer = new IntersectionObserver(this.infiniteScroll);
  },
	
	
		
	computed: {
		hasNextPage () {
			return this.parent.placesLength != 0;
		},
	},

  methods: {
    ...mapActions("geolocalization", [
      "post_geolocalization",
      "add_geolocalization"
    ]),

    ...mapActions("location", [
        "get_countries",
    ]),

    modalClose(done) {

      this.parent.$bvModal.hide("geolocalization-region-edit-modal");

      this.form.reset();

      this.keep_open = false;
    },
    
    openRegionModal() {
      let params = {
        api_token: this.$user.api_token,
      };
      this.get_countries(params)
      .then((resp) => {
      });
      this.parent.$bvModal.show('geolocalization-region-region-modal');
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
          this.parent.placePage = this.parent.placePage+1;
          let params = {
            api_token: this.$user.api_token,
            lang: this.parent.form.language,
            page: this.parent.placePage,
            search: this.parent.searchedPlace,
          };
          this.parent.get_all_region(params).then((data) => {
            this.parent.placesLength = data.length
            this.parent.places = this.parent.places.concat(data);
          });
        }, 1000);
      }
    },

    onSubmit() {
      this.$refs.form.validate();
      let formData = new FormData();
      let place = ""
      if(this.parent.place != null) place = JSON.stringify(this.parent.place)
      formData.append("api_token", this.$user.api_token);
      formData.append("id", this.form.id);
      formData.append("lang", this.form.language);
      formData.append("article_id", this.parent.parent.article.id);
      formData.append("place", place);
      formData.append("area", 'Region');

      this.post_geolocalization(formData)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("geolocalization-region-edit-modal");

          if (this.parent.geolocalizationResponseStatus) {

            let message = {
              update: this.$t( 'updated_message1' ) + this.$t( 'geolocalization' ) + ` ID: ${this.parent.form.id} ` + this.$t( 'updated_message2' ),
              title: {
                update: this.$t( 'record_updated' )
              }
            };
            
            this.parent.makeToast(
              "success",
              message.title.update,
              message.update
            );
            
            this.modal.button.loading = false;
            this.parent.loadItems();
          }
        })
        .catch((error) => {
          this.modal.button.loading = false;
          let errors = error.response.data.errors;
          this.toastError(errors);
        });
    },

    toastError(error){
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        this.parent.makeToast("danger", vm.$t('errors.error'), value);
      }
    },
  },
};
</script>

<style></style>
