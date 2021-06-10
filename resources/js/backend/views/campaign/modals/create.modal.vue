<template>
  <div>
    <b-modal
      size="xl"
      no-close-on-backdrop
      no-close-on-esc
      hide-footer
      id="modal-create-campaign"
      :title="parent.modal.title"
    >
      <form-wizard title color="#3498db" ref="wizard" subtitle>
        <tab-content :title="$t('label.campaigns')" :before-change="onCampaign">
          <div class="row d-flex justify-content-center mt-2">
            <div class="col-md-10">
              <div class="form-group mt-2">
                <label for="campaign">
                  {{ $t( 'label.campaign_name' )}}
                  <span class="text-danger">*</span>
                </label>
                <input
                  type="text"
                  name="campaign"
                  v-model="parent.form.campaign"
                  id="campaign"
                  class="form-control"
                  :placeholder="$t('label.required')"
                />
                <small
                  v-if="parent.form.errors.has('campaign')"
                  class="text-danger"
                  v-text="parent.form.errors.get('campaign')"
                />
              </div>
            </div>
          </div>
          <br />
        </tab-content>
        <tab-content :title="$t('label.email_information')" :before-change="onCampaignEmail">
          <br />
          <div class="row d-flex justify-content-center">
            <div class="col-md-5">
              <h5>{{ $t('label.email_form')}}:</h5>
            </div>
            <div class="col-md-5">
                <div class="form-group mt-2 mb-3">
                    <label for="template">
                    {{ $t('label.template')}}
                    <span class="text-danger">*</span>
                    </label>

                    <v-select
                    id="template_name"
                    name="template_name"
                    label="template_name"
                    v-model="parent.form.template"
                    :options="parent.email_templates"
                    />

                    <small
                    v-if="parent.form.errors.has('template')"
                    class="text-danger"
                    v-text="parent.form.errors.get('template')"
                    />
                </div>
            </div>
          </div>
          <div class="row d-flex justify-content-center" v-if="parent.form.template != null && parent.form.template != undefined && parent.form.template != ''">
            <div class="col-md-5">
              <div class="form-group">
                <label for="brand">
                  {{ $t('label.brand_name')}}
                  <span class="text-danger">*</span>
                </label>
                <v-select
                  id="brand"
                  name="brand"
                  label="name"
                  @input="parent.getBrandId"
                  v-model="parent.form.brand"
                  :options="parent.brands"
                />
                <small
                  v-if="parent.form.errors.has('brand')"
                  class="text-danger"
                  v-text="parent.form.errors.get('brand')"
                />
              </div>
              <div class="form-group" style="margin-top:20px;">
                <label for="subject">{{ $t('label.subject') }}</label>
                <input
                  type="text"
                  v-model="parent.form.subject"
                  name="subject"
                  id="subject"
                  class="form-control"
                  :placeholder="$t('label.required')"
                />
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group">
                <label for="sender">
                  {{ $t('label.sender_name')}}
                  <span class="text-danger">*</span>
                </label>
                <input
                  type="text"
                  name="sender"
                  id="sender"
                  v-model="parent.form.sender_name"
                  class="form-control"
                  :placeholder="$t('label.required')"
                />
                <small
                  v-if="parent.form.errors.has('sender')"
                  class="text-danger"
                  v-text="parent.form.errors.get('sender')"
                />
              </div>
              <div class="form-group">
                <label for="sender_email">
                  {{ $t('label.sender_email')}}
                  <span class="text-danger">*</span>
                </label>
                <b-form-input
                  id="sender_email"
                  name="sender_email"
                  v-model="parent.form.sender_email"
                  type="text"
                  :placeholder="$t('label.required')"
                ></b-form-input>
                <small
                  v-if="parent.form.errors.has('sender_email')"
                  class="text-danger"
                  v-text="parent.form.errors.get('sender_email')"
                />
              </div>
            </div>

            <div class="col-md-10" v-if="parent.showVariables">
                <hr />
                <h5>{{$t('label.variables')}}:</h5>
            </div>

            <div class="col-md-10" v-if="parent.showVariables">
                <div class="row">
                    <variableInput
                        v-for="(variable, index) in parent.form.template.variables"
                        v-bind:key="'variable_' + index"
                        :root_parent="parent"
                        :parent="vm"
                        :index="index"
                    ></variableInput>
                </div>
            </div>
            <div class="col-md-10">
              <hr />
              <h5>
                {{ $t('label.recipients')}}:
                <small v-if="parent.isSearchRecipient">
                  <b-spinner small label="Small Spinner" class="text-info ml-2"></b-spinner>
                </small>
              </h5>
              <div class="form-group">
                <v-select
                  id="recipient"
                  name="recipient"
                  label="full_name"
                  :disabled="parent.recipientDisabled"
                  @input="parent.addRecipient"
                  multiple
                  v-model="parent.form.recipient"
                  :options="parent.profileOptions"
                >
                  <i slot="spinner" class="icon icon-spinner"></i>
                </v-select>
              </div>
            </div>
          </div>
          <br />
        </tab-content>
        <tab-content :title="$t('content')">
          <div class="row d-flex justify-content-center mt-3">
            <div class="col-md-10">
              <h5>{{ $t('label.campaign')}} {{ $t('content')}}:</h5>
              <div class="form-group mt-3">
                <wysiwyg v-model="parent.form.content" @input="reEnableButtons" :placeholder="$t('label.text_here')" />
              </div>
            </div>
          </div>
          <br />
        </tab-content>
        <template slot="footer" slot-scope="props">
          <div class="row d-flex justify-content-center">
            <div class="col-md-10">
              <div class="wizard-footer-right">
                <el-button
                  type="primary"
                  v-if="!props.isLastStep"
                  @click.native="props.nextTab()"
                >Next</el-button>

                <div v-else>
                  <el-button
                    :disabled="button.save.disabled"
                    type="primary"
                    @click.native="onSubmit('save')"
                  >
                    <b-spinner v-if="button.save.disabled" small type="grow" label="Sending..."></b-spinner>
                    {{button.save.text}}
                  </el-button>
                  <el-button
                    type="success"
                    :disabled="!parent.form.recipient || button.email.disabled"
                    @click.native="onSubmit('send')"
                  >
                    <span v-if="button.email.disabled">
                      <b-spinner small type="grow" label="Sending..."></b-spinner>Sending...
                    </span>
                    <span v-else>{{button.email.text}}</span>
                  </el-button>
                  <el-button type="info" @click.native="onSubmit('cancel')">
                    <span>{{button.cancel.text}}</span>
                  </el-button>
                </div>
              </div>
            </div>
          </div>
        </template>
      </form-wizard>
    </b-modal>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import { FormWizard, TabContent } from "vue-form-wizard";
