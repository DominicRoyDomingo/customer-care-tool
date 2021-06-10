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
                    :busy="parent.isLoading && !parent.isDoneInitializing"
                    :items="provinces"
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

                    <template v-slot:cell(actions)="data">
                        <v-btn text color="warning" @click="parent.editProvince(data.item)">
                            <v-icon>
                                mdi-pencil
                            </v-icon>
                            {{ $t('button.edit') }}
                        </v-btn>
                        <v-btn text color="error" @click="deleteProvince(data.item)">
                            <v-icon>
                                mdi-delete
                            </v-icon>
                            {{ $t('button.delete') }}
                        </v-btn>
                        <v-btn text color="primary" @click="addCity(data.item)">
                            <v-icon>
                                mdi-plus
                            </v-icon>
                            {{ $t('label.add_city') }}
                        </v-btn>
                    </template>
                </b-table>
            </div>
            <div class="col-md-8"></div>
            <div class="col-md-4">
                <v-pagination
                    v-if="!parent.isLoading  && parent.isDoneInitializing"
                    v-model="currentPage"
                    :length="Math.ceil((provinces.length == 0) ? totalRows : provinces.length / perPage)"
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
    name: "provinces-table",
props: ["parent", "perPage"],
    data() {
        return {
        sortBy: "name",
        currentPage: 1,
        tableHeaders: [
            {
                key: "name",
                label: this.$t("table.province_name"),
                thClass: "text-center profile__table-header",
                thStyle: { width: "65%" },
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
    
    computed: {
        ...mapGetters("location", {
            provinces: "provinces",
        }),
        totalRows() {
            let items_length = this.provinces.length;
            return Math.ceil((items_length == 0) ? items_length : items_length / this.perPage);
        },
    },

    methods: {
        editProvince(province){
            this.parent.editProvince(province);
        },
        deleteProvince(province){
            this.parent.deleteProvince(province);
        },
        addCity(province){
            this.parent.modalAddNewCity({country: province.country, province: province});
        },
    }
}
</script>