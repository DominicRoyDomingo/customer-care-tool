<template>
  <div class="information_type">
    <div
      class="row mt-4 animated fadeIn"
      v-for="(form, index) in informationForms"
      :key="index"
    >
      <div class="col-md-12" v-if="informationForms.length > 1">
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

      <div class="col-md-12" style="margin-top: -20px">
        <div class="form-group">
          <label for="info_type" class="text-uppercase">
            {{ $t("label.information_type") }}
          </label>
          <v-select
            name="info_type"
            id="info_type"
            label="base_name"
            value="base_name"
            v-model="informationForms[index].information_type"
            :options="infoTypeItems"
            :loading="isLoadinginfoType"
            :disabled="isLoadinginfoType"
            @input="
              on_select_info_type(
                informationForms[index].information_type,
                index
              )
            "
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
                <b-link href="#" @click="on_add_info_type">
                  <b-spinner
                    small
                    label="Small Spinner"
                    v-if="isAddInfoTypeLoading"
                  />

                  <i class="fa fa-plus" v-else aria-hidden="true"></i>
                  {{ $t("button.new") }} {{ $t("label.information_type") }}
                </b-link>
              </li>
            </template>
            <template #option="{ information_type_name }">
              <span v-html="information_type_name" />
            </template>
          </v-select>
        </div>

        <InformationTypeForm
          v-if="informationForms[index].isDisable"
          :info_type="seletectInfoType"
          :infoModel="informationForms[index].information"
          :index="index"
          @info-value="info_value"
        />
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <v-btn
          tile
          block
          color="success lighten-1"
          @click.prevent="on_add_form(false)"
        >
          <i class="fa fa-plus mr-2" aria-hidden="true"></i>
          {{ $t("label.add_other_info") }}
        </v-btn>
      </div>
    </div>

    <CreateIformationType
      :$this="this"
      @loadTable="create_success"
      v-if="isAddInfoType"
    />
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Form from "../../../helpers/form";
import toast from "../../../helpers/toast";
import InformationTypeForm from "./information_types/information_form";
import CreateIformationType from "./../../includes/information-type/CreateComponent";

export default {
  props: {
    course_info_types: {
      type: Array,
    },
  },

  mixins: [toast],

  components: { CreateIformationType, InformationTypeForm },

  data() {
    return {
      isDisable: true,
      isAddInfoType: false,
      isLoadinginfoType: true,
      isAddInfoTypeLoading: false,
      infoTypeItems: [],
      informationForms: [],
      information: "",
      items: ["Long Text", "Short Text", "Date", "Email Format", "Numeric"],

      seletectInfoType: {},

      formData: new FormData(),
      form: new Form({
        id: "",
        information_type_name: "",
        information_type_data: null,
        language: this.$i18n.locale,
      }),

      params: {
        locale: this.$i18n.locale,
        api_token: this.$user.api_token,
        brand_id: this.$brand.id,
      },

      modal: {
        createInformationType: {
          id: "information-type-add-modal",
          name: "information_type_new_button",
          isVisible: false,
          button: {
            save: "save_button",
            cancel: "cancel",
            new: "information_type_new_button",
            loading: false,
          },
        },
      },
    };
  },

  computed: {
    ...mapGetters("information_type", {
      informationTypesResponseStatus: "get_response_status",
    }),
  },

  mounted() {
    this.load_information_types();

    if (this.course_info_types && this.course_info_types.length > 0) {
      this.course_info_types.forEach((item) => {
        this.seletectInfoType = item.information_type;
        this.on_add_form(item);
      });
      return;
    }

    this.on_add_form(false);
  },

  methods: {
    load_information_types() {
      const params = { ...this.params };
      axios.get("/api/select/information_type/all", { params }).then((resp) => {
        this.infoTypeItems = resp.data;
        this.isLoadinginfoType = false;
      });
    },

    on_add_form(data) {
      let params = this.set_form(data);

      // add course form
      this.informationForms.push(params);

      this.$emit("information-type-form", this.informationForms);
    },

    on_add_info_type() {
      this.isAddInfoType = true;
      this.isAddInfoTypeLoading = true;

      setTimeout(() => {
        this.isAddInfoTypeLoading = false;
        this.$bvModal.show("information-type-add-modal");
      }, 1000);
    },

    info_value(value, index) {
      this.informationForms[index].information = value;
      this.set_data(index, value);
    },

    on_select_info_type(model, index) {
      // console.log(model);
      this.informationForms[index].isDisable = false;

      if (model) {
        this.seletectInfoType = model;
        // this.informationForms[index].information_type = model;
        this.$nextTick(() => {
          this.informationForms[index].isDisable = true;
        });
      }

      this.set_data(index, "");
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
              vm.informationForms.splice(index, 1);
              vm.$emit("information-type-form", vm.informationForms);
            },
          },
          cancel: function () {},
        },
      });
    },

    set_data(index, value) {
      // let InfoTypeId = this.seletectInfoType ? this.seletectInfoType : "";
      this.informationForms[index].information = value;
      // this.informationForms[index].information_type = InfoTypeId;
      this.informationForms[index].isDisable = true;

      this.$emit("information-type-form", this.informationForms);
    },

    set_form(object) {
      return {
        information_type: object ? object.information_type : "",
        information: object ? object.information : "",
        isDisable: object ? true : false,
      };
    },

    create_success() {
      this.load_information_types();
    },

    informationTypePage() {
      window.location.href = `/admin/information-type`;
    },
  },
};
</script>

 