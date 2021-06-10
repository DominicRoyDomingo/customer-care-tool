<template>
  <div class="create">
    <b-modal
      id="terminology-details-modal"
      hide-footer
      hide-header
      size="xl"
      no-close-on-backdrop
      :title="$t('label.add')"
    >
      <v-app id="create__container">
        <v-card>
					<v-card-title class="title blue-grey lighten-4 text--secondary">
              <span v-html="name">
              </span>
              <v-spacer></v-spacer>
              <v-btn icon color="error lighten-2" @click="onClose">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-card-title>
						<v-card-text>

						</v-card-text>
						<b-table
							striped
							show-empty
							:empty-text="$t('no_record_found')"
							:fields="tableHeaders"
							:items="parent.linkedMedicalDetailsItem"
						>
							<template v-slot:cell(term)="data">
								<p
									class="mt-0 mb-1"
									v-html="data.item.name"
								/>
							</template>

							<template v-slot:cell(description)="data">
                <template v-if="data.item.descriptions.length != 0">
                  <p
                    class="mt-0 mb-1"
                    v-for="(description,index) in data.item.descriptions"
                    :key="index"
                    v-html="description.value != null ? `${description.term_description.description_name}: ${description.value}` : description.term_description.description_name"
                  >
                  </p>
                </template>
							</template>
							<template v-slot:cell(notes)="data">
                <template v-if="data.item.descriptions.length != 0">
                  <p
                    class="mt-0 mb-1"
                    v-for="(description,index) in data.item.descriptions"
                    :key="index"
                    v-html="description.base_name != '' ? description.note_name : '' " 
                  >
                  </p>
                </template>
							</template>
						</b-table>
        </v-card>
      </v-app>
    </b-modal>
  </div>
</template>

<script>

// import medicalMixin from "./../mixins/medicalMixin";

export default {
	props: ["name","parent"],
  data() {
    return {
			isLoading: true,
			btnloading: false,
			filter: "",
			currentPage: 1,
			perPage: 10,
      tableHeaders: [
        {
          key: "term",
          label: this.$t("term"),
          thClass: "text-left",
          thStyle: { width: "30%" },
          sortable: true,
        },
        {
          key: "description",
          label: this.$t("label.description"),
          thClass: "text-left",
          thStyle: { width: "30%" },
          sortable: true,
        },
        {
          key: "notes",
          label: this.$t("label.notes"),
          thClass: "text-left",
          thStyle: { width: "40%" },
          sortable: true,
        },
      ],
    };
  },
  methods: {
   	onClose() {
      this.$bvModal.hide("terminology-details-modal");
    },
	},
	
	computed: {
		totalRows() {
			return this.parent.linkedMedicalDetailsItem.length;
		},
	},
};
</script>
 