<template>
  <div>
    <b-modal id="platform-modal" hide-header hide-footer size="md">
      <v-app id="job-description__container">
        <v-card>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{
                parent.form.sub_action == "Add"
                  ? $t("label.add")
                  : $t("button.update")
              }}
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="$bvModal.hide('platform-modal')"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <br />
            <form
              class="form"
              @submit.prevent="onSave"
              @keyup="parent.form.errors.clear($event.target.name)"
            >
              <div class="form-group">
                <label :for="'brand_id'">
                  {{ $t("label.brand") }}
                  <strong class="text-danger">*</strong>
                </label>
                <v-select
                  :id="'brand_id'"
                  :name="'brand_id'"
                  label="name"
                  @change="parent.form.errors.clear('brand_id')"
                  :reduce="(brand) => brand.id"
                  v-model="parent.form.brand_id"
                  :options="parent.brands"
                  :closeOnSelect="false"
                >
                <template #list-header>
                  <li style="margin-left:20px;" class="mb-2">
                    <b-link href="#" @click="AddBrand">
                      <i class="fa fa-plus" aria-hidden="true"></i>
                      {{ "Add Brand" }}
                    </b-link>
                  </li>
                </template>
                </v-select>
                <small
                  v-if="parent.form.errors.has('brand_id')"
                  v-text="parent.form.errors.get('brand_id')"
                  class="text-danger"
                />
              </div>

              <div class="form-group">
                <label :for="'platform_type_id'">
                  {{ $t("label.platform_type") }}
                  <strong class="text-danger">*</strong>
                </label>
                <v-select
                  :id="'platform_type_id'"
                  :name="'platform_type_id'"
                  label="platform_type_name"
                  @change="parent.form.errors.clear('platform_type_id')"
                  :reduce="(platform_type) => platform_type.id"
                  v-model="parent.form.platform_type_id"
                  :options="parent.platform_types"
                  :closeOnSelect="false"
                >
                <template #list-header>
                  <li style="margin-left:20px;" class="mb-2">
                    <b-link href="#" @click="AddPlatformType">
                      <i class="fa fa-plus" aria-hidden="true"></i>
                      {{ "New Platform Type" }}
                    </b-link>
                  </li>
                </template>
                </v-select>
                <small
                  v-if="parent.form.errors.has('platform_type_id')"
                  v-text="parent.form.errors.get('platform_type_id')"
                  class="text-danger"
                />
              </div>
            </form>
          </v-card-text>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-spacer></v-spacer>
            <v-btn
              color="error"
              text
              tile
              @click="$bvModal.hide('platform-modal')"
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
                  {{ $t("button.save_change") }}
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
      platformItemStatus: "platforms/get_platform_item_status",
    }),
  },

  methods: {
    ...mapActions("platforms", ["post_platform_item"]),

    hide() {
      this.$emit("hide");
    },
    AddPlatformType(){
        this.parent.$bvModal.show("add-platform-type");
    },
    AddBrand(){
        this.parent.$bvModal.show("brand-modal");
    },
    onSave() {
      // this.parent.btnloading = true;
      let params = {
        api_token: this.$user.api_token,
        action: "Add",
        brand_id: this.parent.form.brand_id,
        platform_type_id: this.parent.form.platform_type_id,
      };

      this.post_platform_item(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("platform-modal");
          if (this.platformItemStatus) {
            let notification_message = this.$t("toasts.added_platform");

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

            this.parent.resetPlatformItemForm();
            this.parent.fetchPlatformItems();
            this.parent.$bvModal.show("edit-client-engagement-modal");
            // this.parent.addedNewPlatformItemSuccessfully();
            this.parent.form.brand_id = "";
            this.parent.form.platform_id = "";
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
