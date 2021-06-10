<template>
  <div>
    <b-card
      :header="$t('label.location_statistics') + ' (' + this.brandName + ')'"
      class="stats__container"
    >
      <vue-good-table
        :columns="locationDataCols"
        :rows="locationData"
        :group-options="{
          enabled: true,
          headerPosition: 'top',
          collapsable: true,
        }"
        :search-options="{
          enabled: true,
        }"
        :fixed-header="true"
        ref="location_table"
        styleClass="vgt-table condensed striped"
        max-height="500px"
      >
        <template slot="table-header-row" slot-scope="props" class="row-header">
          <span>
            {{ props.row.label }}
          </span>
        </template>
        <div slot="table-actions" class="stats__container-actions">
          <b-button-group>
            <b-button
              size="sm"
              @click="() => this.$refs.location_table.expandAll()"
              variant="success"
              squared
            >
              <i class="fas fa-expand"></i> {{ $t("label.expand") }}
            </b-button>
            <b-button
              size="sm"
              @click="() => this.$refs.location_table.collapseAll()"
              variant="danger"
              squared
            >
              <i class="fas fa-compress"></i> {{ $t("label.collapse") }}
            </b-button>
          </b-button-group>
        </div>
        <div slot="emptystate">
          <center>
            {{ $t("label.empty_tabular_stats") }} {{ $t("label.location") }}
          </center>
        </div>
        <div slot="loadingContent">
          <div>
            <b-spinner label="Loading..."></b-spinner>
          </div>
        </div>
        <template slot="table-header-row" slot-scope="props">
          <div v-if="props.column.field == 'cnt'" style="text-align:right">
            {{ $t("label.total") }}: {{ props.row.cnt }}
          </div>
          <span v-else-if="props.column.field == 'pv'">
            {{ props.row.label }}
          </span>
        </template>
        <template slot="table-row" slot-scope="props">
          <span v-if="props.column.field == 'cnt'">
            <span style="font-weight: bold; color: blue;">
              <b-button
                variant="outline-info"
                squared
                v-b-tooltip.hover
                title="Show additional stats"
                @click="
                  showJobDetails([
                    {
                      sourceIndex: props.row.vgt_id,
                      province: props.formattedRow.pv,
                      city: props.formattedRow.ct,
                    },
                  ])
                "
              >
                <strong>{{ props.row.cnt }}</strong>
                &nbsp; <i class="fas fa-info-circle"></i>
              </b-button>
            </span>
          </span>
          <span v-else>
            {{ props.formattedRow[props.column.field] }}
          </span>
        </template>
      </vue-good-table>
    </b-card>
    <br />
    <b-card
      :header="$t('label.job_statistics') + ' (' + this.brandName + ')'"
      class="stats__container"
    >
      <vue-good-table
        :columns="jobDataCols"
        :rows="jobData"
        :group-options="{
          enabled: true,
          headerPosition: 'top',
          collapsable: true,
        }"
        :search-options="{
          enabled: true,
        }"
        :fixed-header="true"
        ref="job_table"
        styleClass="vgt-table condensed striped"
        max-height="500px"
      >
        <template slot="table-header-row" slot-scope="props" class="row-header">
          <span>
            {{ props.row.label }}
          </span>
        </template>
        <div slot="table-actions" class="stats__container-actions">
          <b-button-group>
            <b-button
              size="sm"
              @click="() => this.$refs.job_table.expandAll()"
              variant="success"
              squared
            >
              <i class="fas fa-expand"></i> {{ $t("label.expand") }}
            </b-button>
            <b-button
              size="sm"
              @click="() => this.$refs.job_table.collapseAll()"
              variant="danger"
              squared
            >
              <i class="fas fa-compress"></i> {{ $t("label.collapse") }}
            </b-button>
          </b-button-group>
        </div>
        <div slot="emptystate">
          <center>
            {{ $t("label.empty_tabular_stats") }} {{ $t("label.job") }}
          </center>
        </div>
        <div slot="loadingContent">
          <div>
            <b-spinner label="Loading..."></b-spinner>
          </div>
        </div>
        <template slot="table-header-row" slot-scope="props">
          <div v-if="props.column.field == 'cnt'" style="text-align:right">
            {{ $t("label.total") }}: {{ props.row.cnt }}
          </div>
          <span v-else-if="props.column.field == 'pf'">
            {{ props.row.label }}
          </span>
        </template>
        <template slot="table-row" slot-scope="props">
          <span v-if="props.column.field == 'cnt'">
            <span style="font-weight: bold; color: blue;">
              <b-button
                variant="outline-info"
                squared
                v-b-tooltip.hover
                title="Show additional stats"
                @click="
                  showLocationDetails([
                    {
                      sourceIndex: props.row.vgt_id,
                      profession: props.formattedRow.pf,
                      specialization: props.formattedRow.sp,
                    },
                  ])
                "
              >
                <strong>{{ props.row.cnt }}</strong>
                &nbsp; <i class="fas fa-info-circle"></i>
              </b-button>
            </span>
          </span>
          <span v-else>
            {{ props.formattedRow[props.column.field] }}
          </span>
        </template>
      </vue-good-table>
    </b-card>
    <!-- modal for jobs' details -->
    <b-modal
      ref="jobDetailsModal"
      hide-footer
      hide-header
      max-height="500"
      scrollable
      body-class="modal__detailed_stats"
      id="modal__job_details"
    >
      <v-app id="app__modal_job" class="application__container">
        <v-sheet class="pa-4 info modal__toolbar">
          <v-btn
            small
            icon
            color="white"
            style="float: right"
            @click="
              () => {
                this.$refs.jobDetailsModal.hide();
              }
            "
          >
            <v-icon>mdi-window-close</v-icon>
          </v-btn>
          <h5 v-html="modalTitle" style="color: #fff"></h5>
          <v-text-field
            v-model="search"
            label="Search"
            prepend-inner-icon="mdi-magnify"
            dark
            flat
            solo-inverted
            hide-details
            clearable
            clear-icon="mdi-close-circle-outline"
          ></v-text-field>
        </v-sheet>
        <b-button-group style="margin: 2px 0">
          <download-excel
            v-bind:class="[
              'btn',
              'btn-success',
              'rounded-0',
              { disabled: this.isExcelDownloading },
            ]"
            :fetch="exportExcelJobDetails"
            :fields="jobDetailHeaders"
            :before-generate="startDownload"
            :before-finish="finishDownload"
            worksheet="Job Statistics"
            :name="
              [
                'Job Statistics for ' +
                  this.city +
                  ', ' +
                  this.province +
                  ', ' +
                  this.country +
                  '--' +
                  this.brandName +
                  '.xls',
              ].toString()
            "
          >
            <v-icon
              color="white"
              style="margin-top: -2.5px"
              v-if="!this.isExcelDownloading"
            >
              mdi-file-excel-box
            </v-icon>
            <b-spinner small v-else></b-spinner>
            <span>Export to Excel</span>
          </download-excel>
          <b-button
            variant="info"
            squared
            @click="exportJobDetailsText"
            :disabled="this.isPdfDownloading"
          >
            <v-icon
              color="white"
              style="margin-top: -2.5px"
              v-if="!this.isPdfDownloading"
            >
              mdi-pdf-box
            </v-icon>
            <b-spinner small v-else></b-spinner>
            <span>Export to PDF</span>
          </b-button>
        </b-button-group>
        <v-treeview
          :search="search"
          :items="detailedJobDataSet"
          open-on-click
          open-all
          expand-icon="mdi-chevron-down"
        >
          <template v-slot:prepend="{ item }">
            <div v-if="item.children">
              <v-badge
                v-if="item.root"
                :content="item.count"
                color="info"
                overlap
                left
              >
                <v-icon>mdi-office-building</v-icon>
              </v-badge>
              <v-icon v-else>mdi-briefcase</v-icon>
            </div>
            <v-icon v-else="!item.children">mdi-briefcase-account</v-icon>
          </template>
          <template v-slot:label="{ item }">
            <span v-if="item.children">{{ item.name }}</span>
            <div v-else>
              <strong>{{ item.name }}</strong>
            </div>
          </template>
        </v-treeview>
      </v-app>
    </b-modal>
    <!-- modal for locations' details -->
    <b-modal
      ref="locationDetailsModal"
      hide-footer
      hide-header
      max-height="500"
      scrollable
      body-class="modal__detailed_stats"
      id="modal__location_details"
    >
      <v-app id="app__modal_loc" class="application__container">
        <v-sheet class="pa-4 info modal__toolbar">
          <v-btn
            small
            icon
            color="white"
            style="float: right"
            @click="
              () => {
                this.$refs.locationDetailsModal.hide();
              }
            "
          >
            <v-icon>mdi-window-close</v-icon>
          </v-btn>
          <h5 v-html="modalTitle" style="color: #fff"></h5>
          <v-text-field
            v-model="search"
            label="Search"
            prepend-inner-icon="mdi-magnify"
            dark
            flat
            solo-inverted
            hide-details
            clearable
            clear-icon="mdi-close-circle-outline"
          ></v-text-field>
        </v-sheet>
        <b-button-group style="margin: 2px 0">
          <download-excel
            v-bind:class="[
              'btn',
              'btn-success',
              'rounded-0',
              { disabled: this.isExcelDownloading },
            ]"
            :fetch="exportExcelLocationDetails"
            :fields="locationDetailHeaders"
            :before-generate="startDownload"
            :before-finish="finishDownload"
            worksheet="Location Statistics"
            :name="
              [
                'Location Statistics for ' +
                  this.category +
                  ' (' +
                  this.profession +
                  ') - ' +
                  this.specialization +
                  '--' +
                  this.brandName +
                  '.xls',
              ].toString()
            "
          >
            <v-icon
              color="white"
              style="margin-top: -2.5px"
              v-if="!this.isExcelDownloading"
            >
              mdi-file-excel-box
            </v-icon>
            <b-spinner small v-else></b-spinner>
            <span>Export to Excel</span>
          </download-excel>
          <b-button
            variant="info"
            squared
            @click="exportLocationDetailsText"
            :disabled="this.isPdfDownloading"
          >
            <v-icon
              color="white"
              style="margin-top: -2.5px"
              v-if="!this.isPdfDownloading"
            >
              mdi-pdf-box
            </v-icon>
            <b-spinner small v-else></b-spinner>
            <span>Export to PDF</span>
          </b-button>
        </b-button-group>
        <v-treeview
          :search="search"
          :items="detailedLocationDataSet"
          open-on-click
          open-all
          expand-icon="mdi-chevron-down"
        >
          <template v-slot:prepend="{ item }">
            <div v-if="item.children">
              <v-badge
                v-if="item.root"
                :content="item.count"
                color="info"
                overlap
                left
              >
                <v-icon>mdi-earth</v-icon>
              </v-badge>
              <v-icon v-else>mdi-city</v-icon>
            </div>
            <v-icon v-else="!item.children">mdi-home-account</v-icon>
          </template>
          <template v-slot:label="{ item }">
            <span v-if="item.children">{{ item.name }}</span>
            <div v-else>
              <strong>{{ item.name }}</strong>
            </div>
          </template>
        </v-treeview>
      </v-app>
    </b-modal>
  </div>
