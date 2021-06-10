<template>
  <div>
    <div class="card border-0">
      <div class="card-body">
        <div class="row">
          <div class="col-md-10">
            <h4 class="card-title">
              {{ $t('result.title') }}
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
              &nbsp; {{ $t('result.modal_title') }}
            </v-btn>
          </div>
        </div>

        <div class="row mt-5">
          <v-col cols="12" sm="6" md="6">
              <div class='row'>
                <div class='col-md-10'>
                  <div class='d-flex align-items-center justify-content-start'>
                    <div class='d-flex align-items-center justify-content-start mr-2'>
                      <span class='caption font-weight-regular text--secondary text-left'>{{ $t("button.show") }}</span>
                      <b-form-select :options="showEntriesOpt" v-model="perPage" class='w-50 mx-2'/>
                      <span class='caption font-weight-regular text--secondary text-left'>{{ $t("label.entries") }}</span>
                      </div>
                      <div class='d-flex justify-content-start caption font-weight-regular text--secondary text-left'>
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
                              >
                                <v-icon :size="18"> mdi-filter-menu </v-icon>
                              </v-btn>
                            </template>
                            <v-list dense class="profile__row-menu">
                              <v-list-item-group color="primary">
                                <v-list-item
                                  v-for="(option, index) in noTranslationsOptions"
                                  :key="index"
                                >
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
                      </div>
                    </div>
                </div>
              </div>
          </v-col>
          <v-col cols="12" sm="6" md="6">
            <div class="float-right">
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
            </div>
          </v-col>
        </div>

        <div class="row">
          <div class="col-md-12 mb-0">

              <b-table
                striped
                show-empty
                :empty-text="$t('no_record_found')"
                :fields="tableHeaders"
                :current-page="currentPage"
                :per-page="perPage"
                :busy="isLoading"
                :items="items"
                :sort-by.sync="sort_by"
                :sort-desc.sync="sort_desc"
              >
                <template v-slot:table-busy>
                  <div class="d-flex justify-content-center p-2">
                    <b-spinner label="Small Loading..."></b-spinner>
                  </div>
                </template>

                <template v-slot:cell(index)="data">
                  {{ (data.index + 1 )+ ((currentPage * perPage) - perPage) }}
                </template>

                <template v-slot:cell(name)="data">
                  <span v-html="data.item.name"> </span>
                </template>

                <template v-slot:cell(created_at)="data">
                  <span>{{ data.item.created_at }}</span>
                </template>

                <template v-slot:cell(actions)="data">
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
                              <v-icon color="green darken-3">
                                mdi-pencil
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("button.edit") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="onLinkBrand(data.item)">
                            <v-list-item-icon>
                              <v-icon color="green darken-3">
                                mdi-link-variant
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t('label.link_to_brand') }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="onDelete(data.item)">
                            <v-list-item-icon>
                              <v-icon color="red lighten-1">
                                mdi-delete
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
                </template>
              </b-table>

          </div>

          <div class="col-md-12 mb-0">
            <v-row>
              <v-spacer>
                <v-col cols="12" sm="6" md="4">
                  <span>
                    Showing
                    <strong >{{ ((currentPage * perPage) - perPage) + 1 }}</strong>
                    to {{ total_rows }}
                    <strong />
                    of {{ (currentPage * perPage)}}
                    <strong />
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
      <ResultModal 
        :typeForm="typeForm"
        :rowIndex="rowIndex"
        :dataTable="initTable"
        :resultName="resultName"
      />
      <BrandModal :rowIndex="rowIndex" :resultName="resultName" :brand_session="brand_session"  />
  </div>
</template>
<script>


import { mapActions, mapGetters } from "vuex";
import toast from "../../helpers/toast.js";
import axios from "axios";
import i18n from '../../i18n.js';
import ResultModal from './modal/result-modal'
import BrandModal from './modal/link-brand-modal'
import resultMixins from './mixins/resultMixins.js';

export default {
  mixins: [toast, resultMixins],
  components: {
      ResultModal,
      BrandModal
  },
  data() {
      return {
        resultserv : null,
        isLoading : false,
        typeForm : '',
        sort_by : 'index',
        sort_desc : false,
        perPage : 10,
        total_rows : 0,
        currentPage : 1,
        items : [],
        rowIndex : 0,
        filter : '',
        resultName : '',
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
            label: 'Result',
            thClass: "text-left wide-column",
            thStyle: { width: "50%" },
            sortable: true,
          },
          {
            key: "created_at",
            label: 'Created At',
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
  props : {
    brand_session : {
        type : Object,
        default : {}
    }
  },
  created() {
    this.initTable()
  },
  watch: {
    sort_desc(value) {
      this.sortRows(value);
    },
    filter(val, oldVal){
        this.searchType()
    }
  },
  computed: {
    
  },
  methods: {
    initTable() {
      this.isLoading = true
      this.get_all().then((res) => {
          this.isLoading = false
          if(res){
              this.items = res
              this.total_rows = res.length
          }
      })
    },
    onEdit(data) {

      this.typeForm = 'update' 
      this.$bvModal.show('result-modal');
      this.rowIndex = data.id
      this.resultName = data.base_name
    },
    onDelete(data) {

        var obj = {
            id : data.id
        }

        var msg = {
            baseName : data.base_name
        }

        this.delete_result(obj, this, msg).then((res) => {
            this.isLoading = false
            if(res){
                this.deleteToast(res.message, "result")
                this.initTable()
            }
        })
    },
    onLinkBrand(data){        
        this.rowIndex = data.id
        this.resultName = data.base_name
        this.$bvModal.show('linkbrand-modal');
    },
    showModal() {
       this.typeForm = 'add'
       this.$bvModal.show('result-modal');
    },
    searchType: function () {

        var obj = {
            search : this.filter
        }
        this.isLoading = true
        this.search_result(obj).then((res) => {
            this.isLoading = false
            if(res){
                this.items = res
            }
        })
    },
    sortbyLang: function (lang) {
         var obj = {
            name : lang
         }
         this.isLoading = true
         this.sortByLang(obj).then((res) => {
            this.isLoading = false
            if(res){
                this.items = res
                this.total_rows = res.length
            }
         })
    },
  },
};
</script>
