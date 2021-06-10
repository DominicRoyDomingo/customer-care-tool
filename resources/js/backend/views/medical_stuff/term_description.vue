<template>
  <div class="term-description animated fadeIn">
    <div class="card border-0">
      <div class="card-body">
        <div class="row">
          <div class="col-md-10">
            <h4 class="card-title">
              <a href="/admin/categorized-terms/terminologies">
                {{ $t("label.terminilogies") }}
              </a>
              <small class="text-muted text-capitalize">
                {{ $t("label.descriptions") }}
              </small>
            </h4>
          </div>
          <div class="col-md-2">
            <v-btn
              color="success"
              class="float-right"
              v-b-modal.term-desc-modal
              tile
              @click="onAdd"
              block
            >
              <i class="fas fa-notes-medical"></i>
              &nbsp; {{ $t("button.new") }}
              {{ $t("label.description") }}
            </v-btn>
          </div>
        </div>
        <v-row class="mt-5">
          <v-col cols="12" sm="6" md="6">
            <div class="pl-0">
              <ul class="list-inline">
                <li class="list-inline-item">
                  {{ $t("button.show") }}
                </li>
                <li class="list-inline-item">
                  <b-form-select
                    :options="showEntriesOpt"
                    v-model="perPage"
                    style="border-radius: 0"
                  />
                </li>
                <li class="list-inline-item">
                  {{ $t("label.entries") }}
                </li>
              </ul>
            </div>
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
        </v-row>

        <div class="row">
          <div class="col-md-12">
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
                :items="items"
                :filter="filter"
                @filtered="onFiltered"
              >
                <template v-slot:table-busy>
                  <div class="d-flex justify-content-center p-2">
                    <b-spinner label="Small Loading..."></b-spinner>
                  </div>
                </template>

                <template v-slot:cell(index)="data">
                  {{ data.index + 1 }}
                </template>

                <template v-slot:cell(description_name)="data">
                  <span v-html="data.item.description_name"> </span>
                </template>

                <template v-slot:cell(created_at)="data">
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
                            color="white"
                            indeterminate
                          >
                          </v-progress-circular>
                        </span>

                        <span v-else>{{ $t("button.more_actions") }}</span>
                      </v-btn>
                    </template>
                    <v-list>
                      <v-list-item
                        v-for="(action, index) in actions"
                        :key="index"
                        link
                        @click="onAction(data.item, action.value)"
                      >
                        <v-list-item-title>
                          <b-icon
                            :icon="action.icon"
                            :variant="action.variant"
                          />
                          <span
                            :class="{
                              'text-primary': action.variant === 'primary',
                            }"
                            v-html="action.title"
                          />
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </template>
              </b-table>
            </b-overlay>
          </div>
          <div class="col-md-12 mt-0">
            <b-pagination
              v-model="currentPage"
              :total-rows="total_rows"
              :per-page="perPage"
              align="center"
            />
          </div>
        </div>

        <IndexTermDescModal
          :parent="this"
          @done-success="create_success"
          @on-close="close_modal"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import medicalMixin from "./mixins/medicalMixin";

// Components
import IndexTermDescModal from "./modals/create-termDescription.modal";

export default {
  mixins: [medicalMixin],

  components: {
    IndexTermDescModal,
  },

  data() {
    return {
      isLinked: false,
      withValue: "",
      total_rows: 0,
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
        },
        // {
        //   value: "brand",
        //   title: this.$t("label.link_to_brand"),
        //   icon: "link45deg",
        //   variant: "primary",
        // },
      ],

      tableHeaders: [
        {
          key: "index",
          thClass: "text-left",
          label: "#SN",
          thStyle: { width: "5%" },
        },
        {
          key: "description_name",
          label: this.$t("label.description"),
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

  computed: {
    ...mapGetters("categ_terms", {
      items: "get_term_descriptions_items",
      status: "get_response_status",
    }),
  },

  created() {
    this.loadItems().catch((error) => {
      console.log(error);
    });
  },

  watch: {
    // filter() {
    //   this.loadItems().catch((error) => console.log(error));
    // },
  },

  methods: {
    ...mapActions("categ_terms", [
      "get_terms_descriptions",
      "delete_term_description",
    ]),

    async loadItems() {
      let params = {
        ...this.defaultParams(),
      };

      this.get_terms_descriptions(params).then((_) => {
        this.isLoading = false;
        this.total_rows = this.items.length;
      });
    },

    onAdd() {
      this.termDescForm.reset();
      this.btnloading = false;
      this.termDescForm.action = "Add";
    },

    create_success(item) {
      const records = this.$t("label.descriptions");

      if (this.termDescForm.action === "Add") {
        this.storeToast(item.description_name, records);
      } else {
        this.updateToast(item.id, records);
      }

      this.loadItems().catch((error) => {
        console.log(error);
      });

      this.resetData();
    },

    close_modal() {
      this.resetData();
    },

    onAction(item, title) {
      this.itemSelected = item;

      switch (title) {
        case "edit":
          this.onEdit(item);
          break;
        case "delete":
          this.onDelete(item);
          break;
        // case "brand":
        //   this.onLinkBrand(item);
        //   break;
      }
    },

    onEdit(item) {
      this.termDescForm.reset();
      this.language = item.base_language;
      this.termDescForm.id = item.id;
      this.termDescForm.name = item.base_name;
      this.withValue = item.with_value;
      this.termDescForm.action = "Update";

      // Open Modal
      this.$bvModal.show("term-desc-modal");
    },

    onDelete(item) {
      let baseName = item.base_name;
      let records = this.$t("label.descriptions");

      this.deleteConfirm(baseName, records).then((resp) => {
        if (resp) {
          item.is_loading = true;
          let params = {
            api_token: this.$user.api_token,
            locale: this.language,
            id: item.id,
          };
          this.delete_term_description(params)
            .then((resp) => {
              this.deleteToast(baseName, records);
              this.items.splice(
                this.getRemoveItemIndex(this.items, item.id),
                1
              );
            })
            .catch((error) => {
              if (error.response) {
                this.inusedToast(baseName);
                item.is_loading = false;
              }
            });
        }
      });
    },

    onFiltered(items) {
      this.total_rows = items.length;
      this.currentPage = 1;
    },

    resetData() {
      this.withValue = "";
      this.termDescForm.reset();
    },
  },
};
</script>

