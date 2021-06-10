<template>
  <div class="note-div note-data">
    <div class="row no-gutters" v-if="is_deletable">
      <div class="col-md-11 text-left">
        <hr />
      </div>
      <div class="col-md-1 text-right">
        <v-btn fab dark small color="error" @click.prevent="deleteAttachment">
          <v-icon dark>mdi-close</v-icon>
        </v-btn>
      </div>
    </div>
    <div class="form-group">
      <label for="logo">{{ $t("label.attachment") }}</label>
      <div class="row">
        <div class="col-sm-6">
          <b-form-file
            class="attachment-file"
            @change="onGetFile"
            accept=".png, .jpg, .jpeg, .pdf, .doc, .docx, .xls"
            plain
          ></b-form-file>
        </div>
        <div class="col-sm-6">
          <b-link
            class="attachment-link"
            v-if="root_parent.form.attachments_info[index].id != null"
            :href="root_parent.form.attachments_info[index].file_url"
            target="_blank"
            download
          >
            View this {{ file_info.type }} ({{
              root_parent.form.attachments_info[index].filename
            }})
          </b-link>
        </div>
      </div>
      <v-row>
        <v-col cols="12">
          <v-textarea
            name="input-7-1"
            v-model="root_parent.form.attachments_info[index].description"
            :label="$t('label.description')"
          >
          </v-textarea>
        </v-col>
        <v-col cols="12">
          <label :for="'document_type_for_attachment_' + index">{{
            $t("label.document_type_category")
          }}</label>
          <v-select
            @input="spreadData(document_type)"
            v-model="document_type"
            :id="'document_type_for_attachment_' + index"
            label="document_type_full_name"
            :options="root_parent.document_types"
          >
            <template #list-header>
              <li style="margin-left:20px;" class="mb-2">
                <b-link href="#" @click="root_parent.openAddTypeModal">
                  <i class="fa fa-plus" aria-hidden="true"></i>
                  {{ $t("label.new_document_type_category") }}
                </b-link>
              </li>
            </template>
          </v-select>
        </v-col>
        <v-col cols="12">
          <v-textarea
            name="input-7-1"
            v-model="root_parent.form.attachments_info[index].tracker_notes"
            :label="$t('label.tracker_notes')"
          >
          </v-textarea>
        </v-col>
      </v-row>
    </div>
  </div>
</template>

<script>
export default {
  name: "attachment_div",
  props: ["root_parent", "parent", "index", "is_deletable"],
  data() {
    return {
      date_requested: "",
      notes: "",
      document_type: {},
    };
  },

  mounted() {
    if (
      this.root_parent.form.attachments_info[this.index].document_type_id !=
      undefined
    ) {
      if (
        this.root_parent.form.attachments_info[this.index].document_type_id !=
        null
      ) {
        this.root_parent.document_types.forEach((doc_type) => {
          if (
            doc_type.id ==
            this.root_parent.form.attachments_info[this.index].document_type_id
          ) {
            this.document_type = doc_type;
            return false;
          }
        });
      }
    }
  },

  computed: {
    file_info() {
      let extension = this.root_parent.form.attachments_info[
        this.index
      ].filename
        .split(".")
        .pop();
      let type = "";

      switch (extension.toLowerCase()) {
        case "png":
        case "jpg":
        case "jpeg":
          type = "Image";
          break;
        case "pdf":
          type = "PDF";
          break;
        default:
          type = "Document";
          break;
      }

      return {
        extension: extension,
        type: type,
      };
    },
  },
  methods: {
    onGetFile: function(event) {
      this.root_parent.form.attachments[this.index] = event.target.files[0];
    },
    deleteAttachment: function() {
      let vm = this;

      $.confirm({
        title: this.$t("confirm_action"),
        content: vm.$t("label.delete_attachment_confirmation"),
        type: "red",
        typeAnimated: true,
        buttons: {
          tryAgain: {
            text: this.$t("confirm"),
            btnClass: "btn-red",
            action: function() {
              vm.root_parent.deleteAttachment(vm.index);
            },
          },
          cancel: function() {},
        },
      });
    },
    spreadData(document_type) {
      console.log("Spread");
      console.log(document_type);
      this.root_parent.form.attachments_info[this.index].document_category_id =
        document_type.document_category_id;
      this.root_parent.form.attachments_info[this.index].document_type_id =
        document_type.id;
      console.log("New Value");
      console.log(this.root_parent.form.attachments_info[this.index]);
    },
  },
};
</script>
