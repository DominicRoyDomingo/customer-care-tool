<template>
  <b-modal
    id="linkbrand-modal"
    @shown="onShow"
    @hidden="resetAll"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >

  <div class="create">
     <v-app id="create__container">
      <v-card>
        <v-card-title class="title blue-grey lighten-4 text-capitalize">
          <span
          v-html="$t('label.link_to_brand') + ' [' + parent.itemsSelected.question_name + ']'"
          >
          </span>
           <v-spacer></v-spacer>
          <v-btn icon color="error lighten-2" @click="modalClose('linkbrand-modal')">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        </v-card-title>
        <form ref="form" v-model="valid" @submit.prevent="onSubmit" lazy-validation>
           <v-container>
            <v-container>
              <v-row>
                <v-col cols="12" md="12" class="modal__input-container">
                  <b-form-group>
                    <label for="form-choices">
                      {{ $t("label.brands") }}
                    </label>
                    <v-select
                      id="form-choices"
                      :options="brands"
                      v-model="form.brands"
                      name="form-type"
                      label="brand_name"
                      multiple
                    >
                      <template #list-header>
                        <li style="margin-left: 20px" class="mb-2">
                          <b-link
                            v-b-tooltip.hover
                            href="javascript:;"
                            @click="on_add_brand"
                          >
                            <b-spinner
                              v-if="isAddBrandLoading"
                              small
                              label="Small Spinner"
                              class="text-muted"
                            />
                            <i class="fa fa-plus" v-else aria-hidden="true"></i>
                            {{ $t("label.new") }} {{ $t("label.brand") }}
                          </b-link>
                        </li>
                      </template>

                      <template #option="{ on_select_brand_name }">
                        <span v-html="on_select_brand_name" />
                      </template>
                    </v-select>
                  </b-form-group>
                </v-col>
              </v-row>
            </v-container>
          </v-container>
          <v-divider style="margin-bottom: 0"></v-divider>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-spacer></v-spacer>
            <div class="float-right mr-2">
              <v-btn
                color="error"
                text
                tile
                @click="modalClose('linkbrand-modal')"
              >
                {{ $t("cancel") }}
              </v-btn>
              <v-btn
                color="success"
                tile
                type="submit"
              >
                <div v-if="modal.button.loading" class="text-center">
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
                    {{ $t("save_button") }}
                  </div>
                </div>
              </v-btn>
            </div>
          </v-card-actions>
        </form>
      </v-card>
    </v-app>
  </div>
  <createBrand
      :parent="this"
      @loadTable="parent.loadBrands"
      @done-success="create_success"
    ></createBrand>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import toast from "./../../../../helpers/toast.js";
import questionMixin from '../mixins/questionMixin.js'
import i18n from '../../../../i18n.js';
import CreateBrand from "./../../../workforce_management/modals/brand.modal.vue";

import { fetchList, softDelete } from "./../../../../api/brands.js";

let lang = i18n.locale;


export default {

  mixins: [toast, questionMixin],
  components: {
    CreateBrand,
  },
  data: function() {
    return {
        // brands: [],
        items: [],
        modal : {
          button : {
            loading : false
          }
        },
        form: {
          brands: []
        },

        formData: new FormData(),

        valid: true,
        isAddBrandLoading: false,
    };
  },
  props: ["parent"],
  methods: {
      ...mapActions("brand", [
        "get_brands",
      ]),

      ...mapActions("questions", [
        "storeQuestionBrand",
      ]),

      onClose(){
        this.$bvModal.hide("linkterm-modal");
      },
      resetAll(){
      },
      onShow(){
          this.getBrands()
          this.form.brands = this.parent.brands
      },
      _onSearch(){


      },
      _onCheck(selected){

          
      },
      initTable(){
          
      },
      searchTable(){
        
      },
      _checkTermSel(){

      },
      getBrands(){
        let params = {
          ...this.defaultParams(),
        };

        this.get_brands(params)
      },
      defaultParams() {
         return {
            api_token: this.$user.api_token,
            locale: this.$i18n.locale,
            sortbyLang: this.sortbyLangId,
         }
      },
      modalClose(modalId){
        this.$bvModal.hide(modalId);
      },
      onSubmit(){
        var vm = this;
        let formData = new FormData();

        formData.append("api_token", this.$user.api_token);
        formData.append("locale", this.$i18n.locale);

        formData.append("brand_ids", this.getBrandsIds);

        let params = {
          formData: formData,
          question_id: this.parent.questionId,
        }

        vm.storeQuestionBrand(params).then(function(res){

          let message = {
              create:
                `${vm.parent.questionName}` +
                vm.$t("successfully_link"),
              title: {
                create: vm.$t("create_publishing_link"),
              },
            };

          if (res.data && res.data.success) {
            vm.modalClose('linkbrand-modal');

            vm.makeToast(
              "success",
              message.title.create,
              message.create
            );
            vm.form.brands = []
            vm.parent.brands = [];

            vm.$emit("done-create");
          }
        })
      },
      create_success() {},
      on_add_brand() {
      this.isAddBrandLoading = true;
      // this.load_specialization_category();

      setTimeout(() => {
        this.isAddBrandLoading = false;
        // Open Modal Brand
        this.$bvModal.show("brand-modal");
      }, 700);
    },
  },
  computed: {
      ...mapGetters("brand", {
        brands: "brands",
        categories: "categories",
        responseStatus: "get_response_status",
      }),

      getBrandsIds(){
          // return this.form.brands;
          var brandIdsFinal = [];
          if (this.form.brands) {
            var brandsArrObj = this.form.brands;
            brandsArrObj.forEach(function(q){
              brandIdsFinal.push(q.id)
            });
          }

          return brandIdsFinal
      }
  }
};
</script>

<style>
.v-input--selection-controls__input{
  margin-top: -8px;
}
</style>
