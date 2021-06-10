<template>
  <div>
    <div class="card border-0">
      <div class="card-body">
        <div class="row">
          <div class="col-md-10">
            <h4 class="card-title">
              {{ $t("course_type_title") }}
              <small class="text-muted text-capitalize"> </small>
            </h4>
          </div>
          <div class="col-md-2">
            <v-btn
              color="success"
              class="float-right"
              block
              tile
              @click="showModal"
            >
              <i class="fas fa-notes-medical"></i>
              &nbsp; {{ $t("button.new_type") }}
            </v-btn>
          </div>
        </div>

        <div class="row mt-5">
          <v-col cols="12" sm="6" md="6">
            <v-col
              class="caption font-weight-regular text--secondary text-right"
              style="display: flex; position: absolute; margin-left: -10px"
            >
              {{ $t("button.show") }}
              <b-form-select
                :options="showEntriesOpt"
                v-model="perPage"
                style="
                  border-radius: 0;
                  width: 13% !important;
                  margin-top: -8px;
                  margin-left: 5px;
                  margin-right: 5px;
                "
              />{{ $t("label.entries") }}
            </v-col>
          </v-col>
          <v-col cols="12" sm="6" md="6">
            <div class="float-right">
              <b-form inline style="margin-right: -8px">
                <b-input-group class="mb-2 mr-sm-2">
                  <template #append>
                    <b-input-group-text>
                      <i class="fa fa-search" aria-hidden="true"></i>
                    </b-input-group-text>
                  </template>
                  <b-form-input
                    v-model="filter"
                    :placeholder="$t('search_here')"
                    style="width: 300px"
                    type="search"
                  />
                </b-input-group>
              </b-form>
            </div>
          </v-col>
        </div>

        <div class="row">
          <div class="col-md-12 mb-0">
            <b-overlay
              id="overlay-background"
              :show="isLinked"
              :variant="'light'"
              opacity="0.85"
              :blur="'1px'"
              rounded="sm"
            >
              <b-table
                striped
                show-empty
                :empty-text="$t('no_record_found')"
                :fields="tableHeaders"
                :current-page="currentPage"
                :per-page="perPage"
                :busy="isLoading"
                :items="itemTypes"
                v-model="termTypeTable"
                :sort-by.sync="sort_by"
                :sort-desc.sync="sort_desc"
              >
                <template v-slot:table-busy>
                  <div class="d-flex justify-content-center p-2">
                    <b-spinner label="Small Loading..."></b-spinner>
                  </div>
                </template>

                <template v-slot:cell(index)="data">
                  {{ data.item.row_index }}
                </template>

                <template v-slot:cell(name)="data">
                  <span v-html="data.item.name"> </span>
                </template>

                <template v-slot:cell(created_at)="data">
                  <span>{{ data.item.created_at }}</span>
                </template>

                <template v-slot:cell(actions)="data">
                  <v-btn color="#c8ced3" @click="onEdit(data.item)">                     
                     <b-icon
                        icon="pencil-square"
                        variant="success"
                        style='margin-right:10px;' />
                    Edit
                  </v-btn>
                  <v-btn color="#c8ced3" @click="onDelete(data.item)">
                    <b-icon
                        icon="trash-fill"
                        variant="danger"
                        style="margin-right:10px;" />
                    Delete
                  </v-btn>
                </template>
              </b-table>
            </b-overlay>
          </div>

          <div class="col-md-12 mb-0">
            <v-row>
              <v-spacer>
                <v-col cols="12" sm="6" md="4">
                  <span>
                    Showing
                    <strong v-text="getStartPage"></strong>
                    to
                    <strong v-text="getEndPage" />
                    of
                    <strong v-text="total_rows" />
                    {{ $t("label.entries") }}
                  </span>
                </v-col>
              </v-spacer>
              <v-col>
                <v-spacer>
                  <b-pagination
                    v-model="currentPage"
                    :total-rows="total_rows"
                    :per-page="perPage"
                    style="float: right"
                  ></b-pagination>
                </v-spacer>
              </v-col>
            </v-row>
          </div>
        </div>
      </div>
    </div>
    <courseTypeModal
      :initTable="initTable"
      :typeForm="typeForm"
      :course_item="course_item"
    />
  </div>
</template>
<script>
import { mapActions, mapGetters } from "vuex";

