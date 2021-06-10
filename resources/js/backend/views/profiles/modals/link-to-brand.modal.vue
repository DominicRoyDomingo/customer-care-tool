<template>
  <b-modal
    id="link-to-brand-modal"
    @hide="$bvModal.hide('link-to-brand-modal')"
    hide-header
    hide-footer
    size="md"
  >
    <v-app id="client_engagements__modal">
      <v-card>
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{ $t("label.link_to_brand") }}
              <span
                class="body-1 blue-grey lighten-4 text--disabled font-weight-light"
              >
                ({{ parent.form.profile_name }})
              </span>
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="$bvModal.hide('link-to-brand-modal')"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-container>
              <label :for="'brands'">
                {{ $t("label.brands") }}
                <strong class="text-danger">*</strong>
              </label>
              <v-select
                :id="'brands'"
                label="brand_name"
                :reduce="(brand) => brand.id"
                v-model="parent.form.brands"
                multiple
                :options="brandOptFilter"
                :closeOnSelect="false"
              >
                <template #list-header>
                  <li style="margin-left:20px;" class="mb-2">
                    <b-link href="#" @click="parent.modalAddNewBrand()">
                      <i class="fa fa-plus" aria-hidden="true"></i>
                      {{ $t("label.new_brand") }}
                    </b-link>
                  </li>
                </template>
              </v-select>
              <small
                v-if="parent.form.errors.has('brands')"
                v-text="parent.form.errors.get('brands')"
                class="text-danger"
              />
            </v-container>
          </v-card-text>
          <v-divider style="margin-bottom: 0"></v-divider>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-spacer></v-spacer>
            <v-btn
              color="error"
              text
              tile
              @click="$bvModal.hide('link-to-brand-modal')"
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
                <div v-if="parent.form.action == 'Add'">
                  <v-icon left>mdi-account-plus</v-icon>
                  {{ $t("button.save") }}
                </div>
                <div v-else>
                  <v-icon left>mdi-account-edit</v-icon>
                  {{ $t("button.save_change") }}
                </div>
              </div>
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-app>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
  name: "link_to_brand_modal",
  props: ["parent"],

  data() {
    return {
      loading: true,
      vm: this,
      valid: true
    };
  },

  components: {},
  computed: {
    ...mapGetters("profile", {
      response: "get_response",
    }),
    brandOptFilter(){
        return this.parent.brands.map((item) => ({
            id : item.id,
            brand_name : item.brand_name
        }))
    }
  },
  methods: {
    ...mapActions("profile", ["link_to_brands"]),

    hide() {
      this.$emit("hide");
    },

    onSave() {
      this.parent.btnloading = true;

      let params = {
        api_token: this.$user.api_token,
        action_route: "/link_to_brands",
        profile_id: this.parent.form.profile_id,
        brands: this.parent.form.brands,
      };
      let vm = this;
      this.link_to_brands(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("link-to-brand-modal");

          if (vm.response.responseStatus) {
            let notification_message = this.$t("toasts.updated_profile");
            notification_message = notification_message.replace(
              /%variable%/g,
              this.parent.itemSelected.profile_name
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
              message.update
            );

            //Add callback function here if necessary
            this.parent.linkedBrandsSuccessfully(vm.response);
          }
        })
        .catch((error) => {
          console.log(error);
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnloading = false;
        });
    },
  },
};
</script>
