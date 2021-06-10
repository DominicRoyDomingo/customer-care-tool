<style scoped>
.card-term .card-header {
  border-top-right-radius: 10px;
  border-top-left-radius: 10px;
}
</style>
<template>
  <div class="row">
    <div class="col-md-12">
      <h4 class="card-title text-uppercase mb-0">
        {{ $t("reports") }}
        <small class="text-muted text-capitalize">
          {{ $t("terms_specifications") }}
        </small>
      </h4>
    </div>
    <div class="col-md-3">
      <div class="card border-0 mb-0">
        <div
          class="card-header py-3 d-flex flex-row align-items-center justify-content-between"
        >
          <h6 class="m-0 font-weight-bold text-muted text-uppercase">
            {{ $t("filter_by") }} :
            <span class="font-weight-normal text-capitalize">
              Specifications &amp; {{ $t("date") }}
            </span>
          </h6>
        </div>
        <div class="card-body mb-2 bg-light">
          <form action="">
            <div class="form-group">
              <label>Specifications Options:</label>
              <b-form-select
                v-model="form.name"
                :options="specificationOptions"
                @change="on_select_option"
                class="mb-3"
              >
                <template #first>
                  <b-form-select-option :value="''" disabled>
                    -- Select Option --
                  </b-form-select-option>
                </template>
              </b-form-select>
            </div>

            <div class="form-group mb-5" v-if="formGroup.isShow">
              <div v-if="formGroup.isLoading">
                <b-spinner small label="Small Spinner"></b-spinner>
              </div>
              <span v-else>
                <label>{{ formGroup.title }}:</label>
                <v-select
                  id="specializations"
                  :options="selectItems"
                  v-model="form.specification"
                  @input="on_select_term"
                  name="specializations"
                  label="base_name"
                  multiple
                  required
                  placeholder="-- Required --"
                >
                  <template
                    v-if="form.name === 'term_types'"
                    #option="{ term_type_name }"
                  >
                    <span v-html="term_type_name" />
                  </template>

                  <template v-else #option="{ description_name }">
                    <span v-html="description_name" />
                  </template>
                </v-select>
              </span>
            </div>
            <div class="form-group">
              <label>Choose Date:</label>
              <div :id="`date-range-picker`">
                <DateRangePicker
                  :dateModel="dateModel"
                  :is_disabled="!form.name && !form.specification"
                  @date="on_get_date"
                />
              </div>

              <b-popover
                v-if="!form.name && !form.specification"
                variant="danger"
                :target="`date-range-picker`"
                triggers="hover focus"
                placement="top"
              >
                <p class="mb-0">Please select a Specifications Options</p>
              </b-popover>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-9">
      <div v-if="isFetching" class="p-2">
        <b-spinner small label="Loading..."> </b-spinner>
        {{ $t("fetching_data") }}...
      </div>
      <div class="card mb-0 border-0" v-if="items.length > 0">
        <div
          class="card-header py-3 d-flex flex-row align-items-center justify-content-between"
        >
          <h6 class="m-0 font-weight-bold text-muted text-capitalize">
            <span class="text-uppercase"> {{ $t("date_of_coverage") }} :</span>
            <span
              class="font-weight-normal"
              v-if="form.specification && Object.values(date).length > 0"
            >
              {{ $t("from") }}
              <span class="text-primary font-weight-bold">
                {{ date.startDate | toLocaleString }}
              </span>
              {{ $t("to") }}
              <span class="text-primary font-weight-bold">
                {{ date.endDate | toLocaleString }}
              </span>
            </span>
          </h6>

          <b-link
            href="javascript:;"
            @click="on_refresh"
            v-b-tooltip.hover
            title="Refresh"
          >
            <b-icon icon="arrow-repeat" class="font-xl" />
          </b-link>
        </div>

        <b-overlay
          id="overlay-background"
          :show="isRefresh"
          :variant="'light'"
          opacity="0.85"
          :blur="'1px'"
          rounded="sm"
        >
          <div class="card-body bg-light">
            <CardBox :items="items" :componentName="`term_spec`" />
            <div class="row">
              <div class="col-md-12" v-if="isRendered">
                <AreaChart
                  v-if="Object.values(date).length > 0"
                  :data="items"
                  :date="date"
                  :componentName="`term_spec`"
                />
              </div>
            </div>
          </div>
        </b-overlay>
      </div>
      <div
        class="alert bg-light"
        v-if="!isFetching && items.length === 0"
        role="alert"
      >
        <strong class="mb-0" v-if="showChart && items.length === 0">
          <i class="fa fa-times-circle text-danger" aria-hidden="true"></i>
          <i> {{ $t("no_record_found") }} !</i>
        </strong>
        <strong class="mb-0" v-else>
          <i class="fa fa-info-circle text-info" aria-hidden="true" />
          <i>Please Select Specifications &amp; Date.</i>
        </strong>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

