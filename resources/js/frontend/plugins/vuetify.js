import Vue from "vue";
import Vuetify from "vuetify";
import "vuetify/dist/vuetify.css";
import "vuetify/src/styles/main.sass";
import "@mdi/font/css/materialdesignicons.css";
import minifyTheme from "minify-css-string";

Vue.use(Vuetify);

export default new Vuetify({
  icons: {
    iconfont: "mdi",
  },
  theme: {
    options: { minifyTheme },
    dark: true,
    themes: {
      dark: {
        primary: "#2196f3",
        secondary: "#03a9f4",
        anchor: "#4c71b8",
        accent: "#00bcd4",
        error: "#f44336",
        warning: "#ffc107",
        info: "#3f51b5",
        success: "#8bc34a",
      },
    },
  },
});
