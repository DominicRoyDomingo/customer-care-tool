<template>
  <div>
    <b-button
      variant="light"
      pill
      class="mb-1 mr-1"
      v-if="this.parent.selectedByProviderType"
    >
      {{filters.providertype.name}}   :
          <span class="text-info text-capitalize mr-1 text-underline" v-html="this.providertype_data"/>

          <b-badge
            variant="danger"
            class="badge-close float-right"
            title="Close"
            @click="on_close_search_item('providertype')"
          >

          <i class="fa fa-times" aria-hidden="true"></i>
        </b-badge>
    </b-button>

    <b-button
      variant="light"
      pill
      class="mb-1 mr-1"
      v-if="this.parent.selectedByServiceType"
    >
      {{filters.servicetype.name}}   :
          <span class="text-info text-capitalize mr-1 text-underline" v-html="this.servicetype_data"/>

          <b-badge
            variant="danger"
            class="badge-close float-right"
            title="Close"
            @click="on_close_search_item('servicetype')"
          >

          <i class="fa fa-times" aria-hidden="true"></i>
        </b-badge>
    </b-button>

    <b-button
      variant="light"
      pill
      class="mb-1 mr-1"
      v-if="this.parent.selectedByService"
    >
      {{filters.service.name}}   :
          <span class="text-info text-capitalize mr-1 text-underline" v-html="this.service_data"/>

          <b-badge
            variant="danger"
            class="badge-close float-right"
            title="Close"
            @click="on_close_search_item('service')"
          >

          <i class="fa fa-times" aria-hidden="true"></i>
        </b-badge>
    </b-button>

    <b-button
      variant="light"
      pill
      class="mb-1 mr-1"
      v-if="this.parent.selectedByCountry"
    >
      {{filters.country.name}}   :
          <span class="text-info text-capitalize mr-1 text-underline" v-html="this.country_data"/>

          <b-badge
            variant="danger"
            class="badge-close float-right"
            title="Close"
            @click="on_close_search_item('country')"
          >

          <i class="fa fa-times" aria-hidden="true"></i>
        </b-badge>
    </b-button>

    <b-button
      variant="light"
      pill
      class="mb-1 mr-1"
      v-if="this.parent.selectedByProvinceOrDivison"
    >
      {{filters.division.name}}   :
          <span class="text-info text-capitalize mr-1 text-underline" v-html="this.division_data"/>

          <b-badge
            variant="danger"
            class="badge-close float-right"
            title="Close"
            @click="on_close_search_item('division')"
          >

          <i class="fa fa-times" aria-hidden="true"></i>
        </b-badge>
    </b-button>

    <b-button
      variant="light"
      pill
      class="mb-1 mr-1"
      v-if="this.parent.selectedByCity"
    >
      {{filters.city.name}}   :
          <span class="text-info text-capitalize mr-1 text-underline" v-html="this.city_data"/>

          <b-badge
            variant="danger"
            class="badge-close float-right"
            title="Close"
            @click="on_close_search_item('city')"
          >

          <i class="fa fa-times" aria-hidden="true"></i>
        </b-badge>
    </b-button>

  </div>
</template>

<script>
export default {
  props: ["parent"],

  computed:{
    providertype_data(){
      this.$emit('reload_filter')
      return this.getBaseName(this.parent.providerTypeTerms, this.parent.selectedByProviderType)
    },
    servicetype_data(){
      this.$emit('reload_filter')
      return this.getBaseName(this.parent.categoryServicesTerms, this.parent.selectedByServiceType)
    },
    service_data(){
      this.$emit('reload_filter')
      return this.getBaseName(this.parent.servicesCategoryTerms, this.parent.selectedByService)
    },
    country_data(){
      this.$emit('reload_filter')
      return this.getName(this.parent.countries, this.parent.selectedByCountry)
    },
    division_data(){
      this.$emit('reload_filter')
      return this.getName(this.parent.divisions, this.parent.selectedByProvinceOrDivison)
    },
    city_data(){
      this.$emit('reload_filter')
      return this.getName(this.parent.cities, this.parent.selectedByCity)
    },

  },

  data(){
    return{
       filters:{
         providertype: { 
          name: "Provider Type",
          selected: this.parent.selectedByProviderType
        },
        servicetype: { 
          name: "Service Type",
          selected:  this.parent.selectedByServiceType,
        },
        service: { 
          name: "Service",
          selected:  this.parent.selectedByService,
        },
        country: { 
          name: "Country",
          selected:  this.parent.selectedByCountry,
        },
        division: { 
          name: "Province Or Division",
          selected:  this.parent.selectedByProvinceOrDivison,
        },
        city: { 
          name: "City",
          selected:  this.parent.selectedByCity,
        },
       }
    }
  },

  methods:{
    on_close_search_item(filter){
      switch (filter) {
        case "providertype":
          this.parent.selectedByProviderType = null;
          break;
        case "servicetype":
          this.parent.selectedByServiceType = null;
          break;
        case "service":
          this.parent.selectedByService = null;
          break;
        case "country":
          this.parent.selectedByCountry = null;
          break;
        case "division":
          this.parent.selectedByProvinceOrDivison = null;
          break;
        case "city":
          this.parent.selectedByCity = null;
          break;
      }

      
      this.$emit('close_filter')
    },



    getBaseName(option, selected){
     return (selected) ?  option.find(x => x.id ===  selected)?.base_name : ""
    },

    getName(option, selected){
      return (selected) ?  option.find(x => x.id ===  selected)?.name : ""
    },
  }

}
</script>

<style>
.badge-close {
  margin-top: -10px;
  margin-right: -15px;
}
</style>