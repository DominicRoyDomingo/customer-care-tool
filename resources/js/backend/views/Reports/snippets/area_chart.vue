<style  scoped>
</style>
<template>
  <div>
    <apexchart
      type="area"
      height="500"
      :options="chartOptions"
      :series="series"
    />
  </div>
</template>

<script>
import VueApexCharts from "vue-apexcharts";
import * as moment from "moment/moment";

export default {
  props: ["data", "date", "componentName"],

  components: { apexchart: VueApexCharts },

  data() {
    return {
      series: this.set_series_data(),
      chartOptions: {
        chart: {
          height: 500,
          type: "area",
          zoom: {
            enabled: true,
          },
        },
        title: {
          text: `${this.get_title_name()} By ${this.data[0].date_name}s`,
          align: "left",
        },
        subtitle: {
          text: "Data Movements",
          align: "left",
          style: {
            fontSize: "12px",
            fontWeight: "normal",
            fontFamily: undefined,
            color: "#9699a2",
          },
        },
        legend: {
          position: "top",
          horizontalAlign: "right",
          offsetY: -10,
        },
        dataLabels: {
          enabled: false,
        },
        stroke: {
          curve: "straight",
          // curve: "smooth",
        },
        yaxis: {
          opposite: false,
        },
        xaxis: {
          type: "category",
          // categories: this.set_categories(),

          // labels: {
          //   /**
          //    * Allows users to apply a custom formatter function to xaxis labels.
          //    *
          //    * @param { String } value - The default value generated
          //    * @param { Number } timestamp - In a datetime series, this is the raw timestamp
          //    * @param { object } contains dateFormatter for datetime x-axis
          //    */
          //   formatter: function (value, timestamp, opts) {
          //     const formatted = moment(timestamp).format("DD-MMM-YY");

          //     console.log(formatted);
          //     return formatted;
          //   },
          // },
        },
        tooltip: { shared: true },
        colors: this.set_color(),
      },
    };
  },

  methods: {
    set_color() {
      let arrColor = [];
      if (this.componentName === "per_entries") {
        arrColor.push("#008ffb");
      } else {
        arrColor = this.data.map((item) => {
          return item.chart_color;
        });
      }
      return arrColor;
    },
    set_categories() {
      const dateRange = this.get_date_range();
      return dateRange.map((date) => {
        return moment(date).format("DD-MMM");
      });
    },
    get_date_range() {
      let fromDate = moment(this.date.startDate);
      let toDate = moment(this.date.endDate);
      let diff = toDate.diff(fromDate, "days");
      let range = [];
      for (let i = 0; i <= diff; i++) {
        range.push(
          moment(this.date.startDate).add(i, "days").format("YYYY-MM-DD")
        );
      }
      return range;
    },
    set_series_data() {
      let dataArray = this.data.map((item) => {
        return {
          date_name: item.date_name,
          display_name: item.display_name,
          items: this.set_items(item),
          summaries: item.summaries,
        };
      });

      return dataArray.map((item) => {
        return { name: item.display_name, data: this.set_data(item.items) };
      });
    },
    set_items(item) {
      let items = [];
      this.get_date_range().forEach((date) => {
        let key = false;
        let obj = {};
        item.items.forEach((value) => {
          if (date == value.date) {
            obj = value;
            key = true;
            return;
          }
        });

        obj = !_.isEmpty(obj) ? obj : false;

        items.push({
          count: key ? obj.count : 0,
          date: obj ? obj.date : date,
          date_string: obj ? obj.date_string : moment(date).format("LL"),
          // created_at: obj ? obj.created_at : date,
          // specializations: obj ? obj.specializations : null,
          // term_types: obj ? obj.term_types : null,
        });
      });

      return items;
    },
    set_data(data) {
      return data.map((item) => {
        return { x: moment(item.date).format("DD-MM-Y"), y: item.count };
      });
    },
    set_date_name(data) {
      return data.map((item) => item.date_string);
    },
    get_title_name() {
      let name = "";
      let i = 1;
      this.data.forEach((ele) => {
        name += ele.display_name;
        if (i < this.data.length) {
          name += ", ";
          i++;
        }
      });
      return name;
    },
  },
};
</script>
