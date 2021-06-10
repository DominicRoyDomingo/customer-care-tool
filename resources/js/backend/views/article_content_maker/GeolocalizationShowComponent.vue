<template>
<div class="animated fadeIn">
    <v-app id="tags__container">
      <v-tabs
        show-arrows
        background-color="#F0F3F5"
        color="#2F353A"
        v-model="tab"
        center-active
        grow
      >
        <v-tab @click="openTab('city')">
          {{ $t("geolocalization_in_cities") }}
        </v-tab>
        <v-tab @click="openTab('province')">
          {{ $t("geolocalization_in_provinces") }}
        </v-tab>
        <v-tab @click="openTab('region')">
          {{ $t("geolocalization_in_regions") }}
        </v-tab>
        <v-divider vertical inset v-if="tab_length > 0"></v-divider>
        <v-tab
          v-if="viewing_template"
          style="background-color: #ebf4fe; color: #329ef4;"
        >
          TEMPLATE
          <v-btn
            icon
            @click="
              [
                (tab_length = 0),
                (tab = 0),
                (viewing_template = false),
              ]
            "
            color="error"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-tab>
          <v-tab-item>
            <GeolocalizationCitiesSlot :parent="this" v-if="isCityOpen"/>
          </v-tab-item>
          <v-tab-item>
            <GeolocalizationProvincesSlot :parent="this" v-if="isProvinceOpen"/>
          </v-tab-item>
          <v-tab-item>
            <GeolocalizationRegionsSlot :parent="this" v-if="isRegionOpen"/>
          </v-tab-item>
          <v-tab-item>
            <div>
              <v-row>
                <v-col cols="12" xs="12" md="6">
                  <v-card outlined class="profile__cards mb-5" max-height="140" height="140">
                    <v-row>
                      <v-col cols="10">
                        <span class="title font-weight-light text--secondary">
                          {{ $t("label.title") }}
                        </span>
                      </v-col>
                      <v-col cols="2">
                        <div class="text-right">
                          
                        </div>
                      </v-col>
                    </v-row>
                    <v-divider></v-divider>
                    <v-row>
                      <v-col @click="editTemplate('title')" v-if="isEditingTitle == false">
                        <span >{{ templateForm.page_title }}</span>
                      </v-col>
                      <v-col v-else>
                        <v-text-field autofocus @blur="cancelInput('title')" v-model="templateForm.page_title" v-on:keyup.esc="cancelInput('title')" v-on:keyup.enter="validateInput('title')"></v-text-field>
                      </v-col>
                    </v-row>
                  </v-card>
                  <v-card outlined class="profile__cards mb-5" max-height="140" height="140">
                    <v-row>
                      <v-col cols="10">
                        <span class="title font-weight-light text--secondary">
                          {{ $t("label.meta_description") }}
                        </span>
                      </v-col>
                      <v-col cols="2">
                        <div class="text-right">
                          
                        </div>
                      </v-col>
                    </v-row>
                    <v-divider></v-divider>
                    <v-row>
                      <v-col @click="editTemplate('meta_description')" v-if="isEditingMetaDescription == false">
                        <span >{{ templateForm.meta_description }}</span>
                      </v-col>
                      <v-col v-else>
                        <v-text-field autofocus @blur="cancelInput('meta_description')" v-model="templateForm.meta_description" v-on:keyup.esc="cancelInput('meta_description')" v-on:keyup.enter="validateInput('meta_description')"></v-text-field>
                      </v-col>
                    </v-row>
                  </v-card>
                  <v-card outlined class="profile__cards mb-5" max-height="140" height="140">
                    <v-row>
                      <v-col cols="10">
                        <span class="title font-weight-light text--secondary">
                          {{ $t("label.slug") }}
                        </span>
                      </v-col>
                      <v-col cols="2">
                        <div class="text-right">
                          
                        </div>
                      </v-col>
                    </v-row>
                    <v-divider></v-divider>
                    <v-row>
                      <v-col @click="editTemplate('slug')" v-if="isEditingSlug == false">
                        <span>{{ templateForm.slug }}</span>
                      </v-col>
                      <v-col v-else >
                        <v-text-field autofocus @blur="cancelInput('slug')" v-model="templateForm.slug" v-on:keyup.esc="cancelInput('slug')" v-on:keyup.enter="validateInput('slug')"></v-text-field>
                      </v-col>
                    </v-row>
                  </v-card>
                  <v-card outlined class="profile__cards mb-5" max-height="140" height="140">
                    <v-row>
                      <v-col cols="10">
                        <span class="title font-weight-light text--secondary">
                          {{ $t("label.alt_tag_image") }}
                        </span>
                      </v-col>
                      <v-col cols="2">
                        <div class="text-right">
                          
                        </div>
                      </v-col>
                    </v-row>
                    <v-divider></v-divider>
                    <v-row>
                      <v-col @click="editTemplate('alt_tag_image')" v-if="isEditingAltTagImage == false">
                        <span>{{ templateForm.alt_tag_image }}</span>
                      </v-col>
                      <v-col v-else>
                        <v-text-field autofocus @blur="cancelInput('alt_tag_image')" v-model="templateForm.alt_tag_image" v-on:keyup.esc="cancelInput('alt_tag_image')" v-on:keyup.enter="validateInput('alt_tag_image')"></v-text-field>
                      </v-col>
                    </v-row>
                  </v-card>
                  <v-card outlined class="profile__cards mb-5" max-height="140" height="140">
                    <v-row>
                      <v-col cols="10">
                        <span class="title font-weight-light text--secondary">
                          {{ $t("label.image_description") }}
                        </span>
                      </v-col>
                      <v-col cols="2">
                        <div class="text-right">
                        </div>
                      </v-col>
                    </v-row>
                    <v-divider></v-divider>
                    <v-row>
                      <v-col @click="editTemplate('img_description')" v-if="isEditingImgDescription == false">
                        <span>{{ templateForm.img_description }}</span>
                      </v-col>
                      <v-col v-else>
                        <v-text-field autofocus @blur="cancelInput('img_description')" v-model="templateForm.img_description" v-on:keyup.esc="cancelInput('img_description')" v-on:keyup.enter="validateInput('img_description')"></v-text-field>
                      </v-col>
                    </v-row>
                  </v-card>
                </v-col>
                <v-col cols="12" xs="12" md="6">
                  <v-card outlined class="profile__cards mb-5" max-height="780" height="780">
                    <v-row>
                      <v-col cols="10">
                        <span class="title font-weight-light text--secondary">
                          {{ $t("label.body") }}
                        </span>
                      </v-col>
                      <v-col cols="2">
                        <div class="text-right">
                        </div>
                      </v-col>
                    </v-row>
                    <v-divider></v-divider>
                    <v-row>
                      <v-col v-html="templateForm.body" @click="editTemplate('body')" v-if="isEditingBody == false">
                      </v-col>
                      <v-col v-else>
                        <el-tiptap :height="600" :extensions="extensions" v-model="templateForm.body" placeholder="Write something ...">
                        </el-tiptap>
                        <div class="text-right mt-2">
                          <v-btn
                            color="error"
                            text
                            tile
                            @click="cancelBody"
                          >
                            {{ $t("cancel") }}
                          </v-btn>
                          <v-btn
                            color="success"
                            tile
                            @click="validateInput('body')"
                          >
                            <div>
                              <div>
                                {{ $t("confirm") }}
                              </div>
                            </div>
                          </v-btn>
                        </div>
                      </v-col>
                    </v-row>
                  </v-card>
                </v-col>
              </v-row>
              <v-row>
                <v-col>
                  <div class="text-right mt-2"> 
                    <v-btn
                      color="success"
                      tile
                      @click="onSubmit"
                    >
                      <div>
                        <div>
                          {{ $t("button.save_change") }}
                        </div>
                      </div>
                    </v-btn>
                  </div>
                </v-col>
              </v-row>
            </div>
          </v-tab-item>
        </v-tabs>
    </v-app>
  
  </div>
  

