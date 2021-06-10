<template>
<div class="animated fadeIn">
    <v-app id="tags__container">
      <v-card tile flat>
        <v-card-title>
          <v-row>
            <v-col>
              <span class="title font-weight-light text--secondary">
                {{$t('label.administrative')}}
              </span>
            </v-col>
            <v-spacer> </v-spacer>
            <v-col>
              <v-btn
                color="success"
                 v-b-modal.administrative-modal
                 @click="onAdd"
                class="float-right"
                tile
              >
                <v-icon dark>mdi-newspaper-plus</v-icon>
                &nbsp;
                {{ $t("button.new") }} {{$t('label.administrative')}}
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

              <template v-slot:cell(index)="data">
                <div style="margin-top: 10px">
                  {{ data.index + 1 }}
                </div>
              </template>

              <template v-slot:cell(action_name)="data">
                <div style="margin-top: 10px">
                  <span v-html="listDisplay(data.item.action_name)"></span>
                </div>
              </template>

              <template v-slot:cell(created_at)="data">
                <div style="margin-top: 10px">
                  <span>{{ data.item.created_at | toLocaleString }}</span>
                </div>
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
      <AdministrativeModal :parent="this"></AdministrativeModal>
    </v-app>
  </div>
  <!-- <div class="animated fadeIn">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h4 class="card-title text-muted">{{$t('label.administrative')}}</h4>
          </div>
          <div class="col-md-6">
            <b-button
              variant="primary"
              v-b-modal.administrative-modal
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

              <template v-slot:cell(action_name)="data">
                <span v-html="listDisplay(data.item.action_name)"></span>
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
        </div>
        <AdministrativeModal :parent="this"></AdministrativeModal>
      </div>
    </div>
  </div> -->
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import engMixins from "./mixins/engagementMixins.js";

import AdministrativeModal from "./modals/administrative.modal.vue";

export default {
  mixins: [engMixins],
  components: { AdministrativeModal },
  data() {
    return {
      tableHeaders: [
        {
          key: "index",
          label: "SL",
          thStyle: { width: "10%" }
        },
        {
          key: "action_name",
          label: this.$t("table.action"),
          thClass: "text-left",
          thStyle: { width: "50%" },
          sortable: true
        },
        {
          key: "created_at",
          label: this.$t("table.created_at"),
          thClass: "text-right",
          thStyle: { width: "20%" },
          tdClass: "text-right"
        },
        {
          key: "actions",
          label: this.$t("table.action"),
          thClass: "text-center",
          thStyle: { width: "20%" },
          tdClass: "text-center"
        }
      ]
    };
  },

  created() {
    this.loadItems().catch(errors => {
      console.log(errors);
    });
  },

  computed: {
    ...mapGetters({
      items: "actions/get_administrative_items",
      actionStatus: "actions/get_administrative_status"
    })
  },

  methods: {
    ...mapActions("actions", ["get_administrative", "delete_administrative"]),

    async loadItems() {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale
      };

      this.get_administrative(params).then(_ => {
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
      this.form.action = "Update";
      this.form.default = item.action_name;
      if (this.$i18n.locale === "en") {
        this.form.administrative = item.is_english;
      } else if (this.$i18n.locale === "it") {
        this.form.administrative = item.is_italian;
      } else {
        this.form.administrative = item.is_german;
      }

      this.itemSelected = item;

      // Open Modal
      this.$bvModal.show("administrative-modal");
    },
    onDelete(item) {
      this.$bvModal
        .msgBoxConfirm(
          this.$t( "question_record_delete") + `${item.action_name}` + this.$t( "from") +  this.$t( "label.administrative") + this.$t( "records"),
          {
            title: this.$t( "confirmation_record_delete"),
            okVariant: "danger",
            okTitle: this.$t( "yes"),
            cancelTitle: this.$t( "no"),
            hideHeaderClose: false,
            centered: true
          }
        )
        .then(value => {
          if (value) {
            item.is_loading = true;
            let params = {
              api_token: this.$user.api_token,
              locale: this.language,
              id: item.id
            };

            this.delete_administrative(params)
              .then(resp => {
                item.is_loading = false;
                if (this.actionStatus) {
                  this.loadItems();
                  this.makeToast(
                    "danger",
                    this.$t( "delete_record"),
                    `${item.action_name}` + this.$t( "delete_record_message") + this.$t( "from") +  this.$t( "label.administrative") + this.$t( "records")
                  );
                }
              })
              .catch(error => {
                this.form.errors.record(error.response.data.errors);
                this.makeToast(
                  `danger`,
                  this.$t( "inused_alert"),
                  this.$t( "unable_message1") + `${item.action_name}` + this.$t( "unable_message2" ),
                  `${item.action_name} ` + this.$t( "delete_record_message") + this.$t( "from") +  this.$t( "label.administrative") + this.$t( "records")
                );
                item.is_loading = false;
              });
          }
        })
        .catch(err => {
          // An error occurred
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
  }
};
</script>