// Helpers
import Form from "../../../../../helpers/form";
import * as moment from "moment/moment";

import ReportMixin from "../../../mixins/ReportMixin";

// Snippets
import AreaChart from "../../../snippets/area_chart.vue";
import DateRangePicker from "../../../snippets/date_range_picker.vue";
import CardBox from "../../../snippets/card_box.vue";

export default {
  mixins: [ReportMixin],

  components: {
    AreaChart,
    DateRangePicker,
    CardBox,
  },

  data() {
    // first day of the week
    let startDate = this.local_string(moment().startOf("week"));

    // Last day of the week
    let endDate = this.local_string(moment().endOf("week"));

    return {
      isFetching: false,
      showChart: false,
      isRefresh: false,
      isRendered: true,

      date: {},
      dateModel: {
        startDate: startDate,
        endDate: endDate,
      },

      form: new Form({
        name: "",
        specification: "",
      }),

      specificationOptions: [
        { value: "term_types", text: "Per Type of Terms" },
        { value: "specializations", text: "Per Specializations" },
      ],

      formGroup: {
        isShow: false,
        isLoading: false,
        title: "",
        data: [],
      },

      selectItems: [],

      items: [],
    };
  },

  computed: {
    ...mapGetters("categ_terms", {
      typeTermItems: "get_type_terms_items",
    }),

    ...mapGetters("reports", {
      item: "get_insight_items",
    }),
  },

  methods: {
    ...mapActions("categ_terms", ["get_type_terms"]),
    ...mapActions("reports", ["get_terms"]),

    set_card_box_value(name) {
      let array = [];
      this.items.forEach((item) => {
        item.summaries.forEach((sum) => {
          if (sum.name === name) {
            array.push({
              title: sum.title,
              value: sum.value,
            });
          }
        });
      });
      return array;
    },

    load_terms() {
      let params = {
        name: this.form.name,
        specification: this.set_item(this.form.specification)
          ? JSON.stringify(this.set_item(this.form.specification))
          : "",
        ...this.date,
        ...this.params,
      };

      if (!params.specification) {
        this.errorToast(
          `${this.$t("errors.error")}!`,
          `Specification ${this.$t("errors.field_is_required")}`
        );
        this.isFetching = false;
        return;
      }

      this.get_terms(params)
        .then((_) => {
          this.items = this.item;
        })
        .finally(() => {
          this.showChart = true;
          this.isFetching = false;
          this.isRefresh = false;

          this.$nextTick(() => {
            this.isRendered = true;
          });
        });
    },

    on_select_term(value) {
      if (value) {
        this.date = !_.isEmpty(this.date) ? this.date : this.dateModel;
        this.isFetching = true;
        this.items = [];
        this.load_terms();
      }
    },

    on_get_date(date) {
      this.date = date;
      this.isFetching = true;
      this.items = [];
      this.load_terms();
    },

    on_select_option(value) {
      this.selectItems = [];
      this.form.specification = "";

      this.formGroup.isShow = true;
      this.formGroup.title =
        value === "term_types"
          ? this.$t("label.type_of_terms")
          : this.$t("label.specializations");

      this.formGroup.isLoading = true;

      if (value === "term_types") {
        this.get_type_terms({ ...this.params }).then((_) => {
          this.selectItems = this.typeTermItems;
          this.formGroup.isLoading = false;
        });
      } else {
        this.load_specializations();
      }
    },

    on_refresh() {
      this.isRendered = false;
      this.isRefresh = true;
      this.load_terms();
    },

    load_specializations() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
      };

      axios.get("/api/jobs", { params }).then((resp) => {
        this.formGroup.isLoading = false;
        this.selectItems = resp.data;
      });
    },
  },
};
</script>

