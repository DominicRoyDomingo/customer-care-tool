<template>
  <div class="create">
    <b-modal
      id="article-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
    >
      <v-app id="create__container">
        <v-card>
          <form
            class="form"
            @submit.prevent="on_submit_form"
            @keyup="parent.articleForm.errors.clear($event.target.name)"
          >
            <v-card-title class="title blue-grey lighten-4 text-capitalize">
              <span v-if="parent.articleForm.action == 'Add'">
                {{ $t("publishing_item_add_new_button") }}
              </span>
              <span
                class="d-inline-block text-truncate"
                style="max-width: 700px; letter-spacing: normal"
                v-b-tooltip.hover
                :title="parent.itemSelected.base_name"
                v-else
              >
                {{ $t("button.edit") }} "{{ parent.itemSelected.base_name }}"
              </span>
              <v-spacer></v-spacer>
              <v-btn icon color="error lighten-2" @click="on_close">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-card-title>
            <v-card-text class="modal__content" style="">
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
                        :label="parent.set_placeholder()"
                        id="title"
                        type="text"
                        name="title"
                        v-model="parent.articleForm.title"
                        hide-details="auto"
                        autocomplete="off"
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

                    <br />
                    <div class="form-group">
                      <v-select
                        id="item_type"
                        :options="itemTypes"
                        v-model="parent.articleForm.item_type"
                        name="item_type"
                        label="base_name"
                        multiple
                        required
                        :disabled="isLoadingSelect"
                        :loading="isLoadingSelect"
                        :placeholder="`${$t('select_placeholder')} ${$t(
                          'type_of_publising_item'
                        )}`"
                        @input="on_select_course"
                      >
                        <template #list-header>
                          <li class="mb-2 mt-1 ml-0">
                            <b-link
                              :title="`${$t('label.new')} ${$t('item_type')}`"
                              href="javascript:;"
                              @click="onAddItemType"
                            >
                              <i class="fa fa-plus" aria-hidden="true"></i>
                              {{ $t("item_type") }}
                            </b-link>
                          </li>
                        </template>
                        <template #option="{ item_type_name }">
                          <span v-html="item_type_name" />
                        </template>

                        <template #spinner="{ loading }">
                          <b-spinner
                            v-if="loading"
                            small
                            label="Small Spinner"
                            class="text-primary"
                          />
                        </template>
                      </v-select>
                    </div>

                    <div class="form-group">
                      <v-text-field
                        :label="`https://... ${$t(
                          'publishing'
                        ).toUpperCase()} URL`"
                        id="link"
                        type="text"
                        name="link"
                        v-model="parent.articleForm.link"
                        hide-details="auto"
                      />
                    </div>
                    <br />

                    <div class="form-group">
                      <v-tabs
                        show-arrows
                        center-active
                        grow
                        background-color="blue-grey lighten-5"
                        slider-color="blue-grey darken-2"
                        color="blue-grey darken-2"
                        v-model="tabs"
                      >
                        <v-tab class="caption font-weight-bold">
                          {{ $t("authors") }}
                        </v-tab>
                        <v-tab class="caption font-weight-bold">
                          {{ $t("dates") }}
                        </v-tab>
                        <v-tab class="caption font-weight-bold">
                          {{ $t("images") }}
                        </v-tab>
                        <v-tab
                          class="caption font-weight-bold"
                          v-if="isCourSelect"
                        >
                          {{ $t("course_details") }}
                        </v-tab>
                        <v-tab
                          class="caption font-weight-bold"
                          v-if="isCourSelect"
                        >
                          {{ $t("label.other_info") }}
                        </v-tab>
                        <v-tab-item>
                          <div class="form-group">
                            <b-form-checkbox
                              id="isAuthor"
                              name="isAuthor"
                              v-model="parent.isAddAuthor"
                              @change="onCheckAddAuthor"
                            >
                              {{ $t("author_slot") }}
                            </b-form-checkbox>
                          </div>
                          <template v-if="parent.isAddAuthor">
                            <AuthorSlotForm :parent="this" />
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
                          </template>
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
                        <v-tab-item>
                          <div class="form-group">
                            <b-form-checkbox
                              id="isAddImage"
                              name="isAddImage"
                              v-model="parent.isAddImage"
                              @change="onCheckAddImage"
                            >
                              {{ $t("image_slot") }}
                            </b-form-checkbox>
                          </div>
                          <template v-if="parent.isAddImage">
                            <ImageSlotForm :parent="this" />
                            <div class="form-group mt-5">
                              <v-btn
                                color="success"
                                tile
                                block
                                class="bg-success text-white"
                                @click="onAddImageSlot"
                              >
                                {{ $t("label.add") }} {{ $t("image_slot") }}
                              </v-btn>
                            </div>
                          </template>
                        </v-tab-item>
                        <v-tab-item>
                          <CourseSlotForm
                            v-if="isCourSelect"
                            :course_info="item.course_info"
                            :course="course"
                            @course-form="on_course_form"
                          />
                        </v-tab-item>
                        <v-tab-item>
                          <InformationTypeSlot
                            v-if="isCourSelect"
                            :course_info_types="item.course_information_types"
                            @information-type-form="on_information_type_form"
                          />
                        </v-tab-item>
                      </v-tabs>
                    </div>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>
            <v-divider style="margin-bottom: 0"></v-divider>
            <v-card-actions class="modal__footer blue-grey lighten-5">
              <v-spacer></v-spacer>
              <v-btn color="error" text tile @click="on_close">
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
    <div>
      <CreateAuthor
        :parent="this"
        @loadTable="loadActors"
        @done-success="isActionShow = true"
        @on-close="closeChildModal"
      />

      <CreateMedicalTermModal :parent="this" @done-success="loadTerms()" />

      <CreateJobCategoryModal
        :parent="this"
        @loadTable="loadActors"
        @done-success="isActionShow = true"
        @on-close="closeChildModal"
      />

      <CreateJobDescriptionModal
        :parent="this"
        @loadTable="loadActors"
        @done-success="isActionShow = true"
        @on-close="closeChildModal"
      />

      <CreateJobProfessionModal
        :parent="this"
        @loadTable="loadActors"
        @done-success="isActionShow = true"
        @on-close="closeChildModal"
      />

      <CreatePhysicalCodeModal
        :$this="this"
        @loadTable="loadActors"
        @done-success="isActionShow = true"
        @on-close="closeChildModal"
      />

      <CreateInformationModal
        :$this="this"
        @loadTable="loadActors"
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
        @loadTable="loadAutorType"
        @on-close="closeChildModal"
      />

      <ArticleCreateAddHtmlTag
        :parent="this.parent"
        @done-success="loadHtmlTags"
        @on-close="closeChildModal"
      />

      <CreateItemType :$this="this" @show-modal="on_show_modal" />
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import CreateAuthor from "./../../includes/actor/CreateComponent";
import CreateJobCategoryModal from "./../../includes/actor/jobs_modal/JobCategoryModal";
import CreateJobDescriptionModal from "./../../includes/actor/jobs_modal/JobDescriptionModal";
import CreateJobProfessionModal from "./../../includes/actor/jobs_modal/JobProfessionModal";
import CreatePhysicalCodeModal from "./../../includes/physical-code-type/CreateComponent";
import CreateInformationModal from "./../../includes/information-type/CreateComponent";
import CreateMedicalTermModal from "./../../includes/actor/CreateLinkTermComponent.vue";

import CreateItemType from "./../../includes/item-type/CreateComponent";

import ArticleCreateAddTypeDate from "./create-typedate.modal";
import ArticleCreateAddTypeAuthor from "./create-type-author.modal";
import ArticleCreateAddHtmlTag from "./create-html-tag.modal";

import TypeDateForm from "./../snippets/type_date_form";
import AuthorSlotForm from "./../snippets/authors_slot";
import ImageSlotForm from "./../snippets/images_slot";
import CourseSlotForm from "./../snippets/course_slot";
import InformationTypeSlot from "./../snippets/information_type_slot";
import actorMixin from "./../mixins/actorMixin";

import text from "./../../../helpers/text.js";

export default {
  props: ["parent"],

  mixins: [actorMixin],

  components: {
    ArticleCreateAddTypeDate,
    ArticleCreateAddTypeAuthor,
    ArticleCreateAddHtmlTag,
    CreateAuthor,
    TypeDateForm,
    AuthorSlotForm,
    ImageSlotForm,
    CreateJobCategoryModal,
    CreateJobDescriptionModal,
    CreateJobProfessionModal,
    CreatePhysicalCodeModal,
    CreateInformationModal,
    CreateMedicalTermModal,
    CreateItemType,
    CourseSlotForm,
    InformationTypeSlot,
  },

  data() {
    return {
      isLoadingSelect: true,
      isActionShow: true,
      isAuthorShow: false,
      modalId: "",
      itemTypes: [],
      tabs: 0,

      course: {},
      courses: ["course", "courses", "corso", "corsi"],
      isCourSelect: false,
      courseForm: [],
      informationTypeForm: [],
      item: this.parent.itemSelected,

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
      },

      isCourseFormShow: false,
      test: "",
    };
  },

  created() {
    this.loadTypeDates();
    this.loadAutorType();
    this.loadHtmlTags();
    this.loadItemType();
    this.loadTerms();
    this.loadActors();
  },

  mounted() {
    this.check_course();
  },

  computed: {
    ...mapGetters("categ_terms", {
      typedates: "get_type_dates_items",
      htmlTags: "get_html_tags_items",
      professionalTerms: "get_professional_terms_items",
    }),

    ...mapGetters("actor", {
      actors: "actors",
    }),
  },

  methods: {
    ...mapActions("categ_terms", [
      "post_article",
      "get_type_dates",
      "get_type_authors",
      "get_html_tags",
      "get_professional_terms"
    ]),

    ...mapActions("actor", ["get_actors"]),

    check_course() {
      if (this.item) {
        let dataArray = this.item.publishing_item_type_articles.filter(
          (item) => {
            let basename = item.base_name.toLowerCase();
            return this.courses.includes(basename);
          }
        );

        if (dataArray.length > 0) {
          this.course = dataArray[0];
          this.isCourSelect = true;
          this.tabs = 3;
        }
      }
    },

    loadActors() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        brand_id: this.$brand.id,
      };
      this.get_actors(params).then((_) => {
        this.parent.actors_list = this.actors;
      });
    },

    on_select_course(items) {
      this.tabs = 0;
      this.isCourSelect = false;

      let dataArray = items.filter((item) => {
        let basename = item.base_name.toLowerCase();
        return this.courses.includes(basename);
      });

      if (dataArray.length > 0) {
        this.course = dataArray[0];
        this.$nextTick(() => {
          this.isCourSelect = true;
          this.tabs = 3;
        });
      }
    },

    on_close() {
      this.isCourSelect = false;
      this.isActionShow = true;
      this.formReset();
      this.$emit("on-close");
    },

    loadAutorType() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
      };
      this.get_type_authors(params).then((_) => {
        this.author_type_list = this.type_authors;
      });
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

    loadHtmlTags() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
      };

      this.get_html_tags(params).then((_) => {
        this.isActionShow = true;
      });
    },

    loadItemType() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        // brand_id: this.$brand.id,
      };
      axios.get("/api/select/item_types/all", { params }).then((resp) => {
        this.isLoadingSelect = false;
        this.itemTypes = resp.data;
      });
    },

    async loadTerms() {
      this.loadProfessionalTerms();
      let params = {
        ...this.termDefaultParams,
        perPage: this.termsPerPage,
        page: this.termsCurrentPage,
        filter: this.termsFilter,
      };

      axios
        .get("/api/terms", { params })
        .then((response) => {
          let items = response.data;
          this.terminologies = items.data;
          this.termsTotalRow = this.filter ? items.data.length : items.total;
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.tableTermLoading = false;
          this.isTermOverlay = false;
        });
    },

    loadProfessionalTerms() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
      };
      this.get_professional_terms(params).then((_) => {
      });
    },

    onAddItemType() {
      this.modal.add.isVisible = true;
      this.$bvModal.hide("article-modal");
    },

    on_show_modal() {
      this.$bvModal.show("article-modal");
    },

    closeChildModal() {
      this.isActionShow = true;
      this.parent.typeDateForm.reset();
      this.parent.htmlTagForm.reset();
    },

    showChildModal(modalName) {
      this.isActionShow = false;
      this.parent.typeDateForm.action = "Add";
      this.parent.htmlTagForm.action = "Add";

      // console.log(this.parent.typeDateForm);
      this.$bvModal.show(modalName);
    },

    on_information_type_form(items) {
      this.informationTypeForm = [];
      items.forEach((item) => {
        if (item.information_type) {
          let data = {
            information: item.information,
            information_type: item.information_type.id,
            isDisable: item.isDisable,
          };
          this.informationTypeForm.push(data);
          return;
        }
      });
    },

    on_course_form(items) {
      this.courseForm = [];
      items.forEach((item) => {
        if (item.course_types && item.course_types.length > 0) {
          // console.log(item.course_types);
          this.courseForm.push(item);
          return;
        }
      });
    },

    on_submit_form() {
      // console.log(this.courseForm);
      this.parent.btnloading = true;
      // return;
      let form = this.parent.articleForm;
      let action = form.action;
      let itemTypes = this.setObjectToArray(form.item_type);

      if (!this.checkAuthorSlot()) {
        return false;
      }

      if (!this.checkTypeDate()) {
        return false;
      }
      this.parent.btnloading = true;
      let formData = new FormData();
      this.setImageSlot(formData);
      formData.append("id", form.id);
      formData.append("brand_id", this.$brand.id);
      formData.append("title", form.title);
      formData.append("link", form.link == null ? "" : form.link);
      formData.append("type_dates", JSON.stringify(this.setTypeDateId()));
      formData.append("publish_date", form.publish_date);
      formData.append("actors", JSON.stringify(this.setAuthorSlotId()));
      formData.append("actor_type", "");
      formData.append(
        "item_types",
        itemTypes.length > 0 ? JSON.stringify(itemTypes) : ""
      );
      formData.append("action", action);
      formData.append("api_token", this.$user.api_token);
      formData.append(
        "course_form",
        this.courseForm.length > 0 ? JSON.stringify(this.courseForm) : ""
      );
      formData.append(
        "information_type_form",
        this.informationTypeForm.length > 0
          ? JSON.stringify(this.informationTypeForm)
          : ""
      );

      formData.append(
        "locale",
        action === "Add" ? this.$i18n.locale : this.parent.language
      );

      this.post_article(formData)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$emit("done-success", resp.data);
          this.$bvModal.hide("article-modal");
        })
        .catch((error) => {
          if (error.response) {
            form.errors.record(error.response.data.errors);
            let errors = error.response.data.errors;
            this.toastError(errors);
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
      this.parent.authorSlotForm = [];
      this.parent.isAddTypeDate = false;
      this.parent.isAddAuthor = false;
      this.parent.isAddImage = false;
    },

    onCheckAddAuthor() {
      if (this.parent.isAddAuthor) {
        this.parent.authorSlotForm = [{ actors: "", actor_type: "" }];
      } else {
        this.parent.authorSlotForm = [];
      }
    },

    onCheckAddImage() {
      if (this.parent.isAddImage) {
        this.parent.imageSlotForm = [
          {
            image: null,
            imagePlaceholder: null,
            htmlTags: [],
            id: null,
          },
        ];
      } else {
        this.parent.imageSlotForm = [];
      }
    },

    onCheckAddTypeDate() {
      if (this.parent.isAddTypeDate) {
        this.parent.typedatesForm = [{ name: "", date: "" }];
      } else {
        let vm = this;
        vm.parent.typedatesForm = [];
        // $.confirm({
        //   title: this.$t("confirm_action"),
        //   content: this.$t("label.delete_type_date_confirmation"),
        //   type: "red",
        //   typeAnimated: true,
        //   buttons: {
        //     tryAgain: {
        //       text: this.$t("confirm"),
        //       btnClass: "btn-red",
        //       action: function () {
        //         vm.parent.typedatesForm = [];
        //         console.log("ok");
        //       },
        //     },
        //     cancel: function () {
        //       console.log("cancel");
        //       vm.parent.isAddTypeDate = true;
        //     },
        //   },
        // });
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

    onAddImageSlot() {
      this.parent.imageSlotForm.push({
        image: null,
        imagePlaceholder: null,
        htmlTags: [],
        id: null,
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

    setObjectToArray(array) {
      if (array.length < 1 && !array) {
        return [];
      }
      let items = [];
      array.forEach((ele) => {
        items.push(ele.id);
      });
      return items;
    },

    searchProfession(termTypes) {
      let professionKeywords = [
        'Professional', 
        'Professionals', 
        'Professionista',
      ]

      for (var i=0; i < termTypes.length; i++) {
          for (const [key, value] of Object.entries(JSON.parse(termTypes[i].name))) {
            if (professionKeywords.includes(value)) {
              return termTypes[i];
            }
          }
      }
    },

    setAuthorSlotId() {
      if (this.parent.authorSlotForm.length < 1) {
        return null;
      }

      let items = [];

      this.parent.authorSlotForm.forEach((ele) => {
        items.push({
          actors: ele.actors,
          actor_type: ele.actor_type !== null ? ele.actor_type.id : "",
        });
      });
      return items;
    },

    toastError(error) {
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        if (key == "images")
          this.parent.makeToast("danger", vm.$t("errors.error"), value);
      }
    },

    setImageSlot(formData) {
      let tagsIdArr = [];
      let tagsObject = {};
      let idArr = [];
      let sortedImageSlots = this.parent.imageSlotForm.sort(
        this.sortImageSlot()
      );
      for (var i = 0; i < sortedImageSlots.length; i++) {
        let tagsId = [];
        if (
          sortedImageSlots[i]["imagePlaceholder"] != null ||
          sortedImageSlots[i]["htmlTags"].length != 0
        ) {
          sortedImageSlots[i]["htmlTags"].forEach(function (tags) {
            tagsId.push(tags.id);
          });
          idArr.push(sortedImageSlots[i]["id"]);
          tagsIdArr.push(tagsId);
          tagsObject = {
            tagsId: tagsIdArr,
            id: idArr,
          };
          let file = sortedImageSlots[i]["image"];
          let placeHolder = sortedImageSlots[i]["imagePlaceholder"];
          formData.append("images[" + i + "]", file);
          formData.append("tags", JSON.stringify(tagsObject));
          formData.append("imagePlaceholders[" + i + "]", placeHolder);
        }
      }
    },

    sortImageSlot() {
      return function (a, b) {
        if (a.htmlTags === b.htmlTags) {
          return 0;
        } else if (a.htmlTags.length === 0) {
          return 1;
        } else if (b.htmlTags.length === 0) {
          return -1;
        }
      };
    },

    checkAuthorSlot() {
      let key = true;
      if (this.parent.authorSlotForm.length > 0) {
        this.parent.authorSlotForm.forEach((ele) => {
          if (ele.actor_type && ele.actors.length === 0) {
            this.parent.errorToast(
              this.$t("errors.error"),
              `${this.$t("author_slot")} ${this.$t("errors.field_is_required")}`
            );
            key = false;
          } else if (!ele.actor_type && ele.actors.length > 0) {
            this.parent.errorToast(
              this.$t("errors.error"),
              `${this.$t("author_slot")} ${this.$t("errors.field_is_required")}`
            );
            key = false;
          }
        });
      }
      return key;
    },

    checkTypeDate() {
      let key = true;
      if (this.parent.typedatesForm.length > 0) {
        this.parent.typedatesForm.forEach((ele) => {
          if (ele.name && !ele.date) {
            this.parent.errorToast(
              this.$t("errors.error"),
              `${this.$t("label.type_of_date")} ${this.$t(
                "errors.field_is_required"
              )}`
            );
            key = false;
          } else if (!ele.name && ele.date) {
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
<style>
.v-window {
  overflow: inherit !important;
}
</style>
 