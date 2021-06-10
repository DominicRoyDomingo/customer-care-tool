<template>
  <div style="height: calc(100VH - 150px); background-color: rgb(249, 249, 249);">
    <div class="h-100" style="padding: 12px">
      <v-row class="h-100">
        <v-col
          cols="12"
          md="8"
        >
          <v-card elevation="0" class="h-100">
            <div class="d-flex flex-column" style="height: 100%">
              <v-card-title style="background-color: #56bfff">
                <v-row>
                  <v-col md="12" class="px-15">
                    <div class="d-flex flex-row">
                      <v-carousel
                        v-if="provider.images != null"
                        cycle
                        height="150"
                        style="width: 200px"
                      >
                        <v-carousel-item
                          v-for="(image,i) in provider.images"
                          :key="i"
                        >
                          <img :src="image" style="width:200px ;height:150px;" />
                        </v-carousel-item>
                      </v-carousel>
                      <v-avatar
                        v-else
                        color="primary"
                        size="150"
                        rounded
                      >
                        <span class="white--text h1">{{ provider.provider_profiles.provider_profile.full_name.charAt(0).toUpperCase() }}</span>
                      </v-avatar>
                      <!-- <v-img
                        v-else
                        max-width="275"
                        height="8vw"
                        max-height="260"
                        src="https://dummyimage.com/1024x480/fff/aaa&text=No+image"
                      ></v-img> -->
                      
                      <div class="d-flex flex-column">
                          <v-list-item>
                            <div>
                              <v-list-item-title class="white--text">
                                {{provider.provider_profiles.provider_profile.full_name}}
                              </v-list-item-title>
                              <v-list-item-subtitle class="white--text">{{provider.provider_profiles.provider_profile.nickname}}</v-list-item-subtitle>
                            </div>
                          </v-list-item>
                          <v-list-item>
                            <div>
                              <v-list-item-subtitle class="white--text">
                                {{provider.address}}
                              </v-list-item-subtitle>
                            </div>
                          </v-list-item>
                          <v-list-item>
                            <div>
                              <v-list-item-title class="white--text"><a :href="provider.website">{{ $t("brands_website")}}</a></v-list-item-title>
                              <v-list-item-subtitle class="white--text">{{(provider.contact_nos.length == 0 ? 'No Contact Number' : provider.contact_nos[0])}}</v-list-item-subtitle>
                            </div>
                          </v-list-item>
                      </div>
                    </div>
                  </v-col>
                </v-row>
              </v-card-title>
              <div style="background-color: #f2f2f2">
                <div style="padding: 8px 16px 0 16px;" class="float-right">
                  <div class="d-flex flex-row" style="padding: 0 16px;">
                    <v-tooltip 
                      right
                      z-index="1000000000000000000000000000"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          class="mx-2 mb-2"
                          fab
                          dark
                          small
                          depressed
                          :color="isFirst == true ? '#56bfff': '#4f4f4f'"
                          @click="onFirst()"
                          v-bind="attrs"
                          v-on="on"
                        >
                          <v-icon dark>
                            mdi-account-circle-outline
                          </v-icon>
                        </v-btn>
                      </template>
                      <span>Profile</span>
                    </v-tooltip>  
                    <v-tooltip 
                      right
                      z-index="1000000000000000000000000000"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          class="mx-2 mb-2"
                          fab
                          dark
                          small
                          depressed
                          :color="isSecond == true ? '#56bfff': '#4f4f4f'"
                          @click="onSecond()"
                          v-bind="attrs"
                          v-on="on"
                        >
                          <v-icon dark>
                            mdi-medical-bag
                          </v-icon>
                        </v-btn>
                      </template>
                      <span>Services Table</span>
                    </v-tooltip> 
                    <v-tooltip 
                      right
                      z-index="1000000000000000000000000000"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          class="mx-2 mb-2"
                          fab
                          dark
                          small
                          depressed
                          :color="isThird == true ? '#56bfff': '#4f4f4f'"
                          @click="onThird()"
                          v-bind="attrs"
                          v-on="on"
                        >
                          <v-icon dark>
                            mdi-view-list-outline
                          </v-icon>
                        </v-btn>
                      </template>
                      <span>Services</span>
                    </v-tooltip> 
                  </div>
                </div>
              </div>
              <v-card-text class="flex-grow-1" style="background-color: #f2f2f2" v-if="isFirst">
                <v-row>
                  <v-col md="12" class="px-8">
                    <v-row>
                      <v-col md="12">
                        <v-card
                          elevation="0"
                        >
                          <v-card-text class="d-flex flex-row justify-space-between" style="color: black;">
                            <div class="d-flex flex-column">     
                              <div class="d-flex flex-row">
                                <v-icon class="align-start mr-6" color="black">mdi-map-marker</v-icon>
                                <div>
                                  <span class="font-weight-medium" v-if="provider.address != null">
                                    {{provider.address}}
                                  </span>
                                  <span class="font-weight-medium" v-else>
                                    No Address
                                  </span>
                                </div>
                              </div> 

                              <div class="d-flex flex-row">         
                                <v-icon class="align-start mr-6" color="black">mdi-phone</v-icon>
                                <div>
                                  <span class="font-weight-medium" v-if="provider.contact_nos.length != 0">
                                    <span v-for="(contact,index) in provider.contact_nos" :key="index">
                                      {{contact}}<br>
                                    </span>
                                  </span>

                                  <span class="font-weight-medium" v-else>
                                    No Contact Number
                                  </span>
                                </div>
                              </div>  

                              <div class="d-flex flex-row">
                                <v-icon class="align-start mr-6" color="black">mdi-open-in-new</v-icon>
                                <div>
                                  <a :href="provider.website" target="_blank" class="font-weight-medium" v-if="provider.website != null">
                                    {{ $t("brands_website")}}
                                  </a>
                                  <span class="font-weight-medium" v-else>
                                    {{ $t("label.no_website")}}
                                  </span>
                                </div>
                              </div>

                              <div class="d-flex flex-row">
                                <v-icon class="align-start mr-6" color="black">mdi-facebook</v-icon>
                                <div>
                                  <a :href="provider.facebook_url" target="_blank" class="font-weight-medium" v-if="provider.facebook_url != null">
                                    {{ $t("label.facebook_page") }}
                                  </a>
                                  <span class="font-weight-medium" v-else>
                                    {{ $t("label.no_facebook_page") }}
                                  </span>
                                </div>
                              </div> 
                              <div class="d-flex flex-row">
                                <v-icon class="align-start mr-6" color="black">mdi-linkedin</v-icon>
                                <div>
                                  <a :href="provider.linkedin" target="_blank" class="font-weight-medium" v-if="provider.linkedin != null">
                                    {{ $t("label.linkedin_page") }}
                                  </a>
                                  <span class="font-weight-medium" v-else>
                                    {{ $t("label.no_linkedin_page") }}
                                  </span>
                                </div>
                              </div> 
                            </div>
                          </v-card-text>
                        </v-card>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
              </v-card-text>
              <v-card-text class="d-flex flex-grow-1" style="background-color: #f2f2f2;" v-if="isSecond">
                <v-col
                  cols="12"
                  sm="12"
                  md="12"
                  class="d-flex flex-column"
                >
                  <div 
                    class="flex-grow-1 provider-table-scroll" 
                    v-scrollbar="{
                            useBothWheelAxes: true
                    }"
                    style="overflow:auto; min-height: 0; height: 20vh;"
                    @scroll="handleScroll"
                  >
                    <b-table
                      :bordered="true"
                      head-variant="light"
                      show-empty
                      stacked="md"
                      ref="tableService"
                      :fields="tableServicesHeaders"
                      :current-page="currentPageServices"
                      :per-page="0"
                      :filter="filterServices"
                      :busy="isLoadingServices"
                      :items="provider_services.data"
                    >
                      <template v-slot:table-busy>
                        <v-fade-transition>
                          <v-overlay opacity="0.8" style="z-index: 999999 !important">
                            <v-progress-circular
                              indeterminate
                              size="80"
                              width="2"
                              color="white"
                              class="text-center"
                            ></v-progress-circular>
                          </v-overlay>
                        </v-fade-transition>
                      </template>

                      <template v-slot:cell(name)="data">
                        <div>
                          <span
                            class="text-subtitle-1 text--secondary"
                          >
                            <a href="#" @click.prevent="loadDescription(data.item.description)">{{ data.item.service_item.name }}</a>
                            <small
                              v-if="data.item.convertion == true"
                              style="color:red"
                            >
                              (No {{ data.item.language }} translation yet)</small
                            >
                          </span>
                        </div>
                      </template>

                      <template v-slot:cell(day_start)="data">
                        <div style="margin-top: 10px">
                          <span class="text--disabled">
                            {{ data.item.day_start}}
                          </span>
                        </div>
                      </template>

                      <template v-slot:cell(day_end)="data">
                        <div style="margin-top: 10px">
                          <span class="text--disabled">
                            {{ data.item.day_end != "0000-00-00" ? data.item.day_end: ""}}
                          </span>
                        </div>
                      </template>
                      
                      <template v-slot:cell(parameter)="data">
                        <div style="margin-top: 10px">
                          <span class="text--disabled">
                            {{ data.item.parameter_item != null ? data.item.parameter_item.name : "None"}}
                          </span>
                        </div>
                      </template>

                      <template v-slot:cell(actions)="data">
                        <div style="margin-top: 10px">
                          <v-menu offset-y>
                            <template v-slot:activator="{ on, attrs }">
                              <v-btn
                                color="#56bfff"
                                dark
                                v-bind="attrs"
                                v-on="on"
                                tile
                                depressed
                                small
                              >
                                {{ $t("button.more_actions") }}
                                <v-icon small right>
                                  mdi-chevron-down
                                </v-icon>
                              </v-btn>
                            </template>
                            <v-list dense class="profile__row-menu">
                              <v-list-item-group color="primary">
                                <v-list-item @click="onEditProviderService(data.item, data.index)">
                                  <v-list-item-icon>
                                    <v-icon color="yellow darken-3">
                                      mdi-map-marker-check
                                    </v-icon>
                                  </v-list-item-icon>
                                  <v-list-item-content>
                                    <v-list-item-title>
                                      {{ $t("button.edit") }}
                                    </v-list-item-title>
                                  </v-list-item-content>
                                </v-list-item>
                                <v-list-item @click="onDeleteProviderService(data.item, data.index)">
                                  <v-list-item-icon>
                                    <v-icon color="red lighten-1">
                                      mdi-map-marker-remove
                                    </v-icon>
                                  </v-list-item-icon>
                                  <v-list-item-content>
                                    <v-list-item-title>
                                      {{ $t("button.delete") }}
                                    </v-list-item-title>
                                  </v-list-item-content>
                                </v-list-item>
                              </v-list-item-group>
                            </v-list>
                          </v-menu>
                        </div>
                      </template>
                    </b-table>
                  </div>
                </v-col>
              </v-card-text>
              <v-card-text class="d-flex flex-grow-1 flex-column" style="background-color: #f2f2f2;" v-if="isThird">
                <div 
                  class="flex-grow-1" 
                  v-scrollbar="{
                          useBothWheelAxes: true
                  }"
                  style="overflow:auto; min-height: 0; height: 20vh;"
                >
                    <v-col md="12" class="px-5">
                      <v-row>
                        <v-col md="4" v-for="providerService in allProviderServices" :key="providerService.id">
                          <v-card
                            elevation="0"
                            class="text-center"
                          >
                            
                            <v-card-title 
                            class="font-weight-bold justify-center"
                            >
                              <v-avatar
                                color="#56CCF2"
                                size="30"
                                rounded
                                
                              >
                                <span class="white--text headline">{{ providerService.service_item.name.charAt(0).toUpperCase() }}</span>
                              </v-avatar>
                            </v-card-title>
                            <v-card-subtitle>
                              <span class="text-caption font-weight-bold black--text">{{providerService.service_item.name}}</span>
                            </v-card-subtitle>
                            <v-card-text>
                              <span class="text-caption black--text">{{providerService.description == null ? 'No Description' : providerService.description}}</span>
                            </v-card-text>
                          </v-card>
                        </v-col>
                      </v-row>
                    </v-col>
                </div>
              </v-card-text>
            </div>
          </v-card>
        </v-col>
        <v-col
          cols="12"
          sm="12"
          md="4"
        >
          <v-card class="h-100" elevation="0">
            <div class="d-flex flex-column" style="height: 100%">
            <v-card-title style="background-color: #56bfff" class="justify-end">
              <span class="white--text">NOTES</span>
            </v-card-title>
            <v-card-text class="d-flex flex-column flex-grow-1 flex-shrink-0">
              <div 
                class="flex-grow-1 provider-table-scroll" 
                v-scrollbar="{
                        useBothWheelAxes: true
                }"
                style="overflow:auto; min-height: 0; height: 20vh;"
              >
                <v-col md="12" v-for="note in provider.provider_profiles.provider_profile.notes" :key="note.index">
                      <v-card
                        elevation="0"
                      >
                        <v-card-text style="background-color: #f2f2f2;">
                          <div class="d-flex flex-row pr-10">
                            <v-checkbox class="align-start ma-0 pa-0"></v-checkbox>
                            <div class="w-100">
                              <p>{{note.notes}}</p>
                              <div class="d-flex flex-row">
                                <v-avatar
                                  color="primary"
                                  size="25"
                                ></v-avatar>
                                <div class="text-caption" style="line-height: 0.8rem !important;">
                                  {{note.added_by}}<br>
                                  {{note.date_requested}}<br>
                                </div>
                                <v-btn
                                  icon
                                  color="#cfcfcf"
                                  x-small
                                  class="ml-auto p-2"
                                  @click="modalEditNote(note)"
                                >
                                  <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                              </div>
                            </div>
                          </div>
                        </v-card-text>
                      </v-card>
                </v-col>
              </div>
            </v-card-text>
            <v-card-actions style="background-color: #d7d7d7;" class="justify-end flex-grow-0 flex-shrink-0">
              <v-btn
                class="mx-2"
                fab
                dark
                color="#2f80ed"
                @click="modalAddNotes(provider.provider_profiles.provider_profile)"
              >
                <v-icon dark>
                  mdi-plus
                </v-icon>
              </v-btn>
            </v-card-actions>
            <v-row dense class="mb-1 mt-1 flex-grow-0 flex-shrink-0" justify="end"> 
              <v-col sm="6" md="6" lg="6">
                <v-btn 
                  color="#56BFFF" 
                  :dark="provider.previous == null ? false : true"
                  @click="previousProvider()"
                  :disabled="provider.previous == null ? true : false"
                  block
                >
                  <v-icon
                    left
                    dark
                  >
                    mdi-chevron-left
                  </v-icon>
                  BACK
                </v-btn>
              </v-col>
              <v-col sm="6" md="6" lg="6">
                <v-btn 
                  color="#56BFFF" 
                  :dark="provider.next == null ? false : true"
                  block
                  @click="nextProvider()"
                  :disabled="provider.next == null ? true : false"
                >
                  NEXT
                  <v-icon
                    right
                    dark
                  >
                    mdi-chevron-right
                  </v-icon>
                </v-btn>
              </v-col>
            </v-row>
            <div>
              <v-btn 
                color="#E0E0E0"
                block
                @click="backToProviderList"
              >
              BACK TO LIST
              </v-btn>
            </div>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </div>
    <EditProviderService :$this="this"></EditProviderService>
    <Service :$this="this" @loadTable="loadServicesAndParameters"></Service>
    <Parameter :$this="this" @loadTable="loadServicesAndParameters"></Parameter>
    <AddNotesModal :parent="this"></AddNotesModal>
    <EditNoteModal :parent="this"></EditNoteModal>
    <CreateService :parent="this" @done-success="loadServicesAndParameters" />
  </div>
  <!-- <div
    v-if="isProviderExists"
    class="mx-auto"
    v-scrollbar
    style="height: calc(100VH - 150px); max-width:1641px; background-color: rgb(249, 249, 249);"
  >

      <div class="grey lighten-5 provider-services-container" style="height: 100%; width:1641px">
        <v-row class="pl-5" justify="start" v-if="isProviderLoaded" style="height: 100%; width:1641px">
          <div class="col-1st-provider-services my-10">
            <v-card
              class="d-flex flex-row justify-content-center"
              elevation="0"
              color="#f9f9f9"
              style="height: 100%"
            >
              <v-card 
                color="#f9f9f9"
                elevation="0"
                class="text-center px-4 d-flex flex-column"
                style="min-width: 300px;"
              >
                <v-row>
                  <div class="d-flex flex-column" style="width: 295px; padding: 0 12px 0 12px;">
                    <v-carousel
                      v-if="provider.images != null"
                      cycle
                      height="250"
                    >
                      <v-carousel-item
                        v-for="(image,i) in provider.images"
                        :key="i"
                        :src="image"
                      ></v-carousel-item>
                    </v-carousel>
                    <v-img
                      v-else
                      max-width="275"
                      height="8vw"
                      max-height="260"
                      src="https://dummyimage.com/1024x480/fff/aaa&text=No+image"
                    ></v-img>
                    <v-card-title 
                    class="mt-5 justify-center"
                    >{{provider.name}}</v-card-title>
                    <v-card-subtitle class="subtitle-2 font-weight-bold black--text">
                      <a :href="provider.website">{{provider.website}}</a>
                    </v-card-subtitle>
                    <v-row no-gutters>
                      <v-col md="2">
                        <v-btn color="#fff" fab icon small >
                          <v-avatar
                              size="35"
                          >
                              <img
                                  src="https://med4care-customercare.s3.us-east-2.amazonaws.com/button-icons/facebook-logo.png"
                                >
                          </v-avatar>
                        </v-btn>
                      </v-col>
                      <v-col md="2">
                        <v-btn color="#fff" fab icon small>
                          <v-avatar
                              size="35"
                          >
                              <img
                                  src="https://med4care-customercare.s3.us-east-2.amazonaws.com/button-icons/linkedin-logo.png"
                                >
                          </v-avatar>
                        </v-btn>
                      </v-col>
                      <v-col md="2">
                        <v-btn color="#fff" fab icon small>
                          <v-avatar
                              size="35"
                          >
                              <img
                                  src="https://med4care-customercare.s3.us-east-2.amazonaws.com/button-icons/instagram-logo.png"
                                >
                          </v-avatar>
                        </v-btn>
                      </v-col>
                      <v-col md="2">
                        <v-btn color="#fff" fab icon small>
                          <v-avatar
                              size="35"
                          >
                              <img
                                  src="https://med4care-customercare.s3.us-east-2.amazonaws.com/button-icons/rss-icon.png"
                                >
                          </v-avatar>
                        </v-btn>
                      </v-col>
                      <v-col md="2">
                        <v-btn color="#fff" fab icon small>
                          <v-avatar
                              size="35"
                          >
                              <img
                                  src="https://med4care-customercare.s3.us-east-2.amazonaws.com/button-icons/web-icon.png"
                                >
                          </v-avatar>
                        </v-btn>
                      </v-col>
                      <v-col md="2">
                        <v-btn color="#fff" fab icon small>
                          <v-avatar
                              size="35"
                          >
                              <img
                                  src="https://med4care-customercare.s3.us-east-2.amazonaws.com/button-icons/shopping-bag-icon.png"
                                >
                          </v-avatar>
                        </v-btn>
                      </v-col>
                    </v-row>
                    <v-row dense class="mb-1" justify="end" align="end"> 
                      <v-col sm="6" md="6" lg="6">
                        <v-btn 
                          color="#56BFFF" 
                          :dark="provider.previous == null ? false : true"
                          @click="previousProvider()"
                          :disabled="provider.previous == null ? true : false"
                          block
                        >
                          <v-icon
                            left
                            dark
                          >
                            mdi-chevron-left
                          </v-icon>
                          BACK
                        </v-btn>
                      </v-col>
                      <v-col sm="6" md="6" lg="6">
                        <v-btn 
                          color="#56BFFF" 
                          :dark="provider.next == null ? false : true"
                          block
                          @click="nextProvider()"
                          :disabled="provider.next == null ? true : false"
                        >
                          NEXT
                          <v-icon
                            right
                            dark
                          >
                            mdi-chevron-right
                          </v-icon>
                        </v-btn>
                      </v-col>
                    </v-row>
                    <div>
                      <v-btn 
                        color="#E0E0E0"
                        block
                        @click="backToProviderList"
                      >
                      BACK TO LIST
                      </v-btn>
                    </div>
                  </div>
                  <div style="position: absolute; top: 0; right: -65px;">
                    <div class="d-flex flex-column">
                      <v-tooltip 
                        right
                        z-index="1000000000000000000000000000"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            class="mx-2 mb-2"
                            fab
                            dark
                            small
                            depressed
                            :color="isFirst == true ? '#56bfff': '#4f4f4f'"
                            @click="onFirst()"
                            v-bind="attrs"
                            v-on="on"
                          >
                            <v-icon dark>
                              mdi-account-circle-outline
                            </v-icon>
                          </v-btn>
                        </template>
                        <span>Profile</span>
                      </v-tooltip>  
                      <v-tooltip 
                        right
                        z-index="1000000000000000000000000000"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            class="mx-2 mb-2"
                            fab
                            dark
                            small
                            depressed
                            :color="isSecond == true ? '#56bfff': '#4f4f4f'"
                            @click="onSecond()"
                            v-bind="attrs"
                            v-on="on"
                          >
                            <v-icon dark>
                              mdi-medical-bag
                            </v-icon>
                          </v-btn>
                        </template>
                        <span>Services Table</span>
                      </v-tooltip> 
                      <v-tooltip 
                        right
                        z-index="1000000000000000000000000000"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            class="mx-2 mb-2"
                            fab
                            dark
                            small
                            depressed
                            :color="isThird == true ? '#56bfff': '#4f4f4f'"
                            @click="onThird()"
                            v-bind="attrs"
                            v-on="on"
                          >
                            <v-icon dark>
                              mdi-view-list-outline
                            </v-icon>
                          </v-btn>
                        </template>
                        <span>Services</span>
                      </v-tooltip> 
                    </div>
                  </div>
                </v-row>
                  
              </v-card>
            </v-card>
          </div>

          <div class="col-2nd-provider-services my-10">
            <v-card elevation="0" class="h-100">
              <div class="d-flex flex-column" style="height: 100%">
              <v-card-title style="background-color: #56bfff">
                <v-row>
                  <v-col md="12" class="px-15">
                    <div class="d-flex flex-row">
                      <v-avatar
                        color="primary"
                        size="150"
                        rounded
                      >
                        <span class="white--text h1">{{ provider.provider_profiles.provider_profile.full_name.charAt(0).toUpperCase() }}</span>
                      </v-avatar>
                      <div class="d-flex flex-column">
                          <v-list-item>
                            <div>
                              <v-list-item-title class="white--text">
                                {{provider.provider_profiles.provider_profile.full_name}}
                              </v-list-item-title>
                              <v-list-item-subtitle class="white--text">{{provider.provider_profiles.provider_profile.nickname}}</v-list-item-subtitle>
                            </div>
                          </v-list-item>
                          <v-list-item>
                            <div>
                              <v-list-item-subtitle class="white--text">
                                {{provider.address}}
                              </v-list-item-subtitle>
                            </div>
                          </v-list-item>
                          <v-list-item>
                            <div>
                              <v-list-item-title class="white--text"><a :href="provider.website">{{provider.website}}</a></v-list-item-title>
                              <v-list-item-subtitle class="white--text">{{(provider.contact_nos.length == 0 ? 'No Contact Number' : provider.contact_nos[0])}}</v-list-item-subtitle>
                            </div>
                          </v-list-item>
                      </div>
                    </div>
                  </v-col>
                </v-row>
              </v-card-title>
              <v-card-text class="flex-grow-1" style="background-color: #f2f2f2" v-if="isFirst">
                <v-row>
                  <v-col md="12" class="px-15">
                    <v-row>
                      <v-col md="12">
                        <v-card
                          elevation="0"
                        >
                          <v-card-text class="d-flex flex-row justify-space-between" style="color: black;">
                            <div class="d-flex flex-row">         
                              <v-icon class="align-start mr-6" color="black">mdi-map-marker</v-icon>
                              <div>
                                <p class="font-weight-medium" v-if="provider.address != null">
                                  {{provider.address}}
                                </p>
                                <p class="font-weight-medium" v-else>
                                  No Address
                                </p>
                              </div>
                            </div>

                            <div class="d-flex flex-row">         
                              <v-icon class="align-start mr-6" color="black">mdi-phone</v-icon>
                              <div>
                                <p class="font-weight-medium" v-if="provider.contact_nos.length != 0">
                                  <span v-for="(contact,index) in provider.contact_nos" :key="index">
                                    {{contact}}<br>
                                  </span>
                                </p>

                                <p class="font-weight-medium" v-else>
                                  No Contact Number
                                </p>
                              </div>
                            </div>
                          </v-card-text>
                        </v-card>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
              </v-card-text>
              <v-card-text class="d-flex flex-grow-1 flex-column" style="background-color: #f2f2f2;" v-if="isSecond">
                <div 
                  class="flex-grow-1 provider-table-scroll" 
                  v-scrollbar="{
                          useBothWheelAxes: true
                  }"
                  style="overflow:auto; min-height: 0; height: 20vh;"
                  @scroll="handleScroll"
                >
                  <b-table
                    :bordered="true"
                    head-variant="light"
                    show-empty
                    stacked="md"
                    ref="tableService"
                    :fields="tableServicesHeaders"
                    :current-page="currentPageServices"
                    :per-page="0"
                    :filter="filterServices"
                    :busy="isLoadingServices"
                    :items="provider_services.data"
                  >
                    <template v-slot:table-busy>
                      <v-fade-transition>
                        <v-overlay opacity="0.8" style="z-index: 999999 !important">
                          <v-progress-circular
                            indeterminate
                            size="80"
                            width="2"
                            color="white"
                            class="text-center"
                          ></v-progress-circular>
                        </v-overlay>
                      </v-fade-transition>
                    </template>

                    <template v-slot:cell(name)="data">
                      <div>
                        <span
                          class="text-subtitle-1 text--secondary"
                        >
                          <a href="#" @click.prevent="loadDescription(data.item.description)">{{ data.item.service_item.name }}</a>
                          <small
                            v-if="data.item.convertion == true"
                            style="color:red"
                          >
                            (No {{ data.item.language }} translation yet)</small
                          >
                        </span>
                      </div>
                    </template>

                    <template v-slot:cell(day_start)="data">
                      <div style="margin-top: 10px">
                        <span class="text--disabled">
                          {{ data.item.day_start}}
                        </span>
                      </div>
                    </template>

                    <template v-slot:cell(day_end)="data">
                      <div style="margin-top: 10px">
                        <span class="text--disabled">
                          {{ data.item.day_end != "0000-00-00" ? data.item.day_end: ""}}
                        </span>
                      </div>
                    </template>
                    
                    <template v-slot:cell(parameter)="data">
                      <div style="margin-top: 10px">
                        <span class="text--disabled">
                          {{ data.item.parameter_item != null ? data.item.parameter_item.name : "None"}}
                        </span>
                      </div>
                    </template>

                    <template v-slot:cell(actions)="data">
                      <div style="margin-top: 10px">
                        <v-menu offset-y>
                          <template v-slot:activator="{ on, attrs }">
                            <v-btn
                              color="#56bfff"
                              dark
                              v-bind="attrs"
                              v-on="on"
                              tile
                              depressed
                              small
                            >
                              {{ $t("button.more_actions") }}
                              <v-icon small right>
                                mdi-chevron-down
                              </v-icon>
                            </v-btn>
                          </template>
                          <v-list dense class="profile__row-menu">
                            <v-list-item-group color="primary">
                              <v-list-item @click="onEditProviderService(data.item, data.index)">
                                <v-list-item-icon>
                                  <v-icon color="yellow darken-3">
                                    mdi-map-marker-check
                                  </v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                  <v-list-item-title>
                                    {{ $t("button.edit") }}
                                  </v-list-item-title>
                                </v-list-item-content>
                              </v-list-item>
                              <v-list-item @click="onDeleteProviderService(data.item, data.index)">
                                <v-list-item-icon>
                                  <v-icon color="red lighten-1">
                                    mdi-map-marker-remove
                                  </v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                  <v-list-item-title>
                                    {{ $t("button.delete") }}
                                  </v-list-item-title>
                                </v-list-item-content>
                              </v-list-item>
                            </v-list-item-group>
                          </v-list>
                        </v-menu>
                      </div>
                    </template>
                  </b-table>
                </div>
              </v-card-text>
              <v-card-text class="d-flex flex-grow-1 flex-column" style="background-color: #f2f2f2;" v-if="isThird">
                <div 
                  class="flex-grow-1" 
                  v-scrollbar="{
                          useBothWheelAxes: true
                  }"
                  style="overflow:auto; min-height: 0; height: 20vh;"
                >
                    <v-col md="12" class="px-5">
                      <v-row>
                        <v-col md="4" v-for="providerService in allProviderServices" :key="providerService.id">
                          <v-card
                            elevation="0"
                            class="text-center"
                          >
                            
                            <v-card-title 
                            class="font-weight-bold justify-center"
                            >
                              <v-avatar
                                color="#56CCF2"
                                size="30"
                                rounded
                                
                              >
                                <span class="white--text headline">{{ providerService.service_item.name.charAt(0).toUpperCase() }}</span>
                              </v-avatar>
                            </v-card-title>
                            <v-card-subtitle>
                              <span class="text-caption font-weight-bold black--text">{{providerService.service_item.name}}</span>
                            </v-card-subtitle>
                            <v-card-text>
                              <span class="text-caption black--text">{{providerService.description == null ? 'No Description' : providerService.description}}</span>
                            </v-card-text>
                          </v-card>
                        </v-col>
                      </v-row>
                    </v-col>
                </div>
              </v-card-text>
              </div>
            </v-card>
          </div>

          <div class="col-3rd-provider-services my-10">
            <v-card class="h-100" elevation="0">
              <div class="d-flex flex-column" style="height: 100%">
              <v-card-title style="background-color: #56bfff" class="justify-end">
                <span class="white--text">NOTES</span>
              </v-card-title>
              <v-card-text class="d-flex flex-column flex-grow-1 flex-shrink-0">
                <div 
                  class="flex-grow-1 provider-table-scroll" 
                  v-scrollbar="{
                          useBothWheelAxes: true
                  }"
                  style="overflow:auto; min-height: 0; height: 20vh;"
                >
                  <v-col md="12" v-for="note in provider.provider_profiles.provider_profile.notes" :key="note.index">
                        <v-card
                          elevation="0"
                        >
                          <v-card-text style="background-color: #f2f2f2;">
                            <div class="d-flex flex-row pr-10">
                              <v-checkbox class="align-start ma-0 pa-0"></v-checkbox>
                              <div class="w-100">
                                <p>{{note.notes}}</p>
                                <div class="d-flex flex-row">
                                  <v-avatar
                                    color="primary"
                                    size="25"
                                  ></v-avatar>
                                  <div class="text-caption" style="line-height: 0.8rem !important;">
                                    {{note.added_by}}<br>
                                    {{note.date_requested}}<br>
                                  </div>
                                  <v-btn
                                    icon
                                    color="#cfcfcf"
                                    x-small
                                    class="ml-auto p-2"
                                    @click="modalEditNote(note)"
                                  >
                                    <v-icon>mdi-pencil</v-icon>
                                  </v-btn>
                                </div>
                              </div>
                            </div>
                          </v-card-text>
                        </v-card>
                  </v-col>
                </div>
              </v-card-text>
              <v-card-actions style="background-color: #d7d7d7;" class="justify-end flex-grow-0 flex-shrink-0">
                <v-btn
                  class="mx-2"
                  fab
                  dark
                  color="#2f80ed"
                  @click="modalAddNotes(provider.provider_profiles.provider_profile)"
                >
                  <v-icon dark>
                    mdi-plus
                  </v-icon>
                </v-btn>
              </v-card-actions>
              </div>
            </v-card>
          </div>
        </v-row>
      </div>

         <EditProviderService :$this="this"></EditProviderService>
         <Service :$this="this" @loadTable="loadServicesAndParameters"></Service>
         <Parameter :$this="this" @loadTable="loadServicesAndParameters"></Parameter>
         <AddNotesModal :parent="this"></AddNotesModal>
         <EditNoteModal :parent="this"></EditNoteModal>
         <CreateService :parent="this" @done-success="loadServicesAndParameters" />
  </div> -->
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import ProviderType from "./includes/provider-type/CreateComponent.vue";

