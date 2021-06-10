<style  scoped>
.card-term {
  border-radius: 10px;
}
.card-term .card-header {
  border-top-right-radius: 10px;
  border-top-left-radius: 10px;
}
</style>
<template>
  <div>
    <div
      class="card card-term"
      v-for="(termName, termtypeName, key) in term"
      :key="termtypeName"
    >
      <div class="card-header">
        <a
          href="javascript:;"
          @click.prevent="parent.showTermsDetails(termName, termtypeName)"
          class="text-capitalize font-weight-bold"
          v-html="termtypeName"
        />

        <a
          :href="`#collapseCardExample-${key}`"
          data-toggle="collapse"
          role="button"
          aria-expanded="true"
          :aria-controls="`collapseCardExample-${key}`"
          class="float-right"
        >
          <i class="fa fa-caret-down text-muted" aria-hidden="true"></i>
        </a>
      </div>
      <div class="collapse show" :id="`collapseCardExample-${key}`">
        <div class="card-body p-0">
          <table
            class="table mb-0"
            v-for="(tName, tNameIndex) in termName"
            :key="tNameIndex"
          >
            <tbody>
              <tr>
                <td style="width: 30%">
                  <a
                    :href="`#${tName.id_slug_name}`"
                    v-html="tName.name"
                    @click="on_term_show(tName.route_show)"
                    class="text-capitalize ml-2"
                  />
                </td>
                <td>
                  <ul class="mb-0">
                    <li v-for="desc in tName.descriptions" :key="desc.id">
                      <p
                        class="mb-0"
                        v-if="desc.term_description"
                        v-html="`${desc.term_description.description_name}`"
                      />
                      <span
                        class="form-text text-muted"
                        v-html="desc.note_name"
                      />
                    </li>
                  </ul>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="alert alert-secondary" role="alert" v-if="term.length === 0">
      <div class="text-center p-4">
        <h1 class="fa fa-link mt-3"></h1>
        <p>
          {{ $t("link_to_terminologies") }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
export default {
  props: ["id", "term", "parent"],

  data() {
    return {
      isLoading: false,
    };
  },

  computed: {
    ...mapGetters("categ_terms", {
      items: "get_linked_terms_items",
    }),
  },

  mounted() {
    // console.log(this.term);
    // this.loadLinkedTerms();
  },

  methods: {
    ...mapActions("categ_terms", ["get_linked_terms"]),

    loadLinkedTerms() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        parent_id: this.id,
        value: this.term.medical_terms,
        brand_id: this.$brand.id,
      };

      this.get_linked_terms(params).then((_) => {
        // console.log(this.items);
        // this.linkLoading = false;
        this.isLoading = false;
      });
    },

    on_term_show(route) {
      window.location.href = route;
    },
  },
};
</script>

