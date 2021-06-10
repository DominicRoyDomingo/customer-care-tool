<style scoped>
.link-term-disabled {
  background-color: #f2f2f2;
  cursor: no-drop;
}
.link-term-disabled .d-icon,
.link-term-disabled .d-span {
  color: #c8ced3 !important;
}

.link-article-disabled {
  background-color: #f2f2f2;
  cursor: no-drop;
}
.link-article-disabled .d-icon,
.link-article-disabled .d-span {
  color: #c8ced3 !important;
}
.no-pointer {
  cursor: not-allowed;
}
</style>
<template>
  <div class="animated fadeIn">
    <div class="card border-0">
      <div class="card-body">
        <div class="row">
          <v-col cols="12" sm="8" md="8">
            <h4 class="card-title">
              Payment Plan 
              <!-- {{$t('label.choices')}} -->
            </h4>
          </v-col>
          <v-col cols="12" sm="4" md="4">
            <v-btn color="success" block tile @click="onAddPlan">
              <i class="fas fa-notes-medical"></i>
              <!-- &nbsp; {{$t('button.new') + " " + $t('label.choice')}} -->
              &nbsp; {{$t('button.new') + " " + "Payment Plan"}}
            </v-btn>
          </v-col>
          
        </div>

        <div class="row mt-4 mb-0">
          <div class="col-md-8">
            <b-form inline>
              <label class="mr-sm-2" for="inline-form-custom-select-pref">
                {{ $t("show") }}
              </label>
              <b-form-select
                id="inline-form-custom-select-pref"
                class="mb-2 mr-sm-2 mb-sm-0"
                :options="showEntriesOpt"
                v-model="perPage"
              />
              <label class="mr-sm-2" for="inline-form-custom-select-pref">
                {{ $t("label.entries") }}
              </label>
              <b-input-group-prepend>
                <v-menu offset-y :rounded="false">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      title="Filter by no Translations."
                      color="info"
                      dark
                      v-bind="attrs"
                      v-on="on"
                      tile
                      depressed
                      style="height: 33px; min-width: 64px; padding: 0 16px"
                    >
                      <v-icon :size="18"> mdi-filter-menu </v-icon>
                    </v-btn>
                  </template>
                  <v-list dense class="profile__row-menu">
                    <v-list-item-group color="primary">
                      <v-list-item
                        v-for="(option, index) in sortTranslationOptions"
                        :key="index"
                      >
                        <v-list-item-content
                          @click="sort_by_language(option.value)"
                        >
                          <v-list-item-title>
                            {{ option.text }}
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list-item-group>
                  </v-list>
                </v-menu>
              </b-input-group-prepend>

            </b-form>
          </div>
          <div class="col-md-4">
            <div class="">
              <b-form inline @submit.prevent="on_search_submit">
                <el-input
                  prefix-icon="el-icon-search"
                  v-model="filter"
                  autocomplete="off"
                  @input="on_search_submit"
                  clearable
                >
                </el-input>
              </b-form>
            </div>

          </div>
        </div>

        <div class="row mb-0">
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
                class="mb-0"
                striped
                show-empty
                :empty-text="$t('no_record_found')"
                :fields="tableHeaders"
                :per-page="perPage"
                :busy="isLoading"
                :items="items"
              >
                <template v-slot:table-busy>
                  <div class="d-flex justify-content-center p-2">
                    <b-spinner label="Small Loading..."></b-spinner>
                  </div>
                </template>
                <template v-slot:cell(value)="data">
                  <span>
                    <ul class="list-unstyled pl-0">
                      <b-media tag="li">
                        <span
                          class="mt-0 mb-1 font-weight-bold"
                          v-html="data.item.paymentplan_name"
                        />
                      </b-media>
                    </ul>
                  </span>
                </template>

                <template v-slot:cell(typeOfPlan)="data">
                  <span>{{ data.item.created_at | toLocaleString }}</span>
                </template>

                <template v-slot:cell(actions)="data">
                  <v-menu offset-y>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      color="#c8ced3"
                      small
                      v-bind="attrs"
                      v-on="on"
                      :disabled="data.item.is_loading"
                    >
                      <span v-if="data.item.is_loading" class="text-center">
                        <v-progress-circular
                          size="20"
                          width="1"
                          color="red"
                          indeterminate
                        />
                      </span>
                      <span v-else>{{ $t("button.more_actions") }}</span>
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item
                      v-for="(action, index) in actions"
                      :key="index"
                      link
                      @click="on_action(data.item, action.value)"
                    >
                      <v-list-item-title>
                        <b-icon :icon="action.icon" :variant="action.variant" />
                        <span
                          :class="{
                            'text-primary': action.variant === 'primary',
                          }"
                          v-html="action.title"
                        />
                      </v-list-item-title>
                    </v-list-item>

                    <v-divider></v-divider>
                    <v-list-item @click="on_delete(data.item)" link>
                      <v-list-item-title>
                        <b-icon icon="trash-fill" variant="danger" />
                        <span
                          class="text-danger"
                          v-html="`${$t('button.delete')}`"
                        />
                      </v-list-item-title>
                    </v-list-item>
                  </v-list>
                  </v-menu>
                </template>
              </b-table>
            </b-overlay>
          </div>
        </div>

        <div class="row mt-0" v-if="total_rows > 0">
          <div class="col-md-6">
            <span>
              {{ $t("showing") }}
              <strong v-text="showing.from" />
              {{ $t("label.to") }}
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

        <AddPaymentPlanModal 
          v-if="isPaymentPlanModalVisible"
          :parent="this" 
          @done-create="createdPaymentPlan"
          @done-update="updatedPaymentPlan"
          
        />

        <LinkBrandModal
          v-if="isLinkBrandModalVisible"
          :parent="this" 
          @done-sucess="load_items" 
        />

      </div>
    </div>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

