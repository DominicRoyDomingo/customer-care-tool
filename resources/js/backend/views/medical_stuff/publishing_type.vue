<template>
  <div>
    <div class="card border-0">
      <div class="card-body">
        <div class="row">
          <div class="col-md-10">
            <h4 class="card-title">
              {{ $t("publising_item_title") }}
              <small class="text-muted text-capitalize">
              </small>
            </h4>
          </div>
          <div class="col-md-2">
            <v-btn
              color="success"
              class="float-right"
              v-b-modal.publishing-type-modal
              block
              tile
              @click="onAdd()"
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
                  style="display: flex; position: absolute; margin-left: -10px;"
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

                <template v-slot:cell(term_type_name)="data">
                  <span v-html="data.item.type_name"> </span>
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
        <!-- Modal Here -->
        <CreateModal :parent="this" @done-success="create_success" />
        <LinkBrand :parent="this" @done-sucess="link_brand_success" />

        <CreateBrand :parent="this" @done-success="create_brand_success" />
      </div>
    </div>
  </div>
</template>
<script>
import { mapActions, mapGetters } from "vuex";

import CreateModal from "./modals/create-publishingtype";

import CreateBrand from "./../includes/brands/CreateBrandComponent";

import LinkBrand from "./modals/link/link-brand.modal";
import medicalMixin from "./mixins/medicalMixin";

import Form from "./../../helpers/form";
import { softDelete, searchItemType, fetchList, sortList } from "./../../api/item_type.js";

export default {
  mixins: [medicalMixin],
  components: {
    CreateModal,
    LinkBrand,
    CreateBrand,
  },
  data() {
    return {
      // items: [],
      total_rows : 0,
      isLinked: false,
      btnloading: false,
      linkedBrands: [],
      termTypeTable:[],
      itemTypes : [],
      sort_by : 'index',
      sort_desc : true,
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
          key: "term_type_name",
          label: this.$t("table.publishing_item_type"),
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

      newSpecializationCategories: [],

      form: new Form({
        id: "",
        brand_name: "",
        brandCategories: [],
        website: "",
        logo: "",
        isDefault: 0,
        type_name : '',
        action: "",
      }),

      modal: {
        createBrand: {
          name: "brand_add",
          isVisible: false,
          button: {
            save: "save_button",
            cancel: "cancel",
            new: "content_add_new_button",
            loading: false,
          },
        },
      },

      formData: new FormData(),
    };
  },

  mounted() {
    // console.log(this.$brand);
    // this.loadItems();
    this.loadBrands();
    this.loadItemType();
  },

  watch: {
    filter(value) {
      this.searchType()
    },
    sort_desc(value){
        this.sortRows(value)
    }
  },

  computed: {
    ...mapGetters("categ_terms", {
      items: "get_type_terms_items",
      status: "get_response_status",
    }),

    ...mapGetters("brand", {
      itemBrands: "brands",
    }),

    getStartPage(){
      if(this.currentPage == 1){
          return 1
      }
      else if(this.currentPage > 1){
          return (parseInt(this.currentPage - 1) * parseInt(this.perPage) + 1) 
      }

      return 1
    },
    getEndPage(){
      if(this.currentPage == 1){        
        return this.termTypeTable.length
      }
      else if(this.currentPage > 1){
          return this.termTypeTable.length + (parseInt(this.currentPage - 1) * parseInt(this.perPage))
      }

      return 1
    }
  },

  methods: {
    ...mapActions("categ_terms", ["get_type_terms", "delete_term_type"]),

    ...mapActions("brand", ["get_brands"]),

    loadItemType() {
      fetchList().then((res) => {
          this.isLoading = false;
          var row_arr = [];
          if(res.length){
             for(var i in res){
                var obj = res[i]
                Object.assign(obj, { row_index : parseInt(i) + 1 });
                row_arr.push(obj)
             }
          }
          this.itemTypes = row_arr;
          this.total_rows = res.length
      })
    },

    sortbyLanguage(value) {
      this.sortbyLang = value;
      this.isLoading = true;
      this.loadItems();
    },

    onAction(item, title) {
      switch (title) {
        case "edit":
          this.onEdit(item);
          break;
        case "delete":
          this.onDelete(item);
          break;
        case "brand":
          this.onLinkBrand(item);
          break;
      }
    },

    onLinkBrand(item) {
      this.itemSelected = item;
      this.brandForm.linkedType = "term_type";
      this.mtForm.id = item.id;
      this.brandForm.brands = item.brands;

      this.$bvModal.show("link-brand-modal");
    },

    create_brand_success() {
      this.loadBrands();
      this.$bvModal.show("link-brand-modal");
    },

    loadBrands() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filterBrand,
        org_id: this.$org_id,
      };
      this.get_brands(params).then((_) => {});
    },

    create_success(item) {
      const recordName = this.$t("label.type_of_terms");

      if (this.typeForm.action === "Add") {
        this.storeToast(item.term_type_name, recordName);
      } else {
        this.updateToast(item.id, recordName);
      }

      this.loadItems();
      this.typeForm.reset();
    },

    link_brand_success() {
      this.loadItems();
    },

    onAdd() {
      this.form.reset();
      this.btnloading = false;
      this.form.action = "Add";
      console.log("test")
    },
    onEdit(item) {
      this.itemSelected = item;
      this.form.type_name = item.base_name 
      this.form.id = item.id
      this.form.term_type = item.has_translation == true ? item.base_name : ""
      this.form.action = "Update";

      // Open Modal
      this.$bvModal.show("publishing-type-modal");
    },
    onDelete(item) {
      let baseName = item.base_name;
      let records = "Type of Publishing Items";

      this.deleteConfirm(baseName, records).then((resp) => {
        if (resp) {
          item.is_loading = true;
          let params = {
            api_token: this.$user.api_token,
            locale: this.language,
            id: item.id,
          };
          softDelete(item.id)
            .then((resp) => {
              if(resp.type != 'error'){
                  this.loadItemType();
                  this.deleteToast(resp.message, records);
                  this.items.splice(
                    this.getRemoveItemIndex(this.items, item.id),
                    1
                  );
              }else
              {
                this.inusedToast(resp.message)
              }
              
            })
            .catch((error) => {
              if (error.response) {
                this.inusedToast(resp.message);
                item.is_loading = false;
              }
            });
        }
      });
    },
    searchType : function(){
        var filter = this.filter
        this.isLoading = true
        searchItemType(filter).then((res) => {
            this.isLoading = false;
            var row_arr = [];
            if(res.data.length){
               for(var i in res.data.reverse()){
                  var obj = res.data[i]
                  Object.assign(obj, { row_index : parseInt(i) + 1 });
                  row_arr.push(obj)
               }
            }
            this.itemTypes = row_arr;
            this.total_rows = res.data.length
        })
    },
    sortRows : function(bool){
        var filter = this.filter
        var obj = {
            sortcol : this.sort_by,
            sort : bool,
            filter : filter
        }

        sortList(obj).then((res) => {
            var row_arr = [];
            if(res.length){
               for(var i in res.reverse()){
                  var obj = res[i]
                  Object.assign(obj, { row_index : parseInt(i) + 1 });
                  row_arr.push(obj)
               }
            }
            this.itemTypes = row_arr;
            this.total_rows = res.length
        })
    }
  },
};
</script>