import ProviderService from "./includes/provider-services/CreateComponent.vue";

import Parameter from "./includes/parameter/CreateComponent.vue";

import Description from "./includes/provider-services/DescriptionComponent.vue";

import EditProviderService from "./includes/provider-services/UpdateComponent.vue";

import ServiceType from "./includes/service-type/CreateComponent.vue";

import Service from "./includes/services/CreateComponent.vue";

import Loading from "vue-loading-overlay";

import Form from "./../shared/form.js";

import toast from "./../helpers/toast.js";

import text from "./../helpers/text.js";

import AddNotesModal from "./includes/provider-services/AddNoteComponent.vue";

import CreateService from "./includes/provider-services/CreateServiceComponent";

import EditNoteModal from "./includes/provider-services/EditNoteComponent.vue";

import ProvinceModal from "./../views/locations/modals/province.modal.vue";

import SelectBrand from "./includes/providers/SelectBrandComponent.vue";

import CityModal from "./../views/locations/modals/city.modal.vue";

export default {
  mixins: [toast, text],

  components: {
    Loading,

    ProviderType,

    EditProviderService,

    EditNoteModal,

    Description,

    ProviderService,

    Parameter,

    Service,
    ProvinceModal,

    CreateService,

    CityModal,

    ServiceType,

    AddNotesModal,

    SelectBrand,
  },

  data: function() {
    return {
      isLoading: false,
      
      isProviderExists: false,

      isLoadingServices: false,

      isSearchByHasValue: false,

      btnloading: false,

      isProviderLoaded: false,
      
      isFiltersLoaded: false,

      isCountrySelected: false,

      isFirst: false,

      isSecond: true, 

      isThird: false, 

      isNoteExists: false,

      isProviderType: false,

      language: this.$i18n.locale,

      editEndDateCheckBoxStatus: 'not_accepted',

      filter: "",

      selectedBrand: "",

      filterServices: "",

      sortBy: 'name',

      sortDesc: false,

      currentPage: 1,

      currentPageServices: 1,

      perPage: 20,

      perPageServices: 10,

      providers_iden: "",
      
      provider_name: "",

      contact_number: [],

      selectedServices: [],

      allProviderServices: [],

      itemSelected: {},

      searched: "",

      filterCountry: "",

      filterProviderType: "",

      providerType: "",

      date_today: "",

      filterCity: "",

      filterProvince: "",

      tableTotalRows: "",

      providerServicesTotalRows: "",

      brandId: "",

      profileViewed: {
        profile_name: "",
      },
      
      editingIndex: 0,

      filterBy: "",

      filterBySearch: "",

      firstProviderId: "",

      files: [],

      slide: 0,

      sliding: null,

      providerUrl: null,

      filterByNameOrType: [{
        value: 'service_name',
        label: 'Name'
      }, {
        value: 'service_type',
        label: 'Type'
      }],

      tableHeaders: [
        {
          key: "name",
          label: this.$t("label.name_of_provider"),
          thClass: "text-left",
          tdClass: "text-left",
          sortable: true,
        },

        {
          key: "province",
          label: this.$t("label.province_2"),
          thClass: "text-center",
          tdClass: "text-center",
          sortable: true,
        },

        {
          key: "city",
          label: this.$t("label.city"),
          thClass: "text-center",
          tdClass: "text-center",
          sortable: true,
        },

        {
          key: "website",
          label: this.$t("brands_website"),
          thClass: "text-center",
          thStyle: { width: "15%" },
          tdClass: "text-center",
          sortable: true,
        },

        {
          key: "number_of_services",
          label: this.$t("label.number_of_services"),
          thClass: "text-center",
          thStyle: { width: "15%" },
          tdClass: "text-center",
          sortable: true,
        },

        {
          key: "action",
          thClass: "text-center",
          thStyle: { width: "20%" },
          tdClass: "text-center",
        },
      ],

      tableServicesHeaders: [
        {
          key: "name",
          label: this.$t("service_category"),
          thClass: "text-left",
          tdClass: "text-left",
          thStyle: { width: "20%" },
          sortable: true,
        },

        {
          key: "day_start",
          label: this.$t("label.start_date"),
          thClass: "text-center",
          thStyle: { width: "20%" },
          tdClass: "text-center",
          sortable: true,
        },

        {
          key: "day_end",
          label: this.$t("label.end_date"),
          thClass: "text-center",
          thStyle: { width: "20%" },
          tdClass: "text-center",
          sortable: true,
        },

        {
          key: "parameter",
          label: this.$t("parameter_label"),
          thClass: "text-center",
          thStyle: { width: "20%" },
          tdClass: "text-center",
          sortable: true,
        },

        {
          key: "actions",
          label: this.$t("table.action"),
          thClass: "text-center",
          thStyle: { width: "20%" },
          tdClass: "text-center",
        },
      ],

      modal: {
        add: {
          name: "service_add_new_button",

          id: "provider-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "service_add_new_button",

            loading: false,
          },
        },

        edit: {
          name: "provider_add_edit_button",

          id: "provider-edit-modal",

          isVisible: false,

          button: {
            update: "update",

            cancel: "cancel",

            new: "provider_add_new_button",

            loading: false,
          },
        },

        createProviderType: {
          name: "provider_type_add_new_button",

          id: "provider-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "provider_type_add_new_button",

            loading: false,
          },
        },

        createProviderServices: {
          name: "button.add_services",

          id: "provider-services-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "provider_type_add_new_button",

            loading: false,
          },
        },

        editProviderServices: {
          name: "service_add_edit_button",

          id: "provider-services-edit-modal",

          isVisible: false,

          button: {
            update: "button.save_change",

            cancel: "cancel",

            new: "provider_type_add_new_button",

            loading: false,
          },
        },

        createService: {
          name: "service_type_add_new_button",

          id: "service-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "service_type_add_new_button",

            loading: false,
          },
        },

        parameter: {
          name: "parameter_add_new_button",

          id: "parameter-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "parameter_add_new_button",

            loading: false,
          },
        },
      },

      form: new Form({
        id: "",
        name: "",
        provider_type: "",
        provider_type_name: "",
        country: null,
        division: null,
        city: null,
        website: "",
        address: "",
        providers: "",
        postal_code: "",
        services: "",
        notes: [],
        note: [],
        description: "",
        parameter: "",
        profile_id: "",
        profile_name: "",
        parameter_name: "",
        service_type_name: "",
        day_start: "Sunday",
        day_end: "Sunday",
        language: this.$i18n.locale,
      }),

      languageOptions: [
        { value: "en", text: "English" },
        { value: "de", text: "German" },
        { value: "it", text: "Italian" },
        { value: "ph-fil", text: "Filipino" },
        { value: "ph-bis", text: "Visayan" },
      ],

      mtForm: new Form({
        id: "",
        name: "",
        term_types: "",
        specializations: "",
        action: "",
        file: "",
      }),

      typeForm: new Form({
        id: "",
        name: "",
        term_type: '',
        action: "",
        term_types: '',
        file: ""
      }),

      formData: new FormData(),
    };
  },

  mounted() {
    this.getProviderId()
  },

  methods: {
    ...mapActions("providers", [
      "get_providers",
      "delete_provider",
      "remove_from_provider_array",
      "get_provider",
    ]),
    ...mapActions("provider_type", ["get_provider_types"]),
    ...mapActions("provider_service", ["get_provider_services", "get_provider_service_items", "delete_provider_service", "remove_from_provider_service_array","get_provider_id", "get_all_provider_services"]),
    ...mapActions("parameter", ["get_parameters"]),
    ...mapActions("services", ["get_services", "reset_services"]),
    ...mapActions("country", ["get_countries"]),
    ...mapActions("division", ["get_divisions"]),
    ...mapActions("city", ["get_cities"]),
    ...mapActions("service_type", ["get_service_types", "get_service_type_all"]),
    ...mapActions("brand", ["get_brands"]),
    ...mapActions("profile", [
      "get_profiles",
      "get_filtered_profiles",
      "update_profile",
      "get_profile",
      "delete_profile",
      "remove_from_profiles_array",
    ]),

    async loadItems() {
      this.isLoading = true;
      this.isFiltersLoaded = false;
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        brand_id: this.brandId,
        // entries: this.perPage,
        // page: this.currentPage,
        // sort_name: this.sortBy,
        // sort_desc: this.sortDesc,
        // search: this.searched,
      };

      if(this.filterCountry != "") params.country = this.filterCountry
      if(this.filterCity != "") params.city = this.filterCity
      if(this.filterProvince != "")  params.province = this.filterProvince
      if(this.filterProviderType != "") params.providerType = this.filterProviderType

      this.get_providers(params).then((_) => {
        this.firstProviderId = this.items.data.length != 0 ? this.items.data[0].id : "";
        this.tableTotalRows = this.items.total;
        if(this.items.data.length == 0) {
          this.isLoading = false
          return
        };
        this.isFiltersLoaded = true;
        let formData = new FormData();
        formData.append("api_token", this.$user.api_token);
        formData.append("id", this.firstProviderId);
        formData.append("lang", this.form.language);
        this.get_countries(params).then((_) => {
        });
        this.get_provider_types(params).then((_) => {
        });
        this.isLoading = false;
      });
    },

    async loadProviderServiceItems(button = 'second') {
      this.isLoadingServices = true;
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        id: this.firstProviderId,
        entries: this.perPageServices,
        page: this.currentPageServices
      };
      this.get_provider_services(params).then((_) => {
        this.providerServicesTotalRows = this.provider_services.total;
        this.get_provider_id(this.firstProviderId)
        this.isLoadingServices = false;
        if('second' == button) this.isSecond  = true;
        if('third'  == button) this.isThird   = true;
      });
    },

    loadServiceTypes() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
      };
      this.get_service_type_all(params).then((_) => {
      });
    },

    async loadServiceItems(id) {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        id: id,
      };
      this.get_provider_service_items(params).then((data) => {
        this.selectedServices = data
      });
    },

    async loadBrands() {
      let params = {
        api_token: this.$user.api_token,
        org_id: this.$org_id,
      };
      this.get_brands(params).then((_) => {
        if (this.brands.length == 0) {
          this.$bvModal
            .msgBoxOk(this.$t("brand_not_existing"), {
              title: this.$t("brands_name"),
              okVariant: "success",
              hideHeaderClose: false,
              centered: true,
            })
            .then((value) => {
              window.location.href = `/admin/brands`;
            });
        } else {
          this.$bvModal.show("select-brand-modal");
        }
      });
    },

    successfullySavedProvider() {
      this.$refs.table.refresh();
    },

    successfullySavedProviderService() {
      this.$refs.tableService.refresh();
    },

    addProviderServices(provider) {
      this.form.reset();
      this.filterBy = ""
      this.modalId = this.modal.createProviderServices.id;
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        group: true
      };
      this.form.language = this.$i18n.locale;
      this.providers_iden = provider.id;
      this.provider_name = provider.name;
      this.get_services(params).then((_) => {
        this.isSearchByHasValue = false
        this.$bvModal.show("provider-services-add-modal");
      });
      this.loadServiceItems(provider.id)
      
    },

    getProviderUrl(url) {
      history.pushState({page: 1}, "title 1", url)
    },

    getUrlParameters(str) {
        return '?' + str.split('?')[1];
    },

    getProviderId() {
      let url = window.location.search.substring(1)
      let id = url.split("=");
      this.loadProvider(id[1]);
      this.loadProviderServiceItems()
    },

    resetFormLocations(){
      this.form.country = null;
      this.form.division = null;
      this.form.city = null;
    },
