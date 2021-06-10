<style scoped>
.check-box {
  cursor: pointer !important;
}
</style>
<template>
  <b-table
    class="mb-0"
    show-empty
    :sort-by.sync="sortBy"
    :sort-desc.sync="sortDesc"
    :empty-text="$t('no_record_found')"
    :fields="fields"
    :busy="isLoading"
    :items="items"
    :tbody-tr-class="bg_light_class"
  >
    <template v-slot:table-busy>
      <div class="d-flex justify-content-center p-2">
        <b-spinner label="Small Loading..."></b-spinner>
      </div>
    </template>

    <template v-slot:cell(index)="data">
      <div class="row mb-0 mt-0">
        <div class="col-md-12">
          <span :id="`term-${data.index}`" class="d-inline-block" tabindex="0">
            <b-form-checkbox
              @change="on_term_linked(data.item)"
              v-model="linkedTermsId"
              :id="`medterm-${data.item.id}`"
              :name="`medterm-${data.item.id}`"
              :value="data.item.id"
              :disabled="data.item.is_loading || !data.item.has_term_types"
            >
              <strong
                :for="`medterm-${data.item.id}`"
                class="text-capitalize check-box"
                v-html="data.item.term_name"
              />

              <b-spinner
                label="Loading..."
                class="ml-2"
                style="position: absolute; margin-top: -2px"
                small
                v-if="data.item.is_loading"
              />

              <span
                class="badge badge-success h4 mr-2 text-white text-uppercase"
                style="vertical-align: top; margin-top: -5px"
                v-text="$t('linked')"
                v-if="linked_message(data.item.id) && !data.item.is_loading"
              />
            </b-form-checkbox>
          </span>
          <b-tooltip
            v-if="!data.item.has_term_types"
            variant="danger"
            :target="`term-${data.index}`"
          >
            <p class="mt-2">
              <strong> {{ $t("errors.error") }}! </strong>
              {{ $t("label.linking_to") }}
              <strong>
                {{ data.item.base_name }}
              </strong>
              {{ $t("errors.linking_error") }}
            </p>
          </b-tooltip>
        </div>
      </div>
    </template>
  </b-table>
</template>

<script>
import { link_course_link, get_course_term_id } from "./../../../mixins/query";
import articleMixin from "./../../../mixins/articleMixin";
export default {
  mixins: [articleMixin],
  props: {
    article: {
      type: Object,
    },
    items: {
      type: Array,
    },
    total: {
      type: Number,
    },
    isLoading: {
      type: Boolean,
    },
    type: {
      type: String,
    },
  },
  data() {
    return {
      sortBy: "is_linked_to_article",
      sortDesc: true,
      linkedTermsId: [],
      fields: [
        {
          key: "index",
          thClass: "text-left",
          label: this.$t("label.linking_to") + " ?",
          filterByFormatted: true,
        },
      ],
      params: {
        api_token: this.$user.api_token,
        brand_id: this.$brand.id,
        article_id: this.article.id,
      },
    };
  },
  mounted() {
    this.load_linked_terms_id();
  },
  computed: {
    totalRows() {
      return this.total;
    },
  },
  methods: {
    load_linked_terms_id() {
      this.linkedTermsId = [];
      let params = {
        ...this.params,
        type: this.type,
      };
      get_course_term_id(params)
        .then((data) => {
          this.linkedTermsId = data;
        })
        .finally(() => {});
    },

    linked_message(id) {
      let key = false;

      this.linkedTermsId.forEach((ele) => {
        if (ele === id) {
          key = true;
        }
      });

      return key;
    },

    on_term_linked(item) {
      item.is_loading = true;
      let checked = $(`#medterm-${item.id}`).prop("checked");
      let params = {
        ...this.params,
        term_id: item.id,
        key: checked ? "link" : "unlink",
        type: this.type,
      };

      link_course_link(params)
        .then((data) => {
          console.log(data);
        })
        .finally(() => {
          this.$parent.$parent.$emit("on-update");
          item.is_loading = false;
        });

      this.$emit("on-update");
    },

    bg_light_class(item, type) {
      let key = false;
      if (item) {
        this.linkedTermsId.forEach((id) => {
          if (id === item.id) {
            key = true;
          }
        });
      }
      return key ? "bg-light" : "";
    },
  },
};
</script>

 