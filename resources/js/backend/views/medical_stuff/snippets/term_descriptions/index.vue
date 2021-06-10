<style scoped>
.font-11 {
  font-size: 11px !important;
}
.font-12 {
  font-size: 12px !important;
}
</style>
<template>
  <div class="ml-4 mt-1">
    <ul class="list-unstyled" v-for="desc in term_descriptions" :key="desc.id">
      <li
        v-if="desc.term_description"
        v-html="desc.term_description.description_name"
      />
      <li v-html="desc.note_name" class="text-muted" />
      <li>
        <span>
          <b-link
            @click="on_edit_description(desc)"
            href="javascript:;"
            v-b-tooltip.hover
            :id="`edit-desc-${desc.id}`"
            :title="$t('button.edit')"
          >
            <i class="font-11 fas fa-edit text-success" />
          </b-link>
          <b-link
            href="javascript:;"
            @click="on_delete_description(desc)"
            v-b-tooltip.hover
            :title="$t('button.delete')"
            :disabled="desc.is_loading"
            class="ml-1"
          >
            <b-spinner
              small
              label="Small Spinner"
              class="text-danger"
              style="font-size: 11px !important"
              v-if="desc.is_loading"
            />
            <i class="font-11 fa fa-trash text-danger" v-else />
          </b-link>
        </span>

        <DescriptionForm
          :item="item"
          :action="'Edit'"
          :parent_id="parent_id"
          :term="term"
          :descriptionItems="descriptionItems"
          :event_name="`edit-desc-${desc.id}`"
          :dForm="dForm"
          @done-success="edit_success"
          @show-desciption-modal="show_description_modal"
        />
      </li>
    </ul>

    <b-link
      href="javascript:;"
      class="font-12"
      @click="on_add_description"
      :id="`add-desc-${term.id}`"
      v-text="`${$t('label.add')} ${$t('label.description')}`"
    />

    <DescriptionForm
      :action="'Add'"
      :term="term"
      :parent_id="parent_id"
      :descriptionItems="descriptionItems"
      :event_name="`add-desc-${term.id}`"
      :dForm="dForm"
      @done-success="create_success"
      @show-desciption-modal="show_description_modal"
    />
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import DescriptionForm from "./form";
import Form from "./../../../../helpers/form";
import medicalMixin from "../../mixins/medicalMixin";

export default {
  props: ["term", "parent_id", "term_descriptions", "descriptions"],

  components: {
    DescriptionForm,
  },

  mixins: [medicalMixin],

  data() {
    return {
      isFormShow: false,
      item: "",
      descriptionItems: [],
      dForm: new Form({
        id: "",
        description: "",
        note: "",
        value: "",
        with_value: "",
        method: "",
        action: "",
      }),
    };
  },

  mounted() {
    this.set_descriptions();
  },

  computed: {
    ...mapGetters("categ_terms", {
      status: "get_response_status",
      response_item: "get_response_item",
    }),
  },

  methods: {
    ...mapActions("categ_terms", ["delete_term_connection_description"]),

    on_add_description() {
      this.dForm.reset();
      this.on_hide_form();
    },

    on_edit_description(item) {
      this.item = item;
      this.dForm.id = item.id;
      this.dForm.description = item.term_description;
      this.dForm.note = item.base_name;
      this.dForm.method = item.method !== "none" ? item.method : "";

      if (item.term_description) {
        this.dForm.with_value =
          item.term_description.with_value === 1 ? true : false;
        this.dForm.value = item.value;
      }

      this.on_hide_form();
    },

    on_delete_description(item) {
      let baseName = item.base_name;
      let records = this.$t("label.description");

      this.actionConfirm(baseName, records).then((resp) => {
        if (resp) {
          item.is_loading = true;
          let params = {
            api_token: this.$user.api_token,
            locale: this.language,
            id: item.id,
          };
          this.delete_term_connection_description(params)
            .then((_) => {
              // this.deleteToast(baseName, records);
              this.term_descriptions.splice(
                this.getRemoveItemIndex(this.term_descriptions, item.id),
                1
              );
              // this.descriptions.push(item.term_description);

              this.descriptionItems.push(item.term_description);
              this.$emit("delete-connection-desc-success", item);
            })
            .catch((error) => {
              console.log(error);
            });
        }
      });
    },

    create_success(term, item) {
      if (this.dForm.description) {
        this.descriptionItems.splice(
          this.getRemoveItemIndex(
            this.descriptionItems,
            item.term_description.id
          ),
          1
        );
      }

      this.term_descriptions.push(item);
      this.linkedToast(term.base_name, this.$t("label.descriptions"));
      this.dForm.reset();
      this.$emit("link-success", item);
    },

    edit_success(term, item) {
      this.term_descriptions.splice(
        this.getRemoveItemIndex(this.term_descriptions, item.id),
        1
      );

      if (this.dForm.description) {
        this.descriptionItems.splice(
          this.getRemoveItemIndex(
            this.descriptionItems,
            item.term_description.id
          ),
          1
        );
      }

      this.term_descriptions.push(item);
      this.updateToast(item.id, this.$t("label.descriptions"));
      this.dForm.reset();
      this.$emit("link-success");
    },

    show_description_modal(modalname) {
      this.$emit("show-modal", this.term, modalname);
    },

    set_descriptions() {
      this.descriptionItems = [];
      let idArray = this.term_descriptions.map(function (term) {
        if (term.term_description) {
          return term.term_description.id;
        }
      });
      this.descriptions.forEach((desc) => {
        let k = false;
        idArray.forEach((id) => {
          if (desc.id === id) {
            k = true;
          }
        });

        if (!k) {
          this.descriptionItems.push(desc);
        }
      });
    },

    on_hide_form() {
      this.$root.$emit("bv::hide::popover");
    },
  },
};
</script>
