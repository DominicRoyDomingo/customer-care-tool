<template>
  <div class="create">
    <b-modal
      id="images-add-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
      :title="$t(modal.name)"
    >
      <v-app>
        <v-form ref="form">
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
          <v-card-text class="modal__content" style="min-height: 190px;">
            <v-container class="px-5">
              <v-row>
                <v-col cols="12" md="12" class="modal__input-container">
                  <v-row>
                    <v-col class="sm-12">
                      <div class="box" v-cloak @drop="dropImage" @dragover.prevent @click="selectImages">
                        <div class="d-flex text-center flex-column h-100"> 
                          <div class="d-flex justify-center mt-2">
                            <v-img
                              contain
                              max-height="150"
                              max-width="250"
                              src="/img/backend/geolocalization/upload-image.png"
                            ></v-img>
                          </div>
                          <h2 style="color: #7e7e7e;">Browse or drag an Image</h2>
                          
                        </div>
                      </div>
                    </v-col>
                    <v-col sm="12">
                      <!-- <v-btn 
                        block 
                        color="info"
                        @click="selectImages"
                      >
                        {{ $t(modal.name) }}
                      </v-btn> -->
                      <v-file-input 
                        ref="select_image"
                        class="d-none"
                        :rules="rules"
                        accept="image/png, image/jpeg, image/bmp"
                        placeholder="Select Images"
                        prepend-icon="mdi-camera"
                        multiple chips color="blue"
                        v-model="parent.currentImage"
                        @click:clear="removeAllImage"
                        @change="addImages">
                      </v-file-input>
                    </v-col>
                  </v-row>
									<v-row>
										<v-col sm="4" v-for="(image,index) in parent.imagesForm.imagesPlaceholder" :key="index">
											<v-hover v-slot="{ hover }">
												<v-card>
													<v-img
														height="175"
														contain
														:src="image"
														class="grey darken-4"
													></v-img>
													<v-fade-transition>
														<v-overlay
															v-if="hover"
															absolute
															color="#282828"
															opacity="0.9"
														>
														<div class="d-flex fill-height" style="flex-direction:column">
															<div class="d-flex">
																<v-spacer></v-spacer>
																<div class="pa-1">
																	<v-tooltip top>
																		<template v-slot:activator="{ on, attrs }">
																			<v-btn
																				icon
																				v-bind="attrs"
																				v-on="on"
																				@click="removeImage(index)"
																			>
																				<v-icon>
																					mdi-close
																				</v-icon>
																			</v-btn>
																		</template>
																		<span>Remove</span>
																	</v-tooltip>
																</div>
															</div>
														</div>
														</v-overlay>
													</v-fade-transition>
												</v-card>
											</v-hover>
										</v-col>
									</v-row>
                </v-col>
              </v-row>
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
                    {{ $t(modal.button.save) }}
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

import CreateCities from "./cities/CreateComponent.vue";

import CreateProvinces from "./provinces/CreateComponent.vue";

import CreateRegions from "./regions/CreateComponent.vue";

import Form from "./../../../shared/form.js";

export default {
  props: ["parent"],

  components: {

    CreateCities,
  
  },

  data: function() {
    return {
			modal: this.parent.modalImages.createImages,

			rules: [
				v => !!v || 'Image is required',
				v => (v && v.length > 0) || 'Image is required',
			],

      regionForm: new Form({
        
        id: "",
        places: "",
        language: this.$i18n.locale
      
      }),
    };
  },
  
  methods: {
		...mapActions("geolocalization", [
      "post_geolocalization_image",
    ]),
    modalClose(done) {

			this.parent.resetImagesForm()
      this.parent.$bvModal.hide("images-add-modal");
      
		},

		addImages(){
      this.parent.imagesForm.images = [
        ...this.parent.imagesForm.images,
        ...this.parent.currentImage
      ]
			this.parent.imagesForm.images.forEach((image, index) => {
				this.parent.imagesForm.imagesPlaceholder[index] = URL.createObjectURL(image)
			})
    },

    dropImage(event) {
      let vm = this
      event.preventDefault();
      vm.parent.currentImage = [];
      event.dataTransfer.files.forEach(function(file) {
        vm.parent.currentImage.push(file)
      })
      vm.addImages()
    },
    
    selectImages() {
      this.$refs.select_image.$refs.input.click()
    },

		removeImage(index) {
			this.parent.imagesForm.images.splice(index,1)
			this.parent.imagesForm.imagesPlaceholder.splice(index,1)
		},

		removeAllImage() {
			this.parent.imagesForm.imagesPlaceholder = []
		},

		onSubmit() {
			if(!this.$refs.form.validate()) return;
			this.modal.button.loading = true;
			let formData = new FormData();
			this.parent.imagesForm.images.forEach(function(image,i){
				formData.append('images[' + i + ']', image);
			})
      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.$i18n.locale);
      formData.append("id", this.parent.imagesForm.id);

      this.post_geolocalization_image(formData)
        .then((resp) => {
          let message_text = "";
          this.parent.btnloading = false;
          this.$bvModal.hide("images-add-modal");

          message_text = this.$t("toasts.added_images");
          message_text = message_text.replace(
            /%variable%/g,
            this.parent.imagesForm.place
          );

          this.parent.parent.makeToast(
            "success",
            this.$t("new_record_created"),
            message_text
          );
          
          this.parent.resetImagesForm()

          this.modal.button.loading = false;
          this.$emit("loadTable");
        })
        .catch((error) => {
          console.log(error)
          this.modal.button.loading = false;
          let errors = error.response.data.errors;
          this.toastError(errors);
        });
    },
  }
};
</script>

<style>
.v-overlay__content {
  height: 100%;
  width: 100%;
}

.box{
  width: 100%;
  height: 210px;
  border: 4px dashed #878787;
  border-radius: 14px;
}

.box:hover {
  background-color: #e2e2e2;
  cursor: pointer;
}
</style>
