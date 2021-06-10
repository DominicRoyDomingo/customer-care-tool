<template>
  <div>
    <b-modal
      id="engagement-modal"
      @hide="hide"
      hide-header
      hide-footer
      size="md"
      scrollable
    >
      <v-app id="engagement__container">
        <v-card>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{
                parent.form.sub_action == "Add"
                  ? $t("label.add")
                  : $t("button.add")
              }}
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="$bvModal.hide('engagement-modal')"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <form
            class="form"
            @submit.prevent="onSave"
            @keyup="parent.form.errors.clear($event.target.name)"
          >
            <div
              class="form-group mb-4"
              v-show="parent.form.sub_action === 'Update'"
            >
              <div class="form-inline">
                <v-container>
                  <label class="mr-sm-2" for="inline-form-custom-select-pref"
                    >Language</label
                  >
                  <b-form-select
                    id="inline-form-custom-select-pref"
                    class="mb-2 mr-sm-2 mb-sm-0"
                    v-model="parent.language"
                    :options="parent.languageOptions"
                  />
                </v-container>
              </div>
              <v-divider></v-divider>
            </div>

            <div class="form-group">
              <v-container>
                <label for="engagement">
                  {{ $t("label.engagement") }}
                  <strong class="text-danger">*</strong>
                </label>

                <input
                  id="engagement"
                  type="text"
                  name="engagement"
                  v-model="parent.form.engagement"
                  class="form-control"
                  :placeholder="$t('label.required')"
                  autocomplete="off"
                  aria-describedby="job"
                />
                <small
                  id="job"
                  v-if="parent.form.errors.has('engagement')"
                  v-text="parent.form.errors.get('engagement')"
                  class="text-danger"
                />
              </v-container>

              <v-container>
                <!-- brands data -->
                <label for="brands">
                  {{ $t("brands_name") }}
                </label>
                <v-select
                  v-model="parent.form.engagement_brands"
                  label="brand_name"
                  :reduce="(brand) => brand.id"
                  :options="parent.brands"
                  multiple
                >
                  <template #list-header>
                    <li style="margin-left:20px;" class="mb-2">
                      <b-link
                        href="#"
                        @click="parent.modalAddNewBrand('add engagement form')"
                      >
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        {{ $t("label.new_brand") }}
                      </b-link>
                    </li>
                  </template>
                </v-select>
                <small
                  id="job"
                  v-if="parent.form.errors.has('brands')"
                  v-text="parent.form.errors.get('brands')"
                  class="text-danger"
                />
              </v-container>
            </div>
          </form>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-spacer></v-spacer>
            <v-btn
              color="error"
              text
              tile
              @click="$bvModal.hide('engagement-modal')"
            >
              {{ $t("cancel") }}
            </v-btn>
            <v-btn color="success" tile dark @click="onSave">
              <div v-if="parent.btnloading">
                <v-progress-circular
                  size="20"
                  width="1"
                  color="white"
                  indeterminate
                >
                </v-progress-circular>
              </div>
              <div v-else>
                <div v-if="parent.form.sub_action == 'Add'">
                  <v-icon left>mdi-account-plus</v-icon>
                  {{ $t("button.save") }}
                </div>
                <div v-else>
                  <v-icon left>mdi-account-edit</v-icon>
                  {{ $t("button.save") }}
                </div>
              </div>
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-app>
    </b-modal>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
export default {
  props: ["parent"],

  data() {
    return {
      loading: true,
    };
  },

  computed: {
    ...mapGetters({
      engageStatus: "actions/get_engagament_status",
    }),
  },

  methods: {
    ...mapActions("actions", ["post_engagement"]),

    hide() {
      this.$emit("hide");
    },

    onSave() {
      // this.parent.btnloading = true;
      let params = {
        api_token: this.$user.api_token,
        action: "Add",
        engagement: this.parent.form.engagement,
        brands: this.parent.form.engagement_brands,
        locale: this.parent.language,
      };

      this.post_engagement(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("engagement-modal");
          if (this.engageStatus) {
            let notification_message = this.$t("toasts.added_engagement");
            notification_message = notification_message.replace(
              /%variable%/g,
              this.parent.form.engagement
            );

            let message = {
              create: notification_message,
              update: notification_message,
              title: {
                create: this.$t("new_record_created"),
                update: this.$t("record_updated"),
              },
            };

            this.parent.makeToast(
              "success",
              message.title.create,
              message.create
            );

            this.parent.addedNewEngagementSuccessfully();
            this.parent.form.engagement = "";
          }
        })
        .catch((error) => {
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnloading = false;
        });
    },
  },
};
</script>
