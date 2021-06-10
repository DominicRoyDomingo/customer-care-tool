<template>
  <v-app id="content__container" light>
    <v-card tile flat>
      <v-row style="margin: 20px 15px 0 10px">
        <v-col cols="6" sm="6" md="4">
          <div class="title font-weight-light text--secondary">
            {{ $t("label.templates") }}
          </div>
          <div class="subheading font-weight-medium text--disabled">
            {{ $t("label.campaigns") }}
          </div>
        </v-col>
        <v-spacer></v-spacer>
        <v-col cols="6" sm="6" md="4">
          <v-btn color="success" @click="onAdd" class="float-right" tile>
            <v-icon dark>mdi-text-box-plus</v-icon>&nbsp;
            {{ $t("button.new") }}
          </v-btn>
        </v-col>
      </v-row>
      <v-row style="margin: 0 15px 0 10px">
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
          <div>
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
          </div>
        </v-col>
      </v-row>
      <v-row>
        <v-col>
          <v-container fluid>
            <b-table
              striped
              ref="table"
              show-empty
              stacked="md"
              :fields="tableHeaders"
              :current-page="currentPage"
              :per-page="perPage"
              :filter="filter"
              :busy="isLoading"
              :items="items"
            >
              <template v-slot:table-busy>
                <v-fade-transition>
                  <v-overlay opacity="0.8" style="z-index: 999999 !important">
                    <v-progress-circular
                      indeterminate
                      size="80"
                      width="2"
                      color="white"
                      class="text-center"
                    ></v-progress-circular>
                  </v-overlay>
                </v-fade-transition>
              </template>

              <template v-slot:head(preview)="data">
                <span class="text--disabled">
                  <v-icon small>mdi-information-outline</v-icon>
                  {{ $t("label.click_to_preview") }}
                </span>
              </template>

              <template v-slot:cell(preview)="data">
                <a
                  href="#"
                  @click.prevent="previewTemplate(data.item)"
                  style="margin-left: 25%"
                >
                  <v-avatar
                    v-if="data.item.preview"
                    color="black"
                    size="48"
                    class="template__table-preview"
                  >
                    <v-img :src="data.item.preview"></v-img>
                  </v-avatar>
                  <v-avatar
                    v-else
                    color="primary"
                    size="48"
                    class="template__table-preview"
                  >
                    <span class="white--text">
                      {{ data.item.template_name.charAt(0) }}
                    </span>
                  </v-avatar>
                </a>
              </template>

              <template v-slot:cell(template_name)="data">
                <div style="margin-top: 10px">
                  <span class="overline font-weight-bold">
                    {{ data.item.template_name }}
                  </span>
                </div>
              </template>

              <template v-slot:cell(variables_display)="data">
                <div
                  style="list-style: none"
                  class="text--secondary text-subtitle-2"
                >
                  <v-chip
                    v-for="variable in data.item.variables"
                    :key="variable.variable_text"
                    style="margin: 5px"
                    color="blue-grey lighten-1"
                    dark
                  >
                    {{ variable.variable_name }}
                  </v-chip>
                </div>
              </template>

              <template v-slot:cell(created_at)="data">
                <div style="margin-top: 15px">
                  <span class="text--disabled">
                    {{ data.item.created_at | toLocaleString }}
                  </span>
                </div>
              </template>

              <template v-slot:cell(actions)="data">
                <div style="margin-top: 12px">
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
                              mdi-file-document-edit
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
                              mdi-text-box-remove
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
        </v-col>
      </v-row>
      <v-row class="float-right">
        <v-container>
          <v-col>
            <b-pagination
              v-if="!isLoading"
              v-model="currentPage"
              :total-rows="totalRows"
              :per-page="perPage"
            ></b-pagination>
          </v-col>
        </v-container>
      </v-row>
    </v-card>
    <!-- Modal Here -->
    <emailTemplateModal :parent="this"></emailTemplateModal>

    <!-- Modal Here -->
    <previewTemplateModal
      :parent="this"
      :email_template="viewingTemplate"
    ></previewTemplateModal>
  </v-app>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import templateMixins from "./mixins/templateMixins.js";
import emailTemplateModal from "./modals/email_template.modal.vue";
import previewTemplateModal from "./modals/preview_template.modal.vue";

