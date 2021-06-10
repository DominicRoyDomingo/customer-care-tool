<template>
  <div class="create">
      <b-modal
        id="calendar-notes-add-modal"
        hide-footer
        hide-header
        size="lg"
        no-close-on-backdrop
        :title="$t('calendar_notes_add_new_button')"
      >
        <v-app id="create__container">
          <v-card>
            <form ref="form" v-model="valid" @submit.prevent="onSubmit" lazy-validation>
              <v-card-title class="title blue-grey lighten-4 text--secondary">
                <span>
                  {{$t('calendar_notes_add_new_button')}}
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
              <v-card-text class="modal__content" style="">
                <v-container>
                  <v-container>
                    <v-row>
                      <v-container>

                      <b-form-group style="margin-bottom:1%">
                        <v-text-field
                          v-model="form.title"
                          :label="$t('label.title')"
                          suffix="*"
                          class="modal__input"
                          autocomplete="off"
                        >
                        </v-text-field>
                        <small style="color:red; margin-top:-15px;position:absolute" v-if="title_required"> {{ $t("label.title") }} {{ $t('is_required')}}</small>
                      </b-form-group>
                        <br>
                        <v-textarea 
                          v-model="form.content"
                          :label="$t('label.content')"
                        ></v-textarea>
                        <br>
                        <b-form-group style="margin-bottom:1%">
                          <label for="name">
                            {{ $t("calendar_notes_type_list") }} {{ $t("calendar_type") }} 
                          </label>
                          <v-select
                            id="name"
                            name="name"
                            label="name"
                            v-model="form.type"
                            :options="$this.calendar_notes_type"
                            chips
                            :reduce="(calendar_notes_type) => calendar_notes_type.id"
                            outlined
                          >
                            <template #list-header>
                              <li style="margin-left:20px;" class="mb-2">
                                <b-link href="#" @click="$this.openCalendarNotesTypeModal">
                                  <i class="fa fa-plus" aria-hidden="true"></i>
                                  {{ $t("new_calendar_notes_type_link") }}
                                </b-link>
                              </li>
                            </template>
                            <template v-slot:option="option">
                              {{ option.name }}
                              <small
                                v-if="option.convertion == true"
                                style="color:red"
                              >
                                (No {{ option.language }} translation yet)</small
                              >
                            </template>
                          </v-select>
                        <small style="color:red; position:absolute" v-if="type_required"> {{ $t("calendar_notes_type_list") }} {{ $t("calendar_type") }} {{ $t('is_required')}}</small>
                      </b-form-group>
                        <br>
                        <v-text-field
                          v-model="form.target_date_from"
                          :label="$t('label.target_date_from')"
                          type="date"
                          v-on:input="checkTargetDateFrom()"
                        >
                        </v-text-field>
                        <!-- <v-text-field
                          v-model="form.target_date_from"
                          :label="$t('label.target_date_from')"
                          type="date"
                        >
                        </v-text-field> -->
                         <!-- <label>
                          {{ $t('label.target_date_from') }}
                        </label>
                         <datepicker
                            input-class="form-control bg-white"
                            v-model="form.target_date_from"
                            v-on:input="checkTargetDateFrom()"
                          ></datepicker> -->
                        <br>
                        <v-text-field
                          v-model="form.target_date_to"
                          :label="$t('label.target_date_to')"
                          type="date"
                          v-on:input="checkTargetDateTo()"
                        >
                        </v-text-field>
                        <!-- <v-text-field
                          v-model="form.target_date_to"
                          :label="$t('label.target_date_to')"
                          type="date"
                        >
                        </v-text-field> -->
                         <!-- <label>
                          {{ $t('label.target_date_to') }}
                        </label>
                         <datepicker
                            input-class="form-control bg-white"
                            v-model="form.target_date_to"
                            :format="`MM/dd/yyyy`"
                          ></datepicker> -->
                        <small style="color:red;position:absolute" v-if="date_required"> {{ $t('table.target_date_from') }} {{ $t('errors.target_date')}}</small>
                        <br>
                          <label for="name">
                            {{ $t("label.target_location") }}
                          </label>

                          <el-row>
                            <el-col :span="24">
                              <v-select
                                v-model="form.target_location"
                                :options="$this.target_location"
                              >
                              </v-select>
                               <small style="color:red; margin-top:-15px;position:absolute" v-if="target_location_required"> {{ $t("label.target_location") }} {{ $t('is_required')}}</small>
                            </el-col>
                          </el-row>
                         <br>
                        <div v-if="isCountry">
                          <label for="name">
                            {{ $t("label.country") }}
                          </label>

                          <el-row>
                            <el-col :span="24">

                              <v-select
                                name="provider_type"
                                label="name"
                                v-model="form.country"
                                :options="$this.countries"
                                :reduce="(countrie) => countrie.id"
                                id="countries"
                                multiple
                              >
                              </v-select>
                            </el-col>
                          </el-row>
                        </div>
                         <br>
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
                            <small style="color:red; position:absolute" v-if="brand_required"> {{ $t("brands_name") }} {{ $t('is_required')}}</small>
                        </v-container>
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
                    type="submit"
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
            </form>
          </v-card>
          
        </v-app>
        
      </b-modal>

  </div>
  
