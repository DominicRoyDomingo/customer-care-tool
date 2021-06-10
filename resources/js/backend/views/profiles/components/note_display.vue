<template>
  <div class="note-display">
    <div class="row" v-if="is_editable">
      <div class="col-md-9">
        <div class="body-2 text--secondary">
          {{ $t("label.interaction_date") }}:
          <span class="text--disabled font-weight-light">
            {{ display_array[index].date_requested_display }}
          </span>
        </div>
      </div>
      <div class="col-md-3 text-right">
        <v-tooltip top>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              icon
              color="#f9dd9f"
              @click.prevent="parent.modalEditNote(index)"
              v-bind="attrs"
              v-on="on"
            >
              <v-icon>mdi-lead-pencil</v-icon>
            </v-btn>
          </template>
          <span>{{ $t("label.edit_note") }}</span>
        </v-tooltip>
      </div>
    </div>
    <div class="row" v-if="!is_editable">
      <div class="col-md-12">
        <div class="subheading font-weight-bold text--secondary">
          {{ $t("label.interaction_date") }}:
          {{ display_array[index].date_requested_display }}:
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div
          class="subheading font-weight-regular text--primary"
          v-html="note_display"
        ></div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="caption text--disabled text-right">
          {{ $t("label.added_by") }}:
          <span class="font-italic">{{
            display_array[index].added_by
          }}</span
          >, {{ display_array[index].date_added_display }}
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <hr />
      </div>
    </div>
  </div>
</template>

<style scoped></style>

<script>
export default {
  name: "note_display",
  props: ["root_parent", "parent", "display_array", "index", "is_editable"],
  computed: {
    note_display() {
      let vm = this;
      let display = vm.display_array[vm.index].note_display;
      display = display.replace(/(?:\r\n|\r|\n)/g, "<br>");
      return display;
    },
  },
};
</script>
