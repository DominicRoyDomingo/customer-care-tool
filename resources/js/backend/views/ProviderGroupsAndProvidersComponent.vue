<template>
  <div class="animated fadeIn" style="height: 100%; padding-bottom: 1.5em; padding-top: 1.5em;">
      <v-container class="grey lighten-5 provider-services-container" style="height: 100%;">
        <v-row no-gutters justify="start" v-if="isProviderGroupsLoaded" style="height: 100%;">
            <v-col
                cols="12"
                sm="3"
                md="3"
            >
            <v-card
              class="d-flex flex-row justify-content-center"
              elevation="0"
              color="#f9f9f9"
              style="height: 100%"
            >
              <v-card 
                color="#f9f9f9"
                elevation="0"
                width="288"
                class="text-center px-4 d-flex flex-column"
              >
                  <v-img
                    v-if="provider_group.image != null"
                    max-width="275"
                    max-height="260"
                    :src="provider_group.image"
                  ></v-img>
                  <v-img
                    v-else
                    max-width="275"
                    max-height="260"
                    src="https://dummyimage.com/1024x480/fff/aaa&text=No+image"
                  ></v-img>
                  <v-card-title 
                  class="mt-5 justify-center"
                  >{{provider_group.name}}</v-card-title>
                  <div 
                    class="d-flex justify-space-between mt-auto mb-3"
                  >
                    <v-btn 
                      color="#56BFFF" 
                      :dark="provider_group.previous == null ? false : true"
                      @click="previousProviderGroup()"
                      :disabled="provider_group.previous == null ? true : false"
                    >
                      <v-icon
                        left
                        dark
                      >
                        mdi-chevron-left
                      </v-icon>
                      BACK
                    </v-btn>
                    <v-btn 
                      color="#56BFFF" 
                      :dark="provider_group.next == null ? false : true"
                      @click="nextProviderGroup()"
                      :disabled="provider_group.next == null ? true : false"
                    >
                      NEXT
                      <v-icon
                        right
                        dark
                      >
                        mdi-chevron-right
                      </v-icon>
                    </v-btn>
                  </div>
                  <div>
                    <v-btn 
                      color="#E0E0E0"
                      block
                      @click="backToProviderList"
                    >
                    BACK TO LIST
                    </v-btn>
                  </div>
              </v-card>
            </v-card>
          </v-col>

            <v-col
                cols="12"
                sm="9"
                md="9"
            >
                <v-card 
                    elevation="0"
                    class="h-100"
                >
                    <div class="d-flex flex-column h-100">
                        <v-card-title 
                            class="justify-center"
                            style="background-color: #56BFFF"
                        >
                            <v-btn
                                outlined
                                rounded
                                x-small
                                disabled
                                color="#fff"
                                style="padding: 0px 40px; color: #fff !important"
                            >
                            <span class="font-italic">{{ provider_group.name}}</span>
                            </v-btn>
                        </v-card-title>
                        <div class="d-flex flex-column flex-grow-1" style="background-color: #F2F2F2;">
                            <v-row class="flex-grow-1 mx-0 px-10 py-15">
                                <v-col md="3" 
                                        v-for="provider in paginate(provider_group.providers)"
                                        :key="provider.id">
                                    <v-card
                                        elevation="0"
                                        width="241.78"
                                        style="margin: auto;"
                                        rounded
                                    >
                                        <v-carousel
                                            v-if="provider.images != null"
                                            cycle
                                            height="120.89"
                                        >
                                            <v-carousel-item
                                            v-for="(image,i) in provider.images"
                                            :key="i"
                                            :src="image"
                                            ></v-carousel-item>
                                        </v-carousel>
                                        <v-img
                                            v-else
                                            height="120.89"
                                            width="241.78"
                                            src="https://dummyimage.com/1024x480/fff/aaa&text=No+image"
                                        ></v-img>
                                        <v-card-text style="color: #000">
                                            <div class="d-flex flex-row">         
                                            <v-icon class="align-start" color="#000">mdi-map-marker</v-icon>
                                            <div>
                                                <p class="font-weight-bold">
                                                {{provider.address == null ? 'No Address' : provider.address}}
                                                </p>
                                            </div>
                                            </div>

                                            <div class="d-flex flex-row">         
                                            <v-icon class="align-start" color="#000">mdi-phone</v-icon>
                                            <div>
                                                <p class="font-weight-bold">
                                                {{(provider.contact_no.length == 0 ? 'No Contact Number' : provider.contact_no[0])}}
                                                </p>
                                            </div>
                                            </div>
                                        </v-card-text>
                                    </v-card>
                                </v-col>
                            </v-row>
                            <v-row class="flex-grow-0 flex-shrink-1" v-if="totalPages != 0">
                                <v-col>
                                  <div class="provider_group_table">
                                    <ul>
                                      <li v-if="totalPages > 1" @click="setPage(currentPage)">
                                        <v-icon>
                                          mdi-chevron-left
                                        </v-icon>
                                      </li>
                                      <template 
                                        v-for="pageNumber in totalPages"
                                      >
                                        <li 
                                          :key="pageNumber"
                                        >
                                          <a href="#" @click="setPage(pageNumber)" >{{ pageNumber }}</a>
                                        </li>
                                      </template>
                                      <li v-if="totalPages > 1" @click="setPage(currentPage+2)">
                                        <v-icon>
                                          mdi-chevron-right
                                        </v-icon>
                                      </li>
                                    </ul>
                                  </div>
                                </v-col>
                            </v-row>
                        </div>
                    </div>
                    
                </v-card>
            </v-col>
        </v-row>
      </v-container>

  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import toast from "./../helpers/toast.js";

