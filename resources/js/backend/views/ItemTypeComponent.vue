<template>
  <div class="animated fadeIn">
    <div class="card">
      <div class="card-header">
        <i class="fas fa-list-alt"></i> {{ $t("publishing_type_list") }}

        <button
          type="button"
          class="btn btn-success btn-sm float-right"
          @click="modal.add.isVisible = true"
        >
          <i class="fa fa-plus"></i> {{ $t(modal.add.button.new) }}
        </button>
      </div>

      <div class="card-body">
        <div class="row mt-2">
          <div class="col-md-9">
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
                :placeholder="$t('search_here')"
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

              <template v-slot:cell(type_name)="data">
                <span
                  >{{ data.item.type_name }}
                  <small v-if="data.item.convertion == true" style="color: red"
                    >(No {{ data.item.language }} translation yet)</small
                  ></span
                >
              </template>

              <template v-slot:cell(created_at)="data">
                <span style="text-center"
                  >{{ data.item.created_at | moment("DD/MM/YYYY") }}
                </span>
              </template>

              <template v-slot:cell(updated_at)="data">
                <span style="text-center">{{
                  data.item.updated_at | moment("DD/MM/YYYY")
                }}</span>
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
                    <i class="fas fa-trash"></i>

                    {{ $t("button.delete") }}
                  </b-button>
                </span>
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
      </div>

      <Create :$this="this"></Create>

      <Edit :$this="this"></Edit>
    </div>
  </div>
</template>

<script>
import Create from "./includes/item-type/CreateComponent.vue";

import Edit from "./includes/item-type/UpdateComponent.vue";

import Loading from "vue-loading-overlay";

import Form from "./../shared/form.js";

import toast from "./../helpers/toast.js";

import { fetchList, softDelete, getItemTypeName } from "./../api/item_type.js";

export default {
  mixins: [toast],

  components: {
    Create,

    Edit,

    Loading,
  },

  data: function () {
    return {
      isLoading: true,

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
        { key: "index", label: "ID", thStyle: { width: "5%" } },

        {
          key: "type_name",
          label: this.$t("table.publishing_type_name"),
          thClass: "text-left",
          sortable: true,
        },

        {
          key: "created_at",
          label: this.$t("table.created_at"),
          thClass: "text-center",
          thStyle: { width: "15%" },
          tdClass: "text-center",
          sortable: true,
        },

        {
          key: "updated_at",
          label: this.$t("table.updated_at"),
          thClass: "text-centert",
          thStyle: { width: "15%" },
          tdClass: "text-center",
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

      items: [],

      modal: {
        add: {
          name: "publishing_type_add_new_button",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "publishing_type_add_new_button",

            loading: false,
          },
        },

        edit: {
          name: "publishing_type_add_edit_button",

          isVisible: false,

          button: {
            update: "update",

            cancel: "cancel",

            new: "publishing_type_add_edit_button",

            loading: false,
          },
        },
      },

      form: new Form({
        id: "",

        type_name: "",

        language: this.$i18n.locale,
      }),

      formData: new FormData(),
    };
  },

  mounted() {
    this.loadItemType();
  },

  methods: {
    loadItemType() {
      this.isLoading = true;

      fetchList().then((resp) => {
        this.items = resp;

        this.isLoading = false;
      });
    },

    onEdit(item_type) {
      this.form.id = item_type.id;

      getItemTypeName(this.form.id, this.$i18n.locale).then((resp) => {
        if (resp) {
          this.form.type_name = resp.type_name;
        } else {
          this.form.type_name = "";
        }
      });

      this.modal.edit.isVisible = true;
    },

    onDelete(item_type) {
      // this.$confirm( this.$t( 'delete_transaction_alert' ), {

      //   confirmButtonText: "OK",

      //   cancelButtonText: this.$t( 'cancel' ),

      //   type: "error"

      // })
      this.$bvModal
        .msgBoxConfirm(
          this.$t("question_record_delete") +
            `${item_type.type_name}` +
            this.$t("from") +
            this.$t("item") +
            this.$t("records"),
          {
            title: this.$t("confirmation_record_delete"),
            okVariant: "danger",
            okTitle: this.$t("yes"),
            cancelTitle: this.$t("no"),
            hideHeaderClose: false,
            centered: true,
          }
        )
        .then((resp) => {
          if (resp) {
            item_type.loading = true;

            softDelete(item_type.id).then((resp) => {
              item_type.loading = false;

              if (resp == false) {
                this.makeToast(
                  "danger",

                  this.$t("data_used"),

                  this.$t("used_data_alert")
                );

                // this.$notify({

                //   title: this.$t( 'data_used' ),

                //   message: this.$t( 'used_data_alert' ),

                //   type: "error"

                // });

                return false;
              }

              this.makeToast(
                resp.type,

                this.$("delete_record"),

                resp.message + " " + this.$t("remove_publishing_type_alert")
              );
              // this.$notify({

              //   title: this.$t( 'delete' ),

              //   message: resp.message + ' ' + this.$t( 'remove_publishing_type_alert' ),

              //   type: resp.type

              // });

              this.loadItemType();
            });
          }
        });
    },
  },

  computed: {
    totalRows() {
      return this.items.length;
    },
  },
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