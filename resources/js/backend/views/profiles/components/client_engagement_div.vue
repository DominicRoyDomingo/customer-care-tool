<template>
  <div class="client-engagement-div" v-if="root_parent.form.client_engagements[index]">
    <div class="row no-gutters" v-if="is_deletable">
      <div class="col-md-11 text-left">
        <hr />
      </div>
      <div class="col-md-1 text-right">
        <v-btn
          fab
          dark
          small
          color="error"
          @click.prevent="deleteClientEngagement"
        >
          <v-icon dark>mdi-close</v-icon>
        </v-btn>
      </div>
    </div>
    <div class="form-group">
      <label :for="'engagement_' + index">
        {{ $t("label.engagement") }}
        <strong class="text-danger">*</strong>
      </label>
      <v-select
        :id="'engagement_' + index"
        :name="'engagement_' + index"
        label="client_engagement_with_brand_names"
        @input="engagementChanged"
        v-model="root_parent.form.client_engagements[index].engagement"
        :options="root_parent.engagements"
      >
        <template #list-header>
          <li style="margin-left:20px;" class="mb-2">
            <b-link href="#" @click="root_parent.modalAddNewEngagement">
              <i class="fa fa-plus" aria-hidden="true"></i>
              {{ $t("label.new_engagement") }}
            </b-link>
          </li>
        </template>
      </v-select>
      <small
        v-if="
          root_parent.form.errors.has(
            'client_engagements.' + index + '.engagement_id'
          )
        "
        v-text="
          root_parent.form.errors.get(
            'client_engagements.' + index + '.engagement_id'
          )
        "
        class="text-danger"
      />
    </div>

    <div class="form-group">
      <label :for="'engagement_date_' + index">
        {{ $t("label.engagement_date") }}
        <strong class="text-danger">*</strong>
      </label>
      <datepicker
        :id="'engagement_date_' + index"
        input-class="form-control bg-white"
        v-model="root_parent.form.client_engagements[index].engagement_date"
        @input="changeEngagementDate"
      ></datepicker>
      <small
        v-if="root_parent.form.errors.has('engagement_date')"
        v-text="root_parent.form.errors.get('engagement_date')"
        class="text-danger"
      />
    </div>

    <div
      class="form-group"
      v-show="root_parent.form.client_engagements[index].engagement_id != null"
    >
      <label for="platform_items">
        {{ $t("label.platform") }}
        <strong class="text-danger">*</strong>
      </label>
      <v-select
        id="platform_id"
        name="platform_id"
        label="platform_item_name"
        @change="root_parent.form.errors.clear('engagement')"
        :reduce="(engagement) => engagement.id"
        v-model="root_parent.form.client_engagements[index].platform_id"
        :options="active_platforms"
      >
        <template #list-header>
          <li style="margin-left:20px;" class="mb-2">
            <b-link
              href="#"
              @click.prevent="root_parent.modalAddNewPlatformItem"
            >
              <i class="fa fa-plus" aria-hidden="true"></i>
              {{ $t("label.new_platform") }}
            </b-link>
          </li>
        </template>
      </v-select>
      <small
        v-if="root_parent.form.errors.has('platform_id')"
        v-text="root_parent.form.errors.get('platform_id')"
        class="text-danger"
      />
    </div>

    <div class="form-group">
      <label :for="'details_' + index">
        {{ $t("label.details") }}
      </label>
      <b-form-textarea
        :id="'details_' + index"
        rows="3"
        max-rows="6"
        v-model="root_parent.form.client_engagements[index].details"
      ></b-form-textarea>
      <small
        v-if="root_parent.form.errors.has('details')"
        v-text="root_parent.form.errors.get('details')"
        class="text-danger"
      />
    </div>
    <br />
  </div>
</template>

<style scoped></style>

<script>
import { mapActions, mapGetters } from "vuex";
import axios from "axios";

import Datepicker from "vuejs-datepicker";

export default {
  name: "client_engagement_div",
  props: ["root_parent", "parent", "index", "is_deletable"],
  components: {
    Datepicker,
  },
  data() {
    return {
      engagement_date: "",
      details: "",
    };
  },
  computed: {
    active_platforms() {
      let vm = this;
      let pool = [];
      let engagement = null;
      let brands = null;

      //find engagement first
      if (
        vm.root_parent.form.client_engagements[vm.index].engagement_id != null
      ) {
        vm.root_parent.engagements.forEach(function(engagement_instance) {
          if (
            engagement_instance.id ==
            vm.root_parent.form.client_engagements[vm.index].engagement_id
          ) {
            engagement = engagement_instance;
            brands = JSON.parse(engagement.brands);
          }
        });
      }
      console.log(vm.root_parent.platform_items)
      //then pick out platforms with matching brand
      if (brands != null) {
        vm.root_parent.platform_items.forEach(function(platform, index) {
          if (brands.includes(platform.brand)) {
            pool.push(platform);
          }
        });
      }

      return pool;
    },
  },
  methods: {
    engagementChanged() {
      let vm = this;
      vm.resetPlatform();
      let engagement =
        vm.root_parent.form.client_engagements[vm.index].engagement;
      if (engagement == null) {
        vm.root_parent.form.client_engagements[vm.index].engagement_id = null;
      } else {
        let brands = engagement.brands;

        if (engagement.brands == null || engagement.brands.length == 0) {
          const html = this.$createElement;

          let error_message = vm.$t(
            "errors.the_engagement_that_you_selected_has_no_brand"
          );
          error_message = error_message.replace(
            /%engagement_name%/g,
            engagement.client_engagement_with_brand_names
          );

          const vNodesMsg = html("p", { class: ["text-center", "mb-0"] }, [
            error_message,
            html(
              "b-link",
              {
                props: { href: "/admin/actions/engagements", target: "_blank" },
              },
              " " + vm.$t("errors.here")
            ),
          ]);

          let title_text = vm.$t("errors.error");
          // Create the title
          const vNodesTitle = html(
            "div",
            {
              class: ["d-flex", "flex-grow-1", "align-items-baseline", "mr-2"],
            },
            [html("strong", { class: "mr-2" }, title_text.toUpperCase())]
          );

          vm.root_parent.makeNodeToast("danger", vNodesTitle, vNodesMsg);

          vm.root_parent.form.client_engagements[vm.index].engagement = null;
          vm.root_parent.form.client_engagements[vm.index].engagement_id = null;
        } else {
          vm.root_parent.form.client_engagements[vm.index].engagement_id =
            engagement.id;
        }
      }
    },
    resetPlatform() {
      let vm = this;
      vm.root_parent.form.client_engagements[vm.index].platform_id = null;
    },
    deleteClientEngagement: function() {
      let vm = this;

      let request = {
        index: this.index,
      };

      $.confirm({
        title: this.$t("confirm_action"),
        content: vm.$t("label.delete_client_engagement_confirmation"),
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
        vm.root_parent.form.client_engagements[vm.index].engagement_date;
      var date_object = new Date(engagement_date);
      vm.root_parent.form.client_engagements[
        vm.index
      ].engagement_date = formatDateToYMD(date_object);
    },
  },
};
</script>
