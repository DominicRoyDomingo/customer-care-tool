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
      <!-- <span class="mr-1">Fetching...</span> -->
    </label>
    <div class="row" v-else>
      <!-- <button class="btn" @click="loadLinkedTerms">test</button> -->

      <div class="col-md-12 mt-4">
        <div
          class="card card-term v-sheet v-sheet--outlined theme--light"
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

        <!-- ====================================================================================================== -->
        <table
          class="table table-condensed no-boredered table-striped mb-0"
          v-if="false"
        >
          <tbody>
            <tr
              v-for="(termName, termtypeName, key) in items"
              :key="termtypeName"
            >
              <td width="30%">
                <a
                  href="javascript:;"
                  @click.prevent="
                    parent.showTermsDetails(termName, termtypeName)
                  "
                  class="mb-0 mt-2 text-capitalize ml-2"
                  v-html="termtypeName"
                />
              </td>
              <td>
                <div
                  id="accordianTerm"
                  role="tablist"
                  aria-multiselectable="true"
                >
                  <div class="row">
                    <div
                      class="col-sm-4"
                      v-for="(tName, tNameIndex) in termName"
                      :key="tNameIndex"
                    >
                      <div class="card shadow">
                        <div
                          class="card-header"
                          role="tab"
                          :id="`section1HeaderId-${key}`"
                        >
                          <h6 class="mb-0">
                            <a
                              data-toggle="collapse"
                              data-parent="#accordianTerm"
                              :href="`#${tName.id_slug_name}`"
                              aria-expanded="true"
                              aria-controls="section1ContentId"
                              v-html="tName.name"
                              @click="on_term_show(tName.route_show)"
                              class="font-weight-bold text-capitalize"
                            />
                          </h6>
                        </div>
                        <div
                          :id="`${tName.id_slug_name}`"
                          class="collapse in show"
                          role="tabpanel"
                          :aria-labelledby="`section1HeaderId-${key}`"
                        >
                          <div
                            class="card-body"
                            v-if="tName.descriptions.length > 0"
                          >
                            <div
                              v-for="desc in tName.descriptions"
                              :key="desc.id"
                            >
                              <p
                                class="mb-0"
                                v-if="desc.term_description"
                                v-html="
                                  `${desc.term_description.description_name}`
                                "
                              />
                              <span
                                class="form-text text-muted"
                                v-html="desc.note_name"
                              />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
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
  props: ["term", "parent"],

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
  watch: {
    term() {
      this.loadLinkedTerms();
    }
  },
  mounted() {
    this.loadLinkedTerms();
  },

  methods: {
    ...mapActions("categ_terms", ["get_linked_terms_actor"]),

    loadLinkedTerms() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        value: this.term,
        brand_id: this.$brand.id,
      };
      
      this.get_linked_terms_actor(params).then((_) => {
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

