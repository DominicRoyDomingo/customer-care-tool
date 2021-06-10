<template>
  <div class="contact-display">
    <div class="row" v-if="is_editable">
      <div class="col-md-9">
        <div
          class="body-2 text--secondary"
          v-html="
            display_array[index].contact_type.contact_type_name
          "
        >
          :
        </div>
      </div>
      <div class="col-md-3 text-right">
        <v-tooltip top>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              icon
              color="#f9dd9f"
              @click.prevent="parent.modalEditContact(index)"
              v-bind="attrs"
              v-on="on"
            >
              <v-icon>mdi-lead-pencil</v-icon>
            </v-btn>
          </template>
          <span>{{ $t("label.edit_contact") }}</span>
        </v-tooltip>
      </div>
    </div>
    <div class="row" v-if="!is_editable">
      <div class="col-md-12">
        <div
          class="subheading font-weight-regular text--secondary"
          v-html="
            display_array[index].contact_type.contact_type_name
          "
        >
          :
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="subheading font-weight-regular font-italic text--primary ml-5">
          {{ display_array[index].contact_info_display }}
        </div>
        <hr />
      </div>
    </div>
  </div>
</template>

<style scoped></style>

<script>
export default {
  name: "contact_display",
  props: ["root_parent", "parent", "display_array", "index", "is_editable"],
};
</script>
