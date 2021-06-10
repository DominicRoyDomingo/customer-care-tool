export default {
   data() {
      return {
         total_rows: 0,

         showing: {
            to: 0,
            from: 0,
            total: 0,
         },

      }
   },

   computed: {
      termDefaultParams() {
         return {
            api_token: this.$user.api_token,
            locale: this.$i18n.locale,
            brand_id: this.$brand.id,
         }
      }
   },

   methods: {

      view_item_name(data, type) {
         let name = "";
         switch (this.$i18n.locale) {
            case "en":
               name = data.is_english;
               break;
            case "de":
               name = data.is_german;
               break;
            case "it":
               name = data.is_italian;
               break;
            case "ph-fil":
               name = data.is_filipino;
               break;
            case "ph-bis":
               name = data.is_bisaya;
               break;
         }

         if (type === 'type') {
            return name ? name : data.term_type_name;
         }

         if (type === 'spec') {
            return name ? name : data.description_name;
         }

         if (type === 'item_type') {
            return name ? name : data.item_type_name;
         }

      },

      get_item_array(data, type) {

         if (!data) {
            return null
         }

         let arrrayItems = []

         data.forEach(ele => {

            let data = {
               id: ele.id,
               is_loading: ele.is_loading,
               base_language: ele.base_language,
               has_translation: ele.has_translation,
            }

            if (type === 'type') {
               data = {
                  ...data,
                  is_service: ele.is_service,
                  name: ele.name,
                  on_select_term_type_name: ele.on_select_term_type_name,
                  term_type_name: ele.term_type_name,
               }

            } else {
               data = {
                  ...data,
                  description: ele.description,
                  description_name: ele.description_name,
                  job_profession_id: ele.job_profession_id,
                  on_select_description_name: ele.on_select_description_name,
                  select_description_name: ele.select_description_name,
               }
            }

            arrrayItems.push(this.set_item_array(data, ele));

         });

         return arrrayItems;

      },

      set_item_array(data, item) {
         let object = {}
         switch (this.$i18n.locale) {
            case 'en':
               object = {
                  ...data,
                  base_name: item.is_english ?? item.base_name
               }
               break;
            case 'it':
               object = {
                  ...data,
                  base_name: item.is_italian ?? item.base_name
               }
               break;
            case 'de':
               object = {
                  ...data,
                  base_name: item.is_german ?? item.base_name
               }
               break;
            case 'ph-fil':
               object = {
                  ...data,
                  base_name: item.is_filipino ?? item.base_name
               }
               break;
            case 'ph-bis':
               object = {
                  ...data,
                  base_name: item.is_bisaya ?? item.base_name
               }
               break;
         }

         return object;
      },


   }
}