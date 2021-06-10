<template>
  <b-modal
    id="edit-image-modal"
    hide-footer
    hide-header
    size="md"
    no-close-on-backdrop
  >
    <v-card class="border-0">
      <v-card-title class="bg-light text-uppercase">
        <div>

          <span
            class="d-inline-block text-truncate"
            style="max-width: 700px; letter-spacing: normal"
          >
            {{ $t("button.update") }} {{ $t("brands_image") }} 
          </span>
        </div>

        <v-spacer></v-spacer>
        <v-btn icon color="error lighten-2" @click="onClose">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-container>
        <v-row class="p-1">
          <v-col sm="12" md="12" cols="12">
            <form
              class="form"
              @submit.prevent="onSave"
              @keyup="parent.htmlTagForm.errors.clear($event.target.description)"
            >

              <div class="form-group">
                <label for="image" class="text-uppercase">
                {{ $t("image") }}
                </label>
                <div style="height: 156px; max-width: 200px;">
									<v-img
										v-if="parent.imagePlaceholder != null"
										height="156"
										:src="parent.imagePlaceholder"
									></v-img>

									<v-img
											v-else
											height="156"
											src="https://customer-care-tool.s3.us-east-2.amazonaws.com/image-placeholder/image-placeholder.png"
									></v-img>
                </div><br>
                <input 
									id="fileUpload" 
									type="file" 
									@change="onGetFile($event)"
									accept=".png, .jpg, .jpeg"
									plain
                >
              </div>
              <!-- <p
                class="mt-3 float-left"
                v-if="$options._componentTag !== 'IndexCreateTypeDate'"
              >
                <a
                  href="/admin/categorized-terms/type-dates"
                  class="text-uppercase text-danger"
                  v-html="`${$t('label.goto_date_type_page')}`"
                />
              </p> -->
              <v-card-actions class="float-right mb-0">
                <v-btn color="error" text tile @click="onClose">
                  {{ $t("cancel") }}
                </v-btn>
                <v-btn
                  color="success"
                  tile
                  type="submit"
                  class="bg-success text-white"
                >
                  <div v-if="btnloading" class="text-center">
                    <v-progress-circular
                      size="20"
                      width="1"
                      color="white"
                      indeterminate
                    >
                    </v-progress-circular>
                  </div>
                  <div v-else>
                    <span v-if="parent.htmlTagForm.action == 'Add'">
                      {{ $t("button.save") }}
                    </span>
                    <span v-else>{{ $t("button.save_change") }}</span>
                  </div>
                </v-btn>
              </v-card-actions>
            </form>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
  props: ["parent"],

  data() {
    return {
      itemSelected: {},
      btnloading: false,
    };
  },
  methods: {
    ...mapActions("categ_terms", ["update_image"]),
    onClose() {
      this.resetAll();
      this.$bvModal.hide("edit-image-modal");
    },

    onSave() {
			this.btnloading = true;
			
			let formData = new FormData();
      formData.append("id", this.parent.imageId);
			formData.append("image", this.parent.image);
			formData.append("locale", this.$i18n.locale);
      formData.append("api_token", this.$user.api_token);

      this.update_image(formData)
        .then((resp) => {
          console.log(resp.data);
          this.btnloading = false;
          this.$emit("done-success", resp.data);
          this.$bvModal.hide("edit-image-modal");
        })
        .catch((error) => {
          if (error.response) {
            form.errors.record(error.response.data.errors);
            if (form.description) {
              this.parent.errorToast(
                `${this.$t("errors.duplicate")}!`,
                form.errors.get("description")
              );
            }
          }
          this.btnloading = false;
        });
		},
		
		onGetFile(event) {
			if(event.target.files[0] == undefined) return;
      this.parent.image = event.target.files[0];
  
			this.parent.imagePlaceholder = URL.createObjectURL(this.parent.image);
    },

    resetAll() {
      this.btnloading = false;
      this.parent.imagePlaceholder = null;
      this.parent.image = null
      this.imageId = ''
    },
  },
};
</script>
