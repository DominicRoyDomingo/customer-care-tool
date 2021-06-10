
import { getBrandsAndPlatformTypes} from "./../../../../api/content_item.js";
export default {
    data() {
      return {
          langs: [
          
              { id:'en', value: 'English' },
              
              { id:'it', value: 'Italian' },
              
              { id:'de', value: 'German' },
          
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
  
              $('.error').remove();
          
              $('#class, #status, #sequence').removeAttr('style')
            
            })
            
            .catch(_ => {});
        
        } else {
          
          this.form.reset();
          
          this.file = "";
  
          this.keep_open = false;
          
          this.$this.$bvModal.hide(this.$this.modalId);
        
        }
  
        this.keep_open = false;
      
      },
      
      openItemTypeModal(){

        // this.modal.isVisible = false;
        this.$this.$bvModal.hide(this.$this.modalId);
  
        this.$this.$bvModal.show("create-item-type");
        // this.$this.modal.itemType.isVisible = true;
  
      },

      openPlatformTypeModal(){

        getBrandsAndPlatformTypes().then(resp => {

          this.$this.brands = resp.brands;

          this.$this.platform_type = resp.platformTypes
          
          this.$this.$bvModal.hide(this.$this.modalId);
  
          this.$this.$bvModal.show("create-platform");
  
        });
  
      },
    },
  };
  