<template>
  <div class="note-div note-data">
    <div class="row no-gutters" v-if="is_deletable">
      <div class="col-md-11 text-left">
        <hr />
      </div>
      <div class="col-md-1 text-right">
        <v-btn fab dark small color="error" @click.prevent="deletePhysicalCode">
          <v-icon dark>mdi-close</v-icon>
        </v-btn>
      </div>
    </div>

    <div class="form-group">
      <label for="physical_code_type">{{ $t("label.physical_code_type") }}</label>
        <v-select
            name="name"
            label="name"
            v-model="root_parent.form.physical_codes[index].physical_code_type"
            :options="root_parent.physical_code_types"
            id="physical_code_type"
        >
            <template #list-header>
                <li style="margin-left:20px;" class="mb-2">
                    <b-link href="#" @click="openPhysicalCodeTypeModal">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        {{ $t("physical_code_type_new_button") }}
                    </b-link>
                </li>
            </template>
        </v-select>
    </div>

    <div class="form-group">
      <label for="code">{{ $t("label.code") }}</label>
      <b-form-input 
        v-model="root_parent.form.physical_codes[index].code" 
        placeholder="Code" 
        id="code"
      ></b-form-input>
    </div>
  </div>
</template>

<style scoped></style>

<script>

export default {
  name: "physical_code_div",
  props: ["root_parent", "index", "is_deletable"],

  data() {
    return {
      phyiscal_code_type: "",
      code: "",
    };
  },
  methods: {
    deletePhysicalCode: function() {
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
              vm.root_parent.deletePhysicalCode(vm.index);
            },
          },
          cancel: function() {},
        },
      });
    },

    openPhysicalCodeTypeModal() {
      // this.modal.isVisible = false;
      this.root_parent.$bvModal.hide(this.root_parent.modalId);

      this.root_parent.$bvModal.show("physical-code-type-add-modal");
      // this.$this.modal.itemType.isVisible = true;
    },
  },
};
</script>
