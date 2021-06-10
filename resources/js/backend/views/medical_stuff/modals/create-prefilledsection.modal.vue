<template>
  <div class="create">
    <b-modal
      id="create-prefilledsection"
      width="40%"
      hide-footer
      hide-header
      no-close-on-backdrop
    >
    <v-app id="create__container">
      <form
      @submit.prevent="onSubmit"
      method="post"
      enctype="multipart/form-data"
      @keydown="form.errors.clear($event.target.name)"
    >
    <v-card class="border-0">
      <v-card-title class="title blue-grey lighten-4 text-capitalize">
        <span v-if="this.prefilledsection_modal.type === 'edit'">{{this.$t("button.edit")+" "+$t('label.prefilled_section')}}</span>
        <span v-else>{{this.$t("button.new")+" "+$t('label.prefilled_section')}}</span>
        <v-spacer></v-spacer>
        <v-btn icon color="error lighten-2" @click="modalClose">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-card-text class="modal__content" style="">
        <v-container>
          <v-container v-show="this.prefilledsection_modal.type === 'edit'">
            <v-row>
              <v-col class="modal__input-container">
                <div class="form-group">
                  <div class="form-inline justify-end">
                    <b-form-select
                      id="inline-form-custom-select-pref"
                      class="mr-sm-2 mb-sm-0"
                      v-model="language"
                      :options="this.languageOptions"
                    />
                  </div>
                </div>
              </v-col>
            </v-row>
          </v-container>

          <v-container>
            <div class="p-2 margin-top"> 
                <b-form-group>
                  <v-text-field
                    :label="$t('label.prefilled_section')"
                    id="type_name"
                    type="type_name"
                    name="title"
                    v-model="prefilledsection_form.section_name"
                    hide-details="auto"
                    class="mb-2"
                  />
                   <span  v-show="errors[`form.section_name`]" class="red--text mb-2">This field is required.</span><br/>

                  <span>{{$t('html_tags_priority')}}</span>
                  <v-select
                    v-model="prefilledsection_form.section_priority"
                    :options="this.html_tags"
                    :reduce="options => options.id"
                    label="description"
                    class="mb-2"
                    :placeholder="`${$t('select_placeholder')} HTML TAG`"   
                  >
                    <template #list-header>
                      <li class="mb-2 mt-1 ml-0">
                        <b-link
                          href="javascript:;"
                          @click="showChildModal('html-tag-modal')"
                        >
                          <i class="fa fa-plus" aria-hidden="true"></i>
                          {{$t('html_tag_new')}}
                        </b-link>
                      </li>
                    </template>
                    <!-- <template v-slot:option="option">
                    <span v-html="option.type_name"/>
                  </template> -->
                  </v-select>
                  <span v-show="errors[`form.section_priority`]" class="red--text mb-2">This field is required.</span><br/>

                  <span>{{$t('type_of_publising_item')}}</span>
                  <v-select
                    v-model="prefilledsection_form.section_type"
                    :options="this.publishing_types"
                    :reduce="options => options.id"
                    label="base_name"
                    required
                    :placeholder="`${$t('select_placeholder')} Type`"   
                  >
                    <template v-slot:option="option">
                      <span v-html="option.type_name"/>
                    </template>

                    <template #list-header>
                      <li class="mb-2 mt-1 ml-0">
                        <b-link
                          href="javascript:;"
                          @click="onAddItemType"
                        >
                          <i class="fa fa-plus" aria-hidden="true"></i>
                          {{$t('new_publishing_type')}}
                        </b-link>
                      </li>
                    </template>

                  </v-select>
                  <span v-show="errors[`form.section_type`]" class="red--text">This field is required.</span>
                </b-form-group>
            </div>
          </v-container>
        </v-container>
      </v-card-text>
       <v-divider style="margin-bottom: 0"></v-divider>
       <v-card-actions class="modal__footer blue-grey lighten-5 justify-content-between px-5">

            <div v-if="prefilledsection_modal.publishing">
              <a href="/admin/software-publishing/pre-filled-sections/manage">MANAGE PRE-FILLED SECTION</a>
            </div>
            <div v-else></div>
            
            <div class='d-flex'>
              <v-btn color="error" text tile @click="modalClose">{{
                $t(prefilledsection_modal.button.cancel)
              }}</v-btn>

              <v-btn
                 color="success" 
                 tile 
                 type='submit'
              >
                <div v-if="prefilledsection_modal.button.loading" class="text-center">
                  <v-progress-circular
                    size="20"
                    width="1"
                    color="white"
                    indeterminate
                  >
                  </v-progress-circular>
                </div>

                <div  v-if="this.prefilledsection_modal.type === 'edit'">
                  {{$t("button.save_change")}}
                </div>

                <div v-else>
                  {{$t("button.save")}}
                </div>
                
              </v-btn>
            </div>

      </v-card-actions>
      </v-card>
    </form>
    </v-app>
    </b-modal>

    <ArticleCreateAddHtmlTag
        :parent="this"
        @done-success="loadData"
        @on-close="closeChildModal"
    />
    <CreateItemType 
      :$this="this" 
      @show-modal="on_show_modal" />
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import MedicalMixin from "./../mixins/medicalMixin";
import PrefilledSectionMixin from "./../mixins/prefilledSectionMixin";
import ArticleCreateAddHtmlTag from "./create-html-tag.modal";
import CreateItemType from "./../../includes/item-type/CreateComponent";

