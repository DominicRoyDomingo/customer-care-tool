import { mapActions, mapGetters } from "vuex";

export default {
   data() {
      return {
        algolia:{
            showSyncButton: false,
            syncType: '',
            isSyncing: false,
            class: 'Course',
            table: 'medical_articles',
            summary: [],
        },
      }
   },

   computed: {

   },

   watch: {

   },

   methods: {
    ...mapActions("algolia", [
        "post_courses_sync",
        "get_algolia_summary"
    ]),

    filterAdvanceSearchType(){
      let array = this.searchItems.item_types;
      const courses = ["course", "courses", "corso", "corsi"];
      
      this.algolia.showSyncButton = array.some(r => courses.includes(r.toLowerCase()))
    },

    syncToAlgolia() {
        let formData = new FormData();
        this.algolia.isSyncing = true;
        formData.append("brand_id", this.$brand.id);
        formData.append("api_token", this.$user.api_token);

        this.post_courses_sync(formData)
          .then((resp) => {
            if (resp) {
              let data = resp.data;
              this.algolia.isSyncing = false;
              this.loadAlgoliaSummary(this.table);
              let message = {
                create:
                  `${this.$t("label.courses")} ${this.$t("toasts.synced_to_algolia")}.
                  Added ${data.added} Courses and Updated ${data.updated} courses on Algolia. 
                  `,
                title: {
                  create: `${this.$t("label.course").toUpperCase()} ${this.$t("synced")}. `,
                },
              };

              this.makeToast(
                "success",
                message.title.create,
                message.create
              );
              
              // this.algoliaSummary.synced = resp.data.synced;
              // this.algoliaSummary.unsynced = resp.data.unsynced;
  
  
              // this.$bvToast.show('algolia-toast')
            }
          })
          .catch((error) => {
            console.log(error)
            let errors = error.response.data.errors;
          });
    },
 
    async loadAlgoliaSummary() {
      let params = {
        api_token: this.$user.api_token,
        brand_id:  this.$brand.id,
        lang: this.form.language,
        class: this.algolia.class,
        table: this.algolia.table,
      };

      this.get_algolia_summary(params).then((data) => {
        this.algolia.summary = data;
      });
    },
  }
}