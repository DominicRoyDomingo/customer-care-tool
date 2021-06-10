<template>
  <div>
    <b-card
      :title="$t('label.location')"
      bg-variant="dark"
      text-variant="white"
      body-class="text-center"
    >
      <h6>{{ " (" + this.label.brand_label + ": " + this.brandName + ")" }}</h6>
      <br />
      <b-card-group deck>
        <b-card bg-variant="light" border-variant="light" text-variant="black">
          <div v-if="isLoading">
            <b-progress
              height="5px"
              value="100"
              variant="primary"
              class="mt-3"
              :animated="true"
              striped
            ></b-progress>
          </div>
          <v-chart :options="countryStats" v-if="!isLoading" />
        </b-card>
      </b-card-group>
      <br />
      <b-card-group deck>
        <b-card bg-variant="light" border-variant="light" text-variant="black">
          <div v-if="isLoading">
            <b-progress
              height="5px"
              value="100"
              variant="primary"
              class="mt-3"
              :animated="true"
              striped
            ></b-progress>
          </div>
          <v-chart :options="provinceStats" v-if="!isLoading" />
        </b-card>
      </b-card-group>
      <br />
      <b-card-group deck>
        <b-card bg-variant="light" border-variant="light" text-variant="black">
          <div v-if="isLoading">
            <b-progress
              height="5px"
              value="100"
              variant="primary"
              class="mt-3"
              :animated="true"
              striped
            ></b-progress>
          </div>
          <v-chart :options="cityStats" v-if="!isLoading" />
        </b-card>
      </b-card-group>
      <br />
      <b-card-group deck>
        <b-card bg-variant="light" border-variant="light" text-variant="black">
          <div v-if="isLoading">
            <b-progress
              height="5px"
              value="100"
              variant="primary"
              class="mt-3"
              :animated="true"
              striped
            ></b-progress>
          </div>
          <v-chart :options="aggregatedLocationStats" v-if="!isLoading" />
        </b-card>
      </b-card-group>
    </b-card>
    <br />
    <b-card
      :title="$t('label.job')"
      bg-variant="dark"
      text-variant="white"
      body-class="text-center"
    >
      <h6>{{ " (" + this.label.brand_label + ": " + this.brandName + ")" }}</h6>
      <br />
      <b-card-group deck>
        <b-card bg-variant="light" border-variant="light" text-variant="black">
          <div v-if="isLoading">
            <b-progress
              height="5px"
              value="100"
              variant="primary"
              class="mt-3"
              :animated="true"
              striped
            ></b-progress>
          </div>
          <v-chart :options="categoryStats" v-if="!isLoading" />
        </b-card>
      </b-card-group>
      <br />
      <b-card-group deck>
        <b-card bg-variant="light" border-variant="light" text-variant="black">
          <div v-if="isLoading">
            <b-progress
              height="5px"
              value="100"
              variant="primary"
              class="mt-3"
              :animated="true"
              striped
            ></b-progress>
          </div>
          <v-chart :options="professionStats" v-if="!isLoading" />
        </b-card>
      </b-card-group>
      <br />
      <b-card-group deck>
        <b-card bg-variant="light" border-variant="light" text-variant="black">
          <div v-if="isLoading">
            <b-progress
              height="5px"
              value="100"
              variant="primary"
              class="mt-3"
              :animated="true"
              striped
            ></b-progress>
          </div>
          <v-chart :options="specializationStats" v-if="!isLoading" />
        </b-card>
      </b-card-group>
      <br />
      <b-card-group deck>
        <b-card bg-variant="light" border-variant="light" text-variant="black">
          <div v-if="isLoading">
            <b-progress
              height="5px"
              value="100"
              variant="primary"
              class="mt-3"
              :animated="true"
              striped
            ></b-progress>
          </div>
          <v-chart :options="aggregatedJobStats" v-if="!isLoading" />
        </b-card>
      </b-card-group>
    </b-card>
  </div>
</template>

<style>
.echarts {
  width: 500px;
  height: 500px;
  margin: auto;
}
</style>

<script>
import { mapActions } from "vuex";
import profileMixins from "../mixins/profileMixins";
import ECharts from "vue-echarts";
import "echarts/lib/chart/pie";
import "echarts/lib/component/tooltip";
import "echarts/lib/component/legend";
import "echarts/lib/component/title";
import "echarts/lib/component/legend/scrollableLegendAction";
import "echarts/lib/component/legend/ScrollableLegendModel";
import "echarts/lib/component/legend/ScrollableLegendView";
import "echarts/lib/component/visualMap/ContinuousModel";
import "echarts/lib/component/visualMap/ContinuousView";
import "echarts/lib/component/visualMap/typeDefaulter";
import "echarts/lib/component/visualMap/visualEncoding";

