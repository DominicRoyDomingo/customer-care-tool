<template>
  <div class="note-div note-data">
    <div class="row no-gutters" v-if="is_deletable">
      <div class="col-md-11 text-left">
        <hr />
      </div>
      <div class="col-md-1 text-right">
        <v-btn fab dark small color="error" @click.prevent="deleteSpecializationDiv">
          <v-icon dark>mdi-close</v-icon>
        </v-btn>
      </div>
    </div>

    <div class="form-group">
      <label for="specialization">{{ $t("table.specialization") }}</label>
        <v-select
            name="name"
            label="profession_description"
            v-model="root_parent.form.specializations[index].profession"
            :options="root_parent.newSpecializations"
            id="specialization"
        >
            <template #list-header>
                <li style="margin-left:20px;" class="mb-2">
                    <b-link href="#" @click="openJobDescriptionModal">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        {{ $t("job_description_new_button") }}
                    </b-link>
                </li>
            </template>
        </v-select>
    </div>
  </div>
</template>

<style scoped></style>

<script>

export default {
  name: "specialization_div",
  props: ["root_parent", "index", "is_deletable"],

  data() {
    return {
      phyiscal_code_type: "",
      code: "",
    };
  },
  methods: {
    deleteSpecializationDiv: function() {
      let vm = this;

      $.confirm({
        title: this.$t("confirm_action"),
        content: vm.$t("label.delete_phsyical_code_confirmation"),
        type: "red",
        typeAnimated: true,
        buttons: {
          tryAgain: {
            text: this.$t("confirm"),
            btnClass: "btn-red",
            action: function() {
              vm.root_parent.deleteSpecializationDiv(vm.index);
            },
          },
          cancel: function() {},
        },
      });
    },

    openJobDescriptionModal() {
      // this.modal.isVisible = false;
      this.root_parent.$bvModal.hide(this.root_parent.modalId);

      this.root_parent.loadProfessions()
      // this.$this.modal.itemType.isVisible = true;
    },
  },
};
</script>