import courseMixin from "./mixins/courseMixin";
import toast from "../../helpers/toast.js";
import courseTypeModal from "./modals/course-type-modal";

import {
  softDelete,
  searchItemType,
  fetchList,
  sortList,
} from "./../../api/item_type.js";

export default {
  mixins: [courseMixin, toast],
  components: {
    courseTypeModal,
  },
  data() {
      return {
        isLinked: false,
        btnloading: false,
        linkedBrands: [],
        termTypeTable:[],
        itemTypes : [],
        itemTempRow: [],
        isLoading: true,
        filter : '',
        typeForm : 'add',
        course_item : {},
        sort_by : 'index',
        sort_desc : false,
        actions: [
          {
            value: "edit",
            title: this.$t("button.edit"),
            icon: "pencil-square",
            variant: "success",
          },
          {
            value: "delete",
            title: this.$t("button.delete"),
            icon: "trash-fill",
            variant: "danger",
          }
        ],
        tableHeaders: [
          {
            key: "index",
            thClass: "text-left",
            label: "#",
            thStyle: { width: "5%" },
            sortable: true,
          },
          {
            key: "name",
            label: this.$t("table.course_type"),
            thClass: "text-left wide-column",
            thStyle: { width: "50%" },
            sortable: true,
          },
          {
            key: "created_at",
            label: this.$t("table.created_at"),
            thClass: "text-right",
            thStyle: { width: "20%" },
            tdClass: "text-right",
            sortable: true,
          },
          {
            key: "actions",
            label: this.$t("table.action"),
            thClass: "text-center",
            thStyle: { width: "20%" },
            tdClass: "text-center",
          },
        ],
    }
  },
  created() {
    this.initTable();
  },
  watch: {
    filter(value) {
      this.searchType();
    },
    sort_desc(value) {
      this.sortRows(value);
    },
  },
  computed: {
    getStartPage() {
      if (this.currentPage == 1) {
        return 1;
      } else if (this.currentPage > 1) {
        return parseInt(this.currentPage - 1) * parseInt(this.perPage) + 1;
      }

      return 1;
    },
    getEndPage() {
      if (this.currentPage == 1) {
        return this.termTypeTable.length;
      } else if (this.currentPage > 1) {
        return (
          this.termTypeTable.length +
          parseInt(this.currentPage - 1) * parseInt(this.perPage)
        );
      }

      return 1;
    },
  },
  methods: {
    initTable() {
      this.isLoading = true;
      this.itemTypes = [];
      var $this = this;
      this.loadCourseType().then((res) => {
        this.isLoading = false;
        if (res) {
          var row_arr = [];
          if (res.length) {
            for (var i in res) {
              var obj = res[i];
              Object.assign(obj, { row_index: parseInt(i) + 1 });
              row_arr.push(obj);
            }

            this.total_rows = res.length;
            this.itemTypes = row_arr;
          }
        }
      });
    },
    onEdit(item) {
      this.typeForm = "Update";
      this.course_item = item;
      this.$bvModal.show("course-type-modal");
    },
    onDelete(item) {
      this.deleteCourseType(item).then((res) => {
        this.deleteToast(res.message, "Course Type");
        this.initTable();
      });
    },
    showModal() {
      this.typeForm = "Add";
      this.$bvModal.show("course-type-modal");
    },
    searchType: function () {
      var filter = this.filter;
      this.isLoading = true;
      this.searchCourseType(filter).then((res) => {
        this.isLoading = false;
        var row_arr = [];
        if (res.length) {
          for (var i in res.reverse()) {
            var obj = res[i];
            Object.assign(obj, { row_index: parseInt(i) + 1 });
            row_arr.push(obj);
          }
        }
        this.itemTypes = row_arr;
        this.total_rows = res.length;
      });
    },
    sortRows: function (bool) {
      var filter = this.filter;
      var obj = {
        sortcol: this.sort_by,
        sort: bool,
        filter: filter,
      };

      this.sortList(obj).then((res) => {
        var row_arr = [];
        if (res.length) {
          for (var i in res.reverse()) {
            var obj = res[i];
            Object.assign(obj, { row_index: parseInt(i) + 1 });
            row_arr.push(obj);
          }
        }
        this.itemTypes = row_arr;
        this.total_rows = res.length;
      });
    },
  },
};
</script>
