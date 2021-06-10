<template>
    <v-app>
        <v-card tile flat :loading="loading.query">
            <v-app-bar dark>
                <v-toolbar-title class="title mr-6 hidden-sm-and-down">{{ $t('label.statistics') }}</v-toolbar-title> 

                <v-spacer></v-spacer>

                <v-btn v-if="currentWindow == 1" color="primary" :disabled="!allowSearch" @click="startQuery()">
                    <v-icon>mdi-magnify</v-icon> {{ $t('label.search') }}
                </v-btn>

                <v-btn v-if="currentWindow == 2" color="primary" @click="currentWindow = 1">
                    <v-icon>mdi-arrow-left</v-icon> {{ $t('label.back') }}
                </v-btn>
            </v-app-bar>
            <v-window v-model="currentWindow">
                <v-window-item :value="1">
                    <v-form
                    v-model="allowSearch"
                    @submit.prevent="">
                            
                        <v-card-title class="py-0">
                            <v-row cols="12">
                                <v-col cols="3">
                                <v-autocomplete
                                    v-model="query.question"
                                    :items="questions"
                                    :loading="loading.questions"
                                    :rules="rules.required"
                                    chips
                                    clearable
                                    hide-details
                                    hide-selected
                                    item-text="localized_text"
                                    item-value="result"
                                    :label="$t('label.select_a_question')"
                                    solo
                                    >
                                        <template v-slot:selection="data">
                                            <v-chip
                                            v-bind="data.attrs"
                                            :input-value="data.selected"
                                            close
                                            v-html="data.item.localized_text">
                                            
                                            </v-chip>
                                        </template>
                                        <template v-slot:item="data">
                                            <template>
                                                <v-list-item-content v-html="data.item.localized_text"></v-list-item-content>
                                            </template>
                                        </template>
                                    </v-autocomplete>
                                </v-col>
                                <v-col cols="3">
                                    <v-autocomplete
                                        v-model="query.table"
                                        :disabled="query.question == null"
                                        :items="tables"
                                        :loading="loading.tables"
                                        @change="getTableFields()"
                                        chips
                                        clearable
                                        :rules="rules.required"
                                        hide-details
                                        hide-selected
                                        item-text="display_name"
                                        item-value="id"
                                        :label="$t('label.select_an_object')"
                                        solo
                                    >
                                        <template v-slot:selection="data">
                                            <v-chip
                                            v-bind="data.attrs"
                                            :input-value="data.selected"
                                            close
                                            v-html="data.item.display_name">
                                            
                                            </v-chip>
                                        </template>
                                        <template v-slot:item="data">
                                            <template>
                                                <v-list-item-content v-html="data.item.display_name"></v-list-item-content>
                                            </template>
                                        </template>
                                    </v-autocomplete>
                                </v-col>
                                <v-col cols="3">
                                    <v-autocomplete
                                        v-model="query.fields"
                                        :disabled="query.table == null || loading.fields"
                                        :items="fields"
                                        :loading="loading.fields"
                                        chips
                                        :rules="rules.at_least_one"
                                        clearable
                                        multiple
                                        item-text="display_name"
                                        return-object
                                        :label="$t('label.select_fields')"
                                        solo
                                    >
                                        <template v-slot:selection="data">
                                            <v-chip
                                            v-bind="data.attrs"
                                            :input-value="data.selected"
                                            close
                                            @click="data.select"
                                            @click:close="remove(query.fields, data.item)">
                                            <span v-html="data.item.display_name"></span>
                                            </v-chip>
                                        </template>
                                        <template v-slot:item="data">
                                            <template>
                                                <v-list-item-content v-html="data.item.display_name"></v-list-item-content>
                                            </template>
                                        </template>
                                    </v-autocomplete>
                                </v-col>
                                <v-col cols="3" v-if="query.fields.length > 0">
                                    <v-text-field
                                        v-for="(field, index) in query.fields"
                                        :key="'filter_' + index"
                                        :label="field.display_name"
                                        :hint="field.display_name + ' is equal to'"
                                        v-model="query.filters[index]">
                                        <template v-slot:label>
                                            <span v-html="field.display_name + ' ' + $t('label.filter')"></span>
                                        </template>
                                        <template v-slot:message>
                                            <span v-html="field.display_name +  ' ' + $t('label.is_equal_to')"></span>
                                        </template>
                                    </v-text-field>
                                </v-col>
                                <v-col cols="3" v-else>
                                    <v-text-field
                                        disabled
                                        :label="$t('label.filter')">
                                    </v-text-field>
                                </v-col>
                            </v-row>
                        </v-card-title>
                        <v-card-title class="py-0">
                            <v-row>
                                <v-col cols="3">
                                    <v-autocomplete
                                        v-model="query.fields_to_show"
                                        :disabled="query.table == null || loading.fields"
                                        :items="fields"
                                        :loading="loading.fields"
                                        chips
                                        clearable
                                        multiple
                                        item-text="display_name"
                                        item-value="id"
                                        persistent-hint
                                        :label="$t('label.fields_to_show')"
                                        :hint="$t('label.leave_empty_to_show_all_fields')"
                                        solo
                                    >
                                        <template v-slot:selection="data">
                                            <v-chip
                                            v-bind="data.attrs"
                                            :input-value="data.selected"
                                            close
                                            @click="data.select"
                                            @click:close="remove(query.fields_to_show, data.item.id)"
                                            >
                                                <span v-html="data.item.display_name"></span>
                                            </v-chip>
                                        </template>
                                        <template v-slot:item="data">
                                            <template>
                                                <v-list-item-content v-html="data.item.display_name"></v-list-item-content>
                                            </template>
                                        </template>
                                    </v-autocomplete>
                                </v-col>
                                <v-col cols="6">
                                </v-col>
                                <v-col cols="3">
                                </v-col>
                            </v-row>
                        </v-card-title>

                        <v-card-text>

                        </v-card-text>
                    </v-form>
                </v-window-item>
                <v-window-item :value="2">
                    <v-data-table
                        v-if="query_result != null"
                        :headers="query_result.headers"
                        :items="query_result.items"
                        :page.sync="page"
                    >
                    <template v-slot:body="props">
                        <tr v-for="item in props.items">
                            <td v-for="(header, index) in query_result.headers">
                                <span>{{ item[header.value] }}</span>
                            </td>
                        </tr>
                    </template>
                    </v-data-table>
                </v-window-item>
            </v-window>
                    
        </v-card>
    </v-app>
