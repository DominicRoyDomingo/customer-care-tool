<style scoped>
.div-scroll {
  position: relative;
  height: 500px;
  overflow-y: scroll;
}
.check-box {
  cursor: pointer !important;
}
</style>
<template>
  <b-modal
    id="show-providers-list"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >
    <v-card class="border-0">
      <v-card-title class="bg-light">
        <div class="row">
            <div class="col-sm-11">
                <div
                class="text-dark text-capitalize d-inline-block text-truncate mt-1 ml-1"
                style="max-width: 700px; letter-spacing: normal"
                >
                List of providers in {{ parent.place }}
                  <p>
                    <small style="font-size:13px"> offering {{ parent.term_name }}</small>
                  </p>
                </div>
            </div>
            <div class="col-sm-1">
                <v-btn icon color="error lighten-2" @click="onCancel">
                <v-icon>mdi-close</v-icon>
                </v-btn>
            </div>
        </div>
      </v-card-title>

      <v-container>
        <div class="row mt-3 pl-3 pr-3">
          <!-- <div class="col-md-6">
            <b-form inline>
              <label class="mr-sm-2" for="inline-form-custom-select-pref">
                {{ $t("button.show") }}
              </label>
              <b-form-select
                id="inline-form-custom-select-pref"
                class="mb-2 mr-sm-2 mb-sm-0"
                :options="parent.showEntriesOpt"
                v-model="perPage"
              />
              <label class="mb-2 mr-sm-2 mb-sm-0">
                {{ $t("label.entries") }}
              </label>
            </b-form>
          </div> -->
          <!-- <div class="col-md-6">
            <b-form>
              <b-input-group class="mb-0 float-right">
                <template #append>
                  <b-button variant="light" type="submit" :disabled="!filter">
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
          </div> -->

          <div class="col-md-12" style="margin-top: -15px">
            <b-card no-body>
              <!-- <template #header>
                <h6 class="mb-0 font-weight-bold text-info">
                  {{ $t("label.name_of_provider") }}
                </h6>
              </template> -->
                <b-overlay
                  id="overlay-background"
                  :show="isOverlay"
                  :variant="'light'"
                  opacity="0.85"
                  :blur="'1px'"
                  rounded="sm"
                >
                  <b-table
                    show-empty
                    class="mb-0"
                    :sort-by.sync="sortBy"
                    :sort-desc.sync="sortDesc"
                    :empty-text="$t('no_record_found')"
                    :fields="tableHeaders"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :busy="tableTermLoading"
                    :items="parent.providers"
                  >
                    <template v-slot:table-busy>
                      <div class="d-flex justify-content-center p-2">
                        <b-spinner label="Small Loading..."></b-spinner>
                      </div>
                    </template>

                    <template v-slot:cell(name)="data">
                      <div class="row mb-0 mt-0">
                        <div class="col-md-12">
                            <span class="font-weight-bold">{{ data.item.name }}</span>
                            <p>
                                <small style="font-size:12px"> {{ data.item.address }}</small>
                            </p>
                        </div>
                      </div>
                    </template>
                  </b-table>
                </b-overlay>
              <b-card-footer v-if="parent.providers.length > 0">
                <b-pagination
                  class="mb-0"
                  v-model="currentPage"
                  :total-rows="parent.providers.length"
                  align="center"
                  size="sm"
                ></b-pagination>
              </b-card-footer>
            </b-card>
          </div>
        </div>
      </v-container>
    </v-card>
  </b-modal>
</template>
<script>
import { mapActions, mapGetters } from "vuex";

export default {
  props: ["parent"],

  data() {
    return {
        filter: "",

        isPaginate: false,
        tableTermLoading: false,
        currentPage: 1,
        perPage: 10,
        totalRow: this.parent.providers.length,
        sortBy: 'name',
        sortDesc: false,
        isOverlay: false,
        tableHeaders: [
            {
              key: "name",
              thClass: "text-left",
              label: this.$t("label.provider_name"),
            },
        ],
    };
  },

  methods: {

    onCancel() {
      this.$bvModal.hide("show-providers-list");
    },

    performSearch: _.debounce(function(query) {
        this.searched = query
    }, 1000),
  },

  watch: {

    filter(query) {
      this.performSearch(query)
    },
  },
};
</script>