<template>
  <div class="animated fadeIn">
    <v-app id="tags__container">
      <v-card tile flat>
        <v-card-title>
          <v-row>
            <v-col>
              <span class="title font-weight-light text--secondary">
                {{ $t("label.activities") }}
              </span>
            </v-col>
            <v-spacer> </v-spacer>
            <v-col>
              <v-btn
                color="success"
                 v-b-modal.activity-modal
                 @click="onAdd"
                class="float-right"
                tile
              >
                <v-icon dark>mdi-newspaper-plus</v-icon>
                <!-- <v-icon small dark style="margin-top: 15px; margin-left: -3px;">
                  mdi-plus
                </v-icon> -->
                &nbsp;
                {{ $t("button.new") }} {{ $t("label.activity") }}
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
                  style="display:flex; justify-content: center; padding-top: 5px"
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
                  style="display:flex; justify-content: center; padding: 5px 0 0 5px"
                >
                  {{ $t("label.entries") }}
                </v-col>
              </v-row>
            </v-col>
            <v-spacer></v-spacer>
            <v-col cols="12" sm="12" md="4">
              <b-input-group class="float-right">
                <template v-slot:prepend>
                  <b-input-group-text
                    style="background-color: rgba(0,0,0,0.05); 
                      border-radius: 0;"
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

              <template v-slot:cell(index)="data">{{
                data.index + 1
              }}</template>

              <template v-slot:cell(activity_name)="data">
                <div style="margin-top: 10px">
                  <span v-html="listDisplay( data.item.activity_name )"></span>
                </div>
              </template>

              <template v-slot:cell(created_at)="data">
                <span>{{ data.item.created_at | toLocaleString }}</span>
              </template>

                <template v-slot:cell(actions)="data">
                  <div style="margin-top: 10px">
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
                          <v-icon small right>
                            mdi-chevron-down
                          </v-icon>
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
          <v-row>
            <v-spacer></v-spacer>
            <v-col>
              <b-pagination
              v-model="currentPage"
              :total-rows="totalRows"
              :per-page="perPage"
            ></b-pagination>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
      <ActivityModal :parent="this"></ActivityModal>
    </v-app>
  </div>
  <!-- <div class="animated fadeIn">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h4 class="card-title text-muted">{{ $t("label.activities") }}</h4>
          </div>
          <div class="col-md-6">
            <b-button
              variant="primary"
              v-b-modal.activity-modal
              class="float-right mb-3"
              @click="onAdd"
            >
              <i class="fa fa-plus" aria-hidden="true"></i>
              {{ $t("button.new") }}
            </b-button>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-md-9">
            <div class="float-left">
              <div class="form-inline">
                <label class="mr-sm-2" for="inline-form-custom-select-pref">{{
                  $t("button.show")
                }}</label>
                <b-form-select
                  id="inline-form-custom-select-pref"
                  class="mb-2 mr-sm-2 mb-sm-0"
                  :options="showEntriesOpt"
                  v-model="perPage"
                />
                {{ $t("label.entries") }}
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
              <b-form-input
                v-model="filter"
                type="search"
                :placeholder="$t('table.search')"
              ></b-form-input>
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

              <template v-slot:cell(index)="data">{{
                data.index + 1
              }}</template>

              <template v-slot:cell(activity_name)="data">
                <span v-html="listDisplay( data.item.activity_name )"></span>
              </template>

              <template v-slot:cell(created_at)="data">
                <span>{{ data.item.created_at | toLocaleString }}</span>
              </template>

              <template v-slot:cell(actions)="data">
                <span>
                  <b-button
                    size="sm"
                    variant="success"
                    @click="onEdit(data.item)"
                  >
                    <i class="fas fa-edit"></i>
                    {{ $t("button.edit") }}
                  </b-button>
                  <b-button
                    size="sm"
                    variant="danger"
                    @click="onDelete(data.item)"
                  >
                    <b-spinner
                      v-if="data.item.is_loading"
                      small
                      label="Small Spinner"
                      type="grow"
                    ></b-spinner>
                    <span v-else>
                      <i class="fas fa-trash"></i>
                      {{ $t("button.delete") }}
                    </span>
                  </b-button>
                </span>
              </template>

              <template slot="empty">
                <section class="section">
                  <div class="content has-text-grey has-text-centered">
                    <center>{{ $t("table.empty") }}</center>
                  </div>
                </section>
              </template>
            </b-table>
          </div>
          <div class="col-md-12">
            <b-pagination
              v-model="currentPage"
              :total-rows="totalRows"
              :per-page="perPage"
            ></b-pagination>
          </div>
        </div>
        <ActivityModal :parent="this"></ActivityModal>
      </div>
    </div>
  </div> -->
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import engagementMixins from "./mixins/engagementMixins";
import ActivityModal from "./modals/activity.modal.vue";

