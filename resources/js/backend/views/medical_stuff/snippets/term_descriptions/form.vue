<style scoped>
.popover {
  max-width: 100% !important; /* Max Width of the popover (depending on the container!) */
}
</style>
<template>
  <b-popover :target="event_name" triggers="click" placement="right">
    <template #title>
      <h6 class="mb-0">
        <span v-if="action == 'Add'">
          {{ $t("label.add") }} {{ $t("label.description") }}
        </span>
        <span v-else>
          {{ $t("button.edit") }} {{ $t("label.description") }}
        </span>
      </h6>
    </template>

    <form style="width: 450px !important">
      <div class="form-group mb-4" v-show="action === 'Edit'">
        <div class="form-inline">
          <label class="mr-sm-2" for="inline-form-custom-select-pref">
            Language
          </label>
          <b-form-select
            id="inline-form-custom-select-pref"
            class="mb-2 mr-sm-2 mb-sm-0"
            v-model="language"
            :options="languageOptions"
          />
        </div>
        <hr />
      </div>

      <div class="form-group mt-3">
        <v-select
          id="description"
          :options="descriptionItems"
          v-model="dForm.description"
          @input="on_select_description"
          name="description"
          label="base_name"
          placeholder="Select Description"
        >
          <template #list-header>
            <li style="margin-left: 20px" class="mb-2">
              <b-link
                :title="`${$t('label.new')}  ${$t('label.description')}`"
                href="javascript:;"
                @click="show_description_modal"
              >
                <i class="fa fa-plus" aria-hidden="true" />
                {{ $t("label.new") }}
                {{ $t("label.description") }}
              </b-link>
            </li>
          </template>
          <template #option="{ on_select_description_name }">
            <span v-html="on_select_description_name" />
          </template>
        </v-select>
      </div>

      <div class="form-group mt-3">
        <b-form-select
          :disabled="!dForm.description"
          v-model="dForm.method"
          :options="methodOptions"
        />
      </div>

      <div class="form-group mt-3" v-if="dForm.with_value">
        <input
          type="text"
          v-model="dForm.value"
          name="value"
          id="value"
          class="form-control"
          placeholder="Value"
          autocomplete="off"
        />
      </div>

      <div class="form-group mt-3">
        <b-form-textarea
          id="note"
          name="note"
          v-model="dForm.note"
          placeholder="Notes here..."
          autocomplete="off"
          rows="6"
        >
        </b-form-textarea>
      </div>

      <div class="form-group float-right">
        <b-button size="sm" variant="light" @click="on_cancel_description">
          {{ $t("cancel") }}
        </b-button>
        <b-button
          size="sm"
          class="text-white"
          :variant="action == 'Add' ? 'primary' : 'success'"
          :disabled="is_data_empty || btnLoading"
          @click.prevent="on_form_submit"
        >
          <b-spinner
            v-if="btnLoading"
            small
            label="Small Spinner"
            type="grow"
          />

          <span v-if="action == 'Add'" v-text="$t('button.save')" />
          <span v-else v-text="$t('button.save_change')" />
        </b-button>
      </div>
    </form>
  </b-popover>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import MedicalMixin from "./../../mixins/medicalMixin";

export default {
  props: [
    "event_name",
    "parent_id",
    "dForm",
    "item",
    "term",
    "action",
    "descriptionItems",
  ],

  mixins: [MedicalMixin],

  data() {
    return {
      btnLoading: false,
      method: "",

      methodOptions: [
        {
          text: "Choose a Method",
          value: "",
          disabled: true,
        },
        {
          text: "Mutual",
          value: "mutual",
        },
        {
          text: "Non-Mutual",
          value: "non-mutual",
        },
      ],
    };
  },

  watch: {
    language(value) {
      switch (value) {
        case "en":
          this.dForm.note = this.item.is_english;
          break;
        case "it":
          this.dForm.note = this.item.is_italian;
          break;
        case "de":
          this.dForm.note = this.item.is_german;
          break;
        case "ph-bis":
          this.dForm.note = this.item.is_bisaya;
          break;
        case "ph-fil":
          this.dForm.note = this.item.is_filipino;
          break;
      }
    },
  },

  computed: {
    ...mapGetters("categ_terms", {
      response_item: "get_response_item",
    }),

    is_data_empty() {
      let k = true;
      Object.values(this.dForm.data()).forEach((ele, index) => {
        if (ele && index > 0) {
          k = false;
        }
      });
      return k;
    },
  },

  methods: {
    ...mapActions("categ_terms", ["post_term_connection_description"]),

    on_select_description(item) {
      this.dForm.with_value = false;
      this.dForm.method = "";

      if (item) {
        this.dForm.method = "mutual";
        if (item.with_value === 1) {
          this.dForm.with_value = true;
        }
      }
    },

    on_form_submit() {
      this.btnLoading = true;

      let params = {
        id: this.dForm.id,
        parent_id: this.parent_id,
        child_id: this.term.id,
        api_token: this.$user.api_token,
        description: this.dForm.description ? this.dForm.description.id : "",
        value: this.dForm.value,
        note: this.dForm.note,
        action: this.action,
        method: this.dForm.method,
        locale: this.action === "Add" ? this.$i18n.locale : this.language,
      };

      this.post_term_connection_description(params)
        .then((resp) => {
          this.$emit("done-success", this.term, resp.data);
          this.$root.$emit("bv::hide::popover", this.event_name);
        })
        .catch((error) => {
          if (error.response) {
            this.dForm.errors.record(error.response.data.errors);
            if (this.dForm.note) {
              this.errorToast(
                `${this.$t("errors.duplicate")}!`,
                this.$t("label.existing_notes")
              );
            }
          }
        })
        .finally(() => (this.btnLoading = false));
    },

    show_description_modal() {
      this.$emit("show-desciption-modal", "term-desc-modal");
    },

    on_cancel_description() {
      this.dForm.reset();
      this.$root.$emit("bv::hide::popover", this.event_name);
    },
  },
};
</script>

