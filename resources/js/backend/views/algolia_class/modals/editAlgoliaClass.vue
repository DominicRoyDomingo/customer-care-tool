<template>
  <div class="create">
    <b-modal
      id="algolia-class-edit-modal"
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
                    <v-col cols="12" md="12" class="modal__input-container">
                      <label for="class name" class="mt-2">
                        {{ $t("publishing_type_name") }}
                      </label>
											<input
												id="class_name"
												type="text"
												name="class_name"
												v-model="form.name"
												class="form-control"
												:placeholder="$t('publishing_type_name')"
												autocomplete="off"
												aria-describedby="class_name"
											/>
                    </v-col>
                  </v-row>
                </v-container>
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

export default {
  props: ["parent"],

  data: function() {
    return {
      modal: this.parent.modal.editAlgoliaClass,

      submitted: false,

      keep_open: false,

      form: this.parent.algoliaClassForm,

      valid: true,

      formData: this.parent.formData,

      country_id: "",

      image: undefined,

      formGroups: [],
    };
  },

  methods: {
    ...mapActions("algolia_class", ["post_algolia_class", "update_algolia_class"]),
    
    modalClose(done) {
      this.removeErrors();
      this.parent.$bvModal.hide("algolia-class-edit-modal");

      this.parent.modal.add != undefined
        ? this.parent.$bvModal.show(this.parent.modalId)
        : this.form.reset();
    },

    onSubmit() {
      this.$refs.form.validate();
      this.modal.button.loading = true;

			let params = {
				id: this.form.id,
				api_token: this.$user.api_token,
				name: this.form.name.charAt(0).toUpperCase() + this.form.name.slice(1),
			}
			
      this.post_algolia_class(params)
        .then((resp) => {
          this.removeErrors();
          this.$bvModal.hide("algolia-class-edit-modal");

					let response = this.parent.algoliaClassResponseStatus;
          response.index = this.parent.editingIndex;
          this.update_algolia_class(response);

					let message = {
						update: this.$t( 'updated_message1' ) + this.$t( 'algolia_class' ) + ` ID: ${this.form.id} ` + this.$t( 'updated_message2' ),
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
					this.parent.successfullySavedAlgoliaClass()
        })
        .catch((error) => {
          this.removeErrors();
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

		openClassModal() {
      // this.modal.isVisible = false;
      this.parent.$bvModal.hide(this.parent.modalId);
      this.parent.className = "";
      this.parent.$bvModal.show("algolia-class-add-modal");
      // this.parent.modal.itemType.isVisible = true;
    },

		openBrandModal() {
      this.parent.$bvModal.hide(this.parent.modalId);
      this.parent.modal.createBrand.isVisible = true;
    },

    removeErrors() {
      $('.field-error').remove();
      $('#staging_index_name, #live_index_name').removeAttr('style')
      $('#brand').children().first().removeAttr('style')
      $('#class').children().first().removeAttr('style')
    }
  },
};
</script>

<style>
.v-input--reverse .v-input__slot {
  flex-direction: row-reverse;
  justify-content: flex-end;
}

.v-input--reverse .v-input__slot .v-application--is-ltr .v-input--selection-controls__input {
	margin-right: 0;
	margin-left: 8px;
}

.v-input--reverse .v-input__slot .v-application--is-ltr .v-input--selection-controls__input {
	margin-right: 0;
	margin-left: 8px;
}

.v-input--reverse .v-label {
	margin-right: 16px;
  margin-bottom: 0 !important;
	flex: initial !important;
}
</style>
