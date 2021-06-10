<template>
    <div class="navbar-brands">
     
        <v-card
            class="px-2 mx-auto d-flex flex-row"
            max-width="221"
            elevation="0"
            v-if="isBrand"
            color="#2f80ed"
        >
            <div class="pl-3 d-flex align-center" style="height: 53px">
                <v-avatar
                    color="#54c0ff"
                    size="45"
                    tile
                    style="border-radius: 5px !important;"
                    v-if="brand_session.logo != null"
                >
                    <img
                        :src="brand_session.logo"
                    >
                </v-avatar>

                <v-avatar
                    color="#54c0ff"
                    size="45"
                    tile
                    v-else
                    style="border-radius: 5px !important;"
                >
                    <span class="white--text headline">{{ brand_session.name.charAt(0).toUpperCase() }}</span>
                </v-avatar>
            </div>
            <div class="text-md-left pl-1 workspace-wrapper">
                
                <span class="text-caption white--text" style="font-size: 0.60rem !important">BRAND</span><br>
                <span class="white--text">{{brand_session.name}}</span>
            </div>
            <div class="d-flex align-center" style="height: 53px">
                <v-btn
                    icon
                    color="#fff"
                    @click="isBrand = false"
                >
                    <v-icon color="#fff">mdi-chevron-right</v-icon>
                </v-btn>
            </div>
        </v-card>

        <v-card
            class="d-flex flex-row pl-1"
            max-width="221"
            elevation="0"
            color="#2f80ed"
            v-else
        >
            <div class="d-flex align-center" style="height: 53px; ">
                <v-tooltip 
                    bottom
                    v-for="brand in brands"
                    :key='brand.id'
                >
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn 
                            icon 
                            width="auto" 
                            height="auto"
                            @click="changeBrand(brand.id)"
                            class="mr-1"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-avatar
                                color="#54c0ff"
                                size="45"
                                tile
                                style="border-radius: 5px !important;"
                                v-if="brand.logo != null"
                            >
                                <img
                                    :src="brand.logo"
                                >
                            </v-avatar>

                            <v-avatar
                                color="#54c0ff"
                                size="45"
                                tile
                                v-else
                                style="border-radius: 5px !important;"
                            >
                                <span class="white--text headline">{{ brand.name.charAt(0).toUpperCase() }}</span>
                            </v-avatar>
                        </v-btn> 
                    </template>
                    <span>{{brand.name}}</span>
                </v-tooltip>  
            </div>
            <div class="d-flex align-center" style="height: 53px">
                <v-btn
                    icon
                    color="#fff"
                    @click="isBrand = true"
                >
                    <v-icon color="#fff">mdi-chevron-left</v-icon>
                </v-btn>
            </div>
            
        </v-card>
    </div>
    <!-- <div class="form-inline local-settings" style="color: #73818f;">
        <label>{{$t('label.select_brand')}}</label>
        &nbsp;
        <select class="form-control" v-model="selectedBrand" @change="changeBrand">
            <option v-for="brand in brands" :key="brand.id" :value="{ id: brand.id, name: brand.name }">{{ brand.name }}</option>
        </select>
    </div> -->
</template>

<script>
// Components
import { mapActions, mapGetters } from "vuex";

import SelectBrand from "./includes/providers/SelectBrandComponent.vue";

import Loading from "vue-loading-overlay";

// Custom Script
import Form from "./../shared/form.js";

import toast from "./../helpers/toast.js";


export default {
  mixins: [toast],

  props:['brand_session'],

  components: {
    SelectBrand,
  },  

  mounted() {
    this.loadBrands();
  },

  data: function() {

    return {

        selectedBrand: '',

        modal: {
            create: {
                name: "invite_user_title",

                isVisible: false,

                button: {
                    save: "save_button",

                    cancel: "cancel",

                    new: "content_add_new_button",

                    loading: false,
                },
            },
        },

        isBrand: true,

        form: new Form({
            org_name: "",
            location: "",
            other_info: "",
        }),

        formData: new FormData(),
        };
    },


    methods: {
        ...mapActions("brand", ["get_brands"]),
        async loadBrands() {
            let params = {
                api_token: this.$user.api_token,
                org_id: this.$org_id,
            };
            this.get_brands(params).then((_) => {
            });
        },

        changeBrand(brandId) {

            window.location.href = "/admin/brands/swap/" + brandId;
            
        },
    }, 

    computed: {
        ...mapGetters("brand", {
            brands: "brands",
        }),
    },

    watch: {
        // brands(val) {
        //     const filtered = val
        //     .filter(key => {
        //         return key.isDefault == 1
        //     });
        //     const obj = {};

        //     obj['id'] = filtered[0]['id']
        //     obj['name'] = filtered[0]['name']
            
        //     this.selectedBrand = obj
        // }
    }
};
</script>

<style lang="scss">
</style>
