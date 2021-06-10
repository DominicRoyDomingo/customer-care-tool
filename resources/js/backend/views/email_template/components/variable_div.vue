<template>
  <v-app id="variable__container">
    <div class="variable-div variable-data">
      <div class="row no-gutters" v-if="is_deletable">
        <div class="col-md-11 text-left">
          <hr />
        </div>
        <div class="col-md-1 text-right">
          <v-btn fab dark small color="error" @click.prevent="deleteVariable">
            <v-icon dark>mdi-close</v-icon>
          </v-btn>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label for="variable_name">{{ $t("label.variable_name") }}</label>
            <input
              type="text"
              v-model="root_parent.form.variables[index].variable_name"
              class="form-control"
            />
            <small
              v-if="
                root_parent.form.errors.has(
                  'variables.' + index + '.variable_name'
                )
              "
              v-text="
                root_parent.form.errors.get(
                  'variables.' + index + '.variable_name'
                )
              "
              class="text-danger"
            />
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label for="variable_text">{{ $t("label.variable_text") }}</label>
            <input
              type="text"
              v-model="root_parent.form.variables[index].variable_text"
              class="form-control"
            />

            <small
              v-if="
                root_parent.form.errors.has(
                  'variables.' + index + '.variable_text'
                )
              "
              v-text="
                root_parent.form.errors.get(
                  'variables.' + index + '.variable_text'
                )
              "
              class="text-danger"
            />
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label :for="'variable_type_' + index">{{
              $t("label.variable_type")
            }}</label>
            <v-select
              v-model="root_parent.form.variables[index].variable_type"
              :id="'variable_type_' + index"
              :options="variable_type_options"
            >
            </v-select>
            <small
              v-if="
                root_parent.form.errors.has(
                  'variables.' + index + '.variable_type'
                )
              "
              v-text="
                root_parent.form.errors.get(
                  'variables.' + index + '.variable_type'
                )
              "
              class="text-danger"
            />
          </div>
        </div>
      </div>
    </div>
  </v-app>
</template>

<style scoped></style>

<script>
export default {
  name: "variable_div",
  props: ["root_parent", "parent", "index", "is_deletable"],
  data() {
    return {
      date_requested: "",
      variables: "",
      variable_type_options: ["Text", "Textarea", "Image"],
    };
  },
  methods: {
    deleteVariable: function() {
      let vm = this;

      $.confirm({
        title: this.$t("confirm_action"),
        content: vm.$t("label.delete_variable_confirmation"),
        type: "red",
        typeAnimated: true,
        buttons: {
          tryAgain: {
            text: this.$t("confirm"),
            btnClass: "btn-red",
            action: function() {
              vm.root_parent.deleteVariable(vm.index);
            },
          },
          cancel: function() {},
        },
      });
    },
  },
};
</script>
