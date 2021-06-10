<template>
  <b-modal id="calendar-modal" @hide="hide" hide-footer hide-header size="md">
    <v-app id="brand__container">
      <v-card>
        <v-form ref="form" v-model="valid" lazy-validation>
              <v-card-title class="title blue-grey lighten-4 text--secondary">
                <span>
                  {{$t('calendar_notes_type_add_new_button')}}
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
                        <v-text-field
                          v-model="form.name"
                          :rules="[(v) => !!v || $t('label.name_empty')]"
                          :label="$t('label.name')"
                          suffix="*"
                          class="modal__input"
                          autocomplete="off"
                          required
                        >
                        </v-text-field>
                        <small style="color:red; margin-top:-15px;position:absolute" v-if="name_required"> {{ $t("label.name") }} {{ $t('is_required')}}</small>
                        <div class="div_background" @click="chooseColor" v-bind:style="{ backgroundColor: color_background}"></div>
                        <v-text-field
                          v-model="form.color_hexcode"
                          :rules="[(v) => !!v || $t('label.hexcode_empty')]"
                          :label="$t('label.hexcode')"
                          suffix="*"
                          class="modal__input"
                          autocomplete="off"
                          required
                          @click="chooseColor"
                          style="width: 65%"
                        >
                        </v-text-field>
                        <small style="color:red" v-if="color_hexcode_required"> {{ $t("label.hexcode") }} {{ $t('is_required')}}</small>
                          <!-- <b-form-group>
                            <label for="name">
                              {{ $t("brands_name") }}
                            </label>
                            <v-select
                              id="brands_name"
                              name="brands_name"
                              label="name"
                              v-model="form.brand"
                              :options="$this.brands"
                              chips
                              :reduce="(brand) => brand.id"
                              multiple
                              outlined
                            >
                              <template #list-header>
                                <li style="margin-left:20px;" class="mb-2">
                                  <b-link href="#" @click="$this.openBrandModal">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                    {{ $t("label.new_brand") }}
                                  </b-link>
                                </li>
                              </template>
                            </v-select>
                        </b-form-group> -->
                      </v-col>
                    </v-row>
                  </v-container>
                </v-container>
              </v-card-text>
              <v-divider style="margin-bottom: 0"></v-divider>
                <v-card-actions class="modal__footer blue-grey lighten-5">
                  <a :href="`calendar-notes-type`" class="view-type-notes" style="float:left">{{ $t("view_calendar_notes_type_link") }}</a>
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
                      <div>
                       {{ $t("button.save") }}
                      </div>
                  </v-btn>
                </v-card-actions>
            </v-form>
      </v-card>
    </v-app>

      <b-modal
        id="create-select-color-calendar-modal"
        hide-footer
        hide-header
        size="sm"
        no-close-on-backdrop
      >
        <v-app id="create__container">
          <v-card>
            <v-form ref="form" v-model="valid" lazy-validation>
              <v-card-title class="title blue-grey lighten-4 text--secondary">
                <span>
                  Choose Color
                </span>
                <v-spacer></v-spacer>
                <v-btn
                  icon
                  color="error lighten-2"
                  @click="modalSelectColorCancel"
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-card-title>
              <v-card-text class="modal__content">
                <v-container>
                    <v-color-picker
                      dot-size="11"
                      hide-inputs
                      hide-mode-switch
                      mode="hexa"
                      swatches-max-height="157"
                      v-model="color"
                    ></v-color-picker>  
                </v-container>
              </v-card-text>
              <v-divider style="margin-bottom: 0"></v-divider>
                <v-card-actions class="modal__footer blue-grey lighten-5">
                  <v-spacer></v-spacer>
                  <v-btn
                    color="error"
                    text
                    tile
                    @click="modalSelectColorCancel"
                  >
                    {{ $t("cancel") }}
                  </v-btn>
                  <v-btn
                    color="success"
                    tile
                    @click="modalSelectColorClose"
                  >
                  <div v-if="this.button.loading" class="text-center">
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
                        Okay
                      </div>
                    </div>
                  </v-btn>
                </v-card-actions>
            </v-form>
          </v-card>
        </v-app>
      </b-modal>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import toast from "./../../../helpers/toast.js";
import Form from "./../../../shared/form.js";

export default {
  props: ["parent"],
  mixins: [toast],
  data() {
    return {
      file: null,
      loading: true,
      valid: true,
        button:{
            loading: false
        },
      form: new Form({

        id: "",

        name: "",

        color_hexcode: "",

        language: this.$i18n.locale,

        action: 'Add',



      }),
      color: '#673AB7',
      color_background: '#673AB7',
      
      color_hexcode_required: false,

      name_required: false
    };
  },

  watch : {
    color:function(val) {
      this.form.color_hexcode = val;
      this.color_background = val;
    },
  },

  computed: {
    ...mapGetters("calendar_notes_type", {
      responseStatus: "get_response_status",
    }),
  },

  methods: {
    ...mapActions("calendar_notes_type", ["post_calendar_notes_type", "add_calendar_notes_type", "update_calendar_notes_type"]),
    onGetFile(event) {
      this.file = event.target.files[0];
    },
    hide() {
      this.$emit("hide");
    },
    modalClose(done) {
      this.$bvModal.hide("calendar-modal");
    },
    chooseColor() {
      this.$bvModal.show("create-select-color-calendar-modal");
    },
    modalSelectColorClose() {
      // this.color = this.form.hexcode;
      this.$bvModal.hide("create-select-color-calendar-modal");
    },

    modalSelectColorCancel() {
      this.form.hexcode = "";
      this.$bvModal.hide("create-select-color-calendar-modal");
    },
    onSubmit() {
      let vm = this;
      this.button.loading = true;

      this.name_required = false;
      this.color_hexcode_required = false;
      
      if( this.form.name == '' || this.form.color_hexcode == '' ) {
        this.name_required = true;
        this.color_hexcode_required = true;
        return false;
      }

      let formData = new FormData();

      formData.append("id", this.form.id);
      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      formData.append("action", this.form.action);
      formData.append("name", this.form.name);
      formData.append("color_hexcode", this.form.color_hexcode);

      this.post_calendar_notes_type(formData)
        .then((resp) => {
        this.button.loading = false; 
          if( resp.data.duplicate == true ) {
            this.makeToast(
              "danger",
              "DUPLICATE",
              resp.data.name + this.$t("calendar_notes_type_exist")
            );
            return false;
          }
  
          this.$bvModal.hide("calendar-modal");

            let message = {
              create:
                `${this.form.name}` +
                this.$t("created_message") +
                this.$t("calendar_type_list"),
              title: {
                create: this.$t("new_record_created"),
              },
            };

            this.makeToast(
              "success",
              message.title.create,
              message.create
            );

            // this.form.reset();
            this.$emit("loadTable");
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

<style scoped>

.div_color{
  cursor: pointer;
}
.div_background{
  float:right; 
  width: 30%; 
  height: 35%;
}
</style>
