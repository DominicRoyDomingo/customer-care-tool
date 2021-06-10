<template>
  <b-modal
    id="link-actor-modal"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >
    <v-app id="create__container">
      <!-- <v-card class="border-0">
        <v-card-title class="bg-light">
          <span
            class="text-dark text-capitalize d-inline-block text-truncate"
            style="max-width: 700px; letter-spacing: normal"
          >
            {{ $t('actor_add') }} {{ $t('label.to') }} {{ parent.itemSelected.provider_name }}
          </span>

          <v-spacer></v-spacer>
          <v-btn icon color="error lighten-2" @click="onCancel">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>

        <v-container>
          <div class="row pl-3 pr-3">
            <div class="col-md-6">
              <b-input-group class="mb-0">
                <b-input-group-prepend is-text>
                  <i class="fa fa-search" aria-hidden="true"></i>
                </b-input-group-prepend>
                <b-form-input
                  v-model="parent.filterActor"
                  :placeholder="$t('search_here')"
                />
              </b-input-group>
            </div>
            <div class="col-md-6">
              <v-btn
                color="success"
                @click="parent.createActor"
                class="float-right"
                tile
              >
                <v-icon dark>mdi-hand-heart</v-icon>
                <v-icon small dark style="margin-top: 15px; margin-left: -3px;">
                  mdi-plus
                </v-icon>
                &nbsp;
                {{ $t('actor_add_new_button') }}
              </v-btn>
            </div>

            <div class="col-md-12" style="margin-top: -15px">
              <b-table-simple hover sticky-header responsive>
                <b-thead head-variant="light">
                  <b-tr>
                    <b-th>
                    </b-th>
                  </b-tr>
                </b-thead>
                <b-tbody>
                  <b-tr v-for="actor in parent.actors" :key="actor.id">
                    <b-th
                      class="d-flex justify-content-between align-items-center"
                      :class="{ 'bg-light': onLinkMessage(actor.id) }"
                    >
                      <b-form-checkbox
                        :disabled="actor.is_loading"
                        :title="
                          onLinkMessage(actor.id) ? 'Unlink here.' : 'Link here.'
                        "
                        @change="onChecked(actor)"
                        v-model="parent.linkedActors"
                        :id="`actor-${actor.id}`"
                        :name="`actor-${actor.id}`"
                        :value="actor.id" 
                      >
                        <div class="d-flex flex-row">
                          <div style="width: 280px;">
                            <strong class="text-capitalize" v-html="actor.full_name" />   
                          </div>
                          <div>
                            <span 
                                v-for="(profession,index) in actor.actor_field_of_professions_items" 
                                :key="profession.index" 
                                class="badge badge-info text-capitalize p-1 mr-1 mb-1"
                                style="font-size: 14px"
                              >   
                                {{profession.base_name}}<span v-if="index != actor.actor_field_of_professions_items.length - 1">{{`, `}}</span>
                            </span>
                          </div>
                        </div>
                  
                      
                      </b-form-checkbox>

                      <i
                        class="fa fa-link text-success"
                        aria-hidden="true"
                        v-if="onLinkMessage(actor.id) && !actor.is_loading"
                      />

                      <b-spinner
                        label="Loading..."
                        small
                        v-if="actor.is_loading"
                      />
                    </b-th>
                  </b-tr>
                </b-tbody>
              </b-table-simple>
            </div>
          </div>
        </v-container>
      </v-card> -->
      <v-card class="border-0">
        <v-card-title class="bg-light">
          <span
            class="text-dark text-capitalize d-inline-block text-truncate"
            style="max-width: 700px; letter-spacing: normal"
          >
            {{ $t('actor_add') }} {{ $t('label.to') }} {{ parent.itemSelected.provider_name }}
          </span>
          <v-spacer></v-spacer>
          <v-btn icon color="error lighten-2" @click="on_cancel">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <div class="row mt-3 pl-3 pr-3">
          <div class="col-md-12">
            <v-btn
              color="success"
              @click="parent.createActor"
              class="float-right"
              tile
            >
              <v-icon dark>mdi-hand-heart</v-icon>
              <v-icon small dark style="margin-top: 15px; margin-left: -3px;">
                mdi-plus
              </v-icon>
              &nbsp;
              {{ $t('actor_add_new_button') }}
            </v-btn>
          </div>
        </div>
        <div class="row mt-3 pl-3 pr-3">
          <div class="col-sm-6">
            <b-form inline>
              <label class="mr-sm-2" for="inline-form-custom-select-pref">
                {{ $t("button.show") }}
              </label>
              <b-form-select
                id="inline-form-custom-select-pref"
                class="mb-2 mr-sm-2 mb-sm-0"
                :options="showEntriesOpt"
                v-model="perPage"
              />
              <label class="mb-2 mr-sm-2 mb-sm-0">
                {{ $t("label.entries") }}
              </label>
            </b-form>
          </div>
          <div class="col-sm-6">
            <b-form @submit.prevent="on_search_submit">
              <b-input-group class="mb-0 float-right">
                <b-input-group-prepend v-show="parent.actorFilter">
                  <b-button variant="lignt" @click="parent.actorFilter = ''">
                    <b-icon icon="x" class="text-danger" />
                  </b-button>
                </b-input-group-prepend>

                <template #append>
                  <b-button variant="light" type="submit" :disabled="!parent.actorFilter">
                    <i class="fa fa-search" :class="{ 'text-primary': parent.actorFilter }" />
                  </b-button>
                </template>
                <b-form-input
                  v-model="parent.actorFilter"
                  autocomplete="off"
                  :placeholder="$t('type_and_enter')"
                />
              </b-input-group>
            </b-form>
          </div>
          <div class="col-md-12 mt-0" style="margin-top: -15px !important">
            <b-overlay
              id="overlay-background"
              :variant="'light'"
              opacity="0.85"
              :blur="'1px'"
              rounded="sm"
            >
              <b-card no-body>
                <template #header>
                  <h6 class="mb-0 font-weight-bold text-info">
                    {{ $t("label.actors") }}
                  </h6>
                </template>

                <b-card-body
                  id="nav-scroller"
                  ref="content"
                  class="p-0"
                  :class="{ 'div-scroll': items.length > 7 }"
                >
                  <b-overlay
                    id="overlay-background"
                    :show="parent.actorIsOverlay"
                    :variant="'light'"
                    opacity="0.85"
                    :blur="'1px'"
                    rounded="sm"
                  >
                    <b-table
                      class="mb-0"
                      show-empty
                      ref="actorTable"
                      :sort-by.sync="parent.actorSortBy"
                      :sort-desc.sync="parent.actorSortDesc"
                      :empty-text="$t('no_record_found')"
                      :fields="parent.actorTableHeaders"
                      :per-page="parent.actorPerPage"
                      :busy="parent.actorTableActorLoading"
                      :items="parent.actors.data"
                      :tbody-tr-class="bg_light_class"
                    >
                      <template v-slot:table-busy>
                        <div class="d-flex justify-content-center p-2">
                          <b-spinner label="Small Loading..."></b-spinner>
                        </div>
                      </template>

                      <template v-slot:cell(index)="data">
                        <div class="row mb-0 mt-0">
                          <div class="col-md-4">
                            <span
                              :id="`term-${data.index}`"
                              class="d-inline-block"
                              tabindex="0"
                            >
                              <b-form-checkbox
                                class="check-box"
                                v-model="parent.linkedActors"
                                @change="on_added(data.item, data.index)"
                                :id="`actor-${data.item.id}`"
                                :name="`actor-${data.item.id}`"
                                :value="data.item.id"
                              >
                                <strong
                                  :for="`actor-${data.item.id}`"
                                  class="text-capitalize check-box"
                                  v-html="data.item.full_name"
                                />

                                <b-spinner
                                  label="Loading..."
                                  class="ml-2"
                                  style="position: absolute; margin-top: -2px"
                                  small
                                  v-if="data.item.is_loading"
                                />

                                <span
                                  class="font-weight-bold badge badge-success ml-2 text-uppercase"
                                  style="
                                    position: absolute;
                                    margin-top: -2px;
                                    padding: 4px;
                                  "
                                  v-if="isAdded(data.item.id)"
                                >
                                  {{ $t("added") }}
                                </span>
                              </b-form-checkbox>
                            </span>
  <!-- 
                            <b-tooltip
                              v-if="!data.item.has_term_types"
                              variant="danger"
                              :target="`term-${data.index}`"
                            >
                              <p class="mt-2">
                                <strong> {{ $t("errors.error") }}! </strong>
                                {{ $t("label.linking_to") }}
                                <strong> {{ data.item.base_name }} </strong>
                                {{ $t("errors.linking_error") }}
                              </p>
                            </b-tooltip> -->

                            <!-- <template v-if="on_isLinked(data.item.id)">
                              <TermDescriptionIndex
                                v-if="data.item.is_rendered"
                                :parent_id="
                                  parent_id ? parent_id : this.parent.id
                                "
                                :term="data.item"
                                :descriptions="descriptions"
                                :term_descriptions="data.item.has_descriptions"
                                @link-success="create_term_connection_success"
                                @delete-connection-desc-success="
                                  delete_description_success
                                "
                                @show-modal="show_modal"
                              />
                              <div v-else class="ml-3">
                                <b-spinner
                                  small
                                  label="Small Spinner"
                                  class="text-success"
                                />
                              </div>
                            </template> -->
                          </div>
                          <div class="col-md-8">
                            <span 
                                v-for="(profession,index) in data.item.actor_field_of_professions_items" 
                                :key="profession.index" 
                                class="badge badge-info text-capitalize p-1 mr-1 mb-1"
                                style="font-size: 14px"
                              >   
                                {{profession.base_name}}
                                <!-- <span v-if="index != data.item.actor_field_of_professions_items.length - 1">{{`, `}}</span> -->
                            </span>
                          </div>
                        </div>
                      </template>
                    </b-table>
                  </b-overlay>
                </b-card-body>

                <b-card-footer v-if="parent.actorTotalRow > 0">
                  <b-pagination
                    class="mb-0"
                    v-model="parent.actorCurrentPage"
                    :total-rows="parent.actorTotalRow"
                    :per-page="parent.actorPerPage"
                    align="center"
                    size="sm"
                  />
                </b-card-footer>
              </b-card>
            </b-overlay>
          </div>
        </div>
      </v-card>
    </v-app>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
