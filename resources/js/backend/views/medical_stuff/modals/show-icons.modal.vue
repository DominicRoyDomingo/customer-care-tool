<template>
  <b-modal
    id="icon-show-modal"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >
    <v-card class="border-0">
      <v-card-title class="bg-light text-uppercase">
        <span>
          {{ `${$t('icons_for')} ${parent.termName}` }}
        </span>
        <v-spacer></v-spacer>
        <v-btn icon color="error lighten-2" @click="onCancel">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-container>
        <div class="pa-5 mx-5">
          <v-row>
            
              <v-col
              v-for="(icon,index) in parent.termIcons"
              :key="index"
              class="d-flex child-flex"
              cols="3"
              >
                <div class="text-center">
                  <v-hover v-slot="{ hover }">
                    <v-img
                        :src="icon.icon"
                        :lazy-src="icon.icon"
                        aspect-ratio="1"
                        class="grey lighten-2"
                    >
                      <v-fade-transition>
                        <v-overlay
                          v-if="hover"
                          absolute
                          color="#282828"
                          opacity="0.9"
                        >
                          <div style="position: absolute;top: -76px;left: 40px;">
                            <v-tooltip top>
                              <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                  icon
                                  v-bind="attrs"
                                  v-on="on"
                                  @click="removeIcon(icon.id, index)"
                                >
                                  <v-icon>
                                    mdi-close
                                  </v-icon>
                                </v-btn>
                              </template>
                              <span>Close</span>
                            </v-tooltip>
                          </div>
                        </v-overlay>
                      </v-fade-transition>
                      <template v-slot:placeholder>
                        <div class="fill-height" style="display: flex; align-items: center; justify-content: center;">
                          <v-progress-circular
                          indeterminate
                          color="grey lighten-5"
                          ></v-progress-circular>
                        </div>
                      </template>
                      
                    </v-img>
                  </v-hover>
                  <span class="font-weight-bold" v-html="icon.medical_term_provider_type.term_name" />
                </div>
              </v-col>
          </v-row>
        </div>
      </v-container>
    </v-card>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
  props: ["parent"],

  data() {
    return {
      file: null,
      isActionShow: true,
      filter: "health",
      specializations: [],
      formData: new FormData(),
    };
  },

  computed: {
    ...mapGetters("categ_terms", {
      linkedProviderTerms: "get_linked_provider_terms_items",
    }),
  },

  methods: {
    ...mapActions("categ_terms", ["delete_term_icon"]),

    showChildModal(modalId) {
      this.isActionShow = false;

      if (modalId === "term-type-modal") {
        this.parent.typeForm.action = "Add";
        this.parent.typeForm.term_type = "";
      } else {
        this.parent.form.action = "Add";
      }

      this.$bvModal.show(modalId);
    },

    closeChildModal() {
      this.isActionShow = true;
      this.parent.form.reset();
    },

    queryItems(api, params) {
      return axios.get(api, { params });
    },

    onselectimage() {
      if (this.file) {
        this.parent.iconForm.icon = URL.createObjectURL(this.file);
      } else {
        this.parent.iconForm.icon = "";
      }
    },

    onCancel() {
      this.file = null;
      this.isActionShow = true;
      this.onReset();
      this.$bvModal.hide("icon-show-modal");
    },

    onSubmit() {
      this.parent.btnloading = true;

      this.setFormData();

      this.post_provider_term(this.formData)
        .then((resp) => {
          this.file = null;
          this.parent.btnloading = false;
          this.$emit("done-success", resp.data);
          this.$bvModal.hide("icon-show-modal");
        })
        .catch((error) => {
          if (error.response) {
            this.parent.iconForm.errors.record(error.response.data.errors);
            let errors = error.response.data.errors;
            this.toastError(errors)
            if (this.parent.iconForm.name) {
              this.parent.errorToast(
                `${this.$t("errors.duplicate")}!`,
                this.parent.iconForm.errors.get("name")
              );
            }
          }
          this.parent.btnloading = false;
        });
    },

    toastError(error){
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        this.parent.makeToast("danger", vm.$t('errors.error'), value);
      }
    },

    removeIcon(id, index){
      let vm = this;
      $.confirm({
        title:
          vm.$t("confirmation_record_delete").charAt(0).toUpperCase() +
          vm
            .$t("confirmation_record_delete")
            .slice(1)
            .toUpperCase(),
        content:
          vm.$t("question_record_delete") +
          vm.$t("delete_icon") +"?",
        type: "red",
        typeAnimated: true,
        columnClass: "medium",
        buttons: {
          yes: {
            text: vm.$t("yes"),
            btnClass:
              "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
            action: function(value) {
              if (value) {
                let params = {
                  api_token: vm.$user.api_token,
                  id: id,
                };
                vm.delete_term_icon(params)
                  .then((resp) => {
                    if (resp) {
                      vm.parent.termIcons.splice(index, 1)
                      vm.parent.makeToast(
                        "danger",
                        vm.$t("delete_image_title"),
                          vm.$t("label.icon")+
                          vm.$t("delete_record_message") +
                          vm.$t("term_icons") +
                          vm.$t("records")
                      );
                    }
                  })
                  .catch((error) => {
                    console.log(error);
                  });
              }
            },
          },
          no: {
            text: vm.$t("no"),
            btnClass:
              "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
            action: function() {},
          },
        },
      });
    },

    setFormData() {
      this.formData = new FormData();
      
      let form = this.parent.iconForm;
      let providerType = ''
      let file = ""
      if(this.file != null) file = this.file
      if(form.provider_type != null) {
        if(form.provider_type.id != undefined) providerType = form.provider_type.id
      }
      let action = form.action;
      this.formData.append("id", form.id);
      this.formData.append("api_token", this.$user.api_token);
      this.formData.append("brand_id", this.$brand.id);
      this.formData.append("action", action);
      this.formData.append("icon", file);
      this.formData.append("term", form.term);
      this.formData.append("provider_type", providerType);
    },

    onReset() {
      this.isActionShow = true;
      this.parent.iconForm.reset();
    },
  },
};
</script>

