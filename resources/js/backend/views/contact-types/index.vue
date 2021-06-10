<template>
  <div class="animated fadeIn">
    <v-app id="tags__container">
      <v-card tile flat>
        <v-card-title>
          <v-row>
            <v-col>
              <span class="title font-weight-light text--secondary">
                {{$t('label.contact_types')}}
              </span>
            </v-col>
            <v-spacer> </v-spacer>
            <v-col>
              <v-btn
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
                {{$t('button.new')}} {{$t('label.contact_types')}}
              </v-btn>
            </v-col>
          </v-row>
        </v-card-title>

        <!-- <div class="row">
          <div class="col-md-6">
            <span class="title font-weight-light text--secondary">{{$t('label.contact_types')}}</span>
          </div>
          <div class="col-md-6">
            <b-button
              variant="primary"
              v-b-modal.contact-type-modal
              class="float-right mb-3"
              @click="onAdd"
            >
              <i class="fa fa-plus" aria-hidden="true"></i>
              {{$t('button.new')}}
            </b-button>
          </div>
        </div> -->
        <v-card-text>
          <v-row>
             <v-col  cols="12" sm="6" md="2"
                class="caption font-weight-regular text--secondary text-right"
                style="display:flex; padding-top: 15px"
              >
                {{ $t("button.show") }}
                <b-form-select
                  :options="showEntriesOpt"
                  v-model="perPage"
                  style="border-radius: 0; width: 40% !important; margin-top: -8px; margin-left: 5px; margin-right: 5px"
                />{{ $t("label.entries") }}
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
                v-model="contactTypeTable"
              >
                <template v-slot:table-busy>
                  <div class="d-flex justify-content-center p-2">
                    <b-spinner label="Small Loading..."></b-spinner>
                  </div>
                </template>

                <template v-slot:cell(index)="data">
                  <div style="margin-top: 10px"  class="text-left text--secondary">
                    {{ data.index + 1 }}
                  </div>
                </template>

                <template v-slot:cell(localized_type_name)="data">                
                  <div style="margin-top: 10px">
                    <span  class="text-left text--secondary">{{data.item.localized_type_name}} <small v-if="data.item.convertion==true" style="color:red">(No {{ data.item.language }} translation yet)</small></span>
                  </div>
                </template>

                <template v-slot:cell(created_at)="data">
                  <div style="margin-top: 10px">
                    <span style="text-center"  class="text-left text--secondary">{{ data.item.created_at | moment("DD/MM/YYYY") }} </span>
                  </div>
                </template>

                <template v-slot:cell(updated_at)="data">
                  <div style="margin-top: 10px">
                    <span style="text-center"  class="text-left text--secondary">{{ data.item.updated_at | moment("DD/MM/YYYY") }}</span>
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
              </b-table>
            </v-container>
          </v-row>
          <v-row>
            <v-spacer>
              <v-col cols="12" sm="6" md="4">
                <span>
                  Showing
                  <strong v-text="contactTypeTable.length" />
                  of
                  <strong v-text="totalRows" />
                  {{ $t("label.entries") }}
                </span>
              </v-col>
            </v-spacer>
            <v-col>
              <v-spacer>
                <b-pagination
                  v-model="currentPage"
                  :total-rows="totalRows"
                  :per-page="perPage"
                  style="float: right"
                ></b-pagination>
              </v-spacer>
            </v-col>
          </v-row>

          <!-- <div class="col-md-12">
            <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage"></b-pagination>
          </div> -->
        <!-- Modal Here -->
        <contactTypeModal :parent="vm"></contactTypeModal>
        </v-card-text>
      </v-card>
    </v-app>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import contactTypeMixins from "./mixins/contactTypeMixins.js";
import contactTypeModal from "./modals/contact-type.modal.vue";

export default {
  name: "contact-types-index",
  components: {
    contactTypeModal,
  },
  mixins: [contactTypeMixins],
  data() {
    return {
      vm: this,
      tableHeaders: [
        
        { key: "index", label: "ID", thStyle: { width: "5%" } },
        
        { key: "localized_type_name", label: this.$t('label.contact_type'), thClass: "text-left", sortable: true },
        
        { key: "created_at", label: this.$t("table.created_at"), thClass: "text-center", thStyle: { width: "15%" }, tdClass: "text-center", sortable: true },
        
        { key: "updated_at", label: this.$t("table.updated_at"), thClass: "text-centert", thStyle: { width: "15%" }, tdClass: "text-center", sortable: true },
        
        { key: "actions", label: this.$t("table.action"), thClass: "text-center", thStyle: { width: "20%" }, tdClass: "text-center" }
      
      ]
    };
  },

  created() {
    this.loadItems().catch(errors => {
      console.log(errors);
    });
  },

  computed: {
    ...mapGetters("contact_types", {
      items: "contact_types",
      responseStatus: "get_response_status"
    })
  },

  methods: {
    ...mapActions("contact_types", ["get_contact_types", "delete_contact_type"]),

    async loadItems() {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,
        lang: this.$i18n.locale,
        side: 'vue',
        sortbyLang: this.sortbyLangId,
      };
      this.get_contact_types(params).then(_ => {
        this.isLoading = false;
      });
    },

    sortbyLang(value) {
      this.sortbyLangId = value;
      this.loadItems();
    },

    onAdd() {
      this.form.reset();
      this.btnloading = false;
      this.form.action = "Add";
      // open modal
      this.$bvModal.show("contact-type-modal");
    },

    onEdit(item) {
      this.form.reset();
      this.form.id = item.id;
      this.form.type_name =
        this.$user.locale == "en" ? item.is_english : item.is_italian;
      this.form.action = "Update";
      this.itemSelected = item;

      this.form.default = item.contact_type_name;
      this.form.language = item.language;
      this.form.conversation = item.convertion;
      // Open Modal
      this.$bvModal.show("contact-type-modal");
    },

    onDelete(item) {
      this.$bvModal
       .msgBoxConfirm(
          this.$t( 'question_record_delete' ) + `${item.contact_type_name}` + this.$t( 'from' ) + this.$t( 'label.contact_type' ) + ' ' + this.$t( 'records' ),
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
              id: item.id,
            };
            this.delete_contact_type(params)
              .then(resp => {
                item.is_loading = false;
                if (this.responseStatus) {
                  this.loadItems();
                  this.makeToast(
                    "danger",
                     this.$t( "delete_record"),
                    `${item.contact_type_name}` + this.$t( "delete_record_message") + this.$t( "from") +  this.$t( "label.contact_type") + this.$t( "records")
                  );
                }
              })
              .catch(error => {
                this.form.errors.record(error.response.data.errors);
                this.makeToast(
                  `danger`,
                  this.$t( "inused_alert"),
                  this.$t( "unable_message1") + `${item.contact_type_name}` + this.$t( "unable_message2" ),
                  `${item.contact_type_name} ` + this.$t( "delete_record_message") + this.$t( "from") +  this.$t( "label.contact_type") + this.$t( "records")
                );
                item.is_loading = false;
              });
          }
        })
        .catch(err => {});
    }
  }
};
</script>
