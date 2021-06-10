<template>
  <div class="create">
    <b-modal
      id="add-questionchoices"
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
    >
    <v-card class="border-0">
      <v-card-title class="title blue-grey lighten-4 text-capitalize">
        <span v-if="this.choice.type === 'edit'">{{this.$t("button.edit")+" "+this.choice.data.base_name}}</span>
        <span v-else>{{this.$t("button.new")+" "+this.$t('label.choice')}}</span>
        <v-spacer></v-spacer>
        <v-btn icon color="error lighten-2" @click="modalClose">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-card-text class="modal__content" style="">
        <v-container>
          <v-container v-show="this.choice.type === 'edit'">
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
                    :label="$t('label.choice')"
                    name="value"
                    v-model="form.name"
                    hide-details="auto"
                    class="mb-5"
                  />
                  <span v-if="errors[`form.name`]" class="red--text">The text entry is required.</span>
                </b-form-group>
            </div>
          </v-container>
        </v-container>
      </v-card-text>
       <v-divider style="margin-bottom: 0"></v-divider>
       <v-card-actions class="modal__footer blue-grey lighten-5 justify-content-between px-5">
        <v-spacer></v-spacer>
        <div class='d-flex'>
              <v-btn color="error" text tile @click="modalClose">
                {{$t('cancel')}}
              </v-btn>

              <v-btn
                 color="success" 
                 tile 
                 type='submit'
              >

              <div v-if="this.choice.loading" class="text-center">
                  <v-progress-circular
                    size="20"
                    width="1"
                    color="white"
                    indeterminate
                  >
                  </v-progress-circular>
                </div>

                {{(this.choice.type === 'edit')? $t('button.save_change') : $t('button.save')}}           
              </v-btn>

            </div>
      </v-card-actions>
      </v-card>
    </form>
    </v-app>
    </b-modal>

  </div>
</template>

<script>
import {mapActions} from "vuex";
import choiceMixin from "./../mixins/choiceMixin";

export default {
  props: ["parent"],
  mixins: [choiceMixin],

  data: function () {
    return {
      choice: this.parent.choice_modal,
      form:{
        name:""
      },
      errors:[],
    };
  },
  
  mounted(){
    // this.loadData().catch((errors) => {});
    this.loadFormValues();
    
  },  
  methods: {
    ...mapActions("question_choice", [
      "post_choice", 
    ]),

    loadFormValues(){
      if(this.choice.type === 'edit'){
        this.form = {
          id: this.choice.data.id,
        }
        this.language = this.$i18n.locale
      }
    },

    // loadItemType() {
    //   this.loadData()
    // },
    
    modalClose(done) {
      this.form = {}
      this.choice.type="create";  
      this.parent.isChoiceModalVisible = false,
      this.$bvModal.hide('add-questionchoices')
    },

    onSubmit() {
      // if (this.form.section_name == "") {
      //   this.$alert(this.$t("fill_up_publishing_type_name"), {
      //     confirmButtonText: "OK",
      //     type: "error",
      //   });

      //   return false;
      // }

      this.choice.button.loading = true;

      let params = {
         ...this.defaultParams(),
        form: this.form,
        type:this.choice.type,
        language: (this.language) ? this.language : this.$i18n.locale 
      };

      this.post_choice(params)
        .then((resp) => {
          this.choice.button.loading = false;
          resp = resp.data;
          (this.choice.type === 'edit') ? this.$emit('done-update', params.form.id) : this.$emit('done-create', params.form.name)
          this.form = {}
          this.choice.type="create";  
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
    
          this.choice.button.loading = false;
        });
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
