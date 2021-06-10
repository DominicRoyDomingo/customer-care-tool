<template>
  <date-range-picker
    v-model="dateRange"
    style="min-width: 100%; !important"
    :disabled="is_disabled"
    @select="onSelect"
    @singleDatePicker="singleDatePicker"
  >
    <!--    header slot-->
    <!-- <div slot="header" slot-scope="header" class="card-header">
      <span v-if="header.in_selection"></span>
    </div> -->
    <!--    input slot (new slot syntax)-->
    <!-- <template #input="picker" style="min-width: 350px">
      {{ picker.startDate | date }} - {{ picker.endDate | date }}
    </template> -->
    <!--    ranges (new slot syntax) -->

    <template v-slot:input="picker">
      <strong>
        <i class="fa fa-calendar mr-1 text-info" aria-hidden="true"></i>
        {{ picker.startDate | date }} - {{ picker.endDate | date }}
      </strong>
    </template>

    <template #ranges="ranges">
      <div class="ranges mt-5">
        <div class="list-group p-1">
          <a
            v-for="(range, name) in ranges.ranges"
            :key="name"
            @click="ranges.clickRange(range)"
            href="javascript:;"
            class="list-group-item list-group-item-action mb-1"
            v-text="name"
            v-show="name !== 'This year'"
          />
        </div>
      </div>
    </template>
    <!--    footer slot-->
    <div slot="footer" slot-scope="data" class="card-footer">
      <div class="row">
        <div class="col-sm-6">
          <strong style="margin-top: 6px; position: absolute">
            {{ data.rangeText }}
          </strong>
        </div>
        <div class="col-sm-6">
          <span class="float-right">
            <b-button variant="light" size="sm" @click="data.clickCancel">
              {{ $t("cancel") }}
            </b-button>
            <b-button
              variant="success"
              size="sm"
              class="text-white"
              @click="data.clickApply"
              :disabled="data.in_selection"
            >
              {{ data.locale.applyLabel }}
            </b-button>
          </span>
        </div>
      </div>
    </div>
  </date-range-picker>
</template>

<script>
import DateRangePicker from "vue2-daterange-picker";
import "vue2-daterange-picker/dist/vue2-daterange-picker.css";
import ReportMixin from "../mixins/ReportMixin";

export default {
  props: ["dateModel", "is_disabled"],
  components: { DateRangePicker },
  mixins: [ReportMixin],
  data() {
    return {
      dateRange: this.dateModel,
    };
  },

  watch: {
    dateRange: function (value) {
      let date = {
        startDate: this.local_string(value.startDate),
        endDate: this.local_string(value.endDate),
      };

      this.$emit("date", date);
    },
  },

  filters: {
    date(value) {
      const options = {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
      };
      const d = new Date(value);
      return d.toLocaleString("en", options);
    },
  },
  methods: {
    dateFormat(classes, date) {
      console.log(date);
    },

    singleDatePicker(date) {
      console.log(date);
    },

    onSelect(value) {
      // let date = {
      //   startDate: this.local_string(value.startDate),
      //   endDate: this.local_string(value.endDate),
      // };
      // this.$emit("date", date);
    },
  },
};
</script>

<style scoped>
</style>