export default {
  mixins: [engagementMixins],
  components: { ActivityModal },
  data() {
    return {
      tableHeaders: [
        {
          key: "activity_name",
          label: this.$t("label.activity"),
          thClass: "text-left",
          thStyle: { width: "80%" },
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
    };
  },

  created() {
    this.loadItems().catch((errors) => {
      console.log(errors);
    });
  },

  computed: {
    ...mapGetters({
      items: "actions/get_activity_items",
      activityStatus: "actions/get_activity_status",
    }),
  },

  methods: {
    ...mapActions("actions", ["get_activity", "delete_activity"]),

    async loadItems() {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
      };

      this.get_activity(params).then((_) => {
        this.isLoading = false;
      });
    },
    onAdd() {
      this.form.reset();
      this.btnloading = false;
      this.form.action = "Add";
    },
    onEdit(item) {
      this.form.reset();
      this.form.id = item.id;
      this.form.activity =
        this.$user.locale == "en" ? item.is_english : item.is_italian;
      this.form.action = "Update";
      this.itemSelected = item;
      this.form.default = item.activity_name;
      this.$bvModal.show("activity-modal");
    },
    onDelete(item) {
      this.$bvModal
        .msgBoxConfirm(
         this.$t( "question_record_delete") + `${item.activity_name}` + this.$t( "from") +  this.$t( "label.activity") + this.$t( "records"),
          {
            title: this.$t( "confirmation_record_delete"),
            okVariant: "danger",
            okTitle: this.$t( "yes"),
            cancelTitle: this.$t( "no"),
            hideHeaderClose: false,
            centered: true
          }
        )
        .then((value) => {
          if (value) {
            item.is_loading = true;
            let params = {
              api_token: this.$user.api_token,
              locale: this.language,
              id: item.id,
            };

            this.delete_activity(params)
              .then((resp) => {
                item.is_loading = false;
                if (this.activityStatus) {
                  this.loadItems();
                  this.makeToast(
                    "danger",
                    this.$t( "delete_record"),
                    `${item.activity_name}` + this.$t( "delete_record_message") + this.$t( "from") +  this.$t( "label.activity") + this.$t( "records")
                  );
                }
              })
              .catch((error) => {
                this.form.errors.record(error.response.data.errors);

                this.makeToast(
                  "danger",
                  this.$t( "inused_alert"),
                  this.$t( "unable_message1") + `${item.activity_name}` + this.$t( "unable_message2" ),
                  `${item.activity_name} ` + this.$t( "delete_record_message") + this.$t( "from") +  this.$t( "label.activity") + this.$t( "records")
                );
                item.is_loading = false;
              });
          }
        })
        .catch((err) => {
          this.makeToast(
            "danger",
            this.$t('unexpected_error'),
            this.$t('unexpected_error_message')
          );
        });
    },
    listDisplay( array ) {
      let trans = '';
      let spliter = '';
        spliter = array.split('(')[0];
        trans = array.split('(')[1];
        if( trans !== undefined ){
          if( trans.length > 0 ) {
            spliter = spliter + '<small style="color:red">(' + trans + '</small>';
            array = spliter;
          }
        }
      return array;
    }
  },
};
</script>
