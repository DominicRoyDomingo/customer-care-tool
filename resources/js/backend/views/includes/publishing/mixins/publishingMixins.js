import { getBrandsAndPlatformTypes } from "./../../../../api/publishing.js";
export default {
  data() {
    return {
        langs: [
        
            { id:'en', value: 'English' },
            
            { id:'it', value: 'Italian' },
            
            { id:'de', value: 'German' },

            { id:'ph-fil', value: 'Filipino' },

            { id:'ph-bis', value: 'Bisayan' },
        
        ],

        formGroups: [],

        submitted: false,

    };
  },

  methods: {
    modalClose(done) {

      if (this.form.isDataEmpty()) {
        
        this.$confirm( this.$t( 'close_transaction_alert' ) , {
          
          confirmButtonText: "OK",
          
          cancelButtonText: this.$t( 'cancel' ),
          
          type: "error"
        
        })
          .then(resp => {
            
            this.form.reset();
            
            this.$this.$bvModal.hide(this.$this.modalId);

            this.modal.isVisible = false;

            this.errors()
          
          })
          
          .catch(_ => {});
      
      } else {
        
        this.form.reset();
        
        this.file = "";
        
        this.$this.$bvModal.hide(this.$this.modalId);

        this.modal.isVisible = false;
      
      }
    
      this.keep_open = false;

    },

    openPlatformTypeModal(){

      getBrandsAndPlatformTypes().then(resp => {
        
        this.$this.brands = resp.brands;
          
        this.$this.platform_type = resp.platformTypes;

        this.$this.$bvModal.hide(this.$this.modalId);

        this.$this.$bvModal.show("create-platform");

      });

      

    },

    errors() {

      $('.error').remove();
        
      $('#name, #item, #author, #platform').removeAttr('style')

    }

  },
};
