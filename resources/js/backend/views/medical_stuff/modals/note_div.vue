<template>
  <div class="note-div note-data">
    <div class="row no-gutters" v-if="is_deletable">
      <div class="col-md-11 text-left">
        <hr />
      </div>
      <div class="col-md-1 text-right">
        <v-btn fab dark small color="error" @click.prevent="deleteNote">
          <v-icon dark>mdi-close</v-icon>
        </v-btn>
      </div>
    </div>

    <div class="form-group">
      <label for="date_requested" >{{ $t("date_of_notes") }}</label>
      <datepicker
        input-class="form-control bg-white"
        v-model="root_parent.form.notes[index].date_requested"
        @input="changedDateRequested"
      ></datepicker>
    </div>

    <div class="form-group">
      <label for="note">{{ $t("label.notes") }}</label>
      <textarea
        v-model="root_parent.form.notes[index].notes"
        id="note"
        class="form-control"
        rows="5"
      ></textarea>
    </div>
  </div>
</template>

<style scoped></style>

<script>
import Datepicker from "vuejs-datepicker";

export default {
  name: "note_div",
  props: ["root_parent", "parent", "index", "is_deletable"],
  components: {
    Datepicker,
  },
  data() {
    return {
      date_requested: "",
      notes: "",
    };
  },
  methods: {
    deleteNote: function() {
      let vm = this;

      $.confirm({
        title: this.$t("confirm_action"),
        content: vm.$t("label.delete_note_confirmation"),
        type: "red",
        typeAnimated: true,
        buttons: {
          tryAgain: {
            text: this.$t("confirm"),
            btnClass: "btn-red",
            action: function() {
              vm.root_parent.deleteNote(vm.index);
            },
          },
          cancel: function() {},
        },
      });
    },
    changedDateRequested: function() {
      let vm = this;
      let date_requested = vm.root_parent.form.notes[vm.index].date_requested;
      var date_object = new Date(date_requested);
      vm.root_parent.form.notes[vm.index].date_requested = formatDateToYMD(
        date_object
      );
    },
  },
};
</script>