export default {
  mixins: [profileMixins],
  components: {
    "v-chart": ECharts,
  },
  data() {
    return {
      locationTitleText: this.label.location_statistics,
      countryStatsSubText: "(" + this.label.by_country + ")",
      provinceStatsSubText: "(" + this.label.by_province + ")",
      cityStatsSubText: "(" + this.label.by_city + ")",
      aggLocationStatsSubText: "(" + this.label.country_province_city + ")",
      jobTitleText: this.label.job_statistics,
      categoryStatsSubText: "(" + this.label.by_category + ")",
      professionStatsSubText: "(" + this.label.by_profession + ")",
      specializationStatsSubText: "(" + this.label.by_specialization + ")",
      aggJobStatsSubText:
        "(" + this.label.category_profession_specialization + ")",
    };
  },
  props: ["brand", "label", "brandName"],
  computed: {
    countryStats: function() {
      let dataSet = this.$store.state.profile.country_location_statistics;
      let dataSetLegend = [];
      dataSet.map((item) => dataSetLegend.push(item.name));

      return {
        title: {
          text: this.locationTitleText,
          subtext: this.countryStatsSubText,
          left: "center",
        },
        tooltip: {
          trigger: "item",
          formatter: "{b}: {c} <br /> <b>({d}%)</b>",
        },
        legend: {
          type: "scroll",
          orient: "horizontal",
          left: "center",
          top: "bottom",
          data: dataSetLegend,
        },
        visualMap: {
          show: false,
          min: dataSet.sort(function(a, b) {
            return a.value - b.value;
          })[dataSet.length - 1].value,
          max: dataSet.sort(function(a, b) {
            return a.value - b.value;
          })[0].value,
          inRange: {
            colorLightness: [0.3, 0.8],
          },
        },
        series: [
          {
            name: this.locationTitleText,
            type: "pie",
            radius: "70%",
            center: ["50%", "50%"],
            label: {
              show: dataSet.length < 10,
              position: "inner",
            },
            emphasis: {
              itemStyle: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: "rgba(0, 0, 0, 0.5)",
              },
              label: {
                show: true,
              },
            },
            itemStyle: {
              color: "#c23531",
              shadowBlur: 5,
              shadowColor: "rgba(0, 0, 0, 0.5)",
            },
            data: dataSet.sort(function(a, b) {
              return a.value - b.value;
            }),
          },
        ],
        animationType: "scale",
        animationEasing: "elasticOut",
        animationDelay: function(idx) {
          return Math.random() * 200;
        },
      };
    },
    provinceStats: function() {
      let dataSet = this.$store.state.profile.province_location_statistics;
      let dataSetLegend = [];
      dataSet.map((item) => dataSetLegend.push(item.name));

      return {
        title: {
          text: this.locationTitleText,
          subtext: this.provinceStatsSubText,
          left: "center",
        },
        tooltip: {
          trigger: "item",
          formatter: "{b}: {c} <br /> <b>({d}%)</b>",
        },
        legend: {
          type: "scroll",
          orient: "horizontal",
          left: "center",
          top: "bottom",
          data: dataSetLegend,
        },
        visualMap: {
          show: false,
          min: dataSet.sort(function(a, b) {
            return a.value - b.value;
          })[dataSet.length - 1].value,
          max: dataSet.sort(function(a, b) {
            return a.value - b.value;
          })[0].value,
          inRange: {
            colorLightness: [0.3, 0.8],
          },
        },
        series: [
          {
            name: this.locationTitleText,
            type: "pie",
            radius: "70%",
            center: ["50%", "50%"],
            label: {
              show: dataSet.length < 10,
              position: "inner",
            },
            emphasis: {
              itemStyle: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: "rgba(0, 0, 0, 0.5)",
              },
              label: {
                show: true,
              },
            },
            itemStyle: {
              color: "#2f4554",
              shadowBlur: 5,
              shadowColor: "rgba(0, 0, 0, 0.5)",
            },
            data: dataSet.sort(function(a, b) {
              return b.value - a.value;
            }),
          },
        ],
        animationType: "scale",
        animationEasing: "elasticOut",
        animationDelay: function(idx) {
          return Math.random() * 200;
        },
      };
    },
    cityStats: function() {
      let dataSet = this.$store.state.profile.city_location_statistics;
      let dataSetLegend = [];
      dataSet.map((item) => dataSetLegend.push(item.name));

      return {
        title: {
          text: this.locationTitleText,
          subtext: this.cityStatsSubText,
          left: "center",
        },
        tooltip: {
          trigger: "item",
          formatter: "{b}: {c} <br /> <b>({d}%)</b>",
        },
        legend: {
          type: "scroll",
          orient: "horizontal",
          left: "center",
          top: "bottom",
          data: dataSetLegend,
        },
        visualMap: {
          show: false,
          min: dataSet.sort(function(a, b) {
            return a.value - b.value;
          })[dataSet.length - 1].value,
          max: dataSet.sort(function(a, b) {
            return a.value - b.value;
          })[0].value,
          inRange: {
            colorLightness: [0.3, 0.8],
          },
        },
        series: [
          {
            name: this.locationTitleText,
            type: "pie",
            radius: "70%",
            center: ["50%", "50%"],
            label: {
              show: dataSet.length < 10,
              position: "inner",
            },
            emphasis: {
              itemStyle: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: "rgba(0, 0, 0, 0.5)",
              },
              label: {
                show: true,
              },
            },
            itemStyle: {
              color: "#9cd5bd",
              shadowBlur: 5,
              shadowColor: "rgba(0, 0, 0, 0.5)",
            },
            data: dataSet.sort(function(a, b) {
              return b.value - a.value;
            }),
          },
        ],
        animationType: "scale",
        animationEasing: "elasticOut",
        animationDelay: function(idx) {
          return Math.random() * 200;
        },
      };
    },
    aggregatedLocationStats: function() {
      let dataSetLegend = [];
      let countryDataSet = this.$store.state.profile
        .country_location_statistics;
      countryDataSet.map((item) => dataSetLegend.push(item.name));
      let provinceDataSet = this.$store.state.profile
        .province_location_statistics;
      provinceDataSet.map((item) => dataSetLegend.push(item.name));
      let cityDataSet = this.$store.state.profile.city_location_statistics;
      cityDataSet.map((item) => dataSetLegend.push(item.name));

      let aggregatedDataset = countryDataSet
        .concat(provinceDataSet)
        .concat(cityDataSet);

      return {
        title: {
          text: this.locationTitleText,
          subtext: this.aggLocationStatsSubText,
          left: "center",
        },
        tooltip: {
          trigger: "item",
          formatter: "{b}: {c} <br /> <b>({d}%)</b>",
        },
        legend: {
          type: "scroll",
          orient: "horizontal",
          bottom: 10,
          padding: 10,
          data: dataSetLegend,
        },
        visualMap: {
          show: false,
          min: aggregatedDataset.sort(function(a, b) {
            return a.value - b.value;
          })[aggregatedDataset.length - 1].value,
          max: aggregatedDataset.sort(function(a, b) {
            return a.value - b.value;
          })[0].value,
          inRange: {
            colorLightness: [0.3, 0.8],
          },
        },
        series: [
          {
            name: this.locationTitleText,
            type: "pie",
            radius: [0, "30%"],
            label: {
              show: countryDataSet.length < 10,
              position: "inner",
            },
            emphasis: {
              itemStyle: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: "rgba(0, 0, 0, 0.5)",
              },
              label: {
                show: true,
              },
            },
            itemStyle: {
              color: "#c23531",
              shadowBlur: 5,
              shadowColor: "rgba(0, 0, 0, 0.5)",
            },
            data: countryDataSet,
          },
          {
            name: this.locationTitleText,
            type: "pie",
            radius: ["40%", "55%"],
            label: {
              show: provinceDataSet.length < 10,
              position: "inner",
            },
            emphasis: {
              itemStyle: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: "rgba(0, 0, 0, 0.5)",
              },
              label: {
                show: true,
              },
            },
            itemStyle: {
              color: "#334B5C",
              shadowBlur: 5,
              shadowColor: "rgba(0, 0, 0, 0.5)",
            },
            data: provinceDataSet,
          },
          {
            name: this.locationTitleText,
            type: "pie",
            radius: ["65%", "80%"],
            label: {
              show: cityDataSet.length < 10,
              position: "inner",
            },
            emphasis: {
              itemStyle: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: "rgba(0, 0, 0, 0.5)",
              },
              label: {
                show: true,
              },
            },
            itemStyle: {
              color: "#8EC2AC",
              shadowBlur: 5,
              shadowColor: "rgba(0, 0, 0, 0.5)",
            },
            data: cityDataSet,
          },
        ],
        animationType: "scale",
        animationEasing: "elasticOut",
        animationDelay: function(idx) {
          return Math.random() * 200;
        },
      };
    },
    categoryStats: function() {
      let dataSet = this.$store.state.profile.category_job_statistics;
      let dataSetLegend = [];
      dataSet.map((item) => dataSetLegend.push(item.name));

      return {
        title: {
          text: this.jobTitleText,
          subtext: this.categoryStatsSubText,
          left: "center",
        },
        tooltip: {
          trigger: "item",
          formatter: "{b}: {c} <br /> <b>({d}%)</b>",
        },
        legend: {
          type: "scroll",
          orient: "horizontal",
          left: "center",
          top: "bottom",
          data: dataSetLegend,
        },
        visualMap: {
          show: false,
          min: dataSet.sort(function(a, b) {
            return a.value - b.value;
          })[dataSet.length - 1].value,
          max: dataSet.sort(function(a, b) {
            return a.value - b.value;
          })[0].value,
          inRange: {
            colorLightness: [0.3, 0.8],
          },
        },
        series: [
          {
            name: this.jobTitleText,
            type: "pie",
            radius: "70%",
            center: ["50%", "50%"],
            label: {
              show: dataSet.length < 10,
              position: "inner",
            },
            emphasis: {
              itemStyle: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: "rgba(0, 0, 0, 0.5)",
              },
              label: {
                show: true,
              },
            },
            itemStyle: {
              color: "#d48265",
              shadowBlur: 5,
              shadowColor: "rgba(0, 0, 0, 0.5)",
            },
            data: dataSet.sort(function(a, b) {
              return a.value - b.value;
            }),
          },
        ],
        animationType: "scale",
        animationEasing: "elasticOut",
        animationDelay: function(idx) {
          return Math.random() * 200;
        },
      };
    },
    professionStats: function() {
      let dataSet = this.$store.state.profile.profession_job_statistics;
      let dataSetLegend = [];
      dataSet.map((item) => dataSetLegend.push(item.name));

      return {
        title: {
          text: this.jobTitleText,
          subtext: this.professionStatsSubText,
          left: "center",
        },
        tooltip: {
          trigger: "item",
          formatter: "{b}: {c} <br /> <b>({d}%)</b>",
        },
        legend: {
          type: "scroll",
          orient: "horizontal",
          left: "center",
          top: "bottom",
          data: dataSetLegend,
        },
        visualMap: {
          show: false,
          min: dataSet.sort(function(a, b) {
            return a.value - b.value;
          })[dataSet.length - 1].value,
          max: dataSet.sort(function(a, b) {
            return a.value - b.value;
          })[0].value,
          inRange: {
            colorLightness: [0.3, 0.8],
          },
        },
        series: [
          {
            name: this.jobTitleText,
            type: "pie",
            radius: "70%",
            center: ["50%", "50%"],
            label: {
              show: dataSet.length < 10,
              position: "inner",
            },
            emphasis: {
              itemStyle: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: "rgba(0, 0, 0, 0.5)",
              },
              label: {
                show: true,
              },
            },
            itemStyle: {
              color: "#91c7ae",
              shadowBlur: 5,
              shadowColor: "rgba(0, 0, 0, 0.5)",
            },
            data: dataSet.sort(function(a, b) {
              return b.value - a.value;
            }),
          },
        ],
        animationType: "scale",
        animationEasing: "elasticOut",
        animationDelay: function(idx) {
          return Math.random() * 200;
        },
      };
    },
    specializationStats: function() {
      let dataSet = this.$store.state.profile.specialization_job_statistics;
      let dataSetLegend = [];
      dataSet.map((item) => dataSetLegend.push(item.name));

      return {
        title: {
          text: this.jobTitleText,
          subtext: this.specializationStatsSubText,
          left: "center",
        },
        tooltip: {
          trigger: "item",
          formatter: "{b}: {c} <br /> <b>({d}%)</b>",
        },
        legend: {
          type: "scroll",
          orient: "horizontal",
          left: "center",
          top: "bottom",
          data: dataSetLegend,
        },
        visualMap: {
          show: false,
          min: dataSet.sort(function(a, b) {
            return a.value - b.value;
          })[dataSet.length - 1].value,
          max: dataSet.sort(function(a, b) {
            return a.value - b.value;
          })[0].value,
          inRange: {
            colorLightness: [0.3, 0.8],
          },
        },
        series: [
          {
            name: this.jobTitleText,
            type: "pie",
            radius: "70%",
            center: ["50%", "50%"],
            label: {
              show: dataSet.length < 10,
              position: "inner",
            },
            emphasis: {
              itemStyle: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: "rgba(0, 0, 0, 0.5)",
              },
              label: {
                show: true,
              },
            },
            itemStyle: {
              color: "#ca8622",
              shadowBlur: 5,
              shadowColor: "rgba(0, 0, 0, 0.5)",
            },
            data: dataSet.sort(function(a, b) {
              return b.value - a.value;
            }),
          },
        ],
        animationType: "scale",
        animationEasing: "elasticOut",
        animationDelay: function(idx) {
          return Math.random() * 200;
        },
      };
    },
    aggregatedJobStats: function() {
      let dataSetLegend = [];
      let categoryDataSet = this.$store.state.profile.category_job_statistics;
      categoryDataSet.map((item) => dataSetLegend.push(item.name));
      let professionDataSet = this.$store.state.profile
        .profession_job_statistics;
      professionDataSet.map((item) => dataSetLegend.push(item.name));
      let specializationDataSet = this.$store.state.profile
        .specialization_job_statistics;
      specializationDataSet.map((item) => dataSetLegend.push(item.name));

      let aggregatedDataset = categoryDataSet
        .concat(professionDataSet)
        .concat(specializationDataSet);

      return {
        title: {
          text: this.jobTitleText,
          subtext: this.aggJobStatsSubText,
          left: "center",
        },
        tooltip: {
          trigger: "item",
          formatter: "{b}: {c} <br /> <b>({d}%)</b>",
        },
        legend: {
          type: "scroll",
          orient: "horizontal",
          bottom: 10,
          padding: 10,
          data: dataSetLegend,
        },
        visualMap: {
          show: false,
          min: aggregatedDataset.sort(function(a, b) {
            return a.value - b.value;
          })[aggregatedDataset.length - 1].value,
          max: aggregatedDataset.sort(function(a, b) {
            return a.value - b.value;
          })[0].value,
          inRange: {
            colorLightness: [0.3, 0.8],
          },
        },
        series: [
          {
            name: this.jobTitleText,
            type: "pie",
            radius: [0, "30%"],
            label: {
              show: categoryDataSet.length < 10,
              position: "inner",
            },
            emphasis: {
              itemStyle: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: "rgba(0, 0, 0, 0.5)",
              },
              label: {
                show: true,
              },
            },
            itemStyle: {
              color: "#d48265",
              shadowBlur: 5,
              shadowColor: "rgba(0, 0, 0, 0.5)",
            },
            data: categoryDataSet,
          },
          {
            name: this.jobTitleText,
            type: "pie",
            radius: ["40%", "55%"],
            label: {
              show: professionDataSet.length < 10,
              position: "inner",
            },
            emphasis: {
              itemStyle: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: "rgba(0, 0, 0, 0.5)",
              },
              label: {
                show: true,
              },
            },
            itemStyle: {
              color: "#91c7ae",
              shadowBlur: 5,
              shadowColor: "rgba(0, 0, 0, 0.5)",
            },
            data: professionDataSet,
          },
          {
            name: this.jobTitleText,
            type: "pie",
            radius: ["65%", "80%"],
            label: {
              show: specializationDataSet.length < 10,
              position: "inner",
            },
            emphasis: {
              itemStyle: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: "rgba(0, 0, 0, 0.5)",
              },
              label: {
                show: true,
              },
            },
            itemStyle: {
              color: "#ca8622",
              shadowBlur: 5,
              shadowColor: "rgba(0, 0, 0, 0.5)",
            },
            data: specializationDataSet,
          },
        ],
        animationType: "scale",
        animationEasing: "elasticOut",
        animationDelay: function(idx) {
          return Math.random() * 200;
        },
      };
    },
  },
  created() {
    this.getStats();
  },
  methods: {
    ...mapActions("profile", ["get_stats"]),

    getStats() {
      let params = {
        api_token: this.$user.api_token,
        brand: this.brand,
        lang: this.$i18n.locale,
      };

      this.get_stats(params).then((_) => {
        this.isLoading = false;
      });
    },
  },
};
</script>
