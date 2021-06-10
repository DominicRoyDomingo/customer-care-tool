<template>
  <div class="animated fadeIn">
    <v-app id="tags__container">
      <v-card tile flat>
        <v-card-title>
          <v-row>
            <v-col>
              <span class="title font-weight-light text--secondary">
                {{ $t("label.jobs") }}
                <small class="text-muted">{{ $t("label.category") }}</small>
              </span>
            </v-col>
            <v-spacer> </v-spacer>
            <v-col>
              <v-btn
                v-b-modal.job-category-modal
                color="success"
                @click="onAdd"
                class="float-right"
                tile
              >
                <v-icon dark>mdi-newspaper-plus</v-icon>
                <!-- <v-icon small dark style="margin-top: 15px; margin-left: -3px;">
                  mdi-plus
                </v-icon> -->
                &nbsp;
                {{ $t("button.new") }} {{ $t("label.job_category") }}
              </v-btn>
            </v-col>
          </v-row>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" sm="12" md="2">
              <v-row no-gutters>
                <v-col
                  cols="3"
                  class="caption font-weight-regular text--secondary text-right"
                  style="
                    display: flex;
                    justify-content: center;
                    padding-top: 5px;
                  "
                >
                  {{ $t("button.show") }}
                </v-col>
                <v-col cols="6">
                  <b-form-select
                    :options="showEntriesOpt"
                    v-model="perPage"
                    style="border-radius: 0"
                  />
                </v-col>
                <v-col
                  cols="3"
                  class="caption font-weight-regular text--secondary"
                  style="
                    display: flex;
                    justify-content: center;
                    padding: 5px 0 0 5px;
                  "
                >
                  {{ $t("label.entries") }}
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="12" sm="6" md="2">
              <b-input-group-prepend>
                <v-menu offset-y :rounded="false">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      color="info"
                      dark
                      v-bind="attrs"
                      v-on="on"
                      tile
                      depressed
                      style="height: 33px; min-width: 64px; padding: 0 16px; margin-right: 7px; margin-top: -5px;"
                    >
                      <v-icon :size="18">
                        mdi-filter-menu
                      </v-icon>
                    </v-btn>
                  </template>
                  <v-list dense class="profile__row-menu">
                    <v-list-item-group color="primary">
                      <v-list-item v-for="(option, index) in noTranslationsOptions" :key="index">
                        <v-list-item-content @click="sortbyLang(option.value)">
                          <v-list-item-title>
                            {{ option.text }}
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list-item-group>
                  </v-list>
                </v-menu>
              </b-input-group-prepend>
            </v-col>
            <v-spacer></v-spacer>
            <v-col cols="12" sm="12" md="4">
              <b-input-group class="float-right">
                <template v-slot:prepend>
                  <b-input-group-text
                    style="
                      background-color: rgba(0, 0, 0, 0.05);
                      border-radius: 0;
                    "
                  >
                    <v-icon size="21">mdi-magnify</v-icon>
                  </b-input-group-text>
                </template>
                <b-form-input
                  v-model="filter"
                  :placeholder="$t('search_here')"
                  type="search"
                  style="border-radius: 0; border-left: 0"
                ></b-form-input>
              </b-input-group>
            </v-col>
          </v-row>
          <v-row>
            <v-container fluid>
              <b-table
                striped
                show-empty
                :fields="tableHeaders"
                :per-page="perPage"
                :busy="isLoading"
                :items="categories"
              >
                <!-- <template v-slot:table-busy>
                  <div class="d-flex justify-content-center p-2">
                    <b-spinner label="Small Loading..."></b-spinner>
                  </div>
                </template> -->

                <template v-slot:cell(category_name)="data">
                  <div style="margin-top: 10px">
                    <span
                      class="text-left text--secondary"
                    >
                      {{ data.item.category_name }}
                      <small
                        v-if="data.item.convertion == true"
                        style="color:red"
                      >
                        (No {{ data.item.language }} translation yet)</small
                      >
                    </span>
                  </div>
                </template>

                <template v-slot:cell(index)="data">{{
                  data.index + 1
                }}</template>

                <!-- <template v-slot:cell(category_name)="data">
                  <span
                    v-html="listDisplay(data.item.category_name, data.item)"
                  >
                  </span>
                </template> -->

                <template v-slot:cell(created_at)="data">
                  <span>{{ data.item.created_at | toLocaleString }}</span>
                </template>

                <template v-slot:cell(actions)="data">
                  <div>
                    <v-menu offset-y>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          color="#c8ced3"
                          light
                          v-bind="attrs"
                          v-on="on"
                          tile
                          depressed
                          small
                        >
                          {{ $t("button.more_actions") }}
                          <v-icon small right> mdi-chevron-down </v-icon>
                        </v-btn>
                      </template>
                      <v-list dense class="profile__row-menu">
                        <v-list-item-group color="primary">
                          <v-list-item @click="onEdit(data.item)">
                            <v-list-item-icon>
                              <v-icon color="yellow darken-3">
                                mdi-map-marker-check
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("button.edit") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="onDelete(data.item)">
                            <v-list-item-icon>
                              <v-icon color="red lighten-1">
                                mdi-map-marker-remove
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("button.delete") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                        </v-list-item-group>
                      </v-list>
                    </v-menu>
                  </div>
                </template>

                <template slot="empty">
                  <section class="section">
                    <div class="content has-text-grey has-text-centered">
                      <center>{{ $t("table.empty") }}</center>
                    </div>
                  </section>
                </template>
              </b-table>
            </v-container>
          </v-row>
          <div class="row mt-0" v-if="total_rows > 0">
            <div class="col-md-6">
              <span>
                {{ $t("showing") }}
                <strong v-text="showing.from" />
                {{ $t("to") }}
                <strong v-text="showing.to" />
                of
                <strong v-text="showing.total" />
                {{ $t("label.entries") }}
              </span>
            </div>
            <div class="col-md-6">
              <b-pagination
                v-model="currentPage"
                :total-rows="total_rows"
                :per-page="perPage"
                align="right"
              />
            </div>
          </div>
          <!-- <v-row>
            <v-spacer>
              <v-col cols="12" sm="6" md="4">
                <span>
                  Showing
                  <strong v-text="categoryOption.length" />
                  of
                  <strong v-text="totalRows" />
                  {{ $t("label.entries") }}
                </span>
              </v-col>
            </v-spacer>
            <v-col>
              <b-pagination
                v-if="!this.isLoading"
                v-model="currentPage"
                :total-rows="totalRows"
                :per-page="perPage"
              ></b-pagination>
            </v-col>
          </v-row> -->
        </v-card-text>
        <!-- <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h4 class="card-title">
              {{$t('label.jobs')}}
              <small class="text-muted">{{$t('label.category')}}</small>
            </h4>
          </div>
          <div class="col-md-6">
            <b-button
              variant="primary"
              v-b-modal.job-category-modal
              class="float-right mb-3"
              @click="onAdd"
            >
              <i class="fa fa-plus" aria-hidden="true"></i>
              {{$t('button.new')}}
            </b-button>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-md-9">
            <div class="float-left">
              <div class="form-inline">
                <label class="mr-sm-2" for="inline-form-custom-select-pref">{{$t('button.show')}}</label>
                <b-form-select
                  id="inline-form-custom-select-pref"
                  class="mb-2 mr-sm-2 mb-sm-0"
                  :options="showEntriesOpt"
                  v-model="perPage"
                />
                {{$t('label.entries')}}
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <b-input-group>
              <template v-slot:append>
                <b-input-group-text>
                  <i class="fa fa-search" aria-hidden="true"></i>
                </b-input-group-text>
              </template>
              <b-form-input v-model="filter" type="search" :placeholder="$t( 'search_here' )"></b-form-input>
            </b-input-group>
          </div>
          <div class="col-md-12 mt-3">
            <b-table
              striped
              show-empty
              :fields="tableHeaders"
              :current-page="currentPage"
              :per-page="perPage"
              :filter="filter"
              :busy="isLoading"
              :items="items"
            >
              <template v-slot:table-busy>
                <div class="d-flex justify-content-center p-2">
                  <b-spinner label="Small Loading..."></b-spinner>
                </div>
              </template>

              <template v-slot:cell(index)="data">{{ data.index + 1 }}</template>

              <template v-slot:cell(category_name)="data">
                <span v-html="listDisplay(data.item.category_name)"></span>
              </template>

              <template v-slot:cell(created_at)="data">
                <span>{{ data.item.created_at | toLocaleString }}</span>
              </template>

              <template v-slot:cell(actions)="data">
                <span>
                  <b-button size="sm" variant="success" @click="onEdit(data.item)">
                    <i class="fas fa-edit"></i>
                    {{$t('button.edit')}}
                  </b-button>
                  <b-button size="sm" variant="danger" @click="onDelete(data.item)">
                    <b-spinner v-if="data.item.is_loading" small label="Small Spinner" type="grow"></b-spinner>
                    <span v-else>
                      <i class="fas fa-trash"></i>
                      {{$t('button.delete')}}
                    </span>
                  </b-button>
                </span>
              </template>
            </b-table>
          </div>
          <div class="col-md-12">
            <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage"></b-pagination>
          </div>
        </div> -->

        <!-- category Modal Here -->
        <jobCategoryModal
          :parent="this"
          @done-success="loadItems"
        ></jobCategoryModal>

        <!-- profession Modal Here -->
        <jobProfessionModal :parent="this"></jobProfessionModal>
      </v-card>
    </v-app>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import jobMixins from "./mixins/jobMixins.js";
