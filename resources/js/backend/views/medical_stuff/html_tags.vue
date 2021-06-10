<template>
  <div>
    <div class="card border-0">
      <div class="card-body">
        <div class="row">
          <div class="col-md-10">
            <h4 class="card-title">
              {{ $t("label.categorized_terms") }}
              <small class="text-muted text-capitalize">
                {{ $t("html_tags_priority") }}
              </small>
            </h4>
          </div>
          <div class="col-md-2">
            <v-btn
              color="success"
              class="float-right text-uppercase"
              tile
              @click="onAdd"
              v-b-modal.html-tag-modal
            >
              <i class="fas fa-notes-medical"></i>
              &nbsp;{{ $t("button.new") }} {{ $t("html_tag_priority") }}
            </v-btn>
          </div>
        </div>
        <div class="row mt-3">
          <v-col cols="12" sm="6" md="6">
            <div>
              <b-form inline>
                <label class="mr-sm-2" for="inline-form-custom-select-pref">
                  {{ $t("button.show") }}
                </label>
                <b-form-select
                  id="inline-form-custom-select-pref"
                  class="mb-2 mr-sm-2 mb-sm-0"
                  :options="showEntriesOpt"
                  v-model="perPage"
                ></b-form-select>
                <label class="mr-sm-2" for="inline-form-custom-select-pref">
                  {{ $t("label.entries") }}
                </label>
              </b-form>
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
                    id="inline-form-input-username"
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
              >
                <template v-slot:table-busy>
                  <div class="d-flex justify-content-center p-2">
                    <b-spinner label="Small Loading..."></b-spinner>
                  </div>
                </template>

                <template v-slot:cell(html_tag_priority)="data">
                  <span
                    :href="data.item.route_show"
                    class="mt-0 mb-1 font-weight-bold"
                    v-html="data.item.description"
                  />
                </template>

                <!-- <template v-slot:cell(has_authors)="data">
                    <small pill class="mr-2" size="sm" v-for="list in getAuthorName( data.item.author_slot )">
                      Type: {{ list.type }}
                      <br>
                      Author's: <b-button pill class="mr-2" size="sm" v-for="author in list.authors">{{ author }}</b-button>
                      <hr>
                    </small>
                </template> -->

                <!-- <template v-slot:cell(publish_date)="data">
                  <span>{{ data.item.publish_date | toLocaleString }}</span>
                </template> -->

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
              :total-rows="totalRows"
              :per-page="perPage"
              align="center"
            />
          </div>
        </div>
        <CreateHTMLTagPriority :parent="this" @done-success="create_success" />
        <LinkArticleTerm
          v-if="itemSelected"
          :id="itemSelected.id"
          :parent="this"
          @done-success="link_success"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import medicalMixin from "./mixins/medicalMixin";
import addNotesModal from "./modals/add-notes.modal.vue";
import CreateHTMLTagPriority from "./modals/create-html-tag.modal";

import Form from "./../../helpers/form";

export default {
  mixins: [medicalMixin],

  components: {
    CreateHTMLTagPriority,
  },

  data() {
    return {
      htmlTagPriorityName: '',
      isShowHtmlTag: false,
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
      ],

      tableHeaders: [
        {
          key: "html_tag_priority",
          label: this.$t("label.html_tag"),
          thClass: "text-left",
          thStyle: { width: "35%" },
          sortable: true,
        },
        // {
        //   key: "has_authors",
        //   label: this.$t("item_author_idea"),
        //   thClass: "text-left",
        //   thStyle: { width: "50%" },
        // },
        // {
        //   key: "publish_date",
        //   label: `${this.$t("button.publish")} ${this.$t("date")}`,
        //   thClass: "text-center",
        //   tdClass: "text-center",
        // },
        {
          key: "actions",
          label: this.$t("table.action"),
          thClass: "text-center",
          thStyle: { width: "10%" },
          tdClass: "text-center",
        },
      ],
    };
  },

  created() {
    this.loadHTMLTags();
  },

  watch: {
    filter(value) {
      this.loadHTMLTags();
    },

    filterMedicalTerm(value) {
      this.loadMedicalTerms();
    },
  },

  computed: {
    ...mapGetters("categ_terms", {
      items: "get_html_tags_items",
    }),
  },

  methods: {

    ...mapActions("categ_terms", [
      "get_html_tags",
      "delete_html_tag",
      "get_terms",
      "get_notes_article",
      "get_type_authors",
    ]),

    create_success(item) {
      let recordName = this.$t("html_tag_priority");
      if (this.htmlTagForm.action === "Add") {
        this.storeToast(item.description, recordName);
      } else {
        this.updateToast(item.id, recordName);
      }

      this.htmlTagForm.reset();
      this.htmlTagPriorityName = '';
      this.loadHTMLTags();
    },

    onAction(item, title) {
      switch (title) {
        case "edit":
          this.onEdit(item);
          break;
        case "delete":
          this.onDelete(item);
          break;
      }
    },

    onEdit(item) {
      this.htmlTagForm.reset();
      this.htmlTagForm.id = item.id;
      this.htmlTagForm.description = item.description;
      this.htmlTagPriorityName = item.description;
      this.$bvModal.show("html-tag-modal");
    },

    loadHTMLTags() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
        sortbyLang: this.sortbyLang,
      };

      this.get_html_tags(params).then((_) => {
        this.isLoading = false;
      });
    },

    onDelete(item) {
      let baseName = item.description;
      let records = this.$t("html_tags_priority");

      this.deleteConfirm(baseName, records).then((resp) => {
        if (resp) {
          item.is_loading = true;
          let params = {
            api_token: this.$user.api_token,
            locale: this.language,
            brand_id: this.$brand.id,
            id: item.id,
          };

          this.delete_html_tag(params)
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

    onAdd() {
      this.htmlTagForm.reset();
      this.btnloading = false;
      this.htmlTagForm.action = "Add";
    },
  },
};
</script>

 