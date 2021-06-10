<template>
  <div class="course_form mb-0">
    <div
      class="row animated fadeIn mt-2"
      v-for="(form, index) in courseForms"
      :key="index"
    >
      <div class="col-md-12" v-if="courseForms.length > 1">
        <div class="row">
          <div class="col-md-11">
            <hr />
          </div>
          <div class="col-md-1">
            <v-btn
              fab
              dark
              small
              color="error"
              title="Remove Form"
              @click.prevent="on_remove_course_form(index)"
            >
              <v-icon dark>mdi-close</v-icon>
            </v-btn>
          </div>
        </div>
      </div>

      <div class="col-md-12">
        <div class="form-group">
          <label for="course_type"> COURSE TYPE </label>
          <v-select
            name="course_type"
            id="course_type"
            label="base_name"
            v-model="courseForms[index].course_types"
            @input="
              on_select_course_type(courseForms[index].course_types, index)
            "
            :options="courseTypeItems"
            :loading="isLoadingCourseType"
            :disabled="isLoadingCourseType"
            :reduce="(course) => course.id"
            multiple
          >
            <template #spinner="{ loading }">
              <b-spinner
                v-if="loading"
                class="text-info"
                small
                label="Small Spinner"
              />
            </template>
            <template #list-header>
              <li style="margin-left: 20px" class="mb-2">
                <b-link href="#" @click="on_add_course_type">
                  <b-spinner
                    small
                    label="Small Spinner"
                    v-if="isAddCourseTypeLoading"
                  />
                  <i class="fa fa-plus" v-else aria-hidden="true"></i>
                  {{ $t("button.new") }} {{ $t("table.course_type") }}
                </b-link>
              </li>
            </template>
            <template #option="{ course_type_name }">
              <span v-html="course_type_name" />
            </template>
          </v-select>
        </div>
        <div class="form-group">
          <label for="noh"> NUMBER OF HOURS </label>
          <b-form-input
            id="noh"
            name="noh"
            :disabled="courseForms[index].isDisable"
            v-model="courseForms[index].hours"
            placeholder="0"
            autocomplete="off"
          />
        </div>
        <div class="form-group">
          <label for="credit"> CREDITS </label>
          <b-form-input
            id="credit"
            name="credit"
            :disabled="courseForms[index].isDisable"
            v-model="courseForms[index].credit"
            placeholder="0"
            autocomplete="off"
          />
        </div>
        <div class="form-group">
          <label for="price">
            PRICE IN EURO
            <small>(<i class="fas fa-euro-sign"></i>)</small>
          </label>
          <b-form-input
            id="price"
            name="price"
            :disabled="courseForms[index].isDisable"
            v-model="courseForms[index].price"
            placeholder="0"
            autocomplete="off"
          />
        </div>
        <div class="form-group">
          <label for="address" class="text-uppercase">
            {{ $t("address") }}
          </label>
          <b-form-textarea
            id="address"
            name="address"
            :disabled="courseForms[index].isDisable"
            v-model="courseForms[index].address"
            rows="3"
            max-rows="6"
          />
        </div>
        <div class="form-group mb-0">
          <label for="language" class="text-uppercase"> LANGUAGE </label>
          <b-form-select
            id="language"
            name="language"
            v-model="courseForms[index].language"
            :options="langOptions"
          />
        </div>
      </div>
    </div>
    <CreateCourseTypeModal
      v-if="isAddCourseType"
      :typeForm="'Add'"
      :name="`publishing-item`"
      @on-success="on_success"
      @on-close="close_modal"
    />
  </div>
</template>

<script>
import toast from "../../../helpers/toast";
import CreateCourseTypeModal from "./../../course/modals/course-type-modal";

export default {
  props: {
    course: {
      type: Object,
    },
    course_info: {
      type: Object,
    },
  },

  mixins: [toast],

  components: {
    CreateCourseTypeModal,
  },

  data() {
    return {
      isDisable: true,
      isAddCourseType: false,
      isLoadingCourseType: true,
      isAddCourseTypeLoading: false,
      courseTypeItems: [],
      courseForms: [],

      langOptions: ["English", "Italian"],

      // langOptions: [
      //   { text: "English", value: "English" },
      //   { text: "Italian", value: "Italian" },
      // ],

      params: {
        locale: this.$i18n.locale,
        api_token: this.$user.api_token,
        brand_id: this.$brand.id,
      },
    };
  },

  mounted() {
    this.load_course_types();

    if (this.course_info && Object.values(this.course_info).length > 0) {
      [this.course_info].forEach((item) => {
        this.on_add_form(item);
      });
      return;
    }

    this.on_add_form(false);
  },

  methods: {
    load_course_types() {
      let params = { ...this.params };
      axios
        .get("/api/select/course_type/list_course_types", { params })
        .then((resp) => {
          this.courseTypeItems = resp.data;
          this.isLoadingCourseType = false;
        });
    },

    on_select_course_type(model, index) {
      if (model.length > 0) {
        this.courseForms[index].isDisable = false;
        this.$emit("course-form", this.courseForms);
        return;
      }
      this.courseForms[index].price = "";
      this.courseForms[index].credit = "";
      this.courseForms[index].hours = "";
      this.courseForms[index].address = "";
      this.courseForms[index].language = "English";

      setTimeout(() => {
        this.courseForms[index].isDisable = true;
      }, 100);

      this.$emit("course-form", this.courseForms);
    },

    on_add_course_type() {
      this.isAddCourseType = true;
      this.isAddCourseTypeLoading = true;

      setTimeout(() => {
        this.isAddCourseTypeLoading = false;
        this.$bvModal.show("course-type-modal");
      }, 1000);
    },

    set_form(course) {
      return {
        course_types: course ? course.has_course_types : "",
        price: course ? course.price : "",
        credit: course ? course.number_credit : "",
        hours: course ? course.time_duration : "",
        address: course ? course.address : "",
        language: course ? course.language : "English",
        isDisable: course ? false : true,
      };
    },

    on_add_form(data) {
      // get data form
      let form = this.set_form(data);

      // add course form
      this.courseForms.push(form);

      this.$emit("course-form", this.courseForms);
    },

    on_remove_course_form(index) {
      let vm = this;
      $.confirm({
        title: vm.$t("confirm_action"),
        content: vm.$t("label.delete_other_info_confirmation"),
        type: "red",
        typeAnimated: true,
        buttons: {
          tryAgain: {
            text: vm.$t("confirm"),
            btnClass: "btn-red",
            action: function () {
              vm.courseForms.splice(index, 1);
              vm.$emit("course-form", vm.courseForms);
            },
          },
          cancel: function () {},
        },
      });
    },

    on_success(item) {
      this.load_course_types();
      this.storeToast(item.course_type_name, this.$t("course_type_title"));
    },

    close_modal() {
      this.isAddCourseType = false;
      this.$bvModal.show("article-modal");
    },
  },
};
</script>

<style>
</style>