export default {
  name: "templates-index",
  components: {
    emailTemplateModal,
    previewTemplateModal,
  },
  mixins: [templateMixins],
  data() {
    return {
      viewingTemplate: null,
      tableHeaders: [
        {
          key: "preview",
          label: this.$t("label.click_to_preview"),
          thClass: "text-center",
          thStyle: { width: "8%", fontSize: "12px" },
          sortable: false,
        },
        {
          key: "template_name",
          label: this.$t("label.template_name"),
          thStyle: { width: "25%", textAlign: "left" },
          sortable: true,
        },
        {
          key: "variables_display",
          label: this.$t("label.variables"),
          thClass: "text-left",
          thStyle: { width: "25%" },
          tdClass: "center-content",
          sortable: true,
        },
        {
          key: "created_at",
          label: this.$t("table.created_at"),
          thClass: "text-center",
          thStyle: { width: "25%" },
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
    };
  },

  created() {
    this.loadItems().catch((errors) => {
      console.log(errors);
    });
  },

  computed: {
    ...mapGetters("email_template", {
      items: "templates",
      responseStatus: "get_response_status",
    }),
  },

  methods: {
    ...mapActions("email_template", [
      "get_templates",
      "delete_template",
      "remove_from_templates_array",
    ]),

    async loadItems() {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,
      };
      console.log(params);
      this.get_templates(params).then((_) => {
        this.isLoading = false;
      });
    },

    modalAdd() {
      this.form.reset();
      this.form.variables = [];
      this.addVariable();

      this.form.action = "Add";

      // open category modal
      this.$bvModal.show("template-modal");
    },

    onAdd() {
      this.form.reset();
      this.form.variables = [];
      this.addVariable();
      this.btnloading = false;
      this.form.action = "Add";

      // open modal
      this.$bvModal.show("template-modal");
    },

    onEdit(item) {
      this.editingIndex = item.template_index;

      this.form.reset();
      this.form.variables = [];
      this.language = this.$i18n.locale;
      this.form.id = item.id;
      this.form.preview = item.preview;
      this.form.template_name = item.template_name;
      this.form.html_content = item.html_content;
      this.form.variables = item.variables;
      this.form.action = "Update";
      this.itemSelected = item;

      // Open Modal
      this.$bvModal.show("template-modal");
    },

    previewTemplate(email_template) {
      this.viewingTemplate = email_template;
      // Open Modal
      this.$bvModal.show("preview-template-modal");
    },

    successfullySavedTemplate() {
      this.$refs.table.refresh();
    },

    onDelete(item) {
      let template = item;
      let vm = this;

      $.confirm({
        title:
          vm.$t("confirmation_record_delete").charAt(0) +
          vm
            .$t("confirmation_record_delete")
            .slice(1)
            .toLowerCase(),
        content:
          vm.$t("question_record_delete") +
          `${item.name}` +
          vm.$t("from") +
          vm.$t("label.templates") +
          vm.$t("records"),
        type: "red",
        typeAnimated: true,
        columnClass: "medium",
        buttons: {
          yes: {
            text: vm.$t("yes"),
            btnClass:
              "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
            action: function(value) {
              if (value) {
                item.is_loading = true;
                let params = {
                  api_token: vm.$user.api_token,
                  id: item.id,
                };
                vm.delete_template(params)
                  .then((resp) => {
                    item.is_loading = false;
                    if (vm.responseStatus) {
                      vm.remove_from_templates_array(template);
                      vm.makeToast(
                        "danger",
                        vm.$t("delete_record"),
                        `${item.name}` +
                          vm.$t("delete_record_message") +
                          vm.$t("from") +
                          vm.$t("label.templates") +
                          vm.$t("records")
                      );
                    }
                  })
                  .catch((error) => {
                    vm.form.errors.record(error.response.data.errors);
                    vm.makeToast(
                      `danger`,
                      vm.$t("inused_alert"),
                      vm.$t("unable_message1") +
                        `${item.name}` +
                        vm.$t("unable_message2"),
                      `${item.name} ` +
                        vm.$t("delete_record_message") +
                        vm.$t("from") +
                        vm.$t("label.templates") +
                        vm.$t("records")
                    );
                    item.is_loading = false;
                  });
              } else {
                item.is_loading = false;
              }
            },
          },
          no: {
            text: vm.$t("no"),
            btnClass:
              "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
            action: function() {},
          },
        },
      });
    },
  },
};
</script>

<style>
.template__table-preview {
  box-shadow: 0 2px 1px -1px rgba(0, 0, 0, 0.2), 0 1px 1px 0 rgba(0, 0, 0, 0.14),
    0 1px 3px 0 rgba(0, 0, 0, 0.12) !important;
  margin: auto;
}
</style>
