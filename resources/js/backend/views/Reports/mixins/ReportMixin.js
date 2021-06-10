import toast from "../../../helpers/toast";

export default {
   mixins: [toast],
   data() {
      return {
         isLoadingStat: true,
         all: {
            statistics_table_list: []
         },

         params: {
            api_token: this.$user.api_token,
            brand_id: this.$brand.id
         },

         cardBox: [
            {
               title: 'Average Entries',
               name: 'average_entries'
            },
            {
               title: 'Highest Entries',
               name: 'highest_entries'
            },
            {
               title: 'Total Entries',
               name: 'total_entries'
            },
         ]
      }
   },

   mounted() {
   },
   methods: {
      local_string(value) {
         const options = {
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
         };
         const d = new Date(value);
         return d.toLocaleString("en", options);
      },

      start_of_week() {
         let date = new Date();
         let day = date.getDay(),
            diff = date.getDate() - day + (day == 0 ? -6 : 0); // adjust when day is sunday
         return new Date(date.setDate(diff))
      },

      end_of_week() {
         let date = new Date();
         let lastday = date.getDate() - (date.getDay() - 1) + 5;
         return new Date(date.setDate(lastday));
      },

      load_statistic_table() {
         let params = { ...this.params };
         axios
            .get("/api/select/statistics_table/all", { params })
            .then((res) => {
               this.all.statistics_table_list = res.data;
            })
            .catch((error) => {
               console.log(error);
            }).finally(() => {
               this.isLoadingStat = false
            });
      },

      push_route(params) {
         this.$router.push({
            query: {
               module: params.id,
               startDate: params.startDate,
               endDate: params.endDate,
            },
         });
      },

      set_item(data) {
         if (!data) {
            return "";
         }
         return data.map((x) => {
            return {
               id: parseInt(x.id),
               term_type_name: x.base_name,
               description_name: x.base_name,
               // term_type_name: x.term_type_name,
               // description_name: x.description_name,
            }
         });
      },
   },
}