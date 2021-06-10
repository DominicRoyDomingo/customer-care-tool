<template>
  <div>
    <b-modal
      size="xl"
      no-close-on-backdrop
      no-close-on-esc
      hide-footer
      id="modal-send-campaign"
      :title="parent.modal.title"
    >
      <div class="row d-flex justify-content-center mt-2">
        <div class="col-md-11">
              <form
                class="form-horizontal"
                @submit.prevent="onSubmit"
                @keyup="parent.form.errors.clear($event.target.name)"
                >
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group mt-2 mb-3">
                      <label for="campaign">
                        <strong>{{ $t('label.campaign_name')}}</strong>
                        <span class="text-danger">*</span>
                      </label>

                      <v-select
                        id="campaign"
                        name="campaign"
                        label="campaign_name"
                        v-if="parent.modal.name === 'send_email'"
                        v-model="parent.form.campaign"
                        :options="parent.campaigns"
                      />

                      <input
                        type="text"
                        name="campaign"
                        v-else
                        v-model="parent.form.campaign"
                        id="campaign"
                        :disabled="true"
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
                  <div class="col-md-6">
                    <div class="form-group mt-2 mb-3">
                      <label for="template">
                        <strong>{{ $t('label.template')}}</strong>
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
                <div v-if="parent.form.template != null && parent.form.template != undefined">
                    <div class="row d-flex justify-content-center">
                    <hr />
                    <div class="col-md-12">
                        <h5>{{$t('label.email_form')}}:</h5>
                    </div>
                    </div>
                    <div class="row d-flex justify-content-center mt-2">
                    <div class="col-md-6">
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
                    <div class="col-md-6">
                        <div class="form-group">
                        <label for="sender_name">
                            Sender
                            <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            name="sender_name"
                            id="sender_name"
                            v-model="parent.form.sender_name"
                            class="form-control"
                            :placeholder="$t('label.required')"
                        />
                        <small
                            v-if="parent.form.errors.has('sender_name')"
                            class="text-danger"
                            v-text="parent.form.errors.get('sender_name')"
                        />
                        </div>
                        <div class="form-group">
                        <label for="sender_email">
                            {{ $t('label.sender_email') }}
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
                    <div class="col-md-12" v-if="parent.showVariables">
                        <hr />
                        <h5>{{$t('label.variables')}}:</h5>
                    </div>
                    <div class="col-md-12" v-if="parent.showVariables">
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
                    <div class="col-md-12">
                        <hr />
                        <h5>
                        {{ $t('label.recipient')}}:
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

                        <small
                            v-if="parent.form.errors.has('recipient')"
                            class="text-danger"
                            v-text="parent.form.errors.get('recipient')"
                        />
                        </div>
                    </div>
                    </div>
                    <hr />
                    <div class="row d-flex justify-content-center mt-3">
                    <div class="col-md-12">
                        <h5>{{ $t('label.campaign')}} {{ $t('content')}}:</h5>
                        <div class="form-group mt-3">
                        <wysiwyg v-model="parent.form.content" :placeholder="$t('label.text_here')" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group float-right mt-3">
                        <!-- <b-button
                            variant="primary"
                            v-if="parent.modal.name === 'preview'"
                            :disabled="button.save.disabled"
                            @click="onSave"
                        >
                            <b-spinner small v-if="button.save.disabled" type="grow" class="mr-1"></b-spinner>Save
                        </b-button>-->

                        <b-button
                            type="submit"
                            variant="success"
                            :disabled="!parent.form.recipient || button.disabled"
                        >
                            <span v-if="button.disabled">
                            <b-spinner small type="grow" class="mr-1" label="Sending..."></b-spinner>Sending...
                            </span>
                            <span v-else>
                            <i class="fa fa-paper-plane" aria-hidden="true"></i> Send
                            </span>
                        </b-button>
                        <b-button size="sm" variant="secondary" @click="onCancel">Cancel</b-button>
                        </div>
                    </div>
                    </div>
                </div>
              </form>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import variableInput from "./../components/variable_input.vue";

export default {
  props: ["parent"],
  data() {
    return {
      vm: this,
      tab: "first",
      button: {
        save: {
          disabled: false
        },
        disabled: false
      }
    };
  },
    components: {
        variableInput
    },
  computed: {
    ...mapGetters({
      campaign: "campaigns/campaign",
      status: "campaigns/campaign_status"
    })
  },

  methods: {
    ...mapActions("campaigns", [
      "post_campaign_email",
      "post_campaign",
      "send_email"
    ]),

    onSubmit() {
      this.button.disabled = true;
      
      let formData = this.setDataList();

      formData.append("type", "send");
      
      this.send_email(formData)
        .then(resp => {
          this.button.disabled = false;
          this.$bvModal.hide("modal-send-campaign");
          if (this.status) {
            this.parent.loadItems();
            this.parent.makeToast(
              "success",
              this.$t('email_sent'),
              `${this.parent.form.campaign}` + this.$t('email_sent_customer')
            );
          }
        })
        .catch(error => {
          this.parent.form.errors.record(error.response.data.errors);
          this.button.disabled = false;
        });
    },

    onCancel() {
      this.button.disabled = false;
      this.parent.form.reset();
      this.$bvModal.hide("modal-send-campaign");
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
      console.log(formData);
      return formData;
    }
  }
};
</script>