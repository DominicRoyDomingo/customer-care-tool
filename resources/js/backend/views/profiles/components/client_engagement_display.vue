<template>
  <div
    class="client-engagement-display"
    v-if="display_array[index] != undefined"
  >
    <div class="row" v-if="is_editable">
      <div class="col-md-9">
        <div class="body-2 text--secondary">
          {{ $t("label.engagement") }}:
          <span class="text--disabled font-weight-light">
            {{ display_array[index].engagement_name }}
          </span>
        </div>
      </div>
      <div class="col-md-3 text-right">
        <v-tooltip top>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              icon
              color="#f9dd9f"
              @click.prevent="parent.modalEditClientEngagement(index)"
              v-bind="attrs"
              v-on="on"
            >
              <v-icon>mdi-lead-pencil</v-icon>
            </v-btn>
          </template>
          <span>{{ $t("label.edit_client_engagement") }}</span>
        </v-tooltip>
      </div>
    </div>
    <div class="row" v-if="!is_editable">
      <div class="col-md-12">
        <div
          class="subheading font-weight-regular text--primary"
          v-html="
            $t('label.engagement') +
              ': ' +
              display_array[index].engagement_name
          "
        ></div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div
          class="subheading text--primary"
          v-html="
            $t('label.platform') +
              ': ' +
              display_array[index].platform_name
          "
        ></div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div
          class="subheading text--primary"
          v-html="
            $t('label.details') +
              ': ' +
              display_array[index].details_display
          "
        ></div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="caption text--disabled text-left">
          {{ $t("label.date_of_engagement")
          }}<i
            >:
            {{
              display_array[index].engagement_date_display
            }}</i
          >
        </div>
      </div>
      <div class="col-md-6">
        <div class="caption text--disabled text-right">
          {{ display_array[index].adder_name }},
          <i>
            {{ display_array[index].created_at_display }}
          </i>
        </div>
      </div>
    </div>
    <hr />
  </div>
</template>

<style scoped></style>

<script>
import { mapActions, mapGetters } from "vuex";
import axios from "axios";

import Datepicker from "vuejs-datepicker";

export default {
  name: "client_engagement_display",
  props: ["root_parent", "parent", "display_array", "index", "is_editable"],
  components: {
    Datepicker,
  },
  data() {
    return {
      engagement_date: "",
      details: "",
    };
  },
  computed: {},
  mounted() {},
  methods: {
    deleteClientEngagement: function() {
      let vm = this;
      let request = {
        index: this.index,
      };
      $.confirm({
        title: this.$t("confirm_action"),
        content: this.$t("label.delete_note_confirmation"),
        type: "red",
        typeAnimated: true,
        buttons: {
          tryAgain: {
            text: this.$t("confirm"),
            btnClass: "btn-red",
            action: function() {
              vm.root_parent.deleteClientEngagement(vm.index);
            },
          },
          cancel: function() {},
        },
      });
    },
    changeEngagementDate: function() {
      let vm = this;
      let engagement_date =
        vm.display_array[vm.index].engagement_date;
      var date_object = new Date(engagement_date);
      vm.display_array[
        vm.index
      ].engagement_date = formatDateToYMD(date_object);
    },
  },
};
</script>
