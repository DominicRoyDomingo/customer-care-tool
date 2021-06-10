<template>
  <div class="note-div note-data">
    <div class="row no-gutters" v-if="is_deletable">
      <div class="col-md-11 text-left">
        <hr />
      </div>
      <div class="col-md-1 text-right">
        <v-btn fab dark small color="error" @click.prevent="deleteOtherInfoDiv">
          <v-icon dark>mdi-close</v-icon>
        </v-btn>
      </div>
    </div>

    <div class="form-group">
      <label for="other_info">{{ $t("label.other_info") }}</label>
        <v-select
            name="name"
            label="name"
            v-model="root_parent.form.other_infos[index].type"
            :options="root_parent.information_types"
            id="other_info"
        >
            <template #list-header>
                <li style="margin-left:20px;" class="mb-2">
                    <b-link href="#" @click="openInformationTypeModal">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        {{ $t("information_type_new_button") }}
                    </b-link>
                </li>
            </template>
        </v-select>
    </div>

    <div class="form-group">
      <label for="value">{{ $t("label.value") }}</label>
      <b-form-input 
        v-model="root_parent.form.other_infos[index].value" 
        placeholder="Value" 
        id="value"
      ></b-form-input>
    </div>
  </div>
</template>

<style scoped></style>

<script>

export default {
  name: "other_info_div",
  props: ["root_parent", "index", "is_deletable"],

  data() {
    return {
      phyiscal_code_type: "",
      code: "",
    };
  },
  methods: {
    deleteOtherInfoDiv: function() {
      let vm = this;

      $.confirm({
        title: this.$t("confirm_action"),
        content: vm.$t("label.delete_other_info_confirmation"),
        type: "red",
        typeAnimated: true,
        buttons: {
          tryAgain: {
            text: this.$t("confirm"),
            btnClass: "btn-red",
            action: function() {
              vm.root_parent.deleteOtherInfoDiv(vm.index);
            },
          },
          cancel: function() {},
        },
      });
    },

    openInformationTypeModal() {
      // this.modal.isVisible = false;
      this.root_parent.$bvModal.hide(this.root_parent.modalId);

      this.root_parent.$bvModal.show("information-type-add-modal");
      // this.$this.modal.itemType.isVisible = true;
    },
  },
};
</script>
