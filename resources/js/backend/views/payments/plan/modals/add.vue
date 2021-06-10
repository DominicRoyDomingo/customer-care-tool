<template>
  <div class="create">
    <b-modal
      id="paymentplanmodal"
      width="40%"
      hide-footer
      hide-header
      no-close-on-backdrop
    >
    <v-app id="create__container">
      <form
      @submit.prevent="onSubmit"
      method="post"
      enctype="multipart/form-data"
    >
    <v-card class="border-0">
      <v-card-title class="title blue-grey lighten-4 text-capitalize">
        <span v-if="this.plan.type === 'edit'">{{this.$t("button.edit")+" "+this.plan.data.base_name}}</span>
        <span v-else v-html="`${$t('button.new')} Payment Plan`">

          
        </span>
        <v-spacer></v-spacer>
        <v-btn icon color="error lighten-2" @click="modalClose">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-card-text class="modal__content" style="">
        <v-container>
          <v-container v-show="this.plan.type === 'edit'">
            <v-row>
              <v-col class="modal__input-container">
                <div class="form-group">
                  <div class="form-inline justify-end">
                    <b-form-select
                      id="inline-form-custom-select-pref"
                      class="mr-sm-2 mb-sm-0"
                      v-model="language"
                      :options="this.languageOptions"
                    />
                  </div>
                </div>
              </v-col>
            </v-row>
          </v-container>

          <v-container>
            <div class="p-2 margin-top"> 
                <b-form-group>
                  <v-text-field
                    :label="$t('label.name')"
                    name="value"
                    v-model="form.name"
                    hide-details="auto"
                    class="mb-5"
                  />
                  <span v-if="errors[`form.name`]" class="red--text">The text entry is required.</span>

                  <span>Type Of Plan</span>
                    <v-select class="mb-2"
                      v-model="form.typeOfPlan"
                      :options="this.type_plan"
                      :placeholder="`${$t('select_placeholder')} Plan Type`"
                    >
                  </v-select>
                  <span v-show="errors[`form.typeOfPlan`]" class="red--text mb-2">This field is required.</span><br/>

                  <span>Status</span>
                  <v-select class="mb-2"
                    v-model="form.status"
                    :options="this.status"
                    :placeholder="`${$t('select_placeholder')} Status`"
                  >
                  </v-select>
                  <span v-if="errors[`form.status`]" class="red--text">The text entry is required.</span>

                  <v-text-field
                    :label="$t('label.price')"
                    name="value"
                    v-model="form.price"
                    hide-details="auto"
                    class="mb-5"
                  />
                  <span v-if="errors[`form.price`]" class="red--text">The text entry is required.</span>
                  
                  <v-text-field
                    :label="$t('label.description')"
                    name="value"
                    v-model="form.description"
                    hide-details="auto"
                    class="mb-5"
                  />
                  <span v-if="errors[`form.description`]" class="red--text">The text entry is required.</span>
                  
                  <v-textarea
                    outlined
                    label="Features"
                    v-model="form.features"
                  />
                </b-form-group>
            </div>
          </v-container>
        </v-container>
      </v-card-text>
       <v-divider style="margin-bottom: 0"></v-divider>
       <v-card-actions class="modal__footer blue-grey lighten-5 justify-content-between px-5">
        <v-spacer></v-spacer>
        <div class='d-flex'>
              <v-btn color="error" text tile @click="modalClose">
                {{$t('cancel')}}
              </v-btn>

              <v-btn
                 color="success" 
                 tile 
                 :disabled="this.plan.button.loading"
                 type='submit'
              >

              <div class="text-center"
                v-if="this.plan.button.loading"
              >
                  <v-progress-circular
                    size="20"
                    width="1"
                    color="white"
                    indeterminate
                  >
                  </v-progress-circular>
                </div>

                {{(this.plan.type === 'edit')? $t('button.save_change') : $t('button.save')}}           
              </v-btn>

        </div>
      </v-card-actions>
      </v-card>
    </form>
    </v-app>
    </b-modal>

  </div>
</template>

<script>
import {mapActions} from "vuex";
import PaymentPlanMixin from "./../mixins/PaymentPlanMixin";

export default {
  props: ["parent"],
  mixins: [PaymentPlanMixin],

  data: function () {
    return {
      plan: this.parent.paymentplan_modal,
      form:{
        id:"",
        name:"",
        typeOfPlan:"",
        status:"",
        price:"",
        description:"",
        features:"",
      },
      type_plan:['Bundle', 'Subscription'],
      status:['Available', 'Unavailable', 'Hidden'],
      errors:[],
    };
  },
  
  mounted(){
    // this.loadData().catch((errors) => {});
    this.loadFormValues();
  },  
  
  methods: {
    ...mapActions("payment_plan", [
      "post_paymentplan", 
    ]),

    loadFormValues(){
      if(this.plan.type === 'edit'){
        this.language = this.$i18n.locale,
        this.form = {
          id: this.plan.data.id,
          typeOfPlan:this.plan.data.type_plan,
          status:this.plan.data.status,
          price:this.plan.data.price,
        }
       
      }
    },

    // loadItemType() {
    //   this.loadData()
    // },
    
    modalClose(done) {
      this.form = {}
      this.plan.type="create";  
      this.parent.isPaymentPlanModalVisible = false,
      this.$bvModal.hide('paymentplanmodal')
    },

    onSubmit() {
      this.plan.button.loading = true;

      let params = {
         ...this.defaultParams(),
        form: this.form,
        type:this.plan.type,
        language: (this.language) ? this.language : this.$i18n.locale 
      };

      this.post_paymentplan(params)
        .then((resp) => {
          resp = resp.data;
          (this.plan.type === 'edit') ? this.$emit('done-update', params.form.id) : this.$emit('done-create', params.form.name)
          this.form = {}
          this.plan.type="create";  
          this.plan.button.loading = false;
          this.modalClose()
        })
        .catch((error) => {
          this.errors = error.response.data.errors;
          for (var key in this.errors) {
            if(key == "unique"){
               this.errorToast("Duplicate", this.errors[key]);
            }
          }
    
          this.plan.button.loading = false;
        });
    },
  },
};

</script>

<style>
.margin-top {
  margin-top: -20px !important;
}

.dialog-footer {
  margin-top: -20px !important;
}
</style>