</template>

<script>
import axios from "axios";
import { API_STATISTICS } from "./../../common/API.service";

export default {
    name: "StatisticsComponent",
    
    data() {
        return {
            page: 1,
            currentWindow: 1,
            query: {
                question: null,
                table: null,
                fields: [],
                fields_to_show: [],
                filters: [],
            },
            questions: [],
            tables: [],
            fields: [],
            loading: {
                query: false,
                questions: false,
                tables: false,
                fields: false,
            },
            query_result: null,
            rules: {
                required: [
                    v => !!v || 'This field required'
                ],
                at_least_one: [
                    v => v.length > 0 || 'Please select at least one'
                ],
            },
            allowSearch: false,
        }
    },

    mounted() {
        this.getQuestions();
        this.getTables();
    },

    methods: {
        getQuestions(){
            let vm = this;
            vm.loading.questions = true;
            let params = {
                api_token: this.$user.api_token,
                locale: this.$user.locale
            }

            axios.get(API_STATISTICS, { params })
            .then((resp)=>{
                console.log(resp.data);
                vm.questions = resp.data;
                vm.loading.questions = false;
            })
            .catch((error)=>{
                vm.loading.questions = false;
                console.log(error);
            });
        },
        getTables(){
            let vm = this;
            vm.loading.tables = true;
            vm.query.tables = [];
            let params = {
                api_token: this.$user.api_token,
                locale: this.$user.locale
            }

            axios.get(API_STATISTICS + "/tables", { params })
            .then((resp)=>{
                console.log(resp.data);
                vm.tables = resp.data;
                vm.loading.tables = false;
            })
            .catch((error)=>{
                vm.loading.tables = false;
                console.log(error);
            });
        },
        getTableFields(){
            let vm = this;
            vm.loading.fields = true;
            vm.query.fields = [];
            vm.query.filters = [];
            vm.query.fields_to_show = [];
            let params = {
                api_token: this.$user.api_token,
                table: this.query.table,
                locale: this.$user.locale
            }

            axios.get(API_STATISTICS + "/fields", { params })
            .then((resp)=>{
                console.log(resp.data);
                vm.fields = resp.data;
                vm.loading.fields = false;
            })
            .catch((error)=>{
                vm.loading.fields = false;
                console.log(error);
            });
        },
        startQuery(){
            let vm = this;
            vm.loading.query = true;
            vm.page = 1;
            let params = {
                api_token: this.$user.api_token,
                locale: this.$user.locale,
                ...this.query,
            }

            let fields = [];
            let filters = [];
            
            this.query.fields.forEach((item, index) => {
                fields.push(item.id);

                let filter = vm.query.filters[index];
                if(filter == undefined)
                    filter = "";

                filters.push(filter);
            });

            params.fields = fields;
            params.filters = filters;

            console.log(JSON.stringify(params));

            axios.post(API_STATISTICS + "/fetch",  params)
            .then((resp)=>{
                console.log(resp.data);
                vm.query_result = resp.data;
                vm.currentWindow = 2;
                vm.loading.query = false;
            })
            .catch((error)=>{
                vm.loading.query = false;
                console.log(error);
            });
        },
        remove (table, key) {
            console.log("closing " + key);
            const index = table.indexOf(key);
            console.log("remove " + index);
            if (index >= 0) table.splice(index, 1)
        },
    }

}
</script>