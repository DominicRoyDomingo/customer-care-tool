<template>
  <div>
    <b-modal
    id="activity-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
      :title="
        parent.form.action == 'Add' ? $t('label.add') : $t('button.update')
      "
    >
      <v-app id="create__container">
        <v-card>
          <form
            class="form"
            @submit.prevent="onSave"
            @keyup="parent.form.errors.clear($event.target.name)"
          >
            <v-card-title class="title blue-grey lighten-4 text--secondary">
              <span v-if="parent.form.action == 'Add'">
                {{ $t("button.new") }} {{ $t("label.activity") }}
              </span>
              <span v-else>
                  {{$t('button.edit')}} {{ parent.form.default }}
                  <small
                        v-if="parent.form.conversation == true"
                        style="color:red"
                      >
                        (No {{ parent.form.language }} translation yet)</small
                      >
                </span>

              <v-spacer></v-spacer>
              <v-btn icon color="error lighten-2"  @click="close">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-card-title>
            <v-card-text class="modal__content">
                <v-container>
                  <v-row>
                    <v-col cols="9">
                    </v-col>
                    <v-col cols="3" v-show="parent.form.action === 'Update'">
                      <div>
                        <b-form-select
                          id="inline-form-custom-select-pref"
                          class="mb-2 mr-sm-2 mb-sm-0"
                          v-model="parent.language"
                          :options="parent.languageOptions"
                        />
                      </div>
                    </v-col>
                  </v-row>
                  <v-container>
                    <v-row>
                      <v-col cols="12" md="12" class="modal__input-container">
                      <b-form-group style="margin-bottom:1%">
                        <v-text-field
                          v-model="parent.form.activity"
                          :label="$t('label.activity')"
                          suffix="*"
                          class="modal__input"
                          autocomplete="off"
                          style="position:relative; width:100%;margin: 10px"
                        >
                        </v-text-field>
                        <small style="color:red; margin-top:-15px;position:absolute" v-if="activity_required"> {{ $t("label.activity") }} {{ $t('is_required')}}</small>
                      </b-form-group>
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
                  @click="close"
                >
                  {{ $t("cancel") }}
                </v-btn>
                <v-btn
                  color="success"
                  tile
                  type="submit"
                >
                  <div>
                    <div>
                      {{
                        parent.form.action == "Add"
                          ? $t("button.save")
                          : $t("button.save_change")
                      }}
                    </div>
                  </div>
                </v-btn>
              </v-card-actions>
          </form>
        </v-card>
      </v-app>
    </b-modal>
  </div>
  <!-- <div>
    <b-modal
      id="activity-modal"
      hide-footer
      size="md"
      :title="
        parent.form.action == 'Add' ? $t('label.add') : $t('button.update')
      "
    >
      <form
        class="form"
        @submit.prevent="onSave"
        @keyup="parent.form.errors.clear($event.target.name)"
      >
        <div class="form-group mb-4" v-show="parent.form.action === 'Update'">
          <div class="form-inline">
            <label class="mr-sm-2" for="inline-form-custom-select-pref">{{
              $t("label.language")
            }}</label>
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
          <label for="activity">
            {{ $t("label.activity") }}
            <strong class="text-danger">*</strong>
          </label>

          <input
            id="activity"
            type="text"
            name="activity"
            v-model="parent.form.activity"
            class="form-control"
            :placeholder="$t('label.required')"
            autocomplete="off"
            aria-describedby="job"
          />
          <small
            id="job"
            v-if="parent.form.errors.has('activity')"
            v-text="parent.form.errors.get('activity')"
            class="text-danger"
          />
        </div>

        <div class="form-group">
          <span class="float-right">
            <b-button
              type="submit"
              :variant="parent.form.action == 'Add' ? 'primary' : 'success'"
              :disabled="parent.btnloading"
              class="mb-2 mt-2 text-capitalize mt-1"
            >
              <span v-if="!parent.btnloading">
                <i class="fas fa-save"></i>
                {{
                  parent.form.action == "Add"
                    ? $t("button.save")
                    : $t("button.save_change")
                }}
              </span>
              <b-spinner v-else small label="Small Spinner"></b-spinner>
            </b-button>
            <b-button
              variant="secondary"
              @click="$bvModal.hide('activity-modal')"
              >{{ $t("cancel") }}</b-button
            >
          </span>
        </div>
      </form>
    </b-modal>
  </div> -->
</template>

<script>
import { mapActions, mapGetters } from "vuex";
export default {
  props: ["parent"],

  data() {
    return {
      loading: true,
      activity_required: false
    };
  },

  computed: {
    ...mapGetters({
      activityStatus: "actions/get_activity_status",
    }),
  },

  methods: {
    ...mapActions("actions", ["post_activity"]),
    onSave() {
      this.activity_required = false;
      if( this.parent.form.activity == '' ) {
        this.activity_required = true;
        return false;
      }
      let params = {
        api_token: this.$user.api_token,
        action: this.parent.form.action,
        activity: this.parent.form.activity,
        id: this.parent.form.id,
        locale: this.parent.language,
      };

      this.post_activity(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("activity-modal");
          if (this.activityStatus) {
            this.parent.loadItems();
            let message = {
              create: `${this.parent.form.activity}` + this.$t( 'created_message' ) + this.$t('label.activity'),
              update: this.$t( 'updated_message1' ) + this.$t('label.activity') + ` ID: ${this.parent.form.id}` + this.$t( 'updated_message2' ),
              title: {
                create: this.$t( 'new_record_created' ),
                update: this.$t( 'record_updated' )
              },
            };
            this.parent.makeToast(
              this.parent.form.action === "Add" ? "success" : "warning",
              this.parent.form.action === "Add"
                ? message.title.create
                : message.title.update,
              this.parent.form.action === "Add"
                ? message.create
                : message.update
            );

            this.parent.form.activity = "";
          }
        })
        .catch((error) => {
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnloading = false;
        });
    },
    close(){
      this.activity_required = false;
      this.$bvModal.hide("activity-modal");
    }
  },
};
</script>
