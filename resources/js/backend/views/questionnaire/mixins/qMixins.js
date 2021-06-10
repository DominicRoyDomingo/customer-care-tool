import toast from "../../../helpers/toast";
export default {
  mixins: [toast],

  data() {
    return {
      dParams: {
        brand_id: this.$brand.id,
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
      },

      showEntriesOpt: [
        { value: 10, text: "10" },
        { value: 30, text: "30" },
        { value: 50, text: "50" },
        { value: 100, text: "100" },
      ],

      languageOptions: [
        { value: "en", text: "English" },
        { value: "de", text: "German" },
        { value: "it", text: "Italian" },
        { value: "ph-fil", text: "Filipino" },
        { value: "ph-bis", text: "Visayan" },
      ],

      actions: [
        { value: "edit", title: this.$t("button.edit"), icon: "pencil-square", variant: "success" },
        { value: "organize", title: this.$t("organize") + ' ' + this.$t("questions"), icon: "sort-numeric-up", variant: "primary" },
        { value: "delete", title: this.$t("button.delete"), icon: "trash-fill", variant: "danger" },
      ],

      noTranslationsOptions: [
        { value: 'all', text: "All", disabled: true },
        { value: "en", text: "No English Translation" },
        { value: "de", text: "No German Translation" },
        { value: "it", text: "No Italian Translation" },
        { value: "ph-fil", text: "No Filipino Translation" },
        { value: "ph-bis", text: "No Visayan Translation" },
      ],

      fields: [
        {
          key: "question_name",
          label: this.$t("questionnaires").toUpperCase(),
          thClass: "text-left text-capitalize",
          thStyle: { width: "50%" },
          sortable: true,
        },
        {
          key: "created_at",
          label: this.$t("table.created_at").toUpperCase(),
          thClass: "text-left text-capitalize",
          thStyle: { width: "30%" },
          sortable: true,
        },
        {
          key: "actions",
          label: this.$t("table.action").toUpperCase(),
          thClass: "text-center",
          thStyle: { width: "20%" },
          tdClass: "text-center",
        },
      ],
    }
  },

  methods: {
    delete_confirm(name, records) {
      let message = `${this.$t("question_record_delete")} ${name} ${this.$t("from")} ${records} ${this.$t("records")}?`;
      return new Promise((resolve, reject) => {
        $.confirm({
          title: this.$t("confirmation_record_delete"),
          content: message,
          type: "red",
          typeAnimated: true,
          columnClass: "medium",
          buttons: {
            yes: {
              text: this.$t("yes"),
              btnClass:
                "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
              action: function (value) {
                resolve(value);
              },
            },
            no: {
              text: this.$t("no"),
              btnClass:
                "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
              action: function () {
              },
            },
          },
        });
      });
    },
    set_sequence_json(items) {
      let arr = [];
      items.forEach((ele, index) => {
        arr.push({
          id: ele.pivot.id,
          question_id: ele.pivot.question_id,
          sequence: index + 1,
        });
      });
      return JSON.stringify(arr);
    },
    array_index(items, id) {
      const i = items.map((res) => res.id).indexOf(id);
      return i;
    },
  }
}