<template>
  <div class="row">
    <div class="col-md-12">
      <h4 class="card-title text-uppercase mb-0">
        {{ $t("reports") }}
        <small class="text-muted text-capitalize">
          {{ $t("entries_per_clasifications") }}
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
              Module &amp; {{ $t("date") }}
            </span>
          </h6>
        </div>
        <div class="card-body mb-2 bg-light">
          <form action="">
            <div class="form-group">
              <label>
                Choose Module:
                <b-spinner v-if="isLoadingStat" small label="Small Spinner" />
              </label>
              <b-form-select
                v-model="form.id"
                :options="all.statistics_table_list"
                @change="on_select_module"
                class="mb-3"
                value-field="id"
                text-field="base_name"
              >
                <template #first>
                  <b-form-select-option :value="''" disabled>
                    -- Select Module --
                  </b-form-select-option>
                </template>
              </b-form-select>
            </div>

            <div class="form-group">
              <label>Choose Date:</label>
              <div :id="`date-range-picker`">
                <DateRangePicker
                  :dateModel="dateModel"
                  :is_disabled="!form.id"
                  @date="on_get_date"
                />
              </div>
              <b-popover
                v-if="!form.id"
                variant="danger"
                :target="`date-range-picker`"
                triggers="hover focus"
                placement="top"
              >
                <p class="mb-0">Please select a Module</p>
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
      <div class="card border-0 mb-0" v-if="items.length > 0">
        <div
          class="card-header py-3 d-flex flex-row align-items-center justify-content-between"
        >
          <h6 class="m-0 font-weight-bold text-muted text-capitalize">
            <span class="text-uppercase"> {{ $t("date_of_coverage") }} :</span>
            <span
              class="font-weight-normal"
              v-if="form.id && Object.values(date).length > 0"
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
            <CardBox :items="items" :componentName="`per_entries`" />
            <div class="row">
              <div class="col-md-12">
                <AreaChart
                  :data="items"
                  :date="date"
                  :componentName="`per_entries`"
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
          <i>Please select Module &amp; Date.</i>
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

// Mixins
import ReportMixin from "../../../mixins/ReportMixin";

// Snippets
import AreaChart from "../../../snippets/area_chart.vue";
import DateRangePicker from "../../../snippets/date_range_picker.vue";
import CardBox from "../../../snippets/card_box.vue";

export default {
  mixins: [ReportMixin],
  components: { AreaChart, DateRangePicker, CardBox },

  data() {
    // first day of the week
    let startDate = this.local_string(moment().startOf("week"));

    // Last day of the week
    let endDate = this.local_string(moment().endOf("week"));

    return {
      isFetching: false,
      showChart: false,
      isRefresh: false,
      date: {},
      items: [],

      dateModel: {
        startDate: startDate,
        endDate: endDate,
      },

      form: new Form({
        id: "",
        date: "",
      }),
    };
  },

  created() {
    this.load_statistic_table(); // load statistic table
  },

  computed: {
    ...mapGetters("reports", {
      item: "get_insight_items",
    }),
  },

  methods: {
    ...mapActions("reports", ["get_insights"]),

    load_insight_items() {
      let params = { id: this.form.id, ...this.date, ...this.params };

      this.get_insights(params)
        .then((_) => {
          this.items = this.item;
        })
        .finally(() => {
          this.showChart = true;
          this.isFetching = false;
          this.isRefresh = false;
        });
    },

    on_select_module(value) {
      this.date = !_.isEmpty(this.date) ? this.date : this.dateModel;
      this.items = [];
      this.isFetching = true;
      this.load_insight_items();
    },

    on_get_date(date) {
      this.date = date;
      this.items = [];
      this.isFetching = true;
      this.load_insight_items();
    },

    on_refresh() {
      this.isRefresh = true;
      this.load_insight_items();
    },

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
  },
};
</script>

