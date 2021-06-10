<template>
  <div>
    <b-modal
      id="platform_2-modal"
      hide-footer
      @hide="hide"
      size="md"
      :title="parent.form.sub_action == 'Add' ? $t('label.add') : $t('button.update')"
    >
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
            >
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
            >
            </v-select>
            <small
                v-if="parent.form.errors.has('platform_type_id')"
                v-text="parent.form.errors.get('platform_type_id')"
                class="text-danger"
            />
        </div>

        <div class="form-group">
          <span class="float-right">
            <b-button
              type="submit"
              :variant="parent.form.sub_action == 'Add' ? 'primary' : 'success'"
              :disabled="parent.btnloading"
              class="mb-2 mt-2 text-capitalize mt-1"
            >
              <span v-if="!parent.btnloading">
                <i class="fas fa-save"></i>
                {{ parent.form.sub_action == "Add" ? $t('button.save') : $t('button.save_change') }}
              </span>
              <b-spinner v-else small label="Small Spinner"></b-spinner>
            </b-button>
            <b-button
              variant="secondary"
              size="sm"
              @click="$bvModal.hide('platform_2-modal')"
            >{{$t('cancel')}}</b-button>
          </span>
        </div>
      </form>
    </b-modal>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
export default {
  props: ["parent"],

  data() {
    return {
      loading: true
    };
  },

  computed: {
    ...mapGetters({
      platformItemStatus: "platforms/get_platform_item_status"
    })
  },

  methods: {
    ...mapActions("platforms", ["post_platform_item"]),

    hide() {
      this.$emit("hide");
    },

    onSave() {
      // this.parent.btnloading = true;
      let params = {
        api_token: this.$user.api_token,
        action: "Add",
        brand_id: this.parent.form.brand_id,
        platform_type_id: this.parent.form.platform_type_id
      };

      this.post_platform_item(params)
        .then(resp => {
          this.parent.btnloading = false;
          this.$bvModal.hide("platform_2-modal");
          if (this.platformItemStatus) {
            this.parent.addedNewPlatformItemSuccessfully();
            let message = {
              create: this.$t('successfull_platform_type_entry'),
              update: this.$t( 'updated_message1' ) + this.$t('label.engagement') + ` ID: ${this.parent.form.id}` + this.$t( 'updated_message2' ),
              title: {
                create: this.$t( 'new_record_created' ),
                update: this.$t( 'record_updated' )
              }
            };
            this.parent.makeToast(
              this.parent.form.sub_action === "Add" ? "success" : "warning",
              this.parent.form.sub_action === "Add"
                ? message.title.create
                : message.title.update,
              this.parent.form.sub_action === "Add"
                ? message.create
                : message.update
            );

            this.parent.form.brand_id = "";
            this.parent.form.platform_id = "";
          }
        })
        .catch(error => {
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnloading = false;
        });
    }
  }
};
</script>