</template>

<script>
import { mapActions, mapGetters } from "vuex";

import GeolocalizationCitiesSlot from "./GeolocalizationTabs/GeolocalizationCitiesSlot.vue";

import GeolocalizationProvincesSlot from "./GeolocalizationTabs/GeolocalizationProvincesSlot.vue";

import GeolocalizationRegionsSlot from "./GeolocalizationTabs/GeolocalizationRegionsSlot.vue";

import Loading from "vue-loading-overlay";

import Form from "./../../shared/form.js";

import toast from "./../../helpers/toast.js";

import {
  // all extensions
  Doc,
  Text,
  Paragraph,
  Heading,
  Bold,
  Italic,
  Strike,
  Underline,
  Link,
  Image,
  Blockquote,
  ListItem,
  BulletList, // use with ListItem
  OrderedList, // use with ListItem
  TodoItem,
  TodoList, // use with TodoItem
  TextAlign,
  Indent,
  HorizontalRule,
  HardBreak,
  History,
  LineHeight,
  Iframe,
  CodeBlock,
  TrailingNode,
  Table, // use with TableHeader, TableCell, TableRow
  TableHeader,
  TableCell,
  TableRow,
  FormatClear,
  TextColor,
  TextHighlight,
  Preview,
  Print,
  CodeView
  // SelectAll,
} from "element-tiptap";

