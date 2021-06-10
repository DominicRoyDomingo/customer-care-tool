<template>
  <div class="create">
    <b-modal
      id="update-image-modal"
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
          <v-card-text class="modal__content">
            <v-container class="px-5">
              <v-row>
                <v-col cols="12" md="12" class="modal__input-container">
									<v-file-input 
										:rules="rules"
										accept="image/png, image/jpeg, image/bmp"
										placeholder="Select Image"
										prepend-icon="mdi-camera"
										color="blue"
										v-model="parent.imageUpdateForm.image"
										@click:clear="removeImage"
										@change="addImage">
									</v-file-input>
									<v-row>
										<v-col sm="4" v-if="parent.imageUpdateForm.imagePlaceholder">
											<v-hover v-slot="{ hover }">
												<v-card>
													<v-img
														height="175"
														contain
														:src="parent.imageUpdateForm.imagePlaceholder"
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
																				@click="removeImage"
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

import Form from "./../../../shared/form.js";

export default {
  props: ["parent"],

  data: function() {
    return {
			modal: this.parent.modalImages.changeImage,

			rules: [
				v => !!v || 'Image is required',
			],
    };
  },
  
  methods: {
		...mapActions("geolocalization", [
      "post_geolocalization_image",
    ]),
    modalClose(done) {

			this.parent.resetImageUpdateForm()
      this.parent.$bvModal.hide("update-image-modal");
      
		},

		addImage(){
			if(this.parent.imageUpdateForm.image == null) return
			this.parent.imageUpdateForm.imagePlaceholder = URL.createObjectURL(this.parent.imageUpdateForm.image)
		},

		removeImage(index) {
			this.parent.imageUpdateForm.image = null
			this.parent.imageUpdateForm.imagePlaceholder = ''
		},

		onSubmit() {
			if(!this.$refs.form.validate()) return;
			this.modal.button.loading = true;
			let formData = new FormData();
			formData.append('images', this.parent.imageUpdateForm.image);
      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.$i18n.locale);
      formData.append("geolocalize_image_id", this.parent.imageUpdateForm.geolocalize_image_id);
      

      this.post_geolocalization_image(formData)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("update-image-modal");
            let message = {
              update: this.$t( 'change_image' ) + this.$t( 'geolocalization_geolocalize_images' ) + ` ID: ${this.parent.imageUpdateForm.geolocalize_image_id} ` + this.$t( 'updated_message2' ),
              title: {
                update: this.$t( 'image_updated' )
              }
            };
            
            this.parent.makeToast(
              "success",
              message.title.update,
              message.update
            );
						
						this.parent.resetImageUpdateForm()
            this.parent.loadAlgoliaSummary();
            this.parent.dialog = false;
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
</style>
