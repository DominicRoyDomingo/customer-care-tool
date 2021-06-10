<template>
  <b-modal
    id="course-type-modal"
    hide-footer
    hide-header
    size="lg"
    @hidden="resetModal"
    no-close-on-backdrop
  >
    <v-app id="create__container">
      <v-card class="border-0">
        <v-card-title class="title blue-grey lighten-4 text-capitalize">
          <div>
            <span v-if="typeForm == 'Add'">
              {{ $t("course_type_modal") }}
            </span>

            <span
              class="d-inline-block text-truncate"
              style="max-width: 700px; letter-spacing: normal"
              v-else
            >
              {{ $t("publishing_edit_label") + " - " + course_item.base_name }}
            </span>
          </div>

          <v-spacer></v-spacer>
          <v-btn icon color="error lighten-2" @click="onClose">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>

        <v-row>
          <v-col sm="12" md="12" cols="12">
            <form class="form">
              <v-container>
                <div class="form-group">
                  <div class="d-flex justify-content-between align-items-end">
                    <div></div>
                    <div v-if="typeForm == 'Update'">
                      <select class="form-control" v-model="langsel">
                        <option value="en">ENGLISH</option>
                        <option value="it">ITALIAN</option>
                        <option value="de">GERMAN</option>
                        <option value="ph-fil">FILIPINO</option>
                        <option value="ph-bis">VISAYAN</option>
                      </select>
                    </div>
                  </div>
                  <v-text-field
                    :label="$t('publishing_type_name').toUpperCase()"
                    id="type_name"
                    type="type_name"
                    name="Name"
                    v-model="course_type"
                    hide-details="auto"
                    autocomplete="off"
                    clearable
                  />
                  <div class="text-danger py-2" v-if="is_required">
                    {{ $t("label.name_required") }}
                  </div>
                </div>
              </v-container>
              <v-card-actions
                class="modal__footer blue-grey lighten-5 justify-content-end px-5"
              >
                <div class="row">
                  <div class="col-md-6">
                    <div class="mt-3">
                      <a
                        v-if="name === 'publishing-item'"
                        :href="courseTypeRoute"
                        class="text-uppercase"
                      >
                        {{ $t("manage_type_course") }}
                      </a>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <span class="float-right">
                      <v-btn color="error" text tile @click="onClose">
                        {{ $t("cancel") }}
                      </v-btn>
                      <v-btn
                        color="success"
                        tile
                        type="button"
                        @click.prevent="add"
                        class="bg-success text-white"
                        v-if="typeForm == 'Add'"
                      >
                        <div>
                          <v-progress-circular
                            size="20"
                            width="1"
                            color="white"
                            indeterminate
                            v-if="btnloading"
                          >
                          </v-progress-circular>
                          <span>{{ $t("button.save") }}</span>
                        </div>
                      </v-btn>
                      <v-btn
                        color="success"
                        tile
                        type="button"
                        @click="update"
                        class="bg-success text-white"
                        v-else-if="typeForm == 'Update'"
                      >
                        <div>
                          <v-progress-circular
                            size="20"
                            width="1"
                            color="white"
                            indeterminate
                            v-if="btnloading"
                          >
                          </v-progress-circular>
                          <span>{{ $t("button.save_change") }}</span>
                        </div>
                      </v-btn>
                    </span>
                  </div>
                </div>
              </v-card-actions>
            </form>
          </v-col>
        </v-row>
      </v-card>
    </v-app>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import courseMixin from "../mixins/courseMixin";
import {
  createItemType,
  updateItemType,
  getItemTypeName,
} from "./../../../api/item_type.js";
import i18n from "../../../i18n.js";

let languange = i18n.locale;

export default {
  mixins: [courseMixin],
  data() {
    return {
      // for publishing Item
      courseTypeRoute: "/admin/courses/type",

      formData: new FormData(),
      course_type: "",
      btnloading: false,
      is_required: false,
      langsel: languange,
      modal: {
        isVisible: false,
      },
    };
  },
  props: {
    initTable: {
      type: Function,
    },
    typeForm: {
      type: String,
    },
    course_item: {
      type: Object,
    },
    name: {
      type: String,
    },
  },
  watch: {
    course_item(val) {
      this.course_type = val.base_name;
    },
    langsel(val, old) {
      this.onUpdateLang();
    },
  },
  methods: {
    onClose() {
      this.$bvModal.hide("course-type-modal");

      // for publishing item course info
      this.$emit("on-close");
    },
    add() {
      this.is_required = false;
      if (this.course_type == "" || this.course_type == null) {
        this.is_required = true;
        return;
      }

      this.addCourseType(this.course_type).then((res) => {
        this.onClose();

        // for publishing item course info
        if (this.name === "publishing-item") {
          this.$emit("on-success", res.data);
          return;
        }

        this.initTable();
      });
    },
    update() {
      var lang = this.langsel;
      this.updateCourseType(this.course_type, this.course_item.id, lang).then(
        (res) => {
          this.onClose();
          this.initTable();
        }
      );
    },
    onUpdateLang: function () {
      var id = this.course_item.id;
      var lang = this.langsel;
      this.singleCourseType(id, lang).then((res) => {
        this.course_type = res.name;
      });
    },
    resetModal() {
      this.course_type = "";
    },
  },
};
</script>
