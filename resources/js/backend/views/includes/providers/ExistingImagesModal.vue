<template>
  <b-modal
    id="existing-images-modal"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >
    <v-app id="existing-images">
			<v-card>
				
				<v-card-title class="title blue-grey lighten-4 text--secondary">
					<span>
						{{ $t("label.existing_images") }}
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
						<v-row>
							<v-overlay
								absolute
								:value="isLoading"
							>
								<v-progress-circular
									:size="50"
      						:width="7"
									indeterminate
								></v-progress-circular>
							</v-overlay>
							<v-col sm="3" v-for="(image,index) in parent.existingImages" :key="index">
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
												class="image-overlay"
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
																	@click="removeImage(image, index)"
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
					</v-container>
				</v-card-text>
			</v-card>
		</v-app>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

// Snippets
import TermDescriptionIndex from "../../medical_stuff/snippets/term_descriptions/index";

// Modals
import CreateTermDescription from "../../medical_stuff/modals/create-termDescription.modal";

import medicalMixin from "../../medical_stuff/mixins/medicalMixin";
import termMixin from "../../medical_stuff/mixins/termMixin";

export default {
  props: ["parent"],
	
  data() {
    return {
      isLoading: false,
      
    };
  },

  methods: {
		...mapActions("providers", [
      "delete_image",
    ]),

    removeImage(image, index) {
      let vm = this;
			$.confirm({
        title:
          vm.$t("confirmation_record_delete_image").charAt(0) +
          vm
            .$t("confirmation_record_delete_image")
            .slice(1)
            .toLowerCase(),
        content:
          vm.$t("question_record_delete") +
          vm.$t("delete_image") + "?",
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
								vm.isLoading = true
                let params = {
                  api_token: vm.$user.api_token,
                  id: vm.parent.form.id,
									image: image,
                };
                vm.delete_image(params)
                  .then((resp) => {
                    if (vm.parent.providersResponseStatus) {
											vm.isLoading = false
											vm.parent.existingImages.splice(index,1)
											let response = vm.parent.providersResponseStatus;
											response.index = vm.parent.editingIndex;
											vm.parent.update_provider(response)
                      vm.parent.checkAlgoliaPermission()
											vm.parent.successfullySavedProvider()
                      vm.parent.makeToast(
                        "danger",
                        vm.$t("delete_image_title"),
                          vm.$t("brands_image")+
                          vm.$t("delete_record_message") +
													vm.parent.form.name
                      );
											if(vm.parent.existingImages == 0) vm.$bvModal.hide("existing-images-modal");
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

		modalClose() {
      this.$bvModal.hide("existing-images-modal");
    },
  },
};
</script>

<style>
.image-overlay .v-overlay__content {
  height: 100%;
  width: 100%;
}
</style>