// loadParameter() {
//       let params = {
//         api_token: this.$user.api_token,
//         lang: this.form.language,
//       };
//       this.get_parameters(params).then((_) => {
//         this.$bvModal.hide("parameter-add-modal");
//         this.$bvModal.show(this.modalId);
//       });
//     },
    loadServicesAndParameters() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        brand_id: this.$brand.id,
        locale: this.$i18n.locale,
        provider_type: this.providerType,
        filterByService: true,
      };
      this.get_parameters(params).then((_) => {
        this.$bvModal.hide("parameter-add-modal");
        this.$bvModal.hide("service-add-modal");
        this.$bvModal.show(this.modalId);
      });
    },

    searchService(termTypes){
        let services = [
          'Service',
          'Servizio',
          'Servizi',
          'Serbisyo',
        ]
        for (var i=0; i < termTypes.length; i++) {

            for (const [key, value] of Object.entries(JSON.parse(termTypes[i].name))) {
              if (services.includes(value)) {
                return termTypes[i];
              }
            }
        }
    },

    loadDescription(description) {
      this.form.description = description == null ? 'No Description' : description
      this.$bvModal.show('description-modal');
    },

    scrollToServices() {
      let container = this.$el.querySelector("#services_container");
      container.scrollIntoView();
    },

    // loadServiceTypes() {
    //   let params = {
    //     api_token: this.$user.api_token,
    //     lang: this.form.language,
    //   };
    //   this.get_service_types(params).then((_) => {
    //     this.$bvModal.hide("service-type-add-modal");
    //     this.$bvModal.show("service-add-modal");
    //   });
    // },

    goToProviders() {
        window.location.href = `/admin/article-content-maker/providers`;
    },

    loadProviderTypes() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
      };
      this.get_provider_types(params).then((_) => {
        this.get_countries(params).then((_) => {
          this.$bvModal.hide("provider-type-add-modal");
          this.$bvModal.show(this.modalId);
        });
      });
    },

    loadProvider(id) {
      this.firstProviderId = id;
      let formData = new FormData();
      formData.append("api_token", this.$user.api_token);
      formData.append("id", this.firstProviderId);
      formData.append("brand_id", this.$brand.id);
      formData.append("lang", this.form.language);
      this.get_provider(formData).then((resp) => {
        if(this.provider == "") return;
        this.isProviderExists = true;
        this.isProviderLoaded = true;
        this.profileViewed.id = this.provider.provider_profiles.profile;
        
      console.log(this.provider)
      });

    },

    loadProviderServices(filter) {
      this.reset_services();
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        service_search: this.filterBySearch
      };
      this.isSearchByHasValue = true;
      if(filter == 'service_name') params.service_name = true;
      if(filter == 'service_type') {
        params.service_type = true
        params.group = true
        this.get_services(params).then((_) => {
          this.isSearchByHasValue = false
          return;
        });
      };
      this.get_services(params).then((_) => {
        
      });
    },

    handleScroll(el) {
      if((el.srcElement.offsetHeight + el.srcElement.scrollTop) >= el.srcElement.scrollHeight) {
        let params = {
          api_token: this.$user.api_token,
          lang: this.form.language,
          id: this.firstProviderId,
          entries: this.perPageServices,
          page: this.currentPageServices + 1
        };
        
        this.get_provider_services(params).then((_) => {
          if(this.providerServiceResponseStatus == false) return;
          this.currentPageServices = this.currentPageServices + 1;
        });
      }
    },

    providerTypePage(){
      window.location.href = `/admin/provider-type`;
    },

    onEditProviderService(item, index) {
      let formData = new FormData();
      this.editingIndex = index;
      this.form.reset();
      this.form.language = this.$i18n.locale;
      this.modalId = this.modal.editProviderServices.id;
      this.mtForm.term_types = [this.searchService(this.termtypes)]
      this.providerType = item.provider_item.provider_type
      this.loadServicesAndParameters();
      this.form.id = item.id;
      this.form.services = item.service_item;
      this.form.parameter = item.parameter_item
      this.form.description = item.description == null ? "" : item.description;
      this.providers_iden = item.providers;
      this.form.day_start = item.day_start;
      if(item.day_end == '0000-00-00') {
        this.editEndDateCheckBoxStatus = 'not_accepted'
        let date = new Date(item.day_start)
        date = date.setDate(date.getDate()+1)
        date = this.formatDate(date)
        this.form.day_end = date;
      } else {
        this.editEndDateCheckBoxStatus = 'accepted'
        this.form.day_end = item.day_end;
      }
    },

    onDelete(item, index) {
      let providers = item;
      let vm = this;
      $.confirm({
        title:
          vm.$t("confirmation_record_delete").charAt(0) +
          vm
            .$t("confirmation_record_delete")
            .slice(1)
            .toLowerCase(),
        content:
          vm.$t("question_record_delete") +
          `${item.name}` +
          vm.$t("from") +
          vm.$t("label.providers") +
          vm.$t("records")+"?",
        type: "red",
        typeAnimated: true,
        columnClass: "medium",
        buttons: {
          yes: {
            text: vm.$t("yes"),
            btnClass:
              "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
            action: function(value) {
              if (value) {
                item.is_loading = true;
                let params = {
                  api_token: vm.$user.api_token,
                  id: item.id,
                };
                vm.delete_provider(params)
                  .then((resp) => {
                    item.is_loading = false;
                    if (vm.providersResponseStatus) {
                      providers.index = index;
                      vm.remove_from_provider_array(providers);
                      vm.makeToast(
                        "danger",
                        vm.$t("delete_record"),
                        `${item.name}` +
                          vm.$t("delete_record_message") +
                          vm.$t("label.providers") +
                          vm.$t("records")
                      );
                    }
                  })
                  .catch((error) => {
                    console.log(error);
                  });
              }
            },
          },
          no: {
            text: vm.$t("no"),
            btnClass:
              "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
            action: function() {},
          },
        },
      });
    },

    onDeleteProviderService(item, index) {
      let providerService = item;
      let vm = this;
      $.confirm({
        title:
          vm.$t("confirmation_record_delete").charAt(0) +
          vm
            .$t("confirmation_record_delete")
            .slice(1)
            .toLowerCase(),
        content:
          vm.$t("question_record_delete") +
          `${item.service_item.name}` +
          vm.$t("from") +
          vm.$t("label.services") +
          vm.$t("records")+"?",
        type: "red",
        typeAnimated: true,
        columnClass: "medium",
        buttons: {
          yes: {
            text: vm.$t("yes"),
            btnClass:
              "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
            action: function(value) {
              if (value) {
                item.is_loading = true;
                let params = {
                  api_token: vm.$user.api_token,
                  id: item.id,
                };
                vm.delete_provider_service(params)
                  .then((resp) => {
                    item.is_loading = false;
                    if (vm.providerServiceResponseStatus) {
                      providerService.index = index;
                      vm.loadProviderServiceItems()
                      vm.makeToast(
                        "danger",
                        vm.$t("delete_record"),
                        `${item.service_item.name}` +
                          vm.$t("delete_record_message") +
                          vm.$t("label.providers") +
                          vm.$t("records")
                      );
                    }
                  })
                  .catch((error) => {
                    console.log(error);
                  });
              }
            },
          },
          no: {
            text: vm.$t("no"),
            btnClass:
              "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
            action: function() {},
          },
        },
      });
    },

    onFirst() {
      this.isFirst  = true
      this.isSecond = false
      this.isThird  = false
    },

    onSecond() {
      this.isFirst  = false
      this.isThird  = false
      this.currentPage = 1;
      this.provider_services.data = [];
      this.loadProviderServiceItems('second')
    },

    onThird() {
      this.isFirst  = false
      this.isSecond = false
      this.currentPage = 1;
      this.provider_services.data = [];
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        id: this.provider.id,
      };
      this.get_all_provider_services(params).then((data) => {
        this.allProviderServices = data;
        this.isThird = true
      });
    },

    deleteNote(index) {
      this.form.notes.splice(index, 1);
    },

    formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) 
            month = '0' + month;
        if (day.length < 2) 
            day = '0' + day;

        return [year, month, day].join('-');
    },

    filterByCountryOrProviderType(val) {
      this.filters(this.filterProviderType, this.filterCountry, this.filterProvince, this.filterCity)
    },

    filterByCountry(val){
      this.filterProvince = ""
      this.filterCity = ""
      this.filters(this.filterProviderType, this.filterCountry, this.filterProvince, this.filterCity)
    },

    filterByNOT(val) {
      if(val == "all_services") {
        this.loadProviderServicesGrouped()
        return;
      }
      this.loadProviderServices(val);
    },

    filterByProvince(val){
      this.filterCity = "";
      this.filters(this.filterProviderType, this.filterCountry, this.filterProvince, this.filterCity)
    },

    loadProviderServicesGrouped() {
      this.reset_services();
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        group: true
        };
        this.get_services(params).then((_) => {
          this.isSearchByHasValue = false
        });
    },

    updateLoadProvider(token,id,lang) {
      let formData = new FormData();
      formData.append("api_token", token);
      formData.append("id", id);
      formData.append("lang", lang);
      this.get_provider(formData).then((_) => {
      });
    },

    filters(providerType,country,province,city) {
      this.isLoading = true;
      this.isCountrySelected = false
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        brand_id: this.brandId,
        entries: this.perPage,
        page: this.currentPage,
        sort_name: this.sortBy,
        sort_desc: this.sortDesc,
        search: this.searched,
      };
      if(providerType != "") params.providerType = providerType
      if(country != "") {
        this.isCountrySelected = true
        params.country = country
        let formData = new FormData();
        formData.append("api_token", this.$user.api_token);
        formData.append("id", country);
        formData.append("lang", this.form.language);
          this.get_divisions(formData).then((resp) => {
        });
        formData.append("country_id", 'country_id');
        if(province != "") {
          params.province = province
          formData.set("id", province);
          formData.append("lang", this.form.language);
          formData.delete("country_id")
        }
        if(city != "") {
          params.city = city;
        }
        this.get_cities(formData).then((resp) => {
        });
        
      }
      
      this.get_providers(params).then((_) => {
        this.firstProviderId = this.items.data.length != 0 ? this.items.data[0].id : "";
        this.tableTotalRows = this.items.total;
        if(this.items.data.length == 0) {
          this.isLoading = false
          return
        };
        let formData = new FormData();
        formData.append("api_token", this.$user.api_token);
        formData.append("id", this.firstProviderId);
        formData.append("lang", this.form.language);
        this.loadProviderServiceItems();
        this.get_provider(formData).then((_) => {
        });   
        
        this.isLoading = false;
      });
    },
    
    modalAddNewProvince(country) {
      this.$bvModal.hide("provider-add-modal");
      this.$refs.provinceModal.InitializeModal(country, "Add");
    },

    async loadProfile(id) {
      let params = {
        api_token: this.$user.api_token,
        id: id,
      };

      await this.get_profile(params);
    },

    modalAddNotes(item) {
      let vm = this;
      let id = item.id;

      //vm.resetProfileForm();
      vm.form.notes = [];

      vm.loadProfile(id).then(() => {
        vm.$nextTick(() => {
          vm.form.id = vm.profile.id;
          vm.form.profile_id = vm.profile.id;
          vm.form.notes = vm.deepCopy(vm.profile.notes);
          vm.addNote();
          vm.form.profile_name = vm.profile.full_name;

          vm.itemSelected = vm.profile;

          // open category modal
          vm.$bvModal.show("add-notes-modal");
        });
      });
    },

    modalEditNote(note) {
      
      let vm = this;
      vm.form.note = [];
      vm.editingIndex = note.index
      vm.form.note.push(note);
      vm.isNoteExists = true;
      vm.$bvModal.show("edit-note-modal");
    },

    deepCopy(variable_to_copy) {
      console.log(_.cloneDeep(variable_to_copy))
      return _.cloneDeep(variable_to_copy);
      return JSON.parse(JSON.stringify(variable_to_copy));
    },

    addNote() {
      this.form.notes.push({
        date_requested: this.date_today,
        notes: "",
      });
    },

    addedNotesSuccessfully(notes) {
      let vm = this;
      this.provider.provider_profiles.provider_profile.notes = notes;
    },

    addedProvince(data) {
      let country = data.country;
      this.$refs.createComponent.changeCountry(country);
    },

    hideProvinceModal(){
      if(this.modalId == this.modal.add.id){
        this.$bvModal.show("provider-add-modal");
      }
      else{
        //Show edit/update modal here
      }
    },

    modalAddNewCity(data) {
      let country = data.country;
      let province = data.province;

      this.$bvModal.hide("provider-add-modal");
      this.$refs.cityModal.InitializeModal(data, "Add");
    },

    addedCity(data) {
      let province = data.province;
      this.$refs.createComponent.changeDivision(province);
    },

    hideCityModal(){
      if(this.modalId == this.modal.add.id){
        this.$bvModal.show("provider-add-modal");
      }
      else{
        //Show edit/update modal here
      }
    },

    previousProvider() {
      this.getProviderUrl(document.referrer);
      window.location.href ="/admin/article-content-maker/provider-services?id=" + this.provider.previous
    },
    
    nextProvider() {
      this.getProviderUrl(document.referrer);
      window.location.href ="/admin/article-content-maker/provider-services?id=" + this.provider.next
    },

    backToProviderList() {
      window.location.href = document.referrer
    },

    onFiltered(filteredItems) {
      
      this.tableTotalRows = filteredItems.length
      this.currentPage = 1
    },

    sortChanged(e) {
      this.sortBy = e.sortBy
      this.sortDesc = e.sortDesc
      console.log(this.sortBy)
      this.loadItems()
    },

    changeBrand() {
      this.loadBrands()
    },

    changeCountry() {
      this.isCountrySelected = true
    },

    onReset() {
      this.language = this.$i18n.locale;
    },

    performSearch: _.debounce(function(query) {
        this.searched = query
    }, 1000),

  },

  created() {
    var today = new Date();
    var dd = today.getDate();

    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();

    if (dd < 10) {
      dd = "0" + dd;
    }

    if (mm < 10) {
      mm = "0" + mm;
    }

    this.date_today = yyyy + "-" + mm + "-" + dd;
  },

  watch: {
    
    filter(query) {
      this.performSearch(query)
    },

    searched: {
      handler: function(value) {
        this.loadItems()
      }
    },

    division(val) {
      let formData = new FormData();

      formData.append("api_token", this.$user.api_token);
      formData.append("id", val.id);
      formData.append("lang", this.form.language);
      this.get_cities(formData).then((resp) => {
        this.form.city = "";
      });
    },
  },
  destroyed: function() {
    window.removeEventListener('mousemove');
  },

  computed: {
    ...mapGetters("providers", {
      items: "providers",
      provider: "provider",
      providersResponseStatus: "get_response_status",
    }),

    ...mapGetters("categ_terms", {
      terms: "get_terms_items",
      termtypes: "get_type_terms_items",
    }),

    ...mapGetters("provider_type", {
      provider_types: "provider_types",
      responseStatus: "get_response_status",
    }),

    ...mapGetters("provider_service", {
      provider_services: "provider_services",
      providerServiceResponseStatus: "get_response_status",
      provider_id: "provider_id",
    }),

    ...mapGetters("services", {
      services: "services",
      servicesResponseStatus: "get_response_status",
    }),

    ...mapGetters("country", {
      countries: "countries",
    }),

    ...mapGetters("division", {
      divisions: "divisions",
    }),

    ...mapGetters("city", {
      cities: "cities",
    }),

    ...mapGetters("service_type", {
      service_types: "service_type_all",
      responseStatus: "get_response_status",
    }),

    ...mapGetters("parameter", {
      parameters: "parameters",
      parametersResponseStatus: "get_response_status",
    }),


    ...mapGetters("brand", {
      brands: "brands",
    }),

    ...mapGetters("profile", {
      profile: "profile",
    }),

    providerContactNo() {
      if(this.provider.length != 0) {
        return this.provider.contact_nos.length
      }
    },

    totalRows() {
      return this.items.length;
    },

    totalServiceProviderRows() {
      return this.provider_services.length;
    },
  },
};
</script>

<style lang="scss">

.carousel-item-wrapper .img-fluid{
  height:480px !important;
}
</style>
