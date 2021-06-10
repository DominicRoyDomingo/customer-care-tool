<template>
  <div class="create">
    <b-modal
      id="algolia-class-add-modal"
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
                      {{ $t(modal.button.save) }}
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
      modal: this.parent.modal.createAlgoliaClass,

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
    ...mapActions("algolia_class", ["post_algolia_class", "add_algolia_class"]),
    
    modalClose(done) {

      this.parent.$bvModal.hide("algolia-class-add-modal");

      this.parent.modal.add != undefined
        ? this.parent.$bvModal.show(this.parent.modalId)
        : this.form.reset();
    },

    onSubmit() {
      this.$refs.form.validate();
      this.modal.button.loading = true;

			let params = {
				api_token: this.$user.api_token,
				name: this.form.name.charAt(0).toUpperCase() + this.form.name.slice(1),
			}
      
      this.post_algolia_class(params)
        .then((resp) => {
          this.$bvModal.hide("algolia-class-add-modal");
					let message = {
						create:
							`${this.form.name}` +
							this.$t("created_message") +
							this.$t("algolia_class"),
						title: {
							create: this.$t("new_record_created"),
						},
					};

					this.parent.makeToast(
						"success",
						message.title.create,
						message.create
					);
					this.modal.button.loading = false;
					this.$emit("loadTable");
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

<style>
</style>
