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

    selectedClipart: function(clipart){

      this.form.clipart = clipart

    },

    modalClose(done) {
      
      if (this.form.isDataEmpty()) {
        
        this.$confirm( this.$t( 'close_transaction_alert' ) , {
          
          confirmButtonText: "OK",
          
          cancelButtonText: this.$t( 'cancel' ),
          
          type: "error"
        
        })
          .then(resp => {
            
            this.form.reset();
            
            this.modal.isVisible = false;

            $('.error').remove();
        
            $('#content, #author_idea, #clipart').removeAttr('style')
          
          })
          
          .catch(_ => {});
      
      } else {
        
        this.form.reset();
        
        this.file = "";
        
        this.modal.isVisible = false;
      
      }
    
      this.keep_open = false;

    },

    errors() {
      $('.error').remove();
        
      $('#content, #author_idea, #clipart').removeAttr('style')
    }
    
  },
};
