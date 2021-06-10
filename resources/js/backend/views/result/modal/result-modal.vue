<template>
  <b-modal
    id="result-modal"
    hide-footer
    hide-header
    size="lg"
    @shown="onShow"
    @hidden="resetModal"
    no-close-on-backdrop
  >
  <v-app id="create__container">
    <v-card class="border-0">
      <v-card-title class="title blue-grey lighten-4 text-capitalize">
        <div>
          <span v-if="typeForm == 'add'">
            {{ $t('result.modal_title') }}
          </span>

          <span
            class="d-inline-block text-truncate"
            style="max-width: 700px; letter-spacing: normal"
            v-else
          >
            {{ $t("publishing_edit_label") + ' - ' + resultName}}
          </span>
        </div>

        <v-spacer></v-spacer>
        <v-btn icon color="error lighten-2" @click="onClose">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

     
        <v-row>
          <v-col sm="12" md="12" cols="12">
            <form class="form" @submit="onSubmit">
              <v-container>
              <div class="form-group">
                <div class="d-flex justify-content-between align-items-end">
                  <div></div>
                  <div v-if="typeForm == 'update'">
                    <select class="form-control" v-model="langsel">
                      <option value="en">ENGLISH</option>
                      <option value="it">ITALIAN</option>
                      <option value="de">GERMAN</option>
                      <option value="ph-fil">FILIPINO</option>
                      <option value="ph-bis">VISAYAN</option>
                    </select>
                  </div>
                </div>
                <v-text-field
                  :label="$t('publishing_type_name').toUpperCase()"
                  id="result_name"
                  type="result_name"
                  name="result_name"
                  v-model="result_name"
                  hide-details="auto"
                  autocomplete="off"
                  clearable
                >
                  <template v-slot:append>
                    <v-fade-transition hide-on-leave>
                        <v-progress-circular
                          v-if="is_loading"
                          size="24"
                          color="info"
                          indeterminate
                        ></v-progress-circular>
                    </v-fade-transition>
                  </template>

                </v-text-field>
                <div class="text-danger py-2" v-if="is_required">
                  Name is Required
                </div>
              </div>
              </v-container>
              <v-card-actions class="modal__footer blue-grey lighten-5 justify-content-end px-5">
                <div class="row justify-content-end">
                  
                  <div class="col-md-6 d-flex justify-content-end">

                      <v-btn color="error" text tile @click="onClose">
                        {{ $t("cancel") }}
                      </v-btn>
                      <v-btn
                        color="success"
                        tile
                        type="submit"
                        class="bg-success text-white"
                        v-if="typeForm == 'add'"
                      >
                        <div>
                          <v-progress-circular
                            size="20"
                            width="1"
                            color="white"
                            indeterminate
                            v-if="btnloading"
                          >
                          </v-progress-circular>
                          <span>{{ $t("button.save") }}</span>
                        </div>
                      </v-btn>
                      <v-btn
                        color="success"
                        tile
                        type="submit"
                        class="bg-success text-white"
                        v-else-if="typeForm == 'update'"
                      >
                        <div>
                          <v-progress-circular
                            size="20"
                            width="1"
                            color="white"
                            indeterminate
                            v-if="btnloading"
                          >
                          </v-progress-circular>
                          <span>{{ $t("button.save_change") }}</span>
                        </div>
                      </v-btn>

                  </div>
                </div>
              </v-card-actions>
            </form>
          </v-col>
        </v-row>      
    </v-card>
  </v-app>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import i18n from "../../../i18n.js";
import toast from "../../../helpers/toast.js";
import resultMixins from '../mixins/resultMixins.js';

let languange = i18n.locale;

export default {
  mixins: [toast, resultMixins],
  data() {
    return {
        result_name : '',
        title_name : '',
        is_required : false,
        btnloading : false,
        langsel : languange,
        is_loading : false
    };
  },
  props: {
      typeForm : {
        type : String,
        default : 'add'
      },
      rowIndex : {
        type : Number,
        default : 0
      },
      resultName : {
        type : String,
        default : ''
      },
      dataTable : {
        type : Function,
        default : function(){
            return;
        }
      }
  },
  watch: {
    course_item(val) {

    },
    langsel(val, old) {
        if(val != old){
            this.onUpdateLang()
        }
    },
  },
  methods: {
    onClose() {
      
      this.$bvModal.hide("result-modal");

    },
    onSubmit(e){
        e.preventDefault()
        if(this.typeForm == 'add'){
            this.add()
        }
        else if(this.typeForm == 'update'){
            this.update()
        }
    },
    add() {
        var obj = {
            postdata : this.result_name
        }
        
        this.btnloading = true
        this.add_result(obj).then((res) => {
            this.btnloading = false            
            if(!res.hasOwnProperty('action')){
                this.storeToast(res.message, " result")
                this.dataTable()
                this.$bvModal.hide("result-modal");
            }
            else if(res.action == 'duplicate'){
                this.errorToast(res.title, res.message);
            }
            else
            {

            }
        })
    },
    update() {

        var obj = {
          name : this.result_name,
          lang : this.langsel,
          id : this.rowIndex
        }

        this.btnloading = true
        this.update_result(obj).then((res) => {
            this.btnloading = false
            this.$bvModal.hide("result-modal");
            if(res){
                // this.updateToast(this.rowIndex, res.title + ' - ' + res.message)
                var records = res.title + ' - ' + res.message
                let message = `This information result ID: ${this.rowIndex} has been updated`;

                this.$bvToast.toast(message, {
                  title: this.$t("record_updated"),
                  variant: 'warning',
                  solid: true,
                });

                this.dataTable()           
            }
        })

    },
    onUpdateLang: function () {
        var obj = {
            id : this.rowIndex,
            lang : this.langsel
        }

        this.single_result(obj).then((res) => {            
            if(res){
                this.result_name = res['name']
            }
        })
    },
    onShow(){
      if(this.typeForm == 'update'){
          var obj = {
              id : this.rowIndex
          }

          this.is_loading = true
          this.single_result(obj).then((res) => {
              this.is_loading = false
              if(res){
                  this.title_name = res['name']
                  this.result_name = res['name']
              }
          })
      }    
    },
    resetModal() {
        this.result_name = ''
    },
  },
};
</script>