</template>

<script>
import { mapActions } from "vuex";
import profileMixins from "../mixins/profileMixins";
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";

export default {
  mixins: [profileMixins],
  props: ["brand", "brandName"],
  data() {
    return {
      isExcelDownloading: false,
      isPdfDownloading: false,
      modalTitle: null,
      search: null,
      fullNameLabel: this.$t("label.full_name"),
      locationDataSet: [],
      jobDataSet: [],
      detailedLocationDataSet: [],
      detailedJobDataSet: [],
      country: null,
      province: null,
      city: null,
      category: null,
      profession: null,
      specialization: null,
      jobDetailHeaders: {
        Count: "count",
        Category: "category",
        Profession: "profession",
        Specialization: "specialization",
        "Full Name": "fullname",
      },
      locationDetailHeaders: {
        Count: "count",
        Country: "country",
        "Province/Division": "province",
        City: "city",
        "Full Name": "fullname",
      },
      locationDataCols: [
        {
          label: this.$t("label.province"),
          field: "pv",
          firstSortType: "asc",
        },
        {
          label: this.$t("label.city"),
          field: "ct",
        },
        {
          label: this.$t("label.count"),
          field: "cnt",
          type: "number",
        },
      ],
      jobDataCols: [
        {
          label: this.$t("label.profession"),
          field: "pf",
          firstSortType: "asc",
        },
        {
          label: this.$t("label.specialization"),
          field: "sp",
        },
        {
          label: this.$t("label.count"),
          field: "cnt",
          type: "number",
        },
      ],
    };
  },
  computed: {
    locationData: function() {
      let locationDataRaw = this.$store.state.profile
        .tabular_location_statistics;

      locationDataRaw.map((item) => {
        this.locationDataSet.push(
          JSON.parse(
            '{"label": "' +
              item.label +
              '", "cnt": "' +
              Number(item.cnt) +
              '", "children": ' +
              item.children +
              "}"
          )
        );
      });

      return this.locationDataSet;
    },
    jobData: function() {
      let jobDataRaw = this.$store.state.profile.tabular_job_statistics;

      jobDataRaw.map((item) => {
        this.jobDataSet.push(
          JSON.parse(
            '{"label": "' +
              item.label +
              '", "cnt": "' +
              Number(item.cnt) +
              '", "children": ' +
              item.children +
              "}"
          )
        );
      });

      return this.jobDataSet;
    },
  },
  created() {
    this.getTabularStats();
  },
  methods: {
    ...mapActions("profile", [
      "get_tabular_location_statistics",
      "get_tabular_job_statistics",
      "get_detailed_location_stats",
      "get_detailed_job_stats",
    ]),

    getTabularStats() {
      let params = {
        api_token: this.$user.api_token,
        brand: this.brand.id,
        lang: this.$i18n.locale,
      };

      this.get_tabular_location_statistics(params).then(() => {
        this.$refs.location_table.expandAll();
        this.isLoading = false;
      });

      this.get_tabular_job_statistics(params).then(() => {
        this.$refs.job_table.expandAll();
        this.isLoading = false;
      });
    },
    showJobDetails: async function(params) {
      this.country = this.locationDataSet[params[0].sourceIndex].label.replace(
        "Country: ",
        ""
      );
      this.province = params[0].province;
      this.city = params[0].city;

      let call_params = {
        api_token: this.$user.api_token,
        brand: this.brand.id,
        lang: this.$i18n.locale,
        country: this.country,
        province: this.province,
        city: this.city,
      };

      await this.get_detailed_job_stats(call_params)
        .then(() => {
          this.isLoading = false;
          this.detailedJobDataSet = [];
          this.search = null;

          let jobDataRaw = this.$store.state.profile.detailed_job_stats;

          jobDataRaw.map((item) => {
            this.detailedJobDataSet.push(
              JSON.parse(
                '{"id": ' +
                  item.id +
                  ', "name": "' +
                  item.name +
                  '", "count": ' +
                  item.count +
                  ', "root": true, "children": ' +
                  item.children +
                  "}"
              )
            );
          });

          this.modalTitle =
            "<i class='fas fa-compass header__icon'></i> " +
            this.country +
            " <i class='fas fa-angle-double-right arrow__icon'></i> " +
            this.province +
            " <i class='fas fa-angle-double-right arrow__icon'></i> " +
            this.city +
            "<p class='modal_brand__title'> " +
            this.$t("label.job_statistics") +
            " (" +
            this.$t("label.brand") +
            ": " +
            this.brandName +
            ")</p>";
        })
        .then(() => {
          this.$refs.jobDetailsModal.show();
        });
    },
    showLocationDetails: async function(params) {
      this.category = this.jobDataSet[params[0].sourceIndex].label.replace(
        "Category: ",
        ""
      );
      this.profession = params[0].profession;
      this.specialization = params[0].specialization;

      let call_params = {
        api_token: this.$user.api_token,
        brand: this.brand.id,
        lang: this.$i18n.locale,
        category: this.category,
        profession: this.profession,
        specialization: this.specialization,
      };

      await this.get_detailed_location_stats(call_params)
        .then(() => {
          this.isLoading = false;
          this.detailedLocationDataSet = [];
          this.search = null;

          let locationDataRaw = this.$store.state.profile
            .detailed_location_stats;

          locationDataRaw.map((item) => {
            this.detailedLocationDataSet.push(
              JSON.parse(
                '{"id": ' +
                  item.id +
                  ', "name": "' +
                  item.name +
                  '", "count": ' +
                  item.count +
                  ', "root": true, "children": ' +
                  item.children +
                  "}"
              )
            );
          });

          this.modalTitle =
            "<i class='fas fa-wrench header__icon'></i> " +
            this.category +
            " <i class='fas fa-angle-double-right arrow__icon'></i> " +
            this.profession +
            " <i class='fas fa-angle-double-right arrow__icon'></i> " +
            this.specialization +
            "<p class='modal_brand__title'> " +
            this.$t("label.location_statistics") +
            " (" +
            this.$t("label.brand") +
            ": " +
            this.brandName +
            ")</p>";
        })
        .then(() => {
          this.$refs.locationDetailsModal.show();
        });
    },
    exportExcelJobDetails: async function() {
      const jobDetails = await axios.get("statistics/tabular/export/job", {
        params: {
          api_token: this.$user.api_token,
          brand: this.brand.id,
          lang: this.$i18n.locale,
          country: this.country,
          province: this.province,
          city: this.city,
          search: this.search,
        },
      });
      this.isExcelDownloading = false;
      this.isPdfDownloading = false;

      return jobDetails.data.length > 0 ? jobDetails.data : [];
    },
    exportExcelLocationDetails: async function() {
      const locationDetails = await axios.get(
        "statistics/tabular/export/location",
        {
          params: {
            api_token: this.$user.api_token,
            brand: this.brand,
            lang: this.$i18n.locale,
            category: this.category,
            profession: this.profession,
            specialization: this.specialization,
            search: this.search,
          },
        }
      );

      this.isExcelDownloading = false;
      this.isPdfDownloading = false;

      return locationDetails.data.length > 0 ? locationDetails.data : [];
    },
    startDownload() {
      this.isExcelDownloading = true;
    },
    finishDownload() {
      this.isExcelDownloading = false;
    },
    exportJobDetailsText: async function() {
      this.isPdfDownloading = true;

      var doc = new jsPDF("p", "pt", "Letter");
      const jobDetailsRaw = await this.exportExcelJobDetails();
      let jobDetails = [];

      if (jobDetailsRaw.length > 0) {
        jobDetailsRaw.map((jobItem) =>
          jobDetails.push([
            jobItem.count,
            jobItem.category,
            jobItem.profession,
            jobItem.specialization,
            jobItem.fullname,
          ])
        );

        doc.setFontSize(14);
        doc.setFontStyle("bold");
        doc.text(
          this.city + ", " + this.province + ", " + this.country,
          40,
          40
        );

        doc.setFontSize(12);
        doc.setTextColor(100);
        doc.text("Job Statistics (Brand: " + this.brandName + ")", 40, 60);

        doc.autoTable({
          head: [
            ["Count", "Category", "Profession", "Specialization", "Full Name"],
          ],
          body: jobDetails,
          margin: { top: 70, bottom: 50 },
          didDrawPage: function(data) {
            var str = "Page " + doc.internal.getNumberOfPages();
            doc.setFontSize(10);

            var pageSize = doc.internal.pageSize;
            var pageHeight = pageSize.height
              ? pageSize.height
              : pageSize.getHeight();
            doc.text(str, data.settings.margin.left, pageHeight - 10);
          },
        });

        doc.save(
          "Job Statistics for " +
            this.city +
            ", " +
            this.province +
            ", " +
            this.country +
            "--" +
            this.brandName +
            ".pdf"
        );
      }
    },
    exportLocationDetailsText: async function() {
      this.isPdfDownloading = true;

      var doc = new jsPDF("p", "pt", "Letter");
      const locationDetailsRaw = await this.exportExcelLocationDetails();
      let locationDetails = [];

      if (locationDetailsRaw.length > 0) {
        locationDetailsRaw.map((locationItem) =>
          locationDetails.push([
            locationItem.count,
            locationItem.country,
            locationItem.province,
            locationItem.city,
            locationItem.fullname,
          ])
        );

        doc.setFontSize(14);
        doc.setFontStyle("bold");
        doc.text(
          this.category +
            " (" +
            this.profession.toString().replace(/[\(\)]/gim, "") +
            ") - " +
            this.specialization,
          40,
          40
        );

        doc.setFontSize(12);
        doc.setTextColor(100);
        doc.text("Location Statistics (Brand: " + this.brandName + ")", 40, 60);

        doc.autoTable({
          head: [
            ["Count", "Country", "Province/Division", "City", "Full Name"],
          ],
          body: locationDetails,
          margin: { top: 70, bottom: 50 },
          didDrawPage: function(data) {
            var str = "Page " + doc.internal.getNumberOfPages();
            doc.setFontSize(10);

            var pageSize = doc.internal.pageSize;
            var pageHeight = pageSize.height
              ? pageSize.height
              : pageSize.getHeight();
            doc.text(str, data.settings.margin.left, pageHeight - 10);
          },
        });

        doc.save(
          "Location Statistics for " +
            this.category +
            " (" +
            this.profession.replace(/[\(\)]/gim, "") +
            ") - " +
            this.specialization +
            "--" +
            this.brandName +
            ".pdf"
        );
      }
    },
  },
};
</script>
