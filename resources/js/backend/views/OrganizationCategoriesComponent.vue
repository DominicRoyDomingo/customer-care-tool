<template>

  <div>

    <div class="card">

      <div class="card-header">

          <i class="fas fa-list-alt"></i> {{ $t( 'organization_category_list' )}}

          <button type="button" class="btn btn-success btn-sm float-right"  @click="createOrganizationCategory()"><i class="fa fa-plus"></i> {{ $t( modal.add.button.new ) }} </button>

      </div>

      <div class="card-body">

         <div class="row mt-2">

          <div class="col-md-9">

            <div class="form-inline">

              <label class="mr-sm-2" for="inline-form-custom-select-pref">{{$t('button.show')}}</label>

              <b-form-select id="inline-form-custom-select-pref" class="mb-2 mr-sm-2 mb-sm-0" :options="showEntriesOpt" v-model="perPage" />

              {{$t('label.entries')}}

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
              ref="table"
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

              <template v-slot:cell(icon)="data">
                
                <b-avatar :src="data.item.icon" v-if="data.item.icon"></b-avatar>
                <b-avatar variant="primary" v-else :text="data.item.category[0]"></b-avatar>

              </template>

              <template v-slot:cell(name)="data">

                <span>{{data.item.category}} <small v-if="data.item.convertion==true" style="color:red">(No {{ data.item.language }} translation yet)</small></span>

              </template>

              <template v-slot:cell(created_at)="data">

                <span style="text-center">{{ data.item.created_at | moment("DD/MM/YYYY") }} </span>

              </template>

              <template v-slot:cell(updated_at)="data">

                <span style="text-center">{{ data.item.updated_at | moment("DD/MM/YYYY") }}</span>

              </template>

              <template v-slot:cell(actions)="data">

                <span>

                  <b-button size="sm" variant="success" @click="onEdit(data.item,data.index)">

                    <i class="fas fa-edit"></i>

                    {{$t('button.edit')}}

                  </b-button>

                  <b-button size="sm" variant="danger" @click="onDelete(data.item,data.index)">

                      <i class="fas fa-trash"></i>

                      {{$t('button.delete')}}

                  </b-button>

                </span>

              </template>

            </b-table>

          </div>

          <div class="col-md-12">

            <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage"></b-pagination>

          </div>

        </div>

      </div>

      <Create :$this="this"></Create>

      <Edit :$this="this"></Edit>

    </div>

  </div>

</template>

<script>
import { mapActions, mapGetters } from "vuex";

import Create from "./includes/organization-category/CreateComponent.vue";

import Edit from "./includes/organization-category/UpdateComponent.vue";

import Loading from "vue-loading-overlay";

import Form from "./../shared/form.js";

import toast from "./../helpers/toast.js";

import { fetchList, softDelete, getTagsName } from "./../api/tags.js";


export default {

  mixins: [toast],
  
  components: {
    
    Create,
    
    Edit,

    Loading
  
  },
  
  data: function() {
    
    return {

      isLoading: true,

      btnloading: false,
      
      filter: "",
      
      currentPage: 1,
      
      perPage: 10,

      showEntriesOpt: [

        { value: 10, text: "10" },
      
        { value: 30, text: "30" },
        
        { value: 50, text: "50" },
        
        { value: 100, text: "100" },
      
      ],

      tableHeaders: [
        
        { key: "icon", label: "Icon", class: "text-center", thStyle: { width: "5%" } },
        
        { key: "name", label: this.$t('table.category_name'), thClass: "text-left", sortable: true },
        
        { key: "created_at", label: this.$t("table.created_at"), thClass: "text-center", thStyle: { width: "15%" }, tdClass: "text-center", sortable: true },
        
        { key: "updated_at", label: this.$t("table.updated_at"), thClass: "text-centert", thStyle: { width: "15%" }, tdClass: "text-center", sortable: true },
        
        { key: "actions", label: this.$t("table.action"), thClass: "text-center", thStyle: { width: "20%" }, tdClass: "text-center" }
      
      ],

      modal: {
        
        add: {
          
          name: "organization_category_add_new_button",
          
          isVisible: false,
          
          button: {
            
            save: "save_button",
            
            cancel: "cancel",
            
            new: "organization_category_add_new_button",
            
            loading: false
          
          }

        },
        
        edit: {
          
          name: "organization_category_add_edit_button",
          
          isVisible: false,
          
          button: {
            
            update: "update",
            
            cancel: "cancel",
            
            new: "organization_category_add_new_button",
            
            loading: false
          
          }

        }

      },
      
      form: new Form({
        
        id: "",
        
        category: "",

        icon: "",
        
        language: this.$i18n.locale
      
      }),

      formData: new FormData(),
    
    };
  
  },

  mounted() {
    
    this.loadItems();
  
  },
  
  methods: {
    ...mapActions("organization_categories", ["get_organization_categories", "delete_organization_category", "remove_from_organization_categories_array", "delete_organization_category"]),

    async loadItems() {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token
      };
      this.get_organization_categories(params).then(_ => {
        this.isLoading = false;
      });
    },

    successfullySavedOrganizationCategory(){
      this.$refs.table.refresh();
    },


    // loadTags() {
      
    //   this.isLoading = true;
      
    //   fetchList().then(resp => {
        
    //     this.items = resp;

    //     this.isLoading = false;

    //   });
    
    // },

    createOrganizationCategory() {

      this.$bvModal.show("organization-category-add-modal");

    },
    
    onEdit(item,index) {
      this.editingIndex = index;

      this.form.reset();
      this.language = this.$i18n.locale;
      this.form.id = item.id;
      this.form.category = item.category;
      this.form.icon = item.icon;
      // Open Modal
      this.$bvModal.show("organization-category-edit-modal");
    },

    onDelete(item,index) {
      let organizationCategory = item;

      this.$bvModal
        .msgBoxConfirm(
           this.$t( "question_record_delete") + `${item.category}` + this.$t( "from") +  this.$t( "label.organization_categories") + this.$t( "records"),
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
              id: item.id
            };
            this.delete_organization_category(params)
              .then(resp => {
                item.is_loading = false;
                if (this.responseStatus) {
                  organizationCategory.index = index
                  this.remove_from_organization_categories_array(organizationCategory);
                  this.makeToast(
                    "danger",
                    this.$t( "delete_record"),
                    `${item.category}` + this.$t( "delete_record_message") + this.$t( "from") +  this.$t( "label.organization_categories") + this.$t( "records")
                  );
                }
              })
              .catch(error => {
                console.log(error);
              });
          }
        })
        .catch(err => {});
    },

  },

  computed: {
    ...mapGetters("organization_categories", {
      items: "organization_categories",
      // categories: "categories",
      responseStatus: "get_response_status"
    }),

    totalRows() {

      return this.items.length;

    },

  }

};

</script>

<style lang="scss">

  th:nth-last-child(2) {

    text-align: center;

  }

  th:nth-last-child(3) {

    text-align: center;

  }

</style>