</template>

<script>
// import Api from "./../../../shared/api.js";

import { mapActions, mapGetters } from "vuex";

import Datepicker from 'vuejs-datepicker';

import toast from "./../../../helpers/toast.js";

export default {

  mixins: [toast],

  props: ["$this"],

  components: {
    Datepicker
  },

  data: function() {
    return {

      isLoading: true,

      btnloading: false,

      filter: "",

      tableTotalRows: "",

      searched: "",

      currentPage: 1,

      perPage: 10,

      sortBy: 'name',

      sortDesc: false,

      modal: this.$this.modal.add,

      submitted: false,

      valid: true,

      modalId: "calendar-notes-add-modal",

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,

      title_required: false,

      type_required: false,

      target_location_required: false,

      brand_required: false,

      date_required: false,

      isCountry: false,

    };
  },
  watch: {
    'form.target_location': function( val ) {
      if( val === 'Selected Location' ||  val === 'Ausgewählter Ort' ||  val === 'Posizione selezionata' ||  val === 'Pinili nga Lokasyon'  ||  val === 'Napiling Lokasyon' ){
        this.isCountry = true;
      } else {
        this.isCountry = false;
        this.form.country = [];
      }
      // 
    }
  },

  methods: {
    ...mapActions("calendar_notes", ["post_calendar_notes", "add_calendar_notes", "update_calendar_notes"]),
    
    modalClose(done) {

      $(".error-provider").remove();

      this.$this.$bvModal.hide("calendar-notes-add-modal");

      this.keep_open = false;
    },

    checkTargetDateFrom() {
      let vm = this;
      let data_now = new Date().toISOString().substr(0, 10);
      let data_from = vm.form.target_date_from;
      if( data_now > data_from ) {
        $.confirm({
          title: vm.$t("date_selected_error_title"),
          content: vm.$t("date_selected_error_content"),
          type: "red",
          typeAnimated: true,
          columnClass: "medium",
          buttons: {
            yes: {
              text: vm.$t("yes"),
              btnClass:
                "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
              action: function(value) {
                if (value) {
                }
              },
            },
            no: {
              text: vm.$t("no"),
              btnClass:
                "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
              action: function() {
                vm.form.target_date_from = new Date().toISOString().substr(0, 10);
              },
            },
          },
        });
      }
    },

    checkTargetDateTo() {
      let vm = this;
      let data_now = new Date().toISOString().substr(0, 10);
      let data_to = this.form.target_date_to;
      if( data_now > data_to ) {
        $.confirm({
          title: vm.$t("date_selected_error_title"),
          content: vm.$t("date_selected_error_content"),
          type: "red",
          typeAnimated: true,
          columnClass: "medium",
          buttons: {
            yes: {
              text: vm.$t("yes"),
              btnClass:
                "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
              action: function(value) {
                if (value) {
                }
              },
            },
            no: {
              text: vm.$t("no"),
              btnClass:
                "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
              action: function() {
                vm.form.target_date_to = new Date().toISOString().substr(0, 10);
              },
            },
          },
        });
      }
    },

    closeBrandModal() {
      this.$bvModal.hide("brand-modal");

      if (this.form.actual_action == "Add") {
        this.modal.addItem.isVisible = true;
      } else {
        this.modal.edit.isVisible = true;
      }
    },
    
    onSubmit() {
      this.title_required = false;
      this.type_required = false;
      this.target_location_required = false;
      this.brand_required = false;
      this.date_required = false;

      if( this.form.title == '' ) {
        this.title_required = true;
        return false;
      }

      if( this.form.type == '' ) {
        this.type_required = true;
        return false;
      }
      
      let data_to = this.form.target_date_to;
      let data_from = this.form.target_date_from;
      if( data_from > data_to ) {
        this.date_required = true;
        return false;
      }

      if( this.form.target_location == 'Selected Location' && this.form.country.length == 0 ) {
        this.target_location_required = true;
        return false;
      }

      if( this.form.brand.length === 0 ) {
        this.brand_required = true;
        return false;
      }
      
      let formData = new FormData();

      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      formData.append("title", this.form.title);
      formData.append("content", this.form.content);
      formData.append("type", this.form.type);
      formData.append("target_date_from", this.form.target_date_from);
      formData.append("target_date_to", this.form.target_date_to);
      formData.append("target_location", ( this.form.target_location === 'All' || this.form.target_location === 'Tutte'  || this.form.target_location === 'Alles' ? 'All' : this.form.target_location ) );
      formData.append("brand", JSON.stringify( this.form.brand ) );
      formData.append("country", JSON.stringify( this.form.country ) );
      this.modal.button.loading = true;
      this.post_calendar_notes(formData)
        .then((resp) => {
          this.modal.button.loading = false;
          if( resp.data.duplicate == true ) {
            this.$this.makeToast(
              "danger",
              "DUPLICATE",
              resp.data.name + this.$t("calendar_notes_exist")
            );
            return false;
          }
          this.$bvModal.hide("calendar-notes-add-modal");

            let message = {
              create:
                `${this.form.title}` +
                this.$t("created_message") +
                this.$t("label.calendar_notes"),
              title: {
                create: this.$t("new_record_created"),
              },
            };

            this.$this.makeToast(
              "success",
              message.title.create,
              message.create
            );
            this.form.reset();
            this.$emit("loadTable");
        })
        .catch((error) => {
          let errors = error.response.data.errors;
          $(".error-provider").remove();

          $("#request_type, #affect_limit").removeAttr(
            "style"
          );
          for (let field of Object.keys(errors)) {
            
            let el = $(`#${field}`);
            el.attr(
              "style",
              "border-color: #ff3b3b; box-shadow: 0px 2px 2px -3px rgba(255, 59, 59, 0.2), 0px 5px 7px 1px rgba(255, 59, 59, 0.14), 0px 1px 8px 1px rgba(255, 59, 59, 0.12); #ff3b3b !important; margin-bottom: 5px"
            );

            el.after(
              $(
                '<span style="color: #ff3b3b;" class="error-provider">' +
                  errors[field][0] +
                  "</span>"
              )
            );
          }
        });
    },
    sortChanged(e) {
      this.sortBy = e.sortBy
      this.sortDesc = e.sortDesc
      // this.loadItems()
    },
  },

  computed: {
    ...mapGetters("calendar_notes", {
      calendar_notes: "calendar_notes",
      responseStatus: "get_response_status",
    }),
    
    ...mapGetters("brand", {
      brands: "brands",
    }),

    ...mapGetters("country", {
      countries: "countries",
    }),
  },
}
</script>

<style>
.v-input--selection-controls__input{
  margin-top: -8px;
}

.div_color{
  cursor: pointer;
}
.div_back{
  float:right; 
  width: 30%; 
  height: 13%;
}
.view-type-notes:hover {
  text-decoration:none;
}
</style>
