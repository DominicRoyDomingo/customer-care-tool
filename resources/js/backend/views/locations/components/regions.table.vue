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
                    :busy="(parent.isLoading)"
                    :items="regions"
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
                            {{ data.item.region }}
                        </h6>
                    </template>

                    <template v-slot:cell(actions)="data">
                        <v-btn text color="warning" @click="parent.editRegion(data.item)">
                            <v-icon>
                                mdi-pencil
                            </v-icon>
                            {{ $t('button.edit') }}
                        </v-btn>
                        <v-btn text color="error" @click="deleteRegion(data.item)">
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
                    v-if="(!parent.isLoading)"
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
    name: "regions-table",
props: ["parent", "perPage"],
    data() {
        return {
        sortBy: "name",
        currentPage: 1,
        tableHeaders: [
            {
                key: "name",
                label: this.$t("table.region_name"),
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

    mounted(){
        
        let vm = this;
        vm.$emit("start-loading");
        vm.$nextTick(()=>{
            vm.parent.fetchRegions('all').
            then(resp => {
                vm.$emit("end-loading");
            });
        })
    },
    
    computed: {
        ...mapGetters("location", {
            regions: "regions",
        }),
        totalRows() {
            let items_length = this.regions.length;
            return Math.ceil((items_length == 0) ? items_length : items_length / this.perPage);
        },
    },

    methods: {
        editRegion(region){
            this.parent.editRegion(region);
        },
        deleteRegion(region){
            this.parent.deleteRegion(region);
        },
    }
}
</script>