import variableInput from "./../components/variable_input.vue";
import "vue-form-wizard/dist/vue-form-wizard.min.css";

export default {
  props: ["parent"],

  components: {
    FormWizard,
    TabContent,
    variableInput
  },

  data() {
    return {
      tab: "first",
      vm: this,
      button: {
        email: {
          disabled: false,
          text: "Save & Send"
        },
        save: {
          disabled: false,
          text: "Save"
        },
        cancel: {
          disabled: false,
          text: "Cancel"
        }
      }
    };
  },

  mounted() {},

  computed: {
    ...mapGetters({
      campaign: "campaigns/campaign",
      status: "campaigns/campaign_status"
    })
  },

  methods: {
    ...mapActions("campaigns", ["post_campaign_email", "post_campaign"]),

    onCampaign() {
      if (this.parent.form.id) {
        return true;
      }

      let params = {
        api_token: this.$user.api_token,
        campaign: this.parent.form.campaign,
        locale: this.$i18n.locale,
        type: "create"
      };

      return this.post_campaign(params)
        .then(resp => {
          if (this.campaign) {
            this.parent.form.id = this.campaign.id;
            this.parent.isNextPage = true;
            this.parent.btnLoading = false;
          }
          this.parent.loadItems();
          this.parent.listbrands();
          return true;
        })
        .catch(error => {
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnLoading = false;
          return false;
        });
    },

    onCampaignEmail() {
      let formData = this.setDataList();
      const resp = axios.post("/api/campaigns/checkform/campaign_email", 
        formData
      );

      return resp
        .then(res => {
          console.log(res);
          return true;
        })
        .catch(error => {
          this.parent.form.errors.record(error.response.data.errors);
          return false;
        });
    },

    reEnableButtons(){
        this.button.save.disabled = true;
        this.button.email.disabled = true;
    },

    onSubmit(type) {
      let params = {};
      let vm = this;
      let formData = this.setDataList();

      switch (type) {
        case "save":
            this.button.save.disabled = true;
            formData.append("type", "save");
          break;

        case "send":
            this.button.email.disabled = true;
            formData.append("type", "send");
          break;

        case "cancel":
          this.toReset();
          this.$bvModal.hide("modal-create-campaign");

          break;
      }

      if(type == "send" || type=="save"){
            vm.logFormData(formData);
          
          this.post_campaign_email(formData).then(resp => {
            this.button.save.disabled = false;
            this.$bvModal.hide("modal-create-campaign");

                let alert_message = (type == 'save') ? 
                `${this.parent.form.campaign}` + this.$t('created_message') + ' ' + this.$t( 'label.campaign') :
                `${this.parent.form.campaign}` + this.$t( 'campaign_email_customer' );

            if (this.status) {
              this.parent.loadItems();
              this.parent.makeToast(
                "success",
                this.$t('new_record_created'),
                alert_message
              );

              this.toReset();
            }
          });
      }
    },

    toReset() {
      this.parent.form.reset();
      this.$refs.wizard.reset();
      this.parent.recipients = [];
      this.parent.loadItems();
    },

    setDataList() {
      
      let formData = new FormData();

      let template = this.parent.form.template;
      let vm = this;

      formData.append("id", this.parent.form.id);
      formData.append("campaign", this.parent.form.campaign.id);
      formData.append("api_token", this.$user.api_token);
      formData.append("recipient", JSON.stringify(this.parent.form.recipient));
      formData.append("subject", this.parent.form.subject);
      formData.append("content", this.parent.form.content);
      formData.append("sender_name", this.parent.form.sender_name);
      formData.append("sender_email", this.parent.form.sender_email);
      formData.append("brand", this.parent.form.brand.id);
      formData.append("template_id", template.id);
      formData.append("locale", this.$i18n.locale);

      let variables = [];

      template.variables.forEach(function(variable, index){
        formData.append(vm.parent.slugify(variable.variable_name), variable.value);
        variables.push(vm.parent.slugify(variable.variable_name))
      });

      formData.append("variables", JSON.stringify(variables));
    // Display the key/value pairs
      return formData;
    },
        
    logFormData(formData){
        console.log("formData");
        for (var pair of formData.entries()) {
            console.log(pair[0]+ ', ' + pair[1]); 
        }
    }
  },

};
</script>

<style></style>
