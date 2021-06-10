<template>
    <v-card flat>
        <div class="row mt-2">
            <div class="col-md-12">
                <b-table
                    ref="table"
                    striped
                    stacked="md"
                    show-empty
                    :sort-by.sync="sortBy"
                    primary-key="id"
                    :fields="tableHeaders"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :busy="(parent.isLoading && !parent.isDoneInitializing)"
                    :items="cities"
                >
                    <template v-slot:table-busy>
                    <v-fade-transition>
                        <v-overlay
                        opacity="0.8"
                        style="z-index: 999999 !important"
                        >
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
                        <h6>
                            {{ data.item.name }}
                        </h6>
                    </template>

                    <template v-slot:cell(province_name)="data">
                        <h6 v-if="data.item.division != null">
                            {{ data.item.division.name }}
                        </h6>
                        <h6 v-else>
                            {{ $t('label.no_answer') }}
                        </h6>
                    </template>

                    <template v-slot:cell(actions)="data">
                        <v-btn text color="warning" @click="parent.editCity(data.item)">
                            <v-icon>
                                mdi-pencil
                            </v-icon>
                            {{ $t('button.edit') }}
                        </v-btn>
                        <v-btn text color="error" @click="deleteCity(data.item)">
                            <v-icon>
                                mdi-delete
                            </v-icon>
                            {{ $t('button.delete') }}
                        </v-btn>
                    </template>
                </b-table>
            </div>
            <div class="col-md-8"></div>
            <div class="col-md-4">
                <v-pagination
                    v-if="(!parent.isLoading  && parent.isDoneInitializing)"
                    v-model="currentPage"
                    :length="totalRows"
                    :total-visible="5"
                    color="secondary"
                    circle
                    class="profile__table-pagination"
                >
                </v-pagination>
            </div>
        </div>
        </v-card>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    name: "cities-table",
props: ["parent", "perPage", "filters"],
    data() {
        return {
        sortBy: 'name',
        currentPage: 1,
        tableHeaders: [
            {
                key: "name",
                label: this.$t("table.city_name"),
                thClass: "text-center profile__table-header",
                thStyle: { width: "32.5%" },
                sortable: true,
                tdClass: "text-center",
            },
            {
                key: "province_name",
                label: this.$t("table.province_name"),
                thClass: "text-center profile__table-header",
                thStyle: { width: "32.5%" },
                sortable: true,
                tdClass: "text-center",
            },
            {
                key: "actions",
                label: this.$t("table.action"),
                thClass: "text-center profile__table-header",
                thStyle: { width: "35%" },
                tdClass: "text-center profile__table-row",
            },
        ],
        }
    },
    mounted(){
       
        let vm = this;
        vm.$emit("start-loading");
        vm.$nextTick(()=>{
            vm.parent.fetchCities('all').
            then(resp => {
                vm.$emit("end-loading");
            });
        })
    },
    computed: {
        ...mapGetters("location", {
            cities: "cities",
        }),
            
        totalRows() {
            let items_length = this.cities.length;
            return Math.ceil((items_length == 0) ? items_length : items_length / this.perPage);
        },
    },

    methods: {
        filterCity(key, item){
            if(this.filters.country == "" && this.filters.city == ""){
                return true;
            }

            if(key == "country"){
                return item.country.name.includes(this.filters.country);
            }

            if(key == "city"){
                return item.name.includes(this.filters.city);
            }
        },

        editCity(city){
            this.parent.editCity(city);
        },
        deleteCity(city){
            this.parent.deleteCity(city);
        },
        addCity(city){
            this.parent.modalAddNewCity({country: city.country, province: city.division, city: city});
        },
    }
}
</script>