<template>
  <span>
    <div v-for="(typeF, typeI) in parent.parent.typedatesForm" :key="typeI">
      <div class="row mb-0">
        <div class="col-md-11">
          <hr />
        </div>
        <div class="col-md-1">
          <v-btn
            fab
            small
            color="error"
            class="bg-danger text-white"
            @click.prevent="onRemoveTypeDate(typeI)"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </div>
      </div>
      <div class="form-group">
        <label for="type_date" class="text-uppercase">
          {{ $t("label.type_of_date") }}
        </label>
        <v-select
          id="type_date"
          :options="parent.typedates"
          v-model="typeF.name"
          name="type_date"
          label="base_name"
          required
        >
          <template #list-header>
            <li style="margin-left: 20px" class="mb-2">
              <b-link
                v-b-tooltip.hover
                :title="`${$t('label.new')} ${$t('label.type_of_date')}`"
                href="javascript:;"
                @click="parent.showChildModal('typedate-modal')"
              >
                <i class="fa fa-plus" aria-hidden="true"></i>
                {{ $t("label.type_of_date") }}
              </b-link>
            </li>
          </template>

          <template #option="{ on_select_type_date_name }">
            <span v-html="on_select_type_date_name" />
          </template>
        </v-select>
      </div>
      <div class="form-group">
        <label for="type_date" class="text-uppercase">
          {{ $t("date") }}
        </label>
        <!-- <input type="date" class="form-control" v-model="typeF.date" /> -->
        <datepicker
          input-class="form-control bg-white"
          v-model="typeF.date"
          :format="`dd MMM yyyy`"
        ></datepicker>
        <!-- <b-form-datepicker
          id="datepicker-sm"
          :locale="$i18n.locale"
          v-model="typeF.date"
          :placeholder="`${$t('button.publish')} ${$t('date')}`"
        /> -->
      </div>
    </div>
  </span>
</template>

<script>
import Datepicker from "vuejs-datepicker";
export default {
  props: ["parent"],
  components: {
    Datepicker,
  },
  methods: {
    // customFormatter(date) {
    //   return moment(date).format('');
    // },

    onRemoveTypeDate(index) {
      let vm = this;
      vm.parent.parent.typedatesForm.splice(index, 1);
      if (vm.parent.parent.typedatesForm.length < 1) {
        vm.parent.parent.isAddTypeDate = false;
      }
      // $.confirm({
      //   title: this.$t("confirm_action"),
      //   content: vm.$t("label.delete_type_date_confirmation"),
      //   type: "red",
      //   typeAnimated: true,
      //   buttons: {
      //     tryAgain: {
      //       text: this.$t("confirm"),
      //       btnClass: "btn-red",
      //       action: function () {
      //         vm.parent.parent.typedatesForm.splice(index, 1);

      //         if (vm.parent.parent.typedatesForm.length < 1) {
      //           vm.parent.parent.isAddTypeDate = false;
      //         }
      //       },
      //     },
      //     cancel: function () {},
      //   },
      // });
    },
  },
};
</script>

 