import Loading from "vue-loading-overlay";

export default {
  mixins: [toast],

  components: {
    Loading,
  },

  data: function() {
    return {
      isLoading: false,
      isProviderGroupsLoaded: false,
      currentPage: 0,
      itemsPerPage: 8,
      resultCount: 0
    };
  },

  mounted() {
    // console.log(this.$brand.id)
    this.loadProviderGroup()
    
  },

  methods: {

    ...mapActions("provider_group", ["get_provider_group"]),

    setPage: function(pageNumber) {
      if(pageNumber <= 0 || pageNumber > this.totalPages) return;
      this.currentPage = pageNumber-1
    },

    loadProviderGroup() {
        let url = window.location.search.substring(1)
        let id = url.split("=");
        let params = {
            api_token: this.$user.api_token,
            lang: this.$i18n.locale,
            id: id[1],
            brand_id: this.$brand.id,
        };
        this.get_provider_group(params).then((_) => {
            this.isProviderGroupsLoaded = true;
            console.log(this.provider_group)
        });
    },

    getProviderId() {
      let url = window.location.search.substring(1)
      let id = url.split("=");
      this.loadProvider(id[1]);
      this.loadProviderServiceItems()
    },
    loadProvider(id) {
      this.firstProviderId = id;
      let formData = new FormData();
      formData.append("api_token", this.$user.api_token);
      formData.append("id", this.firstProviderId);
      formData.append("brand_id", this.$brand.id);
      formData.append("lang", this.form.language);
      this.get_provider(formData).then((resp) => {
        this.isProviderLoaded = true;
        this.profileViewed.id = this.provider.provider_profiles.profile;
      });
    },

    previousProviderGroup() {
      window.location.href ="/admin/article-content-maker/provider-groups?id=" + this.provider_group.previous
    },
    
    nextProviderGroup() {
      window.location.href ="/admin/article-content-maker/provider-groups?id=" + this.provider_group.next
    },

    backToProviderList() {
      window.location.href ="/admin/article-content-maker/providers"
    },

    paginate(list) {
      console.log(list);
      this.resultCount = list.length
      if (this.currentPage >= this.totalPages) {
        this.currentPage = this.totalPages - 1
      }
      var index = this.currentPage * this.itemsPerPage
      return list.slice(index, index + this.itemsPerPage)
    }

  },

  computed: {

    ...mapGetters("provider_group", {
      provider_group: "provider_group",
      providerGroupResponseStatus: "get_response_status",
    }),

    totalPages: function() {
      console.log(Math.ceil(this.resultCount / this.itemsPerPage))
      return Math.ceil(this.resultCount / this.itemsPerPage)
    },

    providerContactNo() {
      if(this.provider.length != 0) {
        return this.provider.contact_no.length
      }
    },

    totalRows() {
      return this.items.length;
    },

    totalServiceProviderRows() {
      return this.provider_services.length;
    },
  },

  filters: {
    
  }
};
</script>

<style lang="scss">
</style>
