<template>
  <div class="create">
    <b-modal
      id="article-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
      :title="
        parent.articleForm.action == 'Add'
          ? $t('label.add')
          : $t('button.update')
      "
    >
      <v-app id="create__container">
        <v-card>
          <form
            class="form"
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
              <v-btn icon color="error lighten-2" @click="onClose">
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
                          <label
                            class="mr-sm-2"
                            for="inline-form-custom-select-pref"
                          >
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
                      <div class="form-group">
                        <v-tabs
                          show-arrows
                          center-active
                          grow
                          background-color="blue-grey lighten-5"
                          slider-color="blue-grey darken-2"
                          color="blue-grey darken-2"
                        >
                          <v-tab class="caption font-weight-bold">
                            {{ $t("authors") }}
                          </v-tab>
                          <v-tab class="caption font-weight-bold">
                            {{ $t("dates") }}
                          </v-tab>
                          <v-tab-item>
                            <AuthorSlotForm :parent="this" />

                            <!-- <div class="form-group">
                                <label for="actor" class="text-uppercase">
                                  {{ $t("authors") }}
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
                              <div class="form-group">
                                <label for="actor" class="text-uppercase">
                                  {{ $t("type_of_dates") }}
                                </label>
                                <v-select
                                  id="actor"
                                  :options="parent.type_authors"
                                  v-model="parent.articleForm.actor_type"
                                  name="actor_type"
                                  label="name"
                                  required
                                >
                                  <template #list-header>
                                    <li style="margin-left: 20px" class="mb-2">
                                      <b-link
                                        v-b-tooltip.hover
                                        :title="`${$t('label.new')} ${$t(
                                          'type_of_dates'
                                        )}`"
                                        href="javascript:;"
                                        @click="showChildModal('add-author-type-modal')"
                                      >
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        {{ $t("label.new") }} {{ $t("type_of_dates") }}
                                      </b-link>
                                    </li>
                                  </template>
                                </v-select>
                              </div> -->
                            <div class="form-group mt-5">
                              <v-btn
                                color="success"
                                tile
                                block
                                class="bg-success text-white"
                                @click="onAddAuthorSlot"
                              >
                                {{ $t("label.add") }} {{ $t("author_slot") }}
                              </v-btn>
                            </div>
                          </v-tab-item>
                          <v-tab-item>
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
                                  {{ $t("label.add") }}
                                  {{ $t("label.type_of_date") }}
                                </v-btn>
                              </div>
                            </template>
                          </v-tab-item>
                        </v-tabs>
                      </div>
                    </v-col>
                  </v-row>
                </v-container>
              </v-container>
            </v-card-text>
            <v-divider style="margin-bottom: 0"></v-divider>
            <v-card-actions class="modal__footer blue-grey lighten-5">
              <v-spacer></v-spacer>
              <v-btn color="error" text tile @click="onClose">
                {{ $t("cancel") }}
              </v-btn>
              <v-btn color="success" tile type="submit">
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

    <ArticleCreateAddTypeAuthor
      :parent="this.parent"
      @loadTable="parent.loadAutorType"
      @on-close="closeChildModal"
    />
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CreateAuthor from "./../../../includes/actor/CreateComponent";
import CreateTypeDate from "../../../medical_stuff/modals/create-typedate.modal";
import ArticleCreateAddTypeDate from "../../../medical_stuff/modals/create-typedate.modal";
import ArticleCreateAddTypeAuthor from "../../../medical_stuff/modals/create-type-author.modal";

import TypeDateForm from "../../../medical_stuff/snippets/type_date_form";

import AuthorSlotForm from "../../../medical_stuff/snippets/authors_slot";

import actorMixin from "../../../medical_stuff/mixins/actorMixin";
// import medicalMixin from "./../mixins/medicalMixin";

export default {
  props: ["parent"],

  mixins: [actorMixin],

  components: {
    CreateTypeDate,
    ArticleCreateAddTypeDate,
    ArticleCreateAddTypeAuthor,
    CreateAuthor,
    TypeDateForm,
    AuthorSlotForm,

  },

  data() {
    return {
      isActionShow: true,
      typedates: [],
    };
  },

  mounted() {
    this.loadTypeDates();
  },

  computed: {
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

      axios.get("/api/type-dates", { params }).then((resp) => {
        this.isActionShow = true;
        this.typedates = resp.data;
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
      let action = this.parent.articleForm.action;
      if (!this.checkTypeDate()) {
        return false;
      }
      this.parent.btnloading = true;
      let params = {
        id: this.parent.articleForm.id,
        brand_id: this.$brand.id,
        title: this.parent.articleForm.title,
        link: this.parent.articleForm.link,
        type_dates: this.setTypeDateId(),
        publish_date: this.parent.articleForm.publish_date,
        actors: this.parent.articleForm.actors,
        action: action,
        api_token: this.$user.api_token,
        locale: action === "Add" ? this.$i18n.locale : this.parent.language,
      };
      
      this.post_article(params)
        .then((resp) => {
          this.parent.btnloading = false;
          let recordName = this.$t("label.articles");
          if (action === "Add") {
            this.parent.storeToast(params.title, recordName);
          } else {
            this.parent.updateToast(params.id, recordName);
          }

          this.formReset();

          this.$emit("done-success");
          this.$bvModal.hide("article-modal");
        })
        .catch((error) => {
          if (error.response) {
            this.parent.articleForm.errors.record(error.response.data.errors);

            if (this.parent.articleForm.title) {
              this.parent.errorToast(
                `${this.$t("errors.duplicate")}!`,
                this.parent.articleForm.errors.get("title")
              );
            }
          }

          this.parent.btnloading = false;
        });
    },
    formReset() {
      this.parent.articleForm.reset();
      this.parent.typedatesForm = [];
      this.parent.authorSlotForm = [];
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

    onAddAuthorSlot() {
      this.parent.authorSlotForm.push({
        actors: "",
        actor_type: "",
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

    setAuthorSlotId() {
      if (this.parent.authorSlotForm.length < 1) {
        return null;
      }

      let items = [];

      this.parent.authorSlotForm.forEach((ele) => {
        items.push({
          actors: ele.actors,
          actor_type: ele.actor_type.id,
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

 