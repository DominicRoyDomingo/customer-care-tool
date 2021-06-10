<template>
  <div class="contact-div contact-data" v-if="root_parent.form.contacts[index]">
    <div class="row no-gutters" v-if="is_deletable">
      <div class="col-md-11 text-left">
        <hr />
      </div>
      <div class="col-md-1 text-right">
        <v-btn fab dark small color="error" @click.prevent="deleteContact">
          <v-icon dark>mdi-close</v-icon>
        </v-btn>
      </div>
    </div>

    <div class="form-group">
      <label for="contact_type">{{ $t("label.contact_type") }}</label>
      <v-select
        @change="checkContactMatch()"
        v-model="root_parent.form.contacts[index].contact_type_id"
        :id="'contact_type_for_contact_' + index"
        label="contact_type_name"
        :reduce="(contact_type) => contact_type.id"
        :options="root_parent.contact_types"
        :closeOnSelect="false"
      >
        <template #list-header>
          <li style="margin-left:20px;" class="mb-2">
            <b-link href="#" @click="root_parent.modalAddNewContactType">
              <i class="fa fa-plus" aria-hidden="true"></i>
              {{ $t("label.new_contact_type") }}
            </b-link>
          </li>
        </template>
      </v-select>
      <small
        v-if="
          root_parent.form.errors.has('contacts.' + index + '.contact_type_id')
        "
        v-text="
          root_parent.form.errors.get('contacts.' + index + '.contact_type_id')
        "
        class="text-danger"
      />
    </div>

    <div class="form-group">
      <label for="contact">{{ $t("label.contact_details") }}</label>
      <input
        type="text"
        @input="checkContactMatch()"
        v-model="root_parent.form.contacts[index].contact_info"
        class="form-control"
      />
      <small
        v-if="
          root_parent.form.errors.has('contacts.' + index + '.contact_info')
        "
        v-text="
          root_parent.form.errors.get('contacts.' + index + '.contact_info')
        "
        class="text-danger"
      />

      <div class="col-md-12" v-if="alert_state != 0">
        <p :class="'text-left ' + alerts[alert_state].text_class">
          <i :class="'fas fa ' + alerts[alert_state].icon"></i>
          {{ alerts[alert_state].text }}
          <u v-if="alert_state == 2">{{
            matched_contact.profile.profile_name
          }}</u>
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped></style>

<script>
import Datepicker from "vuejs-datepicker";

function default_matched_contact_state() {
  return {
    profile: {
      full_name: "",
    },
  };
}

export default {
  name: "contact_div",
  props: ["root_parent", "parent", "index", "is_deletable"],
  components: {
    Datepicker,
  },
  data() {
    return {
      date_requested: "",
      contacts: "",
      alerts: [
        {
          icon: "",
          text: "",
        },
        {
          icon: "fa-spin fa-spinner",
          text: "",
          text_class: "text-primary",
        },
        {
          icon: "fa-exclamation-triangle",
          text: "", //Match found
          text_class: "text-danger",
        },
        {
          icon: "fa-check",
          text: "",
          text_class: "text-success",
        },
      ],
      alert_state: 0,
      matched_contact: default_matched_contact_state(),
    };
  },
  mounted() {
    let vm = this;
    vm.alerts[1].text = vm.$t("alerts.checking_database_for_matches");
    vm.alerts[2].text = vm.$t("alerts.existing_contact_information"); //Match found
    vm.alerts[3].text = vm.$t(
      "alerts.no_profile_match_found_for_contact_information"
    );
  },
  methods: {
    deleteContact: function() {
      let vm = this;

      $.confirm({
        title: this.$t("confirm_action"),
        content: vm.$t("label.delete_contact_confirmation"),
        type: "red",
        typeAnimated: true,
        buttons: {
          tryAgain: {
            text: this.$t("confirm"),
            btnClass: "btn-red",
            action: function() {
              vm.root_parent.deleteContact(vm.index);
            },
          },
          cancel: function() {},
        },
      });
    },
    checkContactMatch() {
      let vm = this;
      let ctype = vm.root_parent.form.contacts[vm.index].contact_type_id;
      let cinfo = vm.root_parent.form.contacts[vm.index].contact_info;
      vm.alert_state = 0;
      if (
        ctype == "" ||
        cinfo == "" ||
        vm.root_parent.form.contacts[vm.index].id != ""
      )
        return;

      let params = {
        api_token: vm.$user.api_token,
        contact_type_id: ctype,
        contact_info: cinfo,
      };

      vm.alert_state = 1; //Loading state
      axios
        .get("/api/contact_matches", {
          params,
        })
        .then((resp) => {
          let data = resp.data;
          if (data.length > 0) {
            vm.alert_state = 2;
            vm.root_parent.contacts_have_matches = true;
            vm.matched_contact = data[0];
          } else {
            vm.alert_state = 3;
            vm.root_parent.contacts_have_matches = false;
            vm.matched_contact = default_matched_contact_state();
          }
        });
    },
  },
};
</script>
