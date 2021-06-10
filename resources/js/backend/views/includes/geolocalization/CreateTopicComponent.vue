<template>
<div class="create">
      <b-modal
        id="article-modal"
        hide-footer
        hide-header
        size="lg"
        scrollable
        no-close-on-backdrop
        :title="
          parent.articleForm.action == 'Add' ? $t('label.add') : $t('button.update')
        "
      >
        <v-app id="create__container">
          <v-card>
            <form class="form"
              @submit.prevent="onSave"
              @keyup="parent.articleForm.errors.clear($event.target.name)"
              >
              <v-card-title class="title blue-grey lighten-4 text--secondary">
                <span v-if="parent.articleForm.action == 'Add'">
                  {{ $t("label.new") }} {{ $t("label.article") }}
                </span>
                <span
                  class="d-inline-block text-truncate"
                  style="max-width: 700px; letter-spacing: normal"
                  v-b-tooltip.hover
                  :title="parent.itemSelected.base_name"
                  v-else
                >
                  {{ $t("button.update") }} "{{ parent.itemSelected.base_name }}"
                </span>
                <v-spacer></v-spacer>
                <v-btn
                  icon
                  color="error lighten-2"
                  @click="onClose"
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-card-title>
              <v-card-text class="modal__content" style="">
                <v-container>
                  <v-container>
                    <v-row>
                      <v-col class="modal__input-container">
                        <div
                          class="form-group mb-4"
                          v-show="parent.articleForm.action === 'Update'"
                        >
                          <div class="form-inline">
                            <label class="mr-sm-2" for="inline-form-custom-select-pref">
                              Language
                            </label>
                            <b-form-select
                              id="inline-form-custom-select-pref"
                              class="mr-sm-2 mb-sm-0"
                              v-model="parent.language"
                              :options="parent.languageOptions"
                            />
                          </div>
                          <hr />
                        </div>

                        <div class="form-group">
                          <v-text-field
                            :label="$t('table.title').toUpperCase()"
                            id="title"
                            type="text"
                            name="title"
                            v-model="parent.articleForm.title"
                            hide-details="auto"
                          />
                          <small
                            v-if="
                              !parent.articleForm.title &&
                              parent.articleForm.errors.has('title')
                            "
                            v-text="parent.articleForm.errors.get('title')"
                            class="text-danger"
                          />
                        </div>

                        <div class="form-group">
                          <v-text-field
                            :label="`https://... ${$t(
                              'label.article_url'
                            ).toUpperCase()}`"
                            id="link"
                            type="text"
                            name="link"
                            v-model="parent.articleForm.link"
                            hide-details="auto"
                          />
                        </div>

                        <br />
                        <div class="form-group">
                          <label for="actor" class="text-uppercase">
                            {{ $t("content_author_idea") }}
                          </label>
                          <v-select
                            id="actor"
                            :options="
                              parent.actors.data ? parent.actors.data : parent.actors
                            "
                            v-model="parent.articleForm.actors"
                            name="actor"
                            label="full_name"
                            multiple
                            required
                          >
                            <template #list-header>
                              <li style="margin-left: 20px" class="mb-2">
                                <b-link
                                  v-b-tooltip.hover
                                  :title="`${$t('label.new')} ${$t(
                                    'content_author_idea'
                                  )}`"
                                  href="javascript:;"
                                  @click="showChildModal('actor-add-modal')"
                                >
                                  <i class="fa fa-plus" aria-hidden="true"></i>
                                  {{ $t("label.new") }} {{ $t("content_author_idea") }}
                                </b-link>
                              </li>
                            </template>
                          </v-select>
                        </div>

                        <br />
                        <div class="form-group">
                          <b-form-checkbox
                            id="isTypeDate"
                            name="isTypeDate"
                            v-model="parent.isAddTypeDate"
                            @change="onCheckAddTypeDate"
                          >
                            {{ $t("label.type_of_date") }}
                          </b-form-checkbox>
                        </div>

                        <template v-if="parent.isAddTypeDate">
                          <TypeDateForm :parent="this" />

                          <div class="form-group mt-5">
                            <v-btn
                              color="success"
                              tile
                              block
                              class="bg-success text-white"
                              @click="onAddTypeDate"
                            >
                              {{ $t("label.add") }} {{ $t("label.type_of_date") }}
                            </v-btn>
                          </div>
                        </template>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-container>
              </v-card-text>
              <v-divider style="margin-bottom: 0"></v-divider>
                <v-card-actions class="modal__footer blue-grey lighten-5">
                  <v-spacer></v-spacer>
                  <v-btn
                    color="error"
                    text
                    tile
                    @click="onClose"
                  >
                    {{ $t("cancel") }}
                  </v-btn>
                  <v-btn
                    color="success"
                    tile
                    type="submit"
                  >
                    <div v-if="parent.btnloading" class="text-center">
                      <v-progress-circular
                        size="20"
                        width="1"
                        color="white"
                        indeterminate
                      >
                      </v-progress-circular>
                    </div>
                    <div v-else>
                      <div>
                        <span v-if="parent.articleForm.action == 'Add'">
                            {{ $t("button.save") }}
                          </span>
                          <span v-else>
                            {{ $t("button.save_change") }}
                          </span>
                      </div>
                    </div>
                  </v-btn>
                </v-card-actions>
            </form>
          </v-card>
          
        </v-app>
        
      </b-modal>

      <CreateAuthor
        :parent="this"
        @loadTable="parent.loadActors"
        @done-success="isActionShow = true"
        @on-close="closeChildModal"
      />

      <ArticleCreateAddTypeDate
        :parent="this.parent"
        @done-success="loadTypeDates"
        @on-close="closeChildModal"
      />
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import CreateAuthor from "../actor/CreateComponent";
import ArticleCreateAddTypeDate from "../../medical_stuff/modals/create-typedate.modal";

import TypeDateForm from "../../medical_stuff/snippets/type_date_form";

import actorMixin from "../../medical_stuff/mixins/actorMixin";
// import medicalMixin from "./../mixins/medicalMixin";

export default {
  props: ["parent"],

  mixins: [actorMixin],

  components: {
    ArticleCreateAddTypeDate,
    CreateAuthor,
    TypeDateForm,
  },

  data() {
    return {
      isActionShow: true,
    };
  },

  mounted() {
    this.loadTypeDates();
  },

  computed: {
    ...mapGetters("categ_terms", {
      typedates: "get_type_dates_items",
    }),
  },

  methods: {
    ...mapActions("categ_terms", ["post_article", "get_type_dates"]),

    onClose() {
      this.isActionShow = true;
      this.$emit("on-close");
      this.formReset();
      this.$bvModal.hide("article-modal");
    },
    loadTypeDates() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
      };

      this.get_type_dates(params).then((_) => {
        this.isActionShow = true;
      });
    },
    closeChildModal() {
      this.isActionShow = true;
      this.parent.typeDateForm.reset();
    },
    showChildModal(modalName) {
      this.isActionShow = false;
      this.parent.typeDateForm.action = "Add";

      // console.log(this.parent.typeDateForm);
      this.$bvModal.show(modalName);
    },
    onSave() {
      let form = this.parent.articleForm;
      let action = form.action;

      if (!this.checkTypeDate()) {
        return false;
      }
      this.parent.btnloading = true;
      let params = {
        id: form.id,
        brand_id: this.$brand.id,
        title: form.title,
        link: form.link,
        type_dates: this.setTypeDateId(),
        publish_date: form.publish_date,
        actors: form.actors,
        action: action,
        api_token: this.$user.api_token,
        locale: action === "Add" ? this.$i18n.locale : this.parent.language,
      };

      this.post_article(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$emit("done-success", resp.data);
          this.$bvModal.hide("article-modal");
        })
        .catch((error) => {
          if (error.response) {
            form.errors.record(error.response.data.errors);
            if (form.title) {
              this.parent.errorToast(
                `${this.$t("errors.duplicate")}!`,
                form.errors.get("title")
              );
            }
          }
          this.parent.btnloading = false;
        });
    },
    formReset() {
      this.parent.articleForm.reset();
      this.parent.typedatesForm = [];
      this.parent.isAddTypeDate = false;
    },
    onCheckAddTypeDate() {
      if (this.parent.isAddTypeDate) {
        this.parent.typedatesForm = [{ name: "", date: "" }];
      } else {
        let vm = this;
        $.confirm({
          title: this.$t("confirm_action"),
          content: this.$t("label.delete_type_date_confirmation"),
          type: "red",
          typeAnimated: true,
          buttons: {
            tryAgain: {
              text: this.$t("confirm"),
              btnClass: "btn-red",
              action: function () {
                vm.parent.typedatesForm = [];
                console.log("ok");
              },
            },
            cancel: function () {
              console.log("cancel");
              vm.parent.isAddTypeDate = true;
            },
          },
        });
      }
    },
    onAddTypeDate() {
      this.parent.typedatesForm.push({
        name: "",
        date: "",
      });
    },

    setTypeDateId() {
      if (this.parent.typedatesForm.length < 1) {
        return null;
      }

      let items = [];

      this.parent.typedatesForm.forEach((ele) => {
        items.push({
          id: ele.name.id,
          date: ele.date,
        });
      });
      return items;
    },

    checkTypeDate() {
      let key = true;
      if (this.parent.typedatesForm.length > 0) {
        this.parent.typedatesForm.forEach((ele) => {
          if (!ele.name || !ele.date) {
            this.parent.errorToast(
              this.$t("errors.error"),
              `${this.$t("label.type_of_date")} ${this.$t(
                "errors.field_is_required"
              )}`
            );
            key = false;
          }
        });
      }
      return key;
    },
  },
};
</script>

 