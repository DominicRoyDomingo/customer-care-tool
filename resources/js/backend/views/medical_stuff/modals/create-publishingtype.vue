<template>
  <div class="create">
  <b-modal
    id="publishing-type-modal"
    :visible.sync="modal.isVisible"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >
  <v-app id="create__container">
    <v-card class="border-0">
      <v-card-title class="title blue-grey lighten-4 text-capitalize">
        <div>
          <span v-if="parent.form.action == 'Add'">
            {{ $t('publishing_type_add_new_button')}}
          </span>

          <span
            class="d-inline-block text-truncate"
            style="max-width: 700px; letter-spacing: normal"
            v-else
          >
            {{ $t("publishing_edit_label") }} {{ parent.itemSelected.base_name }}
          </span>
        </div>

        <v-spacer></v-spacer>
        <v-btn icon color="error lighten-2" @click="onClose">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>
      <form class="form" @submit.prevent="onSave">
      <v-container>
        <v-row class="p-1">
          <v-col sm="12" md="12" cols="12">
           
 <!--              <div
                class="form-group mb-4"
                v-show="parent.typeForm.action === 'Update'"
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
              </div> -->
             
                <div class="form-group">
                   <div class='d-flex justify-content-between align-items-end'>
                   <div>
                    </div>
                    <div v-if="parent.form.action == 'Update'">
                    <select class='form-control' v-model='langsel'>
                      <option value='en'>ENGLISH</option>
                      <option value='it'>ITALIAN</option>
                      <option value='de'>GERMAN</option>
                      <option value='ph-fil'>FILIPINO</option>
                      <option value='ph-bis'>VISAYAN</option>                      
                    </select>
                  </div>
                </div>
                   <v-text-field
                      :label="$t('publishing_type_name').toUpperCase()"
                      id="type_name"
                      type="type_name"
                      name="Name"
                      v-model="form.type_name"
                      hide-details="auto"
                      clearable
                    />
                    <div class='text-danger py-2' v-if="is_required">It is required to put an entry for item type data before saving.</div>
                </div>
          </v-col>
        </v-row>
      </v-container>
      <v-card-actions class="modal__footer blue-grey lighten-5 justify-content-end px-5">
                <v-btn color="error" text tile @click="onClose">
                  {{ $t("cancel") }}
                </v-btn>
                <v-btn
                  color="success"
                  tile
                  type="submit"
                  class="bg-success text-white"
                >
                  <div v-if="btnloading" class="text-center">
                    <v-progress-circular
                      size="20"
                      width="1"
                      color="white"
                      indeterminate
                    >
                    </v-progress-circular>
                  </div>
                  <div v-else>
                    <span v-if="parent.form.action == 'Add'">
                      {{ $t("button.save") }}
                    </span>
                    <span v-else>{{ $t("button.save_change") }}</span>
                  </div>
                </v-btn>
              </v-card-actions>
        </form>
    </v-card>
  </v-app>
  </b-modal>
</div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import { createItemType, updateItemType, getItemTypeName } from "./../../../api/item_type.js";

export default {
  props: ["parent"],

  // computed: {
  //   ...mapGetters("categ_terms", {
  //     item: "get_response_item",
  //   }),
  // },
  data(){
    return {
      form : this.parent.form, 
      formData: new FormData(),
      btnloading : false,
      is_required : false,
      langsel : 'en',
      modal : {
        isVisible : false
      }
    }
  },
  mounted: function () {
    this.$nextTick(function () {
        this.langsel = this.form.language
    })
  },
  watch : {
    langsel(val, old){
        this.onUpdateLang()
    },
    form : {
      deep: true,
      handler: function (val, oldVal) { 
          this.is_required = false
      }      
    }
  },
  methods: {
    ...mapActions("categ_terms", ["post_term_type"]),

    onClose() {
      this.$emit("on-close");
      this.formReset();
      this.$bvModal.hide("publishing-type-modal");
    },

    onSave() {
        this.is_required = false
        if(this.form.type_name == "") {
          this.is_required = true
          console.log("asdasd")
          return false;
      }

      if(this.form.action == 'Add'){
          this.onAddNew()
      }
      else if(this.form.action == 'Update'){
         this.onUpdate() 
      }
    },

    onAddNew : function(){
        const data = {
          id: this.form.id,
          type_name: this.form.type_name,
          language: this.form.language,
        };

        let formData = new FormData();

        formData.append("data", JSON.stringify(data));
        this.btnloading = true
        createItemType(formData).then((resp) => {
            var data = resp.data
            if (data.action == "duplicate") {
                this.parent.errorToast(
                  data.title,
                  data.data.type_name +
                    " " +
                    this.$t("is_existing_text") + " " + this.$t("publising_item_title") + " " + 
                    this.$t("record_text")
                );

                this.btnloading = false                
              }else{

                  this.parent.makeToast(
                      data.type,

                      this.$t('new_record_created'),

                      data.message +
                        " " +
                        this.$t('created_message') + this.$t("publising_item_title")
                    );

                  this.form.language = this.$i18n.locale;
                  this.btnloading = false
                  this.onClose()
              }

              this.parent.loadItemType()
        })
    },
    onUpdate : function(){
        const data = {
          id: this.form.id,
          type_name: this.form.type_name,
          language: this.langsel,
        };

        let formData = new FormData();

        formData.append("data", JSON.stringify(data));
        updateItemType(formData).then((res) => {
            if(res.status == 200){
                var data = res.data
                this.parent.updateToast(
                  data.data.id,
                  data.message
                );

              this.form.language = this.$i18n.locale;
              this.btnloading = false
              this.onClose()
            }

            this.parent.loadItemType()
        })
    },
    formReset() {
      this.parent.typeForm.term_type = "";
    },
    onUpdateLang : function(){
      var id = this.form.id;
      var lang = this.langsel
      getItemTypeName(id, lang).then((res) => {
          this.form.type_name = res.type_name
      })
    }
  },
};
</script>
