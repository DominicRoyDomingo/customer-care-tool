<template>
  <div>
    <b-modal
      id="contact-type-modal"
      @hide="hide"
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
                {{$t('button.new')}} {{$t('label.contact_types')}}
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
              <v-btn icon color="error lighten-2" @click="$bvModal.hide('contact-type-modal')">
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
                          v-model="parent.form.type_name"
                          :label="$t('label.type_name')"
                          suffix="*"
                          class="modal__input"
                          autocomplete="off"
                          style="position:relative; width:100%;margin: 10px"
                        >
                        </v-text-field>
                        <small style="color:red; margin-top:-15px;position:absolute" v-if="type_name_required"> {{ $t("label.type_name") }} {{ $t('is_required')}}</small>
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
                  @click="$bvModal.hide('contact-type-modal')"
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
            <!-- <div class="form-group mb-4" v-show="parent.form.action === 'Update'">
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
              <label for="name">
                {{ $t("label.type_name") }}
                <strong class="text-danger">*</strong>
              </label>

              <input
                id="type_name"
                type="text"
                name="type_name"
                v-model="parent.form.type_name"
                class="form-control"
                :placeholder="$t('label.required')"
                autocomplete="off"
                aria-describedby="contact_type"
              />
              <small
                id="type_name"
                v-if="parent.form.errors.has('type_name')"
                v-text="parent.form.errors.get('type_name')"
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
                  size="sm"
                  @click="$bvModal.hide('contact-type-modal')"
                  >{{ $t("cancel") }}</b-button
                >
              </span>
            </div> -->
          </form>
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
      type_name_required: false
    };
  },

  computed: {
    ...mapGetters("contact_types", {
      responseStatus: "get_response_status",
    }),
  },

  methods: {
    ...mapActions("contact_types", ["post_contact_type"]),
    hide() {
      this.$emit("hide");
    },
    onSave() {
      this.parent.btnloading = true;
      this.type_name_required = false;
      if( this.parent.form.type_name === '' ) {
        this.type_name_required = true;
        return false;
      }
      let params = {
        api_token: this.$user.api_token,
        action: this.parent.form.action,
        type_name: this.parent.form.type_name,
        id: this.parent.form.id,
        locale: this.parent.language,
      };

      console.log(params);
      this.post_contact_type(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("contact-type-modal");
          console.log(this.responseStatus);
          if (this.responseStatus) {
            this.parent.loadItems();
            let message = {
              create:
                `${this.parent.form.type_name}` +
                this.$t("created_message") +
                this.$t("label.contact_type"),
              update:
                this.$t("updated_message1") +
                this.$t("label.contact_type") +
                ` ID: ${this.parent.form.id}` +
                this.$t("updated_message2"),
              title: {
                create: this.$t("new_record_created"),
                update: this.$t("record_updated"),
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
