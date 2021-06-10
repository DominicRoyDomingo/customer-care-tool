<template>
  <div>
    <b-modal
      size="lg"
      :no-close-on-backdrop="parent.isSendEmail"
      hide-footer
      id="modal-recipient"
      :title="parent.modal.title"
    >
      <div class="pl-3 pr-3">
        <div class="row" v-if="parent.isSendEmail && parent.selectedItem">
          <div class="col-md-6"></div>
          <div class="col-md-6"></div>
          <div class="col-md-12">
            <label for>{{$t('label.campaign_detail')}}</label>
            <table class="table table-condensed table-striped">
              <tbody>
                <tr>
                  <th class="text-left" style="width:40%">{{$t('label.campaign_name')}}</th>
                  <td v-html="parent.selectedItem.campaign_name"></td>
                </tr>

                <tr>
                  <th class="text-left" style="width:40%">{{$t('label.brand_name')}}</th>
                  <td>
                    <v-select
                      id="brand"
                      name="brand"
                      label="name"
                      @input="parent.getBrandId"
                      v-model="parent.form.brand"
                      :options="parent.brands"
                    />
                  </td>
                </tr>
                <tr>
                  <th class="text-left">{{ $t('label.subject') }}</th>
                  <td>
                    <input class="form-control" v-model="parent.form.subject" type="text" />
                  </td>
                </tr>
                <tr>
                  <th class="text-left">Content</th>
                  <td>
                    <b-form-textarea
                      id="content"
                      v-model="parent.form.content"
                      :placeholder="$t('label.enter_something')"
                      rows="3"
                      max-rows="6"
                    ></b-form-textarea>
                  </td>
                </tr>
                <tr>
                  <th class="text-left">{{ $t('label.sender_name')}}</th>
                  <td>
                    <input class="form-control" v-model="parent.form.sender_name" type="text" />
                  </td>
                </tr>
                <tr>
                  <th class="text-left">{{ $t('label.sender_email')}}</th>
                  <td>
                    <input class="form-control" v-model="parent.form.sender_email" type="text" />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <hr v-show="parent.isSendEmail" />
        <div class="row">
          <div class="col-md-6" v-if="parent.isSendEmail">
            <h5 class="mt-5">{{$t('label.recipients')}}</h5>
          </div>
          <div class="col-md-6" v-if="parent.isSendEmail">
            <div class="form-group">
              <label for>{{ $t( 'label.add_recipients' ) }}</label>
              <v-select
                id="recipient"
                name="recipient"
                label="profile_name"
                @input="onAdd"
                v-model="parent.form.recipient"
                :options="parent.profileOptions"
              ></v-select>
            </div>
          </div>
          <div class="col-md-12">
            <table class="table table-condensed table-striped">
              <thead>
                <tr class="active">
                  <th class="text-left">{{ $t( 'label.email' )}}</th>
                  <th class="text-left">{{ $t( 'label.first_name' )}}</th>
                  <th class="text-left">{{ $t( 'label.surname' )}}</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="parent.recipients.length < 1 || parent.form.recipient === 'all'">
                  <td colspan="5" class="text-center">
                    <strong v-if="parent.form.recipient === 'all'">{{ $t('all_recipient') }}.</strong>
                    <i class="text-muted" v-else>{{ $t('no_recipient_available') }}.</i>
                  </td>
                </tr>
                <tr v-else v-for="(recipient, index) in parent.recipients" :key="index">
                  <td>
                    <b-link variant="link">{{recipient.profile.primary_email}}</b-link>
                  </td> 
                  <td>{{recipient.profile.firstname}}</td>
                  <td>{{recipient.profile.surname}}</td>
                  <td>
                    <b-link
                      variant="link"
                      v-b-tooltip.hover
                      :title="$t('remove_recipient')"
                      @click="removeRecipient(recipient, index)"
                    >
                      <i class="fa fa-times text-danger" aria-hidden="true"></i>
                    </b-link>
                  </td>
                </tr>
              </tbody>
            </table>
            <br />
          </div>
        </div>

        <div class="row" v-if="parent.isSendEmail && parent.selectedItem">
          <div class="col-md-12">
            <div class="form-group text-right">
              <b-button variant="success" @click="onSendEmail" :disabled="btnDisabled">
                <span v-if="parent.btnLoading">
                  <b-spinner small label="Small Spinner" type="grow"></b-spinner>Sending email...
                </span>
                <span v-else>
                  <i class="fa fa-paper-plane" aria-hidden="true"></i>
                  Send
                </span>
              </b-button>
              <b-button variant="secondary" @click="onCancel" size="sm">Cancel</b-button>
            </div>
          </div>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
  props: ["parent"],

  data() {
    return {};
  },

  computed: {
    btnDisabled() {
      if (this.parent.form.recipient === "all") {
        return false;
      }

      if (this.parent.recipients.length < 1 || this.parent.btnLoading) {
        return true;
      }

      return false;
    }
  },

  methods: {
    ...mapActions("campaigns", ["send_email", "remove_recipient"]),

    onAdd(value) {
      if (value.id !== "all") {
        if (this.parent.recipients.length > 0) {
          this.parent.recipients.forEach(ele => {
            if (ele.profile.id !== value.id) {
              this.parent.recipients.push({
                profile: value
              });
            }
          });
        } else {
          this.parent.recipients.push({
            profile: value
          });
        }
        this.parent.form.recipient = "";
      } else {
        this.parent.recipients = [];
        this.parent.form.recipient = "all";
      }
    },

    onSendEmail() {
      this.parent.btnLoading = true;

      // parameter
      let params = {
        id: this.parent.form.id,
        brand: this.parent.form.brand.id,
        content: this.parent.form.content,
        subject: this.parent.form.subject,
        sender_name: this.parent.form.sender_name,
        sender_email: this.parent.form.sender_email,
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        recipients:
          this.parent.form.recipient !== "all" ? this.parent.recipients : "all"
      };

      // console.log(params);
      // return;
      this.send_email(params)
        .then(resp => {
          this.parent.loadItems();
          this.parent.btnLoading = false;
          this.$bvModal.hide("modal-recipient");

          this.$notify({
            title: this.$t('email_sent'),
            message: this.$t('email_has_beensent'),
            type: "success",
            position: "bottom-right"
          });
          this.parent.form.reset();
          this.parent.recipients = [];
        })
        .catch(error => {
          this.parent.makeToast(`danger`, this.$t('system_message'), this.$t('server_error'));
          this.parent.btnLoading = false;
        });
    },

    removeRecipient(recipient, index) {
      if (!recipient.id) {
        this.parent.recipients.splice(index, 1);
        return false;
      }

      this.$bvModal
        .msgBoxConfirm(
          this.$t( "question_record_delete") + `${recipient.profile.firstname}` + this.$t( "from") +  this.$t( "label.recipient") + this.$t( "records"),
          {
            title: this.$t( "confirmation_record_delete"),
            okVariant: "danger",
            okTitle: this.$t( "yes"),
            cancelTitle: this.$t( "no"),
            hideHeaderClose: false,
            centered: true
          }
        )
        .then(value => {
          if (value) {
            let params = {
              api_token: this.$user.api_token,
              id: recipient.id
            };
            this.remove_recipient(params).then(resp => {
              this.parent.recipients.splice(index, 1);
            });
          }
        })
        .catch(err => {});
    },

    onCancel() {
      this.parent.loadItems();
      this.parent.form.reset();
      this.parent.recipients = [];
      this.$bvModal.hide("modal-recipient");
    }
  }
};
</script>

 