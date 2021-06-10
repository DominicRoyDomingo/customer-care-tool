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
  <div class="">
    <label v-if="isLoading" class="mt-3">
      <b-spinner small label="Small Spinner"></b-spinner>
    </label>
    <div class="row" v-else>
      <div class="col-md-12 mt-4">
        <div
          class="card card-term"
          v-for="(termName, termtypeName, key) in items"
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
                    <td>
                      <a
                        :href="`#${tName.id_slug_name}`"
                        v-html="tName.name"
                        @click="on_term_show(tName.route_show)"
                        class="text-capitalize ml-2"
                      />
                    </td>
                    <!-- <td>
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
                    </td> -->
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div
        class="col-md-12"
        v-if="items.length === 0"
        style="margin-top: -10px"
      >
        <div class="alert alert-secondary" role="alert">
          <div class="text-center p-4">
            <h1 class="fa fa-link mt-3"></h1>
            <p>
              {{ $t("link_to_terminologies") }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
export default {
  props: ["term", "id", "parent"],

  data() {
    return {
      isLoading: true,
    };
  },

  computed: {
    ...mapGetters("categ_terms", {
      items: "get_linked_terms_items",
    }),
  },

  mounted() {
    this.loadLinkedTerms();
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
        console.log(this.items);
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

