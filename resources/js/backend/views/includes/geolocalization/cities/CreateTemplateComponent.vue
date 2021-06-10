<template>
  <div class="create">
    <b-modal
      id="geolocalization-template-add-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
      :title="$t(modal.name)"
    >
      <v-app>
        <v-form ref="form" lazy-validation>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
						<div style="line-height: 15px;">
							<span class="text-caption" v-if="form.alt_tag_image != ''"> 
								{{ $t("geolocalization_edit_template_for") }}
							</span>
              <span class="text-caption" v-else> 
								{{ $t("geolocalization_new_template_for") }}
							</span><br>
							<span v-if="parent.article != ''" v-html="parent.article.article_title">
							</span>
						</div>
            
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="modalClose"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text class="modal__content">
            <v-tabs
              v-model="tabIndex"
              show-arrows
              center-active
              grow
              background-color="blue-grey lighten-5"
              slider-color="blue-grey darken-2"
              color="blue-grey darken-2"
            >
              <v-tab class="caption font-weight-bold">
                {{ $t("geolocalization_header_and_other") }}
              </v-tab>
              <v-tab class="caption font-weight-bold">
                {{ $t("geolocalization_body") }}
              </v-tab>
              <v-tab-item eager>
                <v-container>
                  <v-row>
                    <v-col cols="12" md="12" class="modal__input-container">
                      <label for="province_name">
                        <strong class="text-danger">*</strong>
                        Type <span class="red--text">[place]</span> to insert a placeholder of location or place
                      </label>
                      <v-text-field
                        id="title"
                        name="title"
                        v-model="form.title"
                        :label="$t('label.title')"
                        placeholder="eg. Glocare a calcetto in [place]"
                      />
                      <v-text-field
                        id="meta_description"
                        name="meta_description"
                        v-model="form.meta_description"
                        :label="$t('label.meta_description')"
                        placeholder="eg. glocare a calcetto in [place]"
                      />

                      <v-text-field
                        id="slug"
                        name="slug"
                        v-model="form.slug"
                        :label="$t('label.slug')"
                        placeholder="eg. glocare-a-calcetto-in-[place]"
                      />

                      <v-text-field
                        id="alt_tag_image"
                        name="alt_tag_image"
                        v-model="form.alt_tag_image"
                        :label="$t('label.alt_tag_image')"
                        placeholder="eg. Galceto in [place]"
                      />

                      <v-text-field
                        id="image_description"
                        name="image_description"
                        v-model="form.image_description"
                        :label="$t('label.image_description')"
                        :placeholder="$t('label.image_description')"
                      />
                    </v-col>
                  </v-row>
                </v-container>
              </v-tab-item>
              <v-tab-item eager>
                <v-container>
                  <v-row>
                    <v-col cols="12" md="12" class="modal__input-container">
                      <el-tiptap :height="600" :extensions="extensions" v-model="form.body" placeholder="Write something ..."/>
                    </v-col>
                  </v-row>
                </v-container>
              </v-tab-item>
            </v-tabs>
          </v-card-text>
          <v-divider style="margin-bottom: 0"></v-divider>
            <v-card-actions class="modal__footer blue-grey lighten-5">
              <div v-if="this.modal.id != undefined">
                <v-btn
                  color="error"
                  text
                  tile
                  link
                  @click.stop="parent.informationTypePage"
                >
                  <v-icon>
                    mdi-open-in-new
                  </v-icon>
                  {{ $t("label.go_to_information_type_page") }}
                </v-btn>
              </div>
              <v-spacer></v-spacer>
              <v-btn
                color="error"
                text
                tile
                @click="modalClose"
              >
                {{ $t(modal.button.cancel) }}
              </v-btn>
              <v-btn
                color="success"
                tile
                @click="onSubmit"
              >
                <div v-if="this.modal.button.loading" class="text-center">
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
                    {{ $t(modal.button.save) }}
                  </div>
                </div>
              </v-btn>
            </v-card-actions>
        </v-form>
      </v-app>
    </b-modal>
  </div>
</template>

<script>

import { mapActions, mapGetters } from "vuex";

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
  Fullscreen,
  CodeView
  // SelectAll,
} from "element-tiptap";

import codemirror from "codemirror";
import "codemirror/lib/codemirror.css"; // import base style
import "codemirror/mode/xml/xml.js"; // language
import "codemirror/addon/selection/active-line.js"; // require active-line.js
import "codemirror/addon/edit/closetag.js"; // autoCloseTags

export default {
  props: ["parent"],

  name: "InfiniteScroll",

  data: function() {
    return {

      extensions: [
        new Doc(),
        new Text(),
        new Paragraph(),
        new Heading({ level: 5 }),
        new Bold({ bubble: true }),
        new Underline({ bubble: true }),
        new Italic({ bubble: true }),
        new Strike({ bubble: true }),
        new Link({ bubble: true }),
        new Image(),
        new Blockquote(),
        new TextAlign(),
        new ListItem(),
        new BulletList({ bubble: true }),
        new OrderedList({ bubble: true }),
        new TodoItem(),
        new TodoList(),
        new Indent(),
        new LineHeight(),
        new HardBreak(),
        new HorizontalRule({ bubble: true }),
        new History(),
        new CodeView({
          codemirror,
          codemirrorOptions: {
            styleActiveLine: true,
            autoCloseTags: true
          }
        }),
      ],

      modal: this.parent.modalGeolocalizationTemplate.createGeolocalizationTemplate,

      form: this.parent.geolocalizationTemplateForm,

      formData: this.parent.formData,

      formGroups: [],

      observer: null,
      tabIndex: 0,
			limit: 10,
			search: ''
    };
	},

	
  methods: {
    ...mapActions("geolocalization_template", [
      "post_geolocalization_template",
      "add_geolocalization_template"
    ]),

    modalClose(done) {

      this.parent.$bvModal.hide("geolocalization-template-add-modal");

      this.form.reset();

      this.keep_open = false;
		},

    onSubmit() {
			this.modal.button.loading = true;
			
			let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        article_id: this.parent.article.id,
        title: this.form.title,
        body: this.form.body,
        meta_description: this.form.meta_description,
        slug: this.form.slug,
        alt_tag_image: this.form.alt_tag_image,
        image_description: this.form.image_description,
      };
      
      if(this.form.id != '') {
        params.id = this.form.id
      }

      this.post_geolocalization_template(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("geolocalization-template-add-modal");

          if (this.parent.geolocalizationTemplateResponseStatus) {

						let message = {
              create:
                `${this.form.title}` +
                this.$t("created_message") +
                this.$t("geolocalization_template"),
              update: this.$t( 'updated_message1' ) + this.$t( 'geolocalization_template' ) + ` ID: ${this.form.id} ` + this.$t( 'updated_message2' ),
              title: {
                create: this.$t("new_record_created"),
                update: this.$t( 'record_updated' ),
              },
            };


            this.parent.makeToast(
              "success",
              this.form.id == '' ? message.title.create : message.title.update,
              this.form.id == '' ? message.create : message.update,
            );

            this.form.reset();
            this.modal.button.loading = false;
            this.parent.loadItems();
          }
        })
        .catch((error) => {
          this.modal.button.loading = false;
          let errors = error.response.data.errors;
          this.toastError(errors);
        });
    },

    toastError(error){
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        this.parent.makeToast("danger", vm.$t('errors.error'), value);
      }
    },
  },
};
</script>

<style>

.ck-editor__editable_inline {
    min-height: 700px;
}
</style>
