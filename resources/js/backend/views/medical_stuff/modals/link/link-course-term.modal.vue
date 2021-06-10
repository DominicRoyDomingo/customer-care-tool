<style scoped>
.div-scroll {
  position: relative;
  height: 500px;
  overflow-y: scroll;
}
</style>
<template>
  <div class="course-term">
    <b-modal
      id="link-course-term-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
    >
      <v-app>
        <v-card class="border-0">
          <v-card-title class="bg-light">
            Link to Course Term
            <small
              class="text-dark text-capitalize d-inline-block text-truncate mt-1 ml-1"
              style="max-width: 600px; letter-spacing: normal"
              v-html="`(${item.article_title})`"
            />
            <v-spacer></v-spacer>
            <v-btn icon color="error lighten-2" @click="on_cancel">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>

          <v-card-text class="modal__content" style="">
            <v-container>
              <div class="row mt-2">
                <div class="col-md-6">
                  <b-form inline>
                    <label class="mr-sm-2 text-capitalize" for="per-page">
                      {{ $t("show") }}
                    </label>
                    <b-form-select
                      id="per-page"
                      class="mb-2 mr-sm-2 mb-sm-0"
                      :options="showEntriesOpt"
                      v-model="perPage"
                    />
                    <label class="mr-sm-2 text-capitalize">
                      {{ $t("label.entries") }}
                    </label>
                  </b-form>
                </div>
                <div class="col-md-6">
                  <b-form @submit.prevent="on_search_submit">
                    <b-input-group class="mb-0 float-right">
                      <b-input-group-prepend v-show="filter">
                        <b-button variant="lignt" @click="filter = ''">
                          <b-icon icon="x" class="text-danger" />
                        </b-button>
                      </b-input-group-prepend>

                      <template #append>
                        <b-button
                          variant="light"
                          type="submit"
                          :disabled="!filter"
                        >
                          <i
                            class="fa fa-search"
                            :class="{ 'text-primary': filter }"
                          />
                        </b-button>
                      </template>
                      <b-form-input
                        v-model="filter"
                        autocomplete="off"
                        :placeholder="$t('type_and_enter')"
                      />
                    </b-input-group>
                  </b-form>
                </div>
              </div>
              <div class="row mt-3">
                <div class="col-md-12">
                  <v-tabs
                    show-arrows
                    center-active
                    grow
                    background-color="blue-grey lighten-5"
                    slider-color="blue-grey darken-2"
                    color="blue-grey darken-2"
                    v-model="tabs"
                  >
                    <v-tab class="caption font-weight-bold">
                      {{ $t("discipline_ecm") }}
                    </v-tab>
                    <v-tab class="caption font-weight-bold">
                      {{ $t("recipients") }}
                    </v-tab>

                    <v-tab-item>
                      <b-card
                        v-if="Object.values(items).length > 0 && tabs === 0"
                        class="mb-0"
                        no-body
                      >
                        <template #header>
                          <h6 class="mb-0 font-weight-bold text-info">
                            {{ $t("label.terminilogies") }}
                          </h6>
                        </template>

                        <b-card-body
                          id="nav-scroller"
                          ref="content"
                          class="p-0"
                          :class="{ 'div-scroll': items.data.length > 7 }"
                        >
                          <b-overlay
                            id="overlay-background"
                            :show="isOverlay"
                            :variant="'light'"
                            opacity="0.85"
                            :blur="'1px'"
                            rounded="sm"
                          >
                            <TermsList
                              v-on="$listeners"
                              :type="type"
                              :article="item"
                              :items="items.data"
                              :total="items.total"
                              :isLoading="isLoading"
                            />
                          </b-overlay>
                        </b-card-body>
                      </b-card>
                    </v-tab-item>
                    <v-tab-item>
                      <b-card
                        v-if="Object.values(items).length > 0 && tabs === 1"
                        class="mb-0"
                        no-body
                      >
                        <template #header>
                          <h6 class="mb-0 font-weight-bold text-info">
                            {{ $t("label.terminilogies") }}
                          </h6>
                        </template>

                        <b-card-body
                          id="nav-scroller"
                          ref="content"
                          class="p-0"
                          :class="{ 'div-scroll': items.data.length > 7 }"
                        >
                          <b-overlay
                            id="overlay-background"
                            :show="isOverlay"
                            :variant="'light'"
                            opacity="0.85"
                            :blur="'1px'"
                            rounded="sm"
                          >
                            <TermsList
                              :type="type"
                              :article="item"
                              :items="items.data"
                              :total="items.total"
                              :isLoading="isLoading"
                            />
                          </b-overlay>
                        </b-card-body>
                      </b-card>
                    </v-tab-item>
                  </v-tabs>

                  <b-pagination
                    v-model="currentPage"
                    :total-rows="totalRows"
                    :per-page="perPage"
                    align="center"
                  />
                </div>
              </div>
            </v-container>
          </v-card-text>
        </v-card>
      </v-app>
    </b-modal>
  </div>
</template>

<script>
import medicalMixin from "./../../mixins/medicalMixin";
import { get_terms_by_types, get_course_term_id } from "./../../mixins/query";
import TermsList from "./tabs/terms-list";
export default {
  props: {
    item: {
      type: Object,
    },
  },
  components: { TermsList },
  mixins: [medicalMixin],
  data() {
    return {
      tabList: {
        show: false,
      },
      isOverlay: false,
      isLoading: true,
      tabs: 0,
      perPage: 10,
      currentPage: 1,
      type: "course_discipline",
      items: {},
      linkedTermsId: [],
    };
  },
  watch: {
    tabs(value) {
      if (value === 0) {
        this.type = "course_discipline";
      } else {
        this.type = "course_recepient";
      }
      if (Object.values(this.items).length > 0) {
        this.items.data = [];
      }

      if (!this.filter) {
        this.isLoading = true;
      } else {
        this.filter = "";
      }
      this.load_terms();
    },
    filter: function (value) {
      if (!value) {
        this.isOverlay = true;
        this.currentPage = 1;
        this.load_terms();
      }
    },
    currentPage() {
      this.isOverlay = true;
      this.load_terms();
    },
    perPage() {
      this.isOverlay = true;
      this.load_terms();
    },
  },
  computed: {
    totalRows() {
      return this.items.total;
    },
  },
  created() {
    this.load_terms();
  },
  methods: {
    load_terms() {
      let params = {
        ...this.defaultParams(),
        perPage: this.perPage,
        page: this.currentPage,
        filter: this.filter,
        type: this.type,
        article_id: this.item.id,
      };

      get_terms_by_types(params)
        .then((data) => {
          this.items = data;
        })
        .finally(() => {
          this.isLoading = false;
          this.isOverlay = false;
        });
    },
    on_search_submit() {
      this.isOverlay = true;
      this.currentPage = 1;
      this.load_terms();
    },
    on_cancel() {
      this.$emit("on-close");
    },
  },
};
</script>
 