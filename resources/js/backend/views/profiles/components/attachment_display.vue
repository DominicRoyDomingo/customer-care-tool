<template>
  <div class="note-display">
    <div class="row" v-if="is_editable">
      <div class="col-md-9">
        <div class="body-2 text--secondary">
          {{ $t('label.view_this') }} {{ file_info.type }}:<br>
          <b-link
            class="attachment-link"
            v-if="display_array[index].id != null"
            :href="display_array[index].file_url"
            target="_blank"
            download
            >
            {{ display_array[index].filename }}
            </b-link>
            <br>
          <span class="nowrap">
            Description:
            <br>{{ display_array[index].description }}
          </span>
        </div>
      </div>
      <div class="col-md-3 text-right">
        <v-tooltip top>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              icon
              color="#f9dd9f"
              @click.prevent="parent.modalEditAttachment(index)"
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
          {{ $t('label.view_this') }} {{ file_info.type }}: 
          <b-link
            class="attachment-link"
            v-if="display_array[index].id != null"
            :href="display_array[index].file_url"
            target="_blank"
            download
            >
            {{ display_array[index].filename }}
            </b-link>
        </div>
      </div>
    </div>
  </div>
</template>


<style scoped>
.nowrap {
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
}
</style>

<script>
export default {
  name: "attachment_display",
  props: ["root_parent", "parent", "display_array", "index", "is_editable"],
  computed: {
    file_info() {
      let vm = this;
      let extension = vm.display_array[vm.index].filename.split('.').pop();
      let type = "";

      switch(extension.toLowerCase()){
        case "png":
        case "jpg":
        case "jpeg":
          type = "Image";
        break;
        case "pdf":
          type = "PDF";
        break;
        default:
          type = "Document"
        break;
      }

      return {
        extension: extension,
        type: type
      }
    },
  },
};
</script>
