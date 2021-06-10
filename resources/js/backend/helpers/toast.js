export default {
  methods: {
    makeToast(variant = null, title, message) {
      this.$bvToast.toast(message, {
        title: title,
        variant: variant,
        solid: true,
      });
    },
    makeNodeToast(variant = null, title, message) {
      // Pass the VNodes as an array for message and title
      this.$bvToast.toast([message], {
        title: [title],
        solid: true,
        variant: variant
      })
    },
    errorToast(title, message) {
      this.$bvToast.toast(message, {
        title: title,
        variant: 'danger',
        solid: true,
      });
    },
    deleteToast(name, records) {
      let message = `${name} ${this.$t("delete_record_message")} ${records} ${this.$t("records")}`;

      this.$bvToast.toast(message, {
        title: this.$t("delete_record"),
        variant: 'danger',
        solid: true,
      });
    },
    inusedToast(name) {
      let message = `${this.$t('unable_message1')} ${name} ${this.$t("unable_message2")}`;

      this.$bvToast.toast(message, {
        title: this.$t("inused_alert"),
        variant: 'danger',
        solid: true,
      });
    },
    storeToast(name, records) {
      let message = `${name} ${this.$t("created_message")} ${records}`;

      this.$bvToast.toast(message, {
        title: this.$t("new_record_created"),
        variant: 'success',
        solid: true,
      });
    },

    updateToast(id, records) {
      let message = `${this.$t("updated_message1")} ${records} ID: ${id} ${this.$t("updated_message2")}`;

      this.$bvToast.toast(message, {
        title: this.$t("record_updated"),
        variant: 'warning',
        solid: true,
      });
    },

    unlinkedToast(name, records) {
      let message = `${name} ${this.$t("alerts.unlinked_to_terms")} ${records}`;
      this.$bvToast.toast(message, {
        title: this.$t("unlink"),
        variant: 'danger',
        solid: true,
      });

    },

    linkedToast(name, records) {
      let message = `${name} ${this.$t("link_success")} ${records}`;
      this.$bvToast.toast(message, {
        title: this.$t("linked"),
        variant: 'success',
        solid: true,
      });
    },

    linkToast(name) {
      let message = `${name} ${this.$t("successfully_link")}`;
      this.$bvToast.toast(message, {
        title: this.$t("create_publishing_link"),
        variant: 'success',
        solid: true,
      });
    }
  },
};
