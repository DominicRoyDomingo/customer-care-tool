<template>
  <div>
    <b-modal
      id="engagement_2-modal"
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
        <div class="form-group mb-4" v-show="parent.form.sub_action === 'Update'">
          <div class="form-inline">
            <label class="mr-sm-2" for="inline-form-custom-select-pref">Language</label>
            <b-form-select
              id="inline-form-custom-select-pref"
              class="mb-2 mr-sm-2 mb-sm-0"
              v-model="parent.language"
              :options="parent.languageOptions"
            />
          </div>
          <hr />
        </div>

        <div class="form-group">
          <label for="engagement">
            {{$t('label.engagement')}}
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

          <br />
          <!-- brands data -->
          <label for="brands">
            {{ $t("brands_name") }}
          </label>
          <v-select
              @change="checkContactMatch()" 
              v-model="parent.form.engagement_brands" 
              label="brand_name"
              :reduce="(brand) => brand.id"
              :options="parent.brands"
              multiple
            >
              <template #list-header>
                  <li style="margin-left:20px;" class="mb-2">
                      <b-link href="#" @click="parent.modalAddNewBrand('add engagement form')">
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
              @click="$bvModal.hide('engagement_2-modal')"
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
      engageStatus: "actions/get_engagament_status"
    })
  },

  methods: {
    ...mapActions("actions", ["post_engagement"]),
    
    hide() {
      this.$emit('hide');
    },

    onSave() {
      // this.parent.btnloading = true;
      let params = {
        api_token: this.$user.api_token,
        action: "Add",
        engagement: this.parent.form.engagement,
        brands: this.parent.form.engagement_brands,
        locale: this.parent.language
      };

      this.post_engagement(params)
        .then(resp => {
          this.parent.btnloading = false;
          this.$bvModal.hide("engagement_2-modal");
          if (this.engageStatus) {
            this.parent.addedNewEngagementSuccessfully();
            let message = {
              create: `${this.parent.form.engagement}` + this.$t( 'created_message' ) + this.$t('label.engagement'),
              update: this.$t( 'updated_message1' ) + this.$t('label.contact_type') + ` ID: ${this.parent.form.id}` + this.$t( 'updated_message2' ),
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

            this.parent.form.engagement = "";
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
