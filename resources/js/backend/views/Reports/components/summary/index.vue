<style  scoped>
.tab-title {
  color: #2f353a;
}
.border-none {
  border: none;
}
.active-item {
  background-color: red !important;
}
.bg-test {
  background-color: #f0f3f5 !important;
}
.border-top {
  border-top: 20px sold red !important;
}
</style>
<template>
  <div class="animated fadeIn">
    <b-tabs
      v-model="tabModel"
      content-class="mb-5 "
      nav-wrapper-class="bg-light border-0 "
      active-nav-item-class="font-weight-bold text-uppercase bg-light text-info border-top"
      active-tab-class="p-4 mt-4 mb-0"
      nav-class="p-0"
      fill
    >
      <b-tab active>
        <template #title>
          {{ $t("geolocalization") }}
        </template>
        <Geolocalization v-if="tabs.show_entries" />
      </b-tab>
    </b-tabs>
  </div>
</template>

<script>
import ReportMixin from "./../../mixins/ReportMixin";

// tabs components
import PerEntries from "./tabs/per_entries";
import Geolocalization from "./tabs/geolocalization";
import TermSpecifications from "./tabs/term_specifications";

export default {
  components: { PerEntries, TermSpecifications, Geolocalization },

  mixins: [ReportMixin],

  data() {
    return {
      tabs: {
        show_entries: false,
        show_term_spec: false,
      },

      tabModel: null,
    };
  },

  watch: {
    tabModel(val) {
      if (val == 0) {
        this.tabs.show_entries = false;
        this.$nextTick(() => {
          this.tabs.show_entries = true;
        });
        this.tabs.show_term_spec = false;
      }
      if (val == 1) {
        this.tabs.show_term_spec = false;
        this.$nextTick(() => {
          this.tabs.show_term_spec = true;
        });
        this.tabs.show_entries = false;
      }
    },
  },

  methods: {},
};
</script>

