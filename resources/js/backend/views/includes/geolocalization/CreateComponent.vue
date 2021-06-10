<template>
  <div class="create">
    <b-modal
      id="geolocalization-add-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
      :title="$t(modal.name)"
    >
      <v-app>
        <v-form ref="form">
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <span>
              {{ $t(modal.name) }}
            </span>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="modalClose"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text class="modal__content">
            <v-container>
              <v-row>
                <v-col cols="12" md="12" class="modal__input-container">
                  <v-tabs
                    show-arrows
                    background-color="#F0F3F5"
                    color="#2F353A"
                    v-model="parent.parent.createModalTab"
                    center-active
                    grow
                  >
                    <v-tab>
                      {{ $t("label.city") }}
                    </v-tab>
                    <v-tab>
                      {{ $t("label.province_2") }}
                    </v-tab>
                    <v-tab>
                      {{ $t("label.region") }}
                    </v-tab>
                      <v-tab-item>
                        <CreateCities :parent="this" @loadTable="parent.loadItems"/>
                      </v-tab-item>
                      <v-tab-item>
                        <CreateProvinces :parent="this" @loadTable="parent.loadItems"/>
                      </v-tab-item>
                      <v-tab-item>
                        <CreateRegions :parent="this" @loadTable="parent.loadItems"/>
                      </v-tab-item>
                        <!-- <CreateProvinces :parent="this" />
                      <v-tab-item>
                        <CreateRegions :parent="this" />
                      </v-tab-item> -->
                  </v-tabs>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
          <!-- <v-divider style="margin-bottom: 0"></v-divider>
            <v-card-actions class="modal__footer blue-grey lighten-5">
              <div v-if="this.modal.id != undefined">
                <v-btn
                  color="error"
                  text
                  tile
                  link
                  @click.stop="parent.informationTypePage"
                >
                  <v-icon>
                    mdi-open-in-new
                  </v-icon>
                  {{ $t("label.go_to_information_type_page") }}
                </v-btn>
              </div>
              <v-spacer></v-spacer>
              <v-btn
                color="error"
                text
                tile
                @click="modalClose"
              >
                {{ $t(modal.button.cancel) }}
              </v-btn>
              <v-btn
                color="success"
                tile
                @click="onSubmit"
              >
                <div v-if="this.modal.button.loading" class="text-center">
                  <v-progress-circular
                    size="20"
                    width="1"
                    color="white"
                    indeterminate
                  >
                  </v-progress-circular>
                </div>
                <div v-else>
                  <div>
                    {{ $t(modal.button.save) }}
                  </div>
                </div>
              </v-btn>
            </v-card-actions>-->
        </v-form> 
      </v-app>
    </b-modal>
  </div>
</template>

<script>

import { mapActions, mapGetters } from "vuex";

import CreateCities from "./cities/CreateComponent.vue";

import CreateProvinces from "./provinces/CreateComponent.vue";

import CreateRegions from "./regions/CreateComponent.vue";

import Form from "./../../../shared/form.js";

export default {
  props: ["parent"],

  name: "InfiniteScroll",

  components: {

    CreateCities,

    CreateProvinces,

    CreateRegions,
  
  },

  data: function() {
    return {
      modal: this.parent.modalGeolocalization.createGeolocalization,

      geolocalizationCity: {
        createGeolocalization: {
          name: "geolocalization_add_new_button",
          button: {
            save: "save_button",
            cancel: "cancel",
            new: "geolocalization_add_new_button",
            loading: false
          }
        },
        edit: {
          name: "geolocalization_add_edit_button",
          button: {
            update: "button.save_change",
            cancel: "cancel",
            new: "geolocalization_add_new_button",
            loading: false
          }
        },
      },

      geolocalizationProvince: {
        createGeolocalization: {
          name: "geolocalization_add_new_button",
          button: {
            save: "save_button",
            cancel: "cancel",
            new: "geolocalization_add_new_button",
            loading: false
          }
        },
        edit: {
          name: "geolocalization_add_edit_button",
          button: {
            update: "button.save_change",
            cancel: "cancel",
            new: "geolocalization_add_new_button",
            loading: false
          }
        },
      },

      geolocalizationRegion: {
        createGeolocalization: {
          name: "geolocalization_add_new_button",
          button: {
            save: "save_button",
            cancel: "cancel",
            new: "geolocalization_add_new_button",
            loading: false
          }
        },
        edit: {
          name: "geolocalization_add_edit_button",
          button: {
            update: "button.save_change",
            cancel: "cancel",
            new: "geolocalization_add_new_button",
            loading: false
          }
        },
      },

      cityForm: new Form({
        
        id: "",
        places: "",
        language: this.$i18n.locale
      
      }),

      provinceForm: new Form({
        
        id: "",
        places: "",
        language: this.$i18n.locale
      
      }),

      regionForm: new Form({
        
        id: "",
        places: "",
        language: this.$i18n.locale
      
      }),
    };
  },
  
  methods: {
    modalClose(done) {

      this.parent.$bvModal.hide("geolocalization-add-modal");
      
		},
  }
};
</script>

<style>
.v-window {
  overflow: inherit !important;
}
</style>