import codemirror from "codemirror";
import "codemirror/lib/codemirror.css"; // import base style
import "codemirror/mode/xml/xml.js"; // language
import "codemirror/addon/selection/active-line.js"; // require active-line.js
import "codemirror/addon/edit/closetag.js"; // autoCloseTags

export default {

	props: ['article'],

  mixins: [toast],
  
  components: {

    Loading,

    GeolocalizationCitiesSlot,

    GeolocalizationProvincesSlot,

    GeolocalizationRegionsSlot,
  
  },
  
  data: function() {
    
    return {
      
      extensions: [
        new Doc(),
        new Text(),
        new Paragraph(),
        new Heading({ level: 6 }),
        new Bold({  }),
        new Underline({  }),
        new Italic({  }),
        new Strike({  }),
        new Link({  }),
        new Image(),
        new Blockquote(),
        new TextAlign(),
        new ListItem(),
        new BulletList({  }),
        new OrderedList({  }),
        new TodoItem(),
        new TodoList(),
        new Indent(),
        new LineHeight(),
        new HardBreak(),
        new HorizontalRule({  }),
        new History(),
        new CodeView({
          codemirror,
          codemirrorOptions: {
            styleActiveLine: true,
            autoCloseTags: true
          }
        }),
      ],

      viewing_template: false,

      tab: null,

      createModalTab: 0,

      template: [],

      articleItem: [],

      isEditingTitle: false,

      isEditingMetaDescription: false,

      isEditingSlug: false,

      isEditingImgDescription: false,

      isEditingAltTagImage: false,

      isEditingBody: false,

      isCityOpen: true,

      isProvinceOpen: false,

      isRegionOpen: false,

      tab_length: 0,

      templateForm: new Form({

        id: "",
        alt_tag_image: "",
        body: "",
        img_description: "",
        meta_description: "",
        page_title: "",
        slug: "",
        place: "",

      }),

      formData: new FormData(),
    
    };
  
  },

  mounted() {
    this.loadArticle()
  },

  computed: {
    ...mapGetters("geolocalization_template", {
      geolocalizationTemplateResponseStatus: "get_response_status"
    }),
  },
  
  methods: {
    ...mapActions("country", ["get_all_city"]),
    ...mapActions("geolocalization", ["get_geolocalization_article"]),
    ...mapActions("geolocalization_template", ["post_geolocalization_template"]),

    async loadArticle() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
				article_id: this.article.id,
      };
      this.get_geolocalization_article(params).then(data => {
        this.articleItem = data
      });
    },

    editTemplate(name) {
      switch (name) {
        case 'title':
          this.isEditingTitle = true;
          this.templateForm.page_title = this.template.page_title
          break;
        case 'meta_description':
          this.isEditingMetaDescription = true;
          this.templateForm.meta_description = this.template.meta_description 
          break;
        case 'slug':
          this.isEditingSlug = true;
          this.templateForm.slug = this.template.slug
          break;
        case 'alt_tag_image':
          this.isEditingAltTagImage = true;
          this.templateForm.alt_tag_image = this.template.alt_tag_image
          break;
        case 'img_description':
          this.isEditingImgDescription = true;
          this.templateForm.img_description = this.template.img_description
          break;
        case 'body':
          this.isEditingBody = true;
          this.templateForm.body = this.template.content
          break;
      }
    },

    validateInput(inputName) {
      switch (inputName) {
        case 'title':
          this.isEditingTitle = false;
          this.template.page_title = this.templateForm.page_title
          this.templateForm.page_title = this.template.page_title.replace('[place]', this.templateForm.place);
          break;
        case 'meta_description':
          this.isEditingMetaDescription = false;
          this.template.meta_description = this.templateForm.meta_description
          this.templateForm.meta_description = this.template.meta_description.replace('[place]', this.templateForm.place);
          break;
        case 'slug':
          let place = this.templateForm.place.replace(/,|\s/gi, "-")
          place = place.replace(/-{2,}/g, '-');
          this.isEditingSlug = false;
          this.template.slug = this.templateForm.slug
          this.templateForm.slug = this.template.slug.replace('[place]', place);
          break;
        case 'alt_tag_image':
          this.isEditingAltTagImage = false;
          this.template.alt_tag_image = this.templateForm.alt_tag_image
          this.templateForm.alt_tag_image = this.template.alt_tag_image.replace('[place]', this.templateForm.place);
          break;
        case 'img_description':
          this.isEditingImgDescription = false;
          this.template.img_description = this.templateForm.img_description
          this.templateForm.img_description = this.template.img_description.replace('[place]', this.templateForm.place);
          break;
        case 'body':
          this.isEditingBody = false;
          this.template.content = this.templateForm.body
          this.templateForm.body = this.template.content.replaceAll('[place]', this.templateForm.place);
          break;
      }
    },

    cancelInput(inputName) {
      switch (inputName) {
        case 'title':
          this.isEditingTitle = false;
          this.templateForm.page_title = this.template.page_title
          this.templateForm.page_title = this.templateForm.page_title.replace('[place]', this.templateForm.place);
          break;
        case 'meta_description':
          this.isEditingMetaDescription = false;
          this.templateForm.meta_description = this.template.meta_description
          this.templateForm.meta_description = this.templateForm.meta_description.replace('[place]', this.templateForm.place);
          break;
        case 'slug':
          let place = this.templateForm.place.replace(/,|\s/gi, "-")
          place = place.replace(/-{2,}/g, '-');
          this.isEditingSlug = false;
          this.templateForm.slug = this.template.slug
          this.templateForm.slug = this.templateForm.slug.replace('[place]', place);
          break;
        case 'alt_tag_image':
          this.isEditingAltTagImage = false;
          this.templateForm.alt_tag_image = this.template.alt_tag_image
          this.templateForm.alt_tag_image = this.templateForm.alt_tag_image.replace('[place]', this.templateForm.place);
          break;
        case 'img_description':
          this.isEditingImgDescription = false;
          this.templateForm.img_description = this.template.img_description
          this.templateForm.img_description = this.templateForm.img_description.replace('[place]', this.templateForm.place);
          break;
      }
    },

    cancelBody() {
      this.templateForm.body = this.template.content
      this.templateForm.body = this.templateForm.body.replaceAll('[place]', this.templateForm.place);
      this.isEditingBody = false;
    },

    openTab(name) {
      switch (name) {
        case 'city':
          this.isCityOpen = true;
          this.isProvinceOpen = false;
          this.isRegionOpen = false;
          this.createModalTab = 0;
          break;
        case 'province':
          this.isCityOpen = false;
          this.isProvinceOpen = true;
          this.isRegionOpen = false;
          this.createModalTab = 1;
          break;
        case 'region':
          this.isCityOpen = false;
          this.isProvinceOpen = false;
          this.isRegionOpen = true;
          this.createModalTab = 2;
          break;
      }
    },

    onSubmit() {
      let params = {
        api_token: this.$user.api_token,
        id: this.templateForm.id,
        article_id: this.article.id,
        title: this.template.page_title,
        body: this.template.content,
        meta_description: this.template.meta_description,
        slug: this.template.slug,
        alt_tag_image: this.template.alt_tag_image,
        image_description: this.template.img_description,
        lang: this.$i18n.locale,
			};
      this.post_geolocalization_template(params)
        .then((resp) => {
          console.log(this.geolocalizationTemplateResponseStatus);
          if (this.geolocalizationTemplateResponseStatus) {

            let message = {
              update: this.$t( 'updated_message1' ) + this.$t( 'geolocalization_template' ) + ` ID: ${this.templateForm.id} ` + this.$t( 'updated_message2' ),
              title: {
                update: this.$t( 'record_updated' )
              }
            };

            this.makeToast(
              "success",
              message.title.update,
              message.update
            );

          }
        })
        .catch((error) => {
          console.log(error);
          let errors = error.response.data.errors;
          
          this.toastError(errors);
        });
    },

  },

};

</script>

<style>

.ck-editor__editable_inline {
    min-height: 590px;
}
</style>