import PaymentPlanMixin from "./mixins/PaymentPlanMixin";
import AddPaymentPlanModal from "./modals/add";
import LinkBrandModal from "./modals/link-brand";

export default {
  mixins: [PaymentPlanMixin],

  components: {
    AddPaymentPlanModal,  LinkBrandModal
  },

  data() {
    return {
      isLoading: true,
      isPaymentPlanModalVisible: false,
      isLinkBrandModalVisible: false,
      btnloading: false,
      isLinked: false,
      searchItems: {},
      brandSelected:{},
      items:[],
      data:{},

      paymentplan_modal: {
        id:"",
        isVisible: false,
        type: "create",
        data:{},
        button: {
          name: "SAVE",
          save: "save_button",
          cancel: "cancel",
          loading: false,
        },
      },

      show_rows_total: 10,
      
      tableHeaders: [
        {
          key: "value",
          label: "Payment Plan".toUpperCase(),
          thClass: "text-left",
          thStyle: { width: "25%" },
          sortable: true,
        },
        {
          key: "typeOfPlan",
          label: "Type Of Plan".toUpperCase(),
          thStyle: { width: "25%" },
          thClass: "text-center",
          tdClass: "text-center",
          sortable: true,
        },
        {
          key: "actions",
          label: this.$t("table.action").toUpperCase(),
          thStyle: { width: "25%" },
          thClass: "text-center",
          tdClass: "text-center",
        },
      ],

      sortingTermsOptions: [
        { value: "all", text: "All" },
        { value: "least_lt", text: "Least number of linked terms" },
        { value: "most_lt", text: "Most number of linked terms" },
        { value: "least_la", text: "Least number of linked articles" },
        { value: "most_la", text: "Most number of linked articles" },
        { value: "least_tt", text: "Least number of type of terms" },
        { value: "most_tt", text: "Most number of type of terms" },
        { value: "least_spec", text: "Least number of specializations" },
        { value: "most_spec", text: "Most number of specializations" },
      ],

      sortTranslationOptions: [
        { value: "all", text: "All", disabled: true },
        { value: "en", text: "No English Translation" },
        { value: "de", text: "No German Translation" },
        { value: "it", text: "No Italian Translation" },
        { value: "ph-fil", text: "No Filipino Translation" },
        { value: "ph-bis", text: "No Visayan Translation" },
      ],

    };
  },

  watch: {
    filter(value) {
      if (!value) {
        this.isLinked = true;
        this.currentPage = 1;
        this.load_items();
      }
    },

    currentPage(value) {
      this.showAdvanceSearch = false;
      this.isLinked = true;
      this.load_items();
    },

    perPage() {
      this.showAdvanceSearch = false;
      this.isLinked = true;
      this.load_items();
    },
  },

  mounted() {
    this.load_items();
  },

  computed: {
    show_page_total() {
      return this.currentPage;
    },

    isSearchItem() {
      if (_.isEmpty(this.searchItems)) {
        return false;
      }
      let k = false;

      Object.values(this.searchItems).forEach((ele) => {
        if (ele && ele.length > 0) {
          k = true;
        }
      });

      return k;
    },
  },

  methods: {
    ...mapActions("categ_terms", [
      "get_terms",
      "delete_term",
      "get_linked_terms",
      "get_linked_term_id",
      "get_linked_provider_terms",
      "get_advance_search",
      "get_term_icons",
    ]),

    ...mapActions("payment_plan", [
      "delete_paymentplan"
    ]),

    set_params_detail() {
      return {
        filter: this.filter,
        sortbyLang: this.sortbyLang,
        perPage: this.perPage,
        page: this.currentPage,
      };
    },

    on_action(item, title) {
     switch (title) {
       case "edit":
       this.on_edit_payment_plan(item);
       break;
     case "brand":
       this.on_linked_brand(item);
       break;
     }
    },

    load_items() {
      let params = {
        ...this.defaultParams(),
        ...this.set_params_detail(),
      };

      axios
        .get("/api/payment-plans/", { params })
        .then((resp) => {
          let data = resp.data;
          console.log(data);
          this.total_rows = data.total;
          this.items = data.data;
          this.showing.to = data.to;
          this.showing.from = data.from;
          this.showing.total = data.total;
        })
        .finally(() => {
          this.isLinked = false;
          this.isLoading = false;
        });
    },

    on_search_submit(value) {
      this.isLinked = true;
      this.currentPage = 1;
      this.load_items();
    },

    on_search_key() {
      this.searchItems = {};
    },

    close_modal() {
      this.mtForm.reset();
      this.articleForm.reset();
      this.itemSelected = "";
    },

    on_reset_search() {
      this.filter = "";
    },

    sort_by_language(value) {
      this.isLinked = true;
      this.sortbyLang = value;
      this.currentPage = 1;
      this.searchItems = {};
      this.load_items();
    },

    onFiltered(items) {
      this.total_rows = items.length;
      this.currentPage = 1;
    },

    filterbyLang(value) {
      this.isLoading = true;
      this.sortbyLang = value;
      this.sortbyTerm = null;
      this.load_items();
    },

    on_delete(item) {
      let baseName = item.base_name;
      let records = "Payment Plan";

      this.deleteConfirm(baseName, records).then((resp) => {
        if (resp) {
          item.is_loading = true;
          let params = {
            api_token: this.$user.api_token,
            locale: this.language,
            id: item.id,
          };

          this.delete_paymentplan(params)
            .then((_) => {
              this.deleteToast(baseName, records);
              this.items.splice(this.getRemoveItemIndex(this.items, item.id),1
              );
              this.item.is_loading = false;
              this.load_items();
            })
            .catch((error) => {
              console.log(error);
            });
          }
        });
    },

    onAddPlan() {
      this.isPaymentPlanModalVisible = true;
      this.isLinked = true;

      setTimeout(() => {
        this.$nextTick(() => {
          this.isLinked = false;
          this.btnloading = false;
          this.$bvModal.show("paymentplanmodal");
        });
      }, 1000);
    },

    on_edit_payment_plan(item) {
      this.isPaymentPlanModalVisible = true;
      this.isLinked = true;
      this.paymentplan_modal.type = "edit";
      this.paymentplan_modal.data = item; 
      
      setTimeout(() => {
        this.isLinked = false;
        this.btnloading = false;
        this.$bvModal.show("paymentplanmodal");
      }, 1000);
    },

    on_linked_brand(item) {
      this.isLinkBrandModalVisible =true; 
      this.isLinked = true;
      this.itemSelected = item;

      console.log(item.brand_paymentplan)
      
      // setTimeout(() => {
      //   this.isLinked = false;
      //   this.btnloading = false;
      //   this.$bvModal.show("link-brand-modal");
      // }, 1000);
    },

    createdPaymentPlan(e){
      this.sort_by_language("");
      this.storeToast(e, `Payment Plan`);
    },

    updatedPaymentPlan(e){
      this.load_items();
      this.updateToast(e, `Payment Plan`);
    }

  },
};
</script>
