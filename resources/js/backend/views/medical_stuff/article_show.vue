<style  scoped>
.borderless td,
.borderless th {
  border: none;
}
.button_new {
  border: none;
  color: black;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  margin: 4px 2px;
  cursor: pointer;
  border: 1px solid #c8ced3;
  font-size: 0.765625rem;
  line-height: 1.5;
  padding: 0.5rem !important;
  color: inherit;
  margin-right: 4px !important;
  border-radius: 9999px !important;
}
.button_new:hover {
  cursor: pointer;
  background: #c8ced3;
}
</style>
<template>
  <div class="animated fadeIn">
    <div class="card border-0">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <h4 class="card-title">
              <a href="/admin/software-publishing/items">
                {{ $t("publishing_items") }}
              </a>
              <small
                class="text-muted text-capitalize"
                v-html="article.base_name"
              />
            </h4>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="card mb-0">
              <div class="card-header text-capitalize">
                <span class="font-weight-bold">
                  {{ $t("article") }} {{ $t("label.details") }}
                </span>
                <a
                  href="javascript:;"
                  @click="onEdit"
                  v-b-tooltip.hover
                  :title="`${$t('button.edit')} ${article.base_name}`"
                  class="h5 mb-0 float-right"
                >
                  <i class="fas fa-edit"></i>
                </a>
              </div>
              <div class="card-body">
                <div v-if="isLoading">
                  <b-spinner small label="Spinning"></b-spinner>
                </div>
                <div class="row" v-else>
                  <div class="col-md-12">
                    <h3
                      class="card-title ml-5 text-uppercase"
                      v-html="article.article_title"
                    />
                    <ul class="list-unstyled mt-5">
                      <b-media
                        tag="li"
                        v-for="(flag, flagIndex) in flags"
                        :key="flagIndex"
                        class="my-4"
                        @click="onViewLink(flag)"
                        v-show="flag.link"
                      >
                        <template #aside>
                          <b-img
                            :src="flag.image"
                            style="cursor: pointer"
                            width="64"
                            :alt="flag.link"
                          />
                        </template>
                        <a
                          :href="flag.link"
                          v-if="flag.link !== 'null'"
                          target="_blank"
                          class="mb-0"
                        >
                          {{ flag.link }}
                        </a>
                      </b-media>
                    </ul>
                  </div>
                  <div class="col-md-12" v-if="article.author_slot.length > 0">
                    <h5 class="ml-5 mt-4">{{ $t("type_of_dates") }}</h5>
                    <hr />
                    <table class="table table-condensed borderless mb-0 ml-3">
                      <tbody>
                        <tr
                          v-for="Aut in getAuthorName(article.author_slot)"
                          :key="Aut.id"
                        >
                          <!-- {{ Aut }} -->
                          <td style="width: 30%">
                            <span>{{ Aut.type }} </span>
                          </td>
                          <td>
                            <button
                              class="btn btn-default btn-sm button_new"
                              style=""
                              v-for="author in Aut.authors"
                              :key="author.idauthor"
                            >
                              {{ author }}
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="col-md-12">
                    <h5 class="ml-5 mt-4">{{ $t("history") }}</h5>
                    <hr />

                    <table class="table table-condensed borderless mb-0 ml-3">
                      <tbody>
                        <tr v-for="tDate in article.type_dates" :key="tDate.id">
                          <td style="width: 30%">
                            <span v-html="tDate.type_date_name" />
                          </td>
                          <td>
                            <b-button
                              pill
                              variant="outline-secondary"
                              class="p-2 mr-1"
                              size="sm"
                              v-html="tDate.has_date"
                            />
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <table class="table table-condensed borderless mb-0 ml-3">
                      <tbody>
                        <tr>
                          <td style="width: 30%">Date Created</td>
                          <td>
                            <b-button
                              pill
                              class="p-2 mr-1 text-white"
                              size="sm"
                              v-html="article.date_created"
                            />
                          </td>
                        </tr>
                        <tr>
                          <td>Last Modified</td>
                          <td>
                            <b-button
                              pill
                              class="p-2 mr-1 text-white"
                              size="sm"
                              v-html="article.date_modified"
                            />
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="card mb-0">
              <div class="card-header text-capitalize">
                <span class="font-weight-bold">
                  {{ $t("label.terminilogies") }} Connections
                </span>
                <a
                  href="javascript:;"
                  @click="on_get_terms"
                  v-b-tooltip.hover
                  :title="`Link to ${$t('label.terminilogies')}`"
                  class="h5 float-right mb-0"
                >
                  <i class="fas fa-link"></i>
                </a>
              </div>

              <b-overlay
                id="overlay-background"
                :show="isLinked"
                :variant="'light'"
                opacity="0.85"
                :blur="'1px'"
                rounded="sm"
              >
                <div class="card-body">
                  <div v-if="isLoading">
                    <b-spinner small label="Spinning"></b-spinner>
                  </div>
                  <div class="row" v-else>
                    <div class="col-md-12">
                      <table
                        v-for="(terms, termTypeName, key) in linkedTerms"
                        :key="key"
                        class="table table-condensed borderless mb-0 ml-3"
                      >
                        <tbody>
                          <tr>
                            <td width="30%">
                              <p
                                class="mb-0 text-capitalize ml-2"
                                v-html="termTypeName"
                              />
                            </td>
                            <td>
                              <b-button
                                v-for="(term, termIndex) in terms"
                                :key="termIndex"
                                :href="term.route_show"
                                pill
                                variant="outline-secondary"
                                class="p-2 mr-1 mb-1"
                                size="sm"
                                v-html="term.name"
                              />
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="col-md-12" v-if="linkedTerms.length === 0">
                      <div class="text-center">
                        <h1 class="fa fa-link"></h1>
                        <p>{{ $t("link_to_terminologies") }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </b-overlay>
            </div>
          </div>
          <div class="col-md-12">
            <div class="card mb-0">
              <div class="card-header text-capitalize">
                <span class="font-weight-bold">
                  {{ $t("images") }}
                </span>
              </div>
              <div class="card-body">
                <div class="row" v-if="groupedImages.length != 0">
                  <div
                    class="col-md-12"
                    v-for="(image, i) in groupedImages"
                    :key="i"
                  >
                    <h5 class="ml-5 mt-4">{{ image.description }}</h5>
                    <hr />

                    <table class="table table-condensed borderless mb-0 ml-3">
                      <tbody>
                        <tr>
                          <td
                            style="width: 250px"
                            v-if="image.images.length > 2"
                          >
                            <v-hover v-slot="{ hover }">
                              <v-carousel :cycle="!hover" height="200">
                                <v-carousel-item
                                  v-for="(image, i) in image.images"
                                  :key="i"
                                  :src="
                                    JSON.parse(image.image)[
                                      Object.keys(JSON.parse(image.image))[0]
                                    ]
                                  "
                                  style="width: 250px; height: 200"
                                >
                                  <v-fade-transition>
                                    <v-overlay
                                      v-if="hover"
                                      absolute
                                      color="#282828"
                                      opacity="0.9"
                                    >
                                      <div class="align-self-start">
                                        <v-tooltip top>
                                          <template
                                            v-slot:activator="{ on, attrs }"
                                          >
                                            <v-btn
                                              icon
                                              v-bind="attrs"
                                              v-on="on"
                                              @click="editImage(image)"
                                            >
                                              <v-icon>
                                                mdi-square-edit-outline
                                              </v-icon>
                                            </v-btn>
                                          </template>
                                          <span>Edit</span>
                                        </v-tooltip>
                                        <v-tooltip top>
                                          <template
                                            v-slot:activator="{ on, attrs }"
                                          >
                                            <v-btn
                                              icon
                                              v-bind="attrs"
                                              v-on="on"
                                              @click="deleteImage(image)"
                                            >
                                              <v-icon> mdi-delete </v-icon>
                                            </v-btn>
                                          </template>
                                          <span>Delete</span>
                                        </v-tooltip>
                                      </div>
                                    </v-overlay>
                                  </v-fade-transition>
                                </v-carousel-item>
                              </v-carousel>
                            </v-hover>
                            <div></div>
                          </td>
                          <td v-else>
                            <div class="row">
                              <div
                                class="col-md-3"
                                v-for="(image, i) in image.images"
                                :key="i"
                              >
                                <v-hover v-slot="{ hover }">
                                  <v-img
                                    height="200"
                                    max-width="250"
                                    v-if="image !== null"
                                    :src="
                                      JSON.parse(image.image)[
                                        Object.keys(JSON.parse(image.image))[0]
                                      ]
                                    "
                                    :lazy-src="
                                      JSON.parse(image.image)[
                                        Object.keys(JSON.parse(image.image))[0]
                                      ]
                                    "
                                    class="grey lighten-2"
                                    style="width: 250px"
                                  >
                                    <v-fade-transition>
                                      <v-overlay
                                        v-if="hover"
                                        absolute
                                        color="#282828"
                                        opacity="0.9"
                                      >
                                        <div class="align-self-start">
                                          <v-tooltip top>
                                            <template
                                              v-slot:activator="{ on, attrs }"
                                            >
                                              <v-btn
                                                icon
                                                v-bind="attrs"
                                                v-on="on"
                                                @click="editImage(image)"
                                              >
                                                <v-icon>
                                                  mdi-square-edit-outline
                                                </v-icon>
                                              </v-btn>
                                            </template>
                                            <span>Edit</span>
                                          </v-tooltip>
                                          <v-tooltip top>
                                            <template
                                              v-slot:activator="{ on, attrs }"
                                            >
                                              <v-btn
                                                icon
                                                v-bind="attrs"
                                                v-on="on"
                                                @click="deleteImage(image)"
                                              >
                                                <v-icon> mdi-delete </v-icon>
                                              </v-btn>
                                            </template>
                                            <span>Delete</span>
                                          </v-tooltip>
                                        </div>
                                      </v-overlay>
                                    </v-fade-transition>
                                  </v-img>
                                </v-hover>
                              </div>
                            </div>
                          </td>
                          <td></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="row" v-else>
                  <div class="col-md-12">
                    <b-img
                      class="img-thumbnail"
                      src="https://customer-care-tool.s3.us-east-2.amazonaws.com/no-photo.png"
                      width="200"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <LinkedArticleTerm
      v-if="Object.values(terms).length > 0"
      :parent="this"
      :id="id"
      :terms="terms"
      @done-success="link_success"
    />
    <CreateArticleModal :parent="this" @done-success="update_success" />
    <UpdateImageModal :parent="this" @done-success="update_image_success" />
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import medicalMixin from "./mixins/medicalMixin";

import LinkedArticleTerm from "./modals/link/link-articleTerm.modal";
import CreateArticleModal from "./modals/create-article.modal";
import UpdateImageModal from "./modals/edit-image.modal";

export default {
  props: {
    id: {
      type: Number,
      default: () => {},
    },
  },

  mixins: [medicalMixin],

  components: {
    CreateArticleModal,
    LinkedArticleTerm,
    UpdateImageModal,
  },

  data() {
    return {
      name: "article-show",
      isLinked: false,
      isLoading: true,
      isAddTypeDate: false,
      isAddImage: false,
      article: {},
      flags: [],
      typedatesForm: [],
      linkedMedicalTerm: [],
      groupedImages: [],
      linkedTerms: [],
      linkedTermsId: [],
      items: [],
      filterMedicalTerm: "",
      modalType: "article",
      isShowLink: true,
      authorSlotForm: [],
      imageSlotForm: [],
      actors_list: "",
      htmlTagPriorityName: "",
      isShowHtmlTag: true,
      author_type_list: "",
      authors_array: [],
      isAddAuthor: false,
      image: null,
      imagePlaceholder: null,
      imageId: "",
    };
  },

  mounted() {
    this.loadActors();
    this.loadAutorType();
    this.loadArticle();
  },

  computed: {
    ...mapGetters("actor", {
      actors: "actors",
    }),

    ...mapGetters("categ_terms", {
      response_item: "get_response_item",
      terms: "get_terms_items",
      type_authors: "get_type_author_items",
    }),
  },

  watch: {
    filterMedicalTerm(value) {
      this.loadMedicalTerms();
    },
  },

  methods: {
    ...mapActions("actor", ["get_actors"]),

    ...mapActions("categ_terms", [
      "show_article",
      "get_terms",
      "get_type_authors",
    ]),

    on_get_terms() {
      this.isLinked = true;
      this.linkedTermsId = [];
      this.itemSelected = this.article;

      this.article.medical_terms.forEach((mt) => {
        this.linkedTermsId.push(mt.id);
      });

      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
        id: this.article.id,
        by_article: this.name,
      };

      this.get_terms(params).then((_) => {
        this.isLinked = false;
        this.$bvModal.show("link-to-terminology-modal");
      });
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
        this.actors_list = this.actors;
      });
    },

    loadMedicalTerms() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
      };

      axios.get("/api/terms", { params }).then((resp) => {
        this.items = resp.data;
      });
    },

    loadArticle() {
      this.linkedMedicalTems = [];
      let tagsArr = [];
      this.linkedTerms = [];

      let params = {
        article_id: this.id,
        locale: this.$i18n.locale,
        api_token: this.$user.api_token,
        brand_id: this.$brand.id,
      };

      this.show_article(params).then((resp) => {
        if (this.response_item.article === undefined) {
          window.location.href = "/admin/categorized-terms/articles";
        }

        this.linkedTerms = this.response_item.linked_terms;

        this.article = this.response_item.article;
        this.article.image_slot.forEach(function (imageSlot) {
          if (imageSlot.html_tag_items.length == 0) {
            imageSlot.html_tag_items.push({
              article_image_id: imageSlot.article,
              description: "No HTML Tag Priority",
            });
          }
          imageSlot.html_tag_items.forEach(function (tags, index) {
            tags["article_image_id"] = imageSlot.id;
            tags["image"] = imageSlot.image;
            tagsArr.push(tags);
          });
        });
        
        var groupedTags = tagsArr.reduce(function (obj, tags) {
          obj[tags.description] = obj[tags.description] || [];
          obj[tags.description].push({
            image: tags.image,
            id: tags.article_image_id,
          });
          return obj;
        }, {})

        var tagGroups = Object.keys(groupedTags).map(function (key) {
          return {
            description: key,
            images: groupedTags[key],
          };
        });
        this.groupedImages = tagGroups;
        this.articleForm.id = this.article.id;

        this.setFlags(this.article);
        this.isLoading = false;
        // this.isLinked = false;
      });
    },

    loadLinkedTerms() {},

    update_success(item) {
      let recordName = this.$t("label.articles");
      this.updateToast(item.id, recordName);

      this.typedatesForm = [];
      this.isAddTypeDate = false;
      this.articleForm.reset();
      this.loadArticle();
    },

    update_image_success(item) {
      let recordName = this.$t("label.article_images");
      this.updateToast(item, recordName);

      this.image = null;
      this.imagePlaceholder = null;
      this.imageId = "";
      this.loadArticle();
    },

    link_success() {
      this.loadArticle();
    },

    onEdit() {
      this.articleForm.reset();
      this.language = this.article.base_language;
      this.articleForm.id = this.article.id;
      this.articleForm.title = this.article.base_name;
      this.articleForm.type_dates = this.article.type_dates;
      this.articleForm.link = this.article.base_link;
      this.articleForm.publish_date = this.article.publish_date;
      this.articleForm.actors = this.article.has_authors;
      this.articleForm.item_type = this.article.publishing_item_type_articles;

      this.articleForm.action = "Update";

      this.itemSelected = this.article;

      if (this.article.type_dates.length > 0) {
        this.typedatesForm = this.setTypeDates(this.article.type_dates);
        this.isAddTypeDate = true;
      }

      if (this.article.author_slot.length > 0) {
        this.authorSlotForm = this.setAuthorSlot(this.article.author_slot);
        this.isAddAuthor = true;
      }

      if (this.article.image_slot.length > 0) {
        this.imageSlotForm = this.setImageSlot(this.article.image_slot);
        this.isAddImage = true;
      }

      // Open Modal
      this.$bvModal.show("article-modal");
    },

    onViewLink(flag) {
      var win = window.open(flag.link, "_blank");
      win.focus();
    },

    setTypeDates(data) {
      let items = [];
      data.forEach((ele) => {
        items.push({
          name: ele,
          date: ele.pivot.article_date,
        });
      });
      return items;
    },

    setAuthorSlot(data) {
      let items = [];
      data.forEach((ele) => {
        let id = [];
        let authors = jQuery.parseJSON(ele.authors);
        authors.forEach((ele1) => {
          var act = this.actors_list.find((x) => x.id === ele1);
          id.push({ id: ele1, full_name: act.fullname });
        });
        var type = this.author_type_list.find((x) => x.id === ele.author_type);
        items.push({
          actors: id,
          actor_type: { id: ele.author_type, name: type.name },
        });
      });
      return items;
    },

    setImageSlot(data) {
      let items = [];
      data.forEach((ele) => {
        items.push({
          image: null,
          imagePlaceholder:
            ele.image !== null
              ? JSON.parse(ele.image)[Object.keys(JSON.parse(ele.image))[0]]
              : "",
          htmlTags:
            ele.html_tag_items[0].description != "No HTML Tag Priority"
              ? ele.html_tag_items
              : [],
          id: ele.id,
        });
      });
      return items;
    },

    getAuthorName(data) {
      let article_id = [];
      if (data.length > 0) {
        data.forEach((ele) => {
          let id = [];
          let authors_ = jQuery.parseJSON(ele.authors);
          authors_.forEach((ele1) => {
            var act = this.actors_list.find((x) => x.id === ele1);
            id.push(act.fullname);
          });
          if (this.author_type_list.length > 0) {
            var type = this.author_type_list.find(
              (x) => x.id === ele.author_type
            );
            article_id.push({
              type: type.name,
              authors: id,
            });
          }
        });
        return article_id;
      }
    },

    setFlags(data) {
      this.flags = [
        {
          image:
            "https://upload.wikimedia.org/wikipedia/en/thumb/a/ae/Flag_of_the_United_Kingdom.svg/255px-Flag_of_the_United_Kingdom.svg.png",
          link: data.english_link,
        },
        {
          image:
            "https://upload.wikimedia.org/wikipedia/en/thumb/0/03/Flag_of_Italy.svg/1200px-Flag_of_Italy.svg.png",
          link: data.italian_link,
        },
        {
          image: "https://i.redd.it/n1rtishmecvz.png",
          link: data.bisaya_link,
        },
      ];
    },

    deleteImage(image) {
      console.log(image);
    },

    editImage(image) {
      this.imagePlaceholder = JSON.parse(image.image)[
        Object.keys(JSON.parse(image.image))[0]
      ];
      this.imageId = image.id;
      this.$bvModal.show("edit-image-modal");
    },
  },
};
</script>
