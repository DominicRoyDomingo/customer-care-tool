import Vue from "vue";
import Vuetify from "vuetify";
import "vuetify/dist/vuetify.css";
import "vuetify/src/styles/main.sass";
import "@mdi/font/css/materialdesignicons.css";

Vue.use(Vuetify);

export default new Vuetify({
  icons: {
    iconfont: "mdi",
  },
});