export default {
  props: ["parent", 'itemTypes'],
  mixins: [MedicalMixin, PrefilledSectionMixin],

  components:{
    ArticleCreateAddHtmlTag,
    CreateItemType
  },

  data: function () {
    return {
      prefilledsection_modal: this.parent.modal_create,
      htmlTagPriorityName: "",
      isShowHtmlTag:true,
      isActionShow: true,
      submitted: false,
      keep_open: false,
      language:'',
      errors:[],

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

      prefilledsection_form: {
        id: "",
        section_name: "",
        section_priority: "",
        section_type:this.parent.form.articleTypeID,
        language: this.$i18n.locale
      },

      formGroups: [],
      formData: new FormData()
    };
  },
  
  mounted(){
    this.loadData().catch((errors) => {});
    this.loadFormValues();
    
  },  

  computed:{
    ...mapGetters({
      html_tags: "html_tags/get_html_tags_items",
      publishing_types: "html_tags/get_publishing_type_items",
    }),
  },  

  methods: {
    ...mapActions("html_tags", [
      "get_html_tags", 
      "get_publishing_type_items"
    ]),

    ...mapActions("prefilledsection", [
      "post_create_prefilledsection", 
      "post_update_prefilledsection", 
    ]),

    async loadData() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.$i18n.locale,
      };

      this.get_html_tags(params).then((_) => {
        this.isActionShow = true;
      });

      this.get_publishing_type_items(params).then((_) => {});
    },

    loadFormValues(){
      if(this.prefilledsection_modal.type === 'edit'){
        this.prefilledsection_form = {
          id: this.prefilledsection_modal.form.id,
          section_priority: this.prefilledsection_modal.form.tag_used_id,
          section_type: this.prefilledsection_modal.form.type_of_publishing_item_id
        }
        this.language = this.$i18n.locale
      }
    },

    loadItemType() {
      this.loadData()
    },
    
    modalClose(done) {
      this.prefilledsection_form = {},
      this.file = "";
      this.parent.isCreateEnabled = false,
      this.prefilledsection_modal.isVisible = false;
      this.keep_open = false;
      this.$bvModal.hide('create-prefilledsection')
    },

    onSubmit() {
      // if (this.form.section_name == "") {
      //   this.$alert(this.$t("fill_up_publishing_type_name"), {
      //     confirmButtonText: "OK",
      //     type: "error",
      //   });

      //   return false;
      // }

      this.prefilledsection_modal.button.loading = true;

      let params = {
        api_token: this.$user.api_token,
        form: this.prefilledsection_form,
        language: (this.language) ? this.language : this.$i18n.locale 
      };

      switch(this.prefilledsection_modal.type){
        case "create":
          this.onAdd(params)
          break
        case "edit":
          this.onEdit(params)
          break;
      }
    },

    onAdd(params){
      this.post_create_prefilledsection(params)
        .then((resp) => {
          this.prefilledsection_modal.button.loading = false;
          resp = resp.data;     
          this.$emit('loadData')
          this.$emit('createdData', params.form.section_name)
          this.modalClose()
          // if (resp.responseStatus) {
          //   //Add callback function here if necessary

          //   let notification_message = this.$t("toasts.added_note");
          //   notification_message = notification_message.replace(
          //     /%variable%/g,
          //     this.parent.itemSelected.profile_name
          //   );

          //   let message = {
          //     create: notification_message,
          //     update:
          //       this.$t("updated_message1") +
          //       this.$t("label.client_profile") +
          //       ` ID: ${this.parent.form.profile_id}` +
          //       this.$t("updated_message2"),
          //     title: {
          //       create: this.$t("new_record_created"),
          //       update: this.$t("record_updated"),
          //     },
          //   };
          //   this.parent.makeToast(
          //     "success",
          //     message.title.create,
          //     message.create
          //   );

          //   this.parent.addedNotesSuccessfully(resp.notes);
          // }
        })
        .catch((error) => {
          this.errors = error.response.data.errors;

          for (var key in this.errors) {
            if(key == "unique"){
               this.errorToast("Duplicate", this.errors[key]);
            }
          }
    
          this.prefilledsection_modal.button.loading = false;
        });
    },

    onEdit(params){
      this.post_update_prefilledsection(params)
        .then((resp) => {
          this.prefilledsection_modal.button.loading = false;
          resp = resp.data;     
          this.$emit('loadData')
          this.$emit('updatePrefilledSectionList', params.form.id)
          this.modalClose()
          
          // if (resp.responseStatus) {
          //   //Add callback function here if necessary

          //   let notification_message = this.$t("toasts.added_note");
          //   notification_message = notification_message.replace(
          //     /%variable%/g,
          //     this.parent.itemSelected.profile_name
          //   );

          //   let message = {
          //     create: notification_message,
          //     update:
          //       this.$t("updated_message1") +
          //       this.$t("label.client_profile") +
          //       ` ID: ${this.parent.form.profile_id}` +
          //       this.$t("updated_message2"),
          //     title: {
          //       create: this.$t("new_record_created"),
          //       update: this.$t("record_updated"),
          //     },
          //   };
          //   this.parent.makeToast(
          //     "success",
          //     message.title.create,
          //     message.create
          //   );

          //   this.parent.addedNotesSuccessfully(resp.notes);
          // }
        })
        .catch((error) => {
          this.errors = error.response.data.errors;
          for (var key in this.errors) {
            if(key == "unique"){
               this.errorToast("Duplicate", this.errors[key]);
            }
          }
          this.prefilledsection_modal.button.loading = false;
        });
    },

     onAddItemType() {
      this.modal.add.isVisible = true;
      this.$bvModal.hide("create-prefilledsection");
    },

    on_show_modal() {
      this.$bvModal.show("create-prefilledsection");
    },

    showChildModal(modalName) {
      this.isActionShow = false;
      this.typeDateForm.action = "Add";
      this.htmlTagForm.action = "Add";

      this.$bvModal.show(modalName);
    },

    closeChildModal() {
      this.isActionShow = true;
      this.typeDateForm.reset();
      this.htmlTagForm.reset();
    },
  },
};

</script>

<style>
.margin-top {
  margin-top: -20px !important;
}

.dialog-footer {
  margin-top: -20px !important;
}

.avatar-uploader .el-upload {
  border: 1px dashed #d9d9d9;

  border-radius: 6px;

  cursor: pointer;

  position: relative;

  overflow: hidden;

  width: 150px;
}

.avatar-uploader .el-upload:hover {
  border-color: #409eff;
}

.avatar-uploader-icon {
  font-size: 28px;

  color: #8c939d;

  width: auto;

  /*width: 178px;*/

  /*height: 150px;*/

  line-height: 150px;

  text-align: center;
}

.avatar {
  width: 178px;

  height: 178px;

  display: block;
}
</style>

