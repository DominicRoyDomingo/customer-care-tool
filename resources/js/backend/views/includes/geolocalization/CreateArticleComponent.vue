6<template>
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
          : $t('button.edit')
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
                {{ $t("button.new_topic") }}
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
                      <br />
                      <div class="form-group">
                        <v-select
                          id="item_type"
                          :options="parent.itemTypes"
                          v-model="parent.articleForm.item_type"
                          name="item_type"
                          label="base_name"
                          multiple
                          required
                          :disabled="parent.isLoadingSelect"
                          :loading="parent.isLoadingSelect"
                          :placeholder="`${$t('select_placeholder')} ${$t(
                            'type_of_publising_item'
                          )}`"
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
                            <div
                              v-if="loading"
                              style="
                                border-left-color: rgba(88, 151, 251, 0.71);
                              "
                              class="vs__spinner"
                            >
                              The .vs__spinner class will hide the text for me.
                            </div>
                          </template>
                        </v-select>
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
                          <v-tab class="caption font-weight-bold">
                            {{ $t("images") }}
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
      @loadTable="loadActors"
      @done-success="isActionShow = true"
      @on-close="closeChildModal"
    />

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
    <CreateMedicalTermModal :parent="this" @done-success="loadTerms()" />
    <CreateItemType :$this="this" @show-modal="on_show_modal" />
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CreateAuthor from "./../../includes/actor/CreateComponent";
import CreateMedicalTermModal from "./../../includes/actor/CreateLinkTermComponent.vue";

import CreateJobCategoryModal from "./../../includes/actor/jobs_modal/JobCategoryModal";
import CreateJobDescriptionModal from "./../../includes/actor/jobs_modal/JobDescriptionModal";
import CreateJobProfessionModal from "./../../includes/actor/jobs_modal/JobProfessionModal";
import CreatePhysicalCodeModal from "./../../includes/physical-code-type/CreateComponent";
import CreateInformationModal from "./../../includes/information-type/CreateComponent";

import CreateItemType from "./../../includes/item-type/CreateComponent";

import CreateTypeDate from "../../medical_stuff/modals/create-typedate.modal";
import ArticleCreateAddTypeDate from "../../medical_stuff/modals/create-typedate.modal";
import ArticleCreateAddTypeAuthor from "../../medical_stuff/modals/create-type-author.modal";
import ArticleCreateAddHtmlTag from "./CreateHtmlTagComponent";

import TypeDateForm from "../../medical_stuff/snippets/type_date_form";
import AuthorSlotForm from "../../medical_stuff/snippets/authors_slot";
// import ImageSlotForm from "../../medical_stuff/snippets/images_slot";
import ImageSlotForm from "./snippets/images_slot";
import actorMixin from "../../medical_stuff/mixins/actorMixin";
import Form from "./../../../shared/form.js";
// import medicalMixin from "./../mixins/medicalMixin";

export default {
  props: ["parent"],

  mixins: [actorMixin],

  components: {
    CreateTypeDate,
    ArticleCreateAddTypeDate,
    ArticleCreateAddTypeAuthor,
    ArticleCreateAddHtmlTag,
    ImageSlotForm,
    CreateJobCategoryModal,
    CreateJobDescriptionModal,
    CreateJobProfessionModal,
    CreatePhysicalCodeModal,
    CreateInformationModal,
    CreateAuthor,
    TypeDateForm,
    AuthorSlotForm,
    CreateItemType,
    CreateMedicalTermModal

  },

  data() {
    return {
      isActionShow: true,
      modalId: "",
      itemTypes: [],
      terminologies: [],
      termsCurrentPage: 1,
      termsPerPage: 10,
      termsTotalRow: 1,
      tableTermLoading: true,
      isTermOverlay: false,

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

      mtForm: new Form({
        id: "",
        name: "",
        term_types: "",
        specializations: "",
        action: "",
        file: "",
      }),
    };
  },

  mounted() {
    this.loadTypeDates();
    this.loadAutorType();
    this.loadHtmlTags();
    this.loadTerms();
    this.loadActors();
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

    termDefaultParams() {
      return {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
      };
    },
  },

  methods: {
    ...mapActions("categ_terms", ["post_article", "get_type_dates", "get_type_authors", "get_html_tags", "get_professional_terms"]),
    ...mapActions("actor", ["get_actors"]),
    onClose() {
      this.isActionShow = true;
      this.$emit("on-close");
      this.formReset();
      this.$bvModal.hide("article-modal");
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

    loadTypeDates() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
      };

      this.get_type_dates(params).then((_) => {
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
        this.parent.isLoadingSelect = false;
        this.parent.itemTypes = resp.data;
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

    loadHtmlTags() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
      };

      this.get_html_tags(params).then((_) => {
        this.isActionShow = true;
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
      console.log(modalName)
      // console.log(this.parent.typeDateForm);
      this.$bvModal.show(modalName);
    },
    onSave() {
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
        "locale",
        action === "Add" ? this.$i18n.locale : this.parent.language
      );

      this.post_article(formData)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$emit("done-success", resp.data);
          this.parent.loadAlgoliaSummary();
          this.$bvModal.hide("article-modal");
        })
        .catch((error) => {
          if (error.response) {
            form.errors.record(error.response.data.errors);
            let errors = error.response.data.errors;
            this.toastError(errors)
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

    onCheckAddImage() {
      if (this.parent.isAddImage) {
        this.parent.imageSlotForm = [{ 
          image: null,
          imagePlaceholder: null,
          htmlTags: [],
          id: null, 
        }];
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

    setAuthorSlotId() {
      if (this.parent.authorSlotForm.length < 1) {
        return null;
      }

      let items = [];

      
      this.parent.authorSlotForm.forEach((ele) => {
        items.push({
          actors: ele.actors,
          actor_type: ( ele.actor_type !== null ? ele.actor_type.id : '' ),
        });
      });
      return items;
    },

    setImageSlot(formData) {
      let tagsIdArr = []
      let tagsObject = {}
      let idArr = []
      let sortedImageSlots = this.parent.imageSlotForm.sort(this.sortImageSlot())
      for( var i = 0; i < sortedImageSlots.length; i++ ){
        let tagsId = []
        if(sortedImageSlots[i]['imagePlaceholder'] != null  || sortedImageSlots[i]['htmlTags'].length != 0) {
            sortedImageSlots[i]['htmlTags'].forEach(function(tags) {
              tagsId.push(tags.id)
            })
            idArr.push(sortedImageSlots[i]['id'])
            tagsIdArr.push(tagsId);
            tagsObject = {
              tagsId: tagsIdArr,
              id: idArr
            }
            let file = sortedImageSlots[i]['image'];
            let placeHolder = sortedImageSlots[i]['imagePlaceholder']
            formData.append('images[' + i + ']', file);
            formData.append('tags', JSON.stringify(tagsObject))
            formData.append('imagePlaceholders[' + i + ']', placeHolder)
        }
      }
      
    },

    sortImageSlot() {
      return function (a, b) {

        if (a.htmlTags === b.htmlTags) {
            return 0;
        }

        else if (a.htmlTags.length === 0) {
            return 1;
        }
        else if (b.htmlTags.length === 0) {
            return -1;
        }

      };

    },

    toastError(error){
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        if(key == 'images') this.parent.makeToast("danger", vm.$t('errors.error'), value);
      }
    },

    checkAuthorSlot() {
      let key = true;
      if (this.parent.authorSlotForm.length > 0) {
        this.parent.authorSlotForm.forEach((ele) => {
          if (ele.actor_type && ele.actors.length === 0) {
            this.parent.errorToast(
              this.$t("errors.error"),
              `${this.$t("author_slot")} ${this.$t(
                "errors.field_is_required"
              )}`
            );
            key = false;
          }

          else if ( !ele.actor_type && ele.actors.length > 0) {
            this.parent.errorToast(
              this.$t("errors.error"),
              `${this.$t("author_slot")} ${this.$t(
                "errors.field_is_required"
              )}`
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
          }

          else if ( !ele.name && ele.date) {
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
 