import jobProfessionModal from "./modals/job-profession.modal.vue";
import jobCategoryModal from "./modals/job-category.modal.vue";

export default {
  components: {
    jobCategoryModal,
    jobProfessionModal,
  },
  mixins: [jobMixins],
  data() {
    return {
      tableHeaders: [
        {
          key: "index",
          label: "SL",
          thStyle: { width: "10%" },
        },
        {
          key: "category_name",
          label: this.$t("table.job_category"),
          thClass: "text-left",
          thStyle: { width: "50%" },
          sortable: true,
        },
        {
          key: "created_at",
          label: this.$t("table.created_at"),
          thClass: "text-right",
          thStyle: { width: "20%" },
          tdClass: "text-right",
        },
        {
          key: "actions",
          label: this.$t("table.action"),
          thClass: "text-center",
          thStyle: { width: "20%" },
          tdClass: "text-center",
        },
      ],

    };
  },

  created() {
    this.loadItems().catch((errors) => {
      console.log(errors);
    });
  },

  watch: {
    filter(value) {
      if (!value) {
        this.isLinked = true;
        this.currentPage = 1;
        this.loadItems();
      }
    },

    currentPage(value) {
      this.isLinked = true;
      this.loadItems();
    },

    perPage() {
      this.isLinked = true;
      this.loadItems();
    },
  },

  computed: {
    ...mapGetters({
      items: "jobs/get_job_items",
    }),
  },

  methods: {
    ...mapActions("jobs", ["get_jobs", "delete_job_category"]),

    async loadItems() {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        page: this.currentPage,
        filter: this.filter,
        type: "category",
        sortbyLang: this.sortbyLangId,
        perPage: this.perPage,
        brand_id: this.$brand.id,
      };
      axios
        .get("/api/jobs", { params })
        .then((resp) => {
          let data = resp.data;
          this.categories = data.data;
          console.log(this.categories);
          this.total_rows = data.total;

          this.showing.to = data.to;
          this.showing.from = data.from;
          this.showing.total = data.total;
        })
        .finally(() => {
          this.isLinked = false;
          this.isLoading = false;
        });
      // this.get_jobs(params).then((_) => {
      //   this.isLoading = false;
      // });
    },

    sortbyLang(value) {
      this.sortbyLangId = value;
      this.loadItems();
    },

    onAdd() {
      this.form.reset();
      this.btnloading = false;
      this.form.action = "Add";
    },

    onEdit(item) {
      this.form.reset();
      this.form.id = item.id;
      this.form.category = item.category_name;
      this.$user.locale == "en" ? item.is_english : item.is_italian;

      this.form.default = item.category_name;
      if (this.$user.locale == "en") {
        this.form.language = "English";
        if (item.is_english == null) {
          this.form.convertion == true;
        } else {
          this.form.convertion == false;
        }
      }
      if (this.$user.locale == "it") {
        this.form.language = "Italian";
        if (item.is_italian == null) {
          this.form.convertion == true;
        } else {
          this.form.convertion == false;
        }
      }
      if (this.$user.locale == "de") {
        this.form.language = "German";
        if (item.is_german == null) {
          this.form.convertion == true;
        } else {
          this.form.convertion == false;
        }
      }
      if (this.$user.locale == "ph-fil") {
        this.form.language = "Filipino";
        if (item.is_filipino == null) {
          this.form.convertion == true;
        } else {
          this.form.convertion == false;
        }
      }
      if (this.$user.locale == "ph-bis") {
        this.form.language = "Visayan";
        if (item.is_visayan == null) {
          this.form.convertion == true;
        } else {
          this.form.convertion == false;
        }
      }

      this.form.action = "Update";
      this.form.type = "category";

      this.itemSelected = item;

      // Open Modal
      this.$bvModal.show("job-category-modal");
    },

    onShowDescription(item, index) {
        this.category_name = item.category_name;
        let descript = item.content;
        if( item.content === '' || item.content === null || item.content === 'null' ) {
          descript = '<i><small>No Description</small></i>';
        }
        this.description = descript;
        this.default = item.category_name;
        this.language = item.language;
        this.convertion = item.convertion;
        this.$bvModal.show("job-category-modal");
    },

    onDelete(item) {
      let vm = this;
      $.confirm({
        title: vm.$t("confirmation_record_delete"),
        content:
          vm.$t("question_record_delete") +
          `${item.category_name}` +
          vm.$t("from") +
          vm.$t("label.category") +
          vm.$t("records") +
          "?",
        type: "red",
        typeAnimated: true,
        columnClass: "medium",
        buttons: {
          yes: {
            text: vm.$t("yes"),
            btnClass:
              "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
            action: function (value) {
              if (value) {
                item.is_loading = true;
                let params = {
                  api_token: vm.$user.api_token,
                  locale: vm.language,
                  id: item.id,
                };
                vm.delete_job_category(params)
                  .then((resp) => {
                    item.is_loading = false;
                    if (resp.data == false) {
                      vm.form.errors.record(error.response.data.errors);
                      vm.makeToast(
                        `danger`,
                        vm.$t("inused_alert"),
                        vm.$t("unable_message1") +
                          `${item.category_name}` +
                          vm.$t("unable_message2"),
                        `${item.category_name} ` +
                          vm.$t("delete_record_message") +
                          vm.$t("from") +
                          vm.$t("label.category") +
                          vm.$t("records")
                      );
                      item.is_loading = false;
                    } else {
                      vm.loadItems();
                      vm.makeToast(
                        "danger",
                        vm.$t("delete_record"),
                        `${item.category_name}` +
                          vm.$t("delete_record_message") +
                          vm.$t("label.category") +
                          vm.$t("records")
                      );
                    }
                  })
                  .catch((error) => {
                    console.log(error);
                  });
              }
            },
          },
          no: {
            text: vm.$t("no"),
            btnClass:
              "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
            action: function () {},
          },
        },
      });
    },

    listDisplay(array, item) {
      let trans = "";
      let spliter = "";
      spliter = array.split("(")[0];
      trans = array.split("(")[1];
      if (trans !== undefined) {
        if (trans.length > 0) {
          // if( spliter === null || spliter === '' || spliter === undefined) {
          let def = item.category.split(":")[1];
          let def1 = def.replace('"}', "");
          spliter = def1.replace('"', "");
          // }
          spliter = spliter + '<small style="color:red">(' + trans + "</small>";
          array = spliter;
        }
      }
      return array;
    },
  },
};
</script>
