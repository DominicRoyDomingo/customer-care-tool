<template>
  <v-row justify="center">
    <v-dialog v-model="dialog" persistent max-width="1000px">
      <v-card>
        <v-app-bar
          dark
          color="secondary"
        >
          <v-btn icon @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
          <v-toolbar-title>{{ $t('label.campaign_is_ready_for_sending')}}!</v-toolbar-title>
            
        <v-spacer></v-spacer>

        <v-chip
          color="success"
          class="subheading white--text"
          size="24"
        >
          {{ $t('label.campaign_email_saved')}}
        </v-chip>
        
        </v-app-bar>
        <v-card-text class="my-3">
          <v-container>
            <v-row style="min-height:65vh;">
              <v-col cols="12">
                  <div v-html="dynamicPart"></div>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions v-if="!canSend">
          <v-btn color="red darken-1" class="centered-fab"  style="margin-top:-7%;" dark fab @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
        <v-card-actions v-else>
          <v-btn color="red darken-1" style="margin-top:-7%;" text dark @click="dialog = false">
            <v-icon>mdi-close</v-icon> Close
          </v-btn>
          <v-spacer></v-spacer>
          <v-btn color="success darken-1" style="margin-top:-7%;" text dark :disabled="sending" :loading="sending" @click="sendCampaignEmail">
            <v-icon>mdi-mail</v-icon> Send Now
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<style>
  .my-frame {
    border: 1px solid #CCC;
    box-shadow: 0 0 3px 2x rgba(0,0,0,.3);
    margin: 20px auto;
    height: 200px;
    width: 95%;
  }

  .centered-fab {
    position: absolute;
    left: 46.5%;
  }
</style>

<script>
  import { mapActions, mapGetters } from "vuex";

Vue.component('i-frame', {
  render(h) {
    return  h('iframe', {
    	on: { load: this.renderChildren }
    })
  },
  beforeUpdate() {
    //freezing to prevent unnessessary Reactifiation of vNodes
    this.iApp.children = Object.freeze(this.$slots.default)
  },  
  methods: {
    renderChildren() {
      const children = this.$slots.default
      const body = this.$el.contentDocument.body      
      const el = document.createElement('DIV') // we will mount or nested app to this element
      body.appendChild(el)

      const iApp = new Vue({
      	name: 'iApp',
        //freezing to prevent unnessessary Reactifiation of vNodes
        data: { children: Object.freeze(children) }, 
        render(h) {
          return h('div', this.children)
        },
      })

      iApp.$mount(el) // mount into iframe

      this.iApp = iApp // cache instance for later updates
    }
  }
});

Vue.component('test-child', {
  template: `<div>
    <h3>{{ title }}</h3>
    <p>
      <slot/>
    </p>
  </div>`,
  props: ['title'],
  methods: {
    log:  _.debounce(function() {
      console.log('resize!')
    }, 200)
  },
  mounted() {
    this.$nextTick(() => {
    	const doc = this.$el.ownerDocument
      const win = doc.defaultView
      win.addEventListener('resize', this.log)
    })
  },
  beforeDestroy() {
  	const doc = this.$el.ownerDocument
    const win = doc.defaultView
    win.removeEventListener('resize', this.log)
  }
});


  export default {
    props: ["parent"],
    data: () => ({
      dialog: false,
      dynamicPart: "",
      show: false,
      canSend: false,
      sending: false
    }),
    methods: {
      ...mapActions("campaigns", ["send_campaign_email"]),

      sendCampaignEmail() {
        console.log("sendCampaignEmail");
          this.sending = true;
          
          let formData = new FormData();

          formData.append("api_token", this.$user.api_token);
          formData.append("id", this.parent.form.campaign_id);
          
          this.send_campaign_email(formData)
          .then(resp => {
            console.log(resp);
            console.log(resp.data);
            this.sending = false;
            this.$bvModal.hide("modal-send-campaign");
            if (resp.data == "campaign sent") {
              this.dialog = false;

              this.parent.loadItems();
              this.parent.makeToast(
                "success",
                this.$t('email_sent'),
                `${this.parent.form.campaign}` + this.$t('email_sent_customer')
              );

              this.$emit("on-send-complete");

              console.log("finished sending");
            }
            else{
              this.parent.makeToast(
                "error",
                this.$t('errors.error'),
                `${this.parent.form.campaign}` + this.$t('errors.email_not_sent')
              );

              this.parent.form.errors.record(error.response.data.errors);
              this.sending = false;
            }
          })
          .catch(error => {
            this.parent.form.errors.record(error.response.data.errors);
            this.sending = false;
          });
      },

    }
  }
</script>