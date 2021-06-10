<template>
<div>
  <v-card>
    <v-app-bar
      dark
      color="secondary"
    >
      
      <v-btn icon @click="attemptCloseForm()">
        <v-icon>mdi-arrow-left</v-icon>
      </v-btn>

      <v-toolbar-title>{{ parent.modal.title }}<span v-if="step>1">: {{parent.form.campaign}}</span></v-toolbar-title>

      <v-spacer></v-spacer>

      <v-chip
        v-if="step <= maxStep"
        color="primary"
        class="subheading white--text"
        size="24"
        v-text="step + '/' + maxStep + ' ' + stepTexts[step - 1]"
      ></v-chip>
      
      <v-chip
        v-else-if="step == 5"
        color="primary"
        class="subheading white--text"
        size="24"
        v-text="$t('label.preview_template') + ': ' + preview_template_name"
      ></v-chip>
    </v-app-bar>
    <v-card-text>
      <v-container>
        <v-window v-model="step">
          <v-window-item :value="1">
            <v-form ref="dialogForm1" v-model="validSteps[0]">
              <v-text-field
                v-model="parent.form.campaign"
                :label="$t('label.campaign_name') + ' *'"
                :rules="rules.campaign"
                :hint="$t('label.required')"
                @input="errors.campaign = []"
                :error-messages="errors.campaign"
              >
              </v-text-field>
            </v-form>
          </v-window-item>

          <v-window-item :value="2">
            <v-sheet class="mx-auto" color="secondary lighten-4"
                    max-width="100%">
              <v-row no-gutters class="pa-5">
                <v-col cols="12">
                  <TemplatePicker :parent="vm" :root_parent="parent"
                        @select-template="selectTemplate"
                        @preview-template="previewTemplate"></TemplatePicker>
                </v-col>
              </v-row>
            </v-sheet>
            <v-row>
              <v-col cols="12">
                <v-card height="100%" width="100%">
                  <v-card-title>
                    <v-row>
                      <v-col cols="8">
                        {{ selected_template.template_name }}
                      </v-col>
                      <v-col cols="4" class="text-right">
                        <v-btn
                          text
                          small
                          color="primary"
                          v-if="selected_template.id != null"
                          @click="previewTemplate(selected_template)"
                        >
                          <v-icon>mdi-magnify</v-icon> {{ $t('button.preview') }}
                        </v-btn>
                      </v-col>
                    </v-row>
                  </v-card-title>
                  <v-card-subtitle>
                    {{ $t("label.variables") }}
                  </v-card-subtitle>
                  <v-card-text>
                    {{
                      selected_template.variables_display != ""
                        ? selected_template.variables_display
                        : $t("label.no_variables")
                    }}
                  </v-card-text>

                  <v-card-actions class="w-100 px-10"> </v-card-actions>
                </v-card>
              </v-col>
            </v-row>
          </v-window-item>

          <v-window-item :value="3">
            <v-form ref="dialogForm2" v-model="validSteps[2]">
              <v-row>
                <v-col cols="6">
                  <label for="brand">
                    {{ $t("label.brand_name") }}
                    <span class="text-danger">*</span>
                  </label>
                  <v-select
                    required
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
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    required
                    v-model="parent.form.sender_name"
                    :rules="rules.sender_name"
                    :label="$t('label.sender_name') + ' *'"
                    :hint="$t('label.required')"
                  ></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    required
                    v-model="parent.form.subject"
                    :rules="rules.subject"
                    :label="$t('label.subject') + ' *'"
                    :hint="$t('label.required')"
                  ></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    required
                    v-model="parent.form.sender_email"
                    :rules="rules.sender_email"
                    :label="$t('label.sender_email') + ' *'"
                    :hint="$t('label.required')"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" v-show="parent.form.brand != null">
                  <hr />
                  <h5>
                    {{ $t("label.recipients") }}:
                    <small v-if="parent.isSearchRecipient">
                      <b-spinner
                        small
                        label="Small Spinner"
                        class="text-info ml-2"
                      ></b-spinner>
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
                </v-col>
              </v-row>
              <v-row v-if="selected_template.variables.length > 0">
                <v-col cols="12">
                  <hr />
                  <h5>{{ $t("label.variables") }}:</h5>
                </v-col>
                <variableInput
                  v-bind:key="'variable_' + index"
                  v-for="(variable, index) in selected_template.variables"
                  :root_parent="parent"
                  :parent="vm"
                  :index="index"
                ></variableInput>
              </v-row>
            </v-form>
          </v-window-item>

          <v-window-item :value="4">
            <v-form ref="dialogForm3" v-model="validSteps[3]">
                {{ $t("label.campaign") + " " + $t("content") }}
              <div class="form-group mt-3">
                <wysiwyg
                  v-model="parent.form.content"
                  :placeholder="$t('label.text_here')"
                />
              </div>
            </v-form>
          </v-window-item>
        </v-window>
      </v-container>
    </v-card-text>
    <v-card-actions>
      <v-btn  v-if="step > 1 && parent.form.action == 'create'" text @click="step--">
        {{ $t('button.back') }}
      </v-btn>
      <v-btn  v-if="step > 2 && parent.form.action == 'send'" text @click="step--">
        {{ $t('button.back') }}
      </v-btn>
      <v-spacer></v-spacer>
      <v-btn
        v-if="step === maxStep"
        :loading="!buttons.save"
        :disabled="!buttons.save"
        color="primary"
        depressed
        @click="onSubmit('save')"
      >
        {{ $t('button.save') }}
      </v-btn>

      <!--
        <v-btn
          v-if="step === maxStep"
          :loading="!buttons.save_and_send"
          :disabled="!buttons.save_and_send"
          color="success"
          depressed
          @click="onSubmit('send')"
        >
          {{ $t('button.save_and_send') }}
        </v-btn>
      -->
       
      <v-btn
        v-else-if="step < maxStep && step != 1"
        :disabled="!validSteps[step - 1]"
        color="primary"
        depressed
        @click="step++"
      >
        {{ $t('button.next') }}
      </v-btn>
      <v-btn
        v-else-if="step == 1"
        :disabled="!validSteps[step - 1]"
        :loading="!buttons.create_campaign"
        color="primary"
        depressed
        @click="onCampaign"
      >
        {{ $t('button.next') }}
      </v-btn>
    </v-card-actions>
  </v-card>

  <v-dialog v-model="previewTemplateDialog" @click:outside="previewTemplateDialog = false" max-width="1200px">
    <v-card>
      <v-app-bar
        dark
        color="secondary"
      >
        
        <v-btn icon @click="previewTemplateDialog = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>

        <v-toolbar-title>{{ $t('label.previewing_template') + ': ' + preview_template_name }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-chip
          color="primary"
          class="subheading white--text"
          size="24"
          depressed
        >
          <v-icon>mdi-pencil</v-icon> {{  parent.form.campaign  }}
        </v-chip>
      </v-app-bar>
      <v-img :src="template_image" class="ma-5">
        <template v-slot:placeholder>
          <v-row
            class="fill-height ma-0"
            align="center"
            justify="center"
          >
            <v-progress-circular
              indeterminate
              color="grey lighten-5"
            ></v-progress-circular>
          </v-row>
        </template>
      </v-img>

      <v-card-actions>
        <v-btn text @click="previewTemplateDialog = false">
          {{ $t('button.close') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</div>
      
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import { validationMixin } from "vuelidate";
import { required, maxLength, email } from "vuelidate/lib/validators";
import TemplateCard from "../components/TemplateCard";
import TemplatePicker from "../components/TemplatePicker";
import variableInput from "./../components/variable_input.vue";

export default {
  name: "CampaignForm",
  props: ["parent"],
  components: {
    TemplateCard,
    variableInput,
    TemplatePicker,
  },
  mounted() {
    this.resetForms();
  },
  data() {
    return {
      vm: this,
      previewTemplateDialog: false,
      alert: false,
      alertText: "",
      alertColor: "primary",
      template_image: "",
      preview_template_name: "",

      step: 1,
      maxStep: 4,
      validSteps: [false, false, false, false],
      stepTexts: [
        this.$t("label.campaign"),
        this.$t("label.template"),
        this.$t("label.email_information"),
        this.$t("label.subject"),
      ],
      rules: {
        campaign: [
          (v) => !!v || this.$t("errors.this_field_is_required"),
          (v) =>
            (v && v.length > 2) ||
            this.$t("errors.the_minimum_characters_for_this_field_is") + "3",
        ],
        subject: [(v) => !!v || this.$t("errors.this_field_is_required")],
        sender_name: [(v) => !!v || this.$t("errors.this_field_is_required")],
        sender_email: [
          (v) => !!v || this.$t("errors.this_field_is_required"),
          (v) => /.+@.+\..+/.test(v) || this.$t("label.email") + " " + this.$t("errors.must_be_valid"),
        ],
      },
      buttons: {
        save: true,
        save_and_send: true,
        back: true,
        next: true,
        create_campaign: true,
      },
      errors: {
        campaign: [],
      },
    };
  },
  computed: {
    ...mapGetters({
      campaign: "campaigns/campaign",
      status: "campaigns/campaign_status",
    }),

    selected_template() {
      if (this.parent.form.template == null) {
        return {
          id: null,
          template_name: this.$t("label.none_selected"),
          variables_display: "Lorem, Ipsum, Dolor, Sit amet",
          description: "Lorem ipsum dolor sit amet",
          variables: [],
        };
      } else {
        return this.parent.form.template;
      }
    },
  },
  methods: {
    ...mapActions("campaigns", ["post_campaign_email", "post_campaign"]),

    attemptCloseForm(){
     let vm = this;

     $.confirm({
        title:
          vm.$t("confirm_action"),
        content: 
          vm.$t("label.are_you_sure_that_you_want_to_close_this_window") + " " + vm.$t("label.you_will_lose_all_your_progress_if_you_do_so"),
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
                vm.parent.currentWindow = 0;
              } else {
              }
            },
          },
          no: {
            text: vm.$t("no"),
            btnClass:
              "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
            action: function() {},
          },
        },
      });
    },

    onCampaign() {
      console.log("On Campaign");
      this.buttons.create_campaign = false;
      if (this.parent.form.id) {
        this.buttons.create_campaign = true;
        this.step++;
        return true;
      }

      let params = {
        api_token: this.$user.api_token,
        campaign: this.parent.form.campaign,
        locale: this.$i18n.locale,
        type: "create",
      };

      return this.post_campaign(params)
        .then((resp) => {
          if (this.campaign) {
            this.parent.form.id = this.campaign.id;
            this.buttons.create_campaign = true;
            this.step++;
          }
          this.parent.loadItems();
          this.parent.listbrands();
          return true;
        })
        .catch((error) => {
          this.parent.form.errors.record(error.response.data.errors);

          this.errors.campaign.push(error.response.data.errors.campaign[0]);
          this.buttons.create_campaign = true;
          return false;
        });
    },

    logFormData(formData) {
      console.log("formData");
      for (var pair of formData.entries()) {
        console.log(pair[0] + ", " + pair[1]);
      }
    },

    onCampaignEmail() {
      let formData = this.setDataList();
      const resp = axios.post(
        "/api/campaigns/checkform/campaign_email",
        formData
      );

      return resp
        .then((res) => {
          console.log(res);
          return true;
        })
        .catch((error) => {
          this.parent.form.errors.record(error.response.data.errors);
          return false;
        });
    },

    resetForms() {
      if (this.$refs.dialogForm1 != undefined) {
        this.$refs.dialogForm1.resetValidation();
      }

      if (this.$refs.dialogForm2 != undefined) {
        this.$refs.dialogForm2.resetValidation();
      }

      if (this.$refs.dialogForm3 != undefined) {
        this.$refs.dialogForm3.resetValidation();
      }

      this.step = 0;
      this.parent.resetCampaignData();
    },

    previewTemplate(selected_template) {
      console.log(selected_template);
      this.template_image = selected_template.preview;
      this.preview_template_name = selected_template.template_name;
      this.previewTemplateDialog = true;
    },

    selectTemplate(selected_template) {
      console.log(selected_template);
      this.parent.form.template = selected_template;
      this.validSteps[1] = true;
    },

    activeTemplate(id) {
      if (this.parent.form.template == null) {
        return false;
      }

      if (id == this.parent.form.template.id) {
        return true;
      }

      return false;
    },

    validateStep(step) {
      let validity = false;
      let vm = this;
      if (step == "Campaign") {
        if (vm.parent.form.campaign != "") {
          validity = true;
        }
      }

      return validity;
    },
    nextStep(currentStep) {
      let vm = this;
      if (vm.validateStep(currentStep)) {
        if (vm.step < vm.maxStep) {
          vm.step++;
        }
      }
    },

    setDataList() {
      let formData = new FormData();

      let template = this.parent.form.template;
      let vm = this;

      formData.append("id", this.parent.form.id);
      formData.append("campaign", this.parent.form.campaign.id);
      formData.append("campaign_name", this.parent.form.campaign);
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

      template.variables.forEach(function(variable, index) {
        formData.append(
          vm.parent.slugify(variable.variable_name),
          variable.value
        );
        variables.push(vm.parent.slugify(variable.variable_name));
      });

      formData.append("variables", JSON.stringify(variables));
      // Display the key/value pairs
      return formData;
    },

    onSubmit(type) {
      let params = {};
      let vm = this;
      let formData = this.setDataList();

      this.buttons.save = false;
      this.buttons.save_and_send = false;

      switch (type) {
        case "save":
          formData.append("type", "save");
          break;

        case "send":
          formData.append("type", "send");
          break;
      }

      if (type == "send" || type == "save") {
        vm.logFormData(formData);

        vm.post_campaign_email(formData)
          .then((resp) => {
            vm.buttons.save = true;
            vm.buttons.save_and_send = true;

            vm.parent.campaignDialog = false;
            let campaignEmail = resp.data;
            console.log(campaignEmail);

            let alert_message =
              type == "save"
                ? `${vm.parent.form.campaign}` +
                  vm.$t("created_message") +
                  " " +
                  vm.$t("label.campaign")
                : `${vm.parent.form.campaign}` +
                  vm.$t("campaign_email_customer");

            if (this.status) {
              vm.parent.loadItems();
              vm.parent.makeToast(
                "success",
                vm.$t("new_record_created"),
                alert_message
              );

              vm.parent.form.campaign_id = campaignEmail.id;

              vm.parent.viewEmail(campaignEmail, true);
              //vm.resetForms();
            }
          })
          .catch((error) => {
            console.log(error);
            vm.buttons.save = true;
            vm.buttons.save_and_send = true;
          });
      }
    },
  },
};
</script>

<style>
.v-slide-group__prev, .v-slide-group__next {
  display: flex !important;
}
</style>