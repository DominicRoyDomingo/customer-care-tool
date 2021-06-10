export default {

  data() {
    return {
      isLinkCourseTerm: false,
      showing: {
        to: 0,
        from: 0,
        total: 0,
      },

      fields: [
        {
          key: "article_title",
          label: this.$t("table.title"),
          thClass: "text-left text-capitalize",
          thStyle: { width: "40%" },
          sortable: true,
        },

        {
          key: "publishing_item_type_articles",
          label: this.$t("type_of_publising_item"),
          thClass: "text-left text-capitalize",
          thStyle: { width: "40%" },
          sortable: true,
        },
        {
          key: "actions",
          label: this.$t("table.action"),
          thClass: "text-center",
          thStyle: { width: "10%" },
          tdClass: "text-center",
        },
      ],

      actions: [
        {
          value: "edit",
          title: this.$t("button.edit"),
          icon: "pencil-square",
          variant: "success",
        },
        {
          value: "notes",
          title: this.$t("label.add_notes"),
          icon: "file-earmark-plus",
          variant: "primary",
        },
        {
          value: "contenteditor",
          title: this.$t("label.content_editor"),
          icon: "file-earmark-plus",
          variant: "primary",
        },
        {
          value: "brand",
          title: this.$t("label.link_to_brand"),
          icon: "link45deg",
          variant: "primary",
        },
        {
          value: "term",
          title: "Link to " + this.$t("label.terminilogies"),
          icon: "link45deg",
          variant: "primary",
        },
      ],
    }
  },


  methods: {
    set_article_title(item) {
      switch (this.$i18n.locale) {
        case "en":
          return item.is_english ?? '';
        case "it":
          return item.is_italian ?? '';
        case "de":
          return item.is_german ?? '';
        case "ph-bis":
          return item.is_bisaya ?? '';
        case "ph-fil":
          return item.is_filipino ?? '';
      }
    },

    set_placeholder() {
      let title = this.$t('table.title').toUpperCase();

      if (this.articleForm.action === 'Update' && !this.articleForm.title) {
        title += ': No Translation Available';
      }

      return title;
    },

    set_item_search(data) {
      if (!data) {
        return "";
      }
      return data.map((x) => parseInt(x.id));
    },

    set_advance_search_params() {
      return {
        authors: this.set_item_search(this.form.author),
        terms: this.set_item_search(this.form.term),
        item_types: this.set_item_search(this.form.item_types),
        type_dates: this.form.type_dates ? this.form.type_dates.id : "",
        from: this.form.type_dates ? this.dateModel.startDate : "",
        to: this.form.type_dates ? this.dateModel.endDate : "",
      }
    }

  }
}