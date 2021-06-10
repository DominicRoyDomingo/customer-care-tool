<template>
  <div class="job-div job-data">
    <div class="row no-gutters" v-if="is_deletable">
      <div class="col-md-11 text-left">
        <hr />
      </div>
      <div class="col-md-1 text-right">
        <v-btn fab dark small color="error" @click.prevent="deleteJob">
          <v-icon dark>mdi-close</v-icon>
        </v-btn>
      </div>
    </div>

    <div class="form-group">
      <label>{{ $t("label.job_category") }}</label>
      <v-select
        name="category"
        label="category_name"
        v-model="root_parent.form.jobs[index].job_category_id"
        :reduce="(category) => category.id"
        @input="resetProfession()"
        :options="root_parent.categories"
      >
        <template #list-header>
          <li style="margin-left:20px;" class="mb-2">
            <b-link href="#" @click="root_parent.modalAddNewCategory">
              <i class="fa fa-plus" aria-hidden="true"></i>
              {{ $t("label.new_category") }}
            </b-link>
          </li>
        </template>
      </v-select>
      <small
        v-if="root_parent.form.errors.has('jobs.' + index + '.job_category_id')"
        v-text="
          root_parent.form.errors.get('jobs.' + index + '.job_category_id')
        "
        class="text-danger"
      />
    </div>

    <div
      class="form-group"
      v-show="root_parent.form.jobs[index].job_category_id != null"
    >
      <label>{{ $t("label.profession") }}</label>
      <v-select
        name="category"
        label="profession_name"
        v-model="root_parent.form.jobs[index].job_profession_id"
        :reduce="(profession) => profession.id"
        @input="resetDescription()"
        :options="job_professions"
      >
        <template #list-header>
          <li style="margin-left:20px;" class="mb-2">
            <b-link href="#" @click="root_parent.modalAddNewProfession">
              <i class="fa fa-plus" aria-hidden="true"></i>
              {{ $t("label.new_profession") }}
            </b-link>
          </li>
        </template>
      </v-select>
    </div>

    <div
      class="form-group"
      v-show="root_parent.form.jobs[index].job_profession_id != null"
    >
      <label>{{ $t("label.specialization") }}</label>
      <v-select
        name="category"
        label="description_name"
        v-model="root_parent.form.jobs[index].job_description_id"
        :reduce="(description) => description.id"
        :options="job_descriptions"
      >
        <template #list-header>
          <li style="margin-left:20px;" class="mb-2">
            <b-link href="#" @click="root_parent.modalAddNewDescription">
              <i class="fa fa-plus" aria-hidden="true"></i>
              {{ $t("new_specialization") }}
            </b-link>
          </li>
        </template>
      </v-select>
    </div>

    <br />
  </div>
</template>

<style scoped></style>

<script>
import axios from "axios";
import { API_JOB } from "./../../../common/API.service";

export default {
  name: "job_div",
  props: ["root_parent", "parent", "index", "is_deletable"],
  data() {
    return {
      job_categories: [],
      job_professions: [],
      job_descriptions: [],
    };
  },
  mounted() {
    this.syncJob();
  },
  methods: {
    loadCategories() {
      let vm = this;
      vm.job.job_profession_id = null;
      vm.job.job_description_id = null;

      vm.fetchFilteredJobs("category", "").then((data) => {
        vm.job_categories = data;
      });
    },
    loadProfessions() {
      let vm = this;
      let job_category_id = vm.root_parent.form.jobs[vm.index].job_category_id;
      if (job_category_id != null || job_category_id != undefined)
        vm.fetchFilteredJobs("profession", job_category_id).then((data) => {
          vm.job_professions = data;
        });
    },
    loadDescriptions() {
      let vm = this;
      let job_profession_id =
        vm.root_parent.form.jobs[vm.index].job_profession_id;

      if (job_profession_id != null || job_profession_id != undefined)
        vm.fetchFilteredJobs("description", job_profession_id).then((data) => {
          vm.job_descriptions = data;
        });
    },
    resetProfession() {
      let vm = this;
      vm.root_parent.form.jobs[vm.index].job_profession_id = null;
      vm.root_parent.form.jobs[vm.index].job_description_id = null;
      vm.loadProfessions();
    },
    resetDescription() {
      let vm = this;
      vm.root_parent.form.jobs[vm.index].job_description_id = null;
      vm.loadDescriptions();
    },
    fetchFilteredJobs(type, filter_id) {
      let vm = this;

      let params = {
        api_token: vm.$user.api_token,
        locale: vm.$user.locale,
        type: type,
        filter_id: filter_id,
      };

      return axios
        .get(API_JOB + "/get_filtered", { params })
        .then(function(response) {
          return response.data;
        })
        .catch(function(error) {
          return null;
        });
    },
    syncJob() {
      let vm = this;

      /*
          vm.fetchFilteredJobs("profession", vm.root_parent.form.jobs[vm.index].job_category_id).then(data => {
              vm.job_professions = data;
          });
        */

      vm.fetchFilteredJobs(
        "profession",
        vm.root_parent.form.jobs[vm.index].job_category_id
      ).then((data) => {
        vm.job_professions = data;
      });

      vm.fetchFilteredJobs(
        "description",
        vm.root_parent.form.jobs[vm.index].job_profession_id
      ).then((data) => {
        vm.job_descriptions = data;
      });
    },
    deleteJob() {
      let vm = this;

      $.confirm({
        title: this.$t("confirm_action"),
        content: vm.$t("label.delete_job_confirmation"),
        type: "red",
        typeAnimated: true,
        buttons: {
          tryAgain: {
            text: this.$t("confirm"),
            btnClass: "btn-red",
            action: function() {
              vm.root_parent.deleteJob(vm.index);
              vm.$nextTick(() => {
                vm.root_parent.$refs.profileModal.syncJobs();
              });
            },
          },
          cancel: function() {},
        },
      });
    },
  },
};
</script>