export default {
  props: ["parent"],

  data() {
    return {
			showEntriesOpt: [
        { value: 10, text: "10" },
        { value: 30, text: "30" },
        { value: 50, text: "50" },
        { value: 100, text: "100" },
      ],

			sortBy: "is_linked_term",
      sortDesc: true,

      isPaginate: false,
      tableActorLoading: true,
      currentPage: 1,
      perPage: 10,
      totalRow: 1,
      filter: null,

      selectedTerm: "",
      term: "",
      isOverlay: false,

      description: "",
      medtermId: [],
      termName: "",
      selected: "",
      withValue: "",
      terms: [],

      tableHeaders: [
        {
          key: "index",
          thClass: "text-left",
          label: this.$t("label.adding_to") + " ?",
          filterByFormatted: true,
        },
      ],
    };
  },

  mounted() {
    this.parent.loadActors()
  },

  methods: {
    ...mapActions("providers", ["post_link_actor", "update_provider"]),
    ...mapActions("actor", ["get_actors"]),
    onCancel() {
      this.parent.form.reset();
      this.$bvModal.hide("link-actor-modal");
    },

    loadActors() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.$i18n.locale,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
        entries: this.parent.actorPerPage,
        page: this.parent.actorCurrentPage,
        search: this.parent.actorFilter,
      };
      this.get_actors(params).then((_) => {
        this.parent.actorTotalRow = this.items.total;
      })
      .catch((error) => {
        console.log(error);
      })
      .finally(() => {
        this.parent.actorTableActorLoading = false;
        this.parent.actorTsOverlay = false;
      });
    },

    isAdded(id) {
      let key = false;
      this.parent.linkedActors.forEach((ele) => {
        if (ele === id) {
          key = true;
        }
      });
      return key;
    },

    on_added(actor, index) {
      let checked = $(`#actor-${actor.id}`).prop("checked");
      // term.is_loading = true;

      if (!checked) {
        this.postLink(actor, checked);

        return false;
      }
      this.postLink(actor, checked);
    },

    postLink(actor, checked) {
      this.linkQuery({ actor: actor, key: checked })
        .then((resp) => {
          
          if (checked) {
            this.parent.makeToast(
              "success",
              this.$t("added"),
							`${actor.full_name} ` +
							this.$t("toasts.added") +
							` ${this.parent.itemSelected.provider_name}`,
            );
          } else {
            this.parent.makeToast(
              "danger",
              this.$t("removed"),
							`${actor.full_name} ` +
							this.$t("toasts.removed") +
							` ${this.parent.itemSelected.provider_name}`,
            );
          }
          let response = resp.data;
          response.index = this.parent.editingIndex;
          this.update_provider(response)
          this.parent.successfullySavedProvider()
        })
        .catch((error) => {
          const index = this.parent.linkedActors.indexOf(actor.id);
          if (index > -1) {
            this.parent.linkedActors.splice(index, 1);
          }
          this.$bvToast.show("link-error-toast");
        })
        .finally(() => {
          this.parent.successfullySavedProvider()
          this.$refs.actorTable.refresh();
        });
    },

    linkQuery(data) {
      let params = {
        id: this.parent.itemSelected.id,
        actor_id: data.actor.id,
        api_token: this.$user.api_token,
        key: data.key ? "link" : "unlink",
      };

      return new Promise((resolve, reject) => {
        axios
          .post("/api/provider/link-actor", params)
          .then((response) => resolve(response))
          .catch((error) => reject(error));
      });
    },

    // onLinkMessage(id) {
    //   let key = false;
    //   this.parent.linkedActors.forEach((ele) => {
    //     if (ele === id) {
    //       key = true;
    //     }
    //   });
    //   return key;
    // },

    // onChecked(item) {
    //   item.is_loading = true;
    //   let checked = $(`#actor-${item.id}`).prop("checked");
    //   let params = {
    //     id: this.parent.itemSelected.id,
    //     actor_id: item.id,
    //     api_token: this.$user.api_token,
    //     key: checked ? "link" : "unlink",
    //   };

    //   this.post_link_actor(params)
    //   .then((resp) => {
    //     let response = this.parent.providersResponseStatus;
    //     response.index = this.parent.editingIndex;
    //     this.update_provider(response)
    //     this.parent.successfullySavedProvider()
    //     item.is_loading = false;
    //   })
    // },

    on_cancel() {
      // this.parent.filter = "";
      this.$bvModal.hide("link-actor-modal");
    },

    on_search_submit() {
      this.parent.actorIsOverlay = true;
      let linkedActors = "";
      if (this.parent.linkedActors.length > 0) {
        linkedActors = JSON.stringify(this.parent.linkedActors);
      }
      this.parent.loadActors()
    },

    bg_light_class(item, type) {
      let key = false;
      if (item) {
        this.parent.linkedActors.forEach((id) => {
          if (id === item.id) {
            key = true;
          }
        });
      }
      return key ? "bg-light" : "";
    },
  },

  computed: {
    ...mapGetters("actor", {
      items: "actors",
      providersResponseStatus: "get_response_status",
    }),

    totalRows() {
      return this.items.length;
    },
  },

  watch: {

		filter(value) {
      if (!value) {
        this.tableTermLoading = true;
        this.loadActors().catch((error) => {
          console.log(error);
        });
      }
    },

    perPage: {
      handler: function(value) {
        this.loadActors()
      }
    },

    currentPage: {
      handler: function(value) {
        this.loadActors()
      }
    },
  },
};
</script>

<style>
</style>