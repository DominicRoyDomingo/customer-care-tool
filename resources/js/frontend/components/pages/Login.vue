<template>
  <ValidationObserver ref="observer">
    <v-snackbar
      v-model="snackbar"
      :color="snackbarColor"
      top
      class="snackbar__container"
    >
      <span v-html="snackbarMessage"></span>
      <v-btn color="white" text @click="snackbar = false">
        Close
      </v-btn>
    </v-snackbar>
    <v-row class="pre-auth-form__container" align="center" justify="center">
      <v-col>
        <v-img
          :src="require('../../static/images/login-fg.png')"
          id="login__image-fg"
        ></v-img>
        <v-img
          :src="require('../../static/images/login-bg.png')"
          :lazy-src="require('../../static/images/login-bg.png')"
          id="login__image"
        >
          <template v-slot:placeholder>
            <v-row class="fill-height ma-0" align="center" justify="center">
              <v-progress-circular
                indeterminate
                color="yellow darken-1"
              ></v-progress-circular>
            </v-row>
          </template>
        </v-img>
      </v-col>
      <v-col class="pre-auth-form__container">
        <v-card
          dark
          color="rgba(0,0,0,0.5)"
          class="pre-auth-form__card"
          elevation="4"
        >
          <v-form id="login__form">
            <v-text-field
              type="hidden"
              name="_token"
              :value="csrf"
              class="form__hidden-field"
            ></v-text-field>
            <ValidationProvider
              v-slot="{ errors }"
              name="E-mail"
              rules="email|required|max:191"
            >
              <v-text-field
                v-model="email"
                label="E-mail"
                name="email"
                id="email"
                append-icon="mdi-at"
                autocomplete="username"
                outlined
                rounded
                required
                dark
                filled
                :counter="191"
                :error-messages="errors"
                class="form__input-field"
                @keydown.enter="submitLogin"
              ></v-text-field>
            </ValidationProvider>
            <ValidationProvider
              vid="password"
              v-slot="{ errors }"
              name="Password"
              rules="required|min:6"
            >
              <v-text-field
                v-model="password"
                ref="password"
                :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                :type="showPassword ? 'text' : 'password'"
                label="Password"
                hint="At least 6 characters"
                name="password"
                id="password"
                autocomplete="password-current"
                counter
                outlined
                rounded
                required
                dark
                filled
                :error-messages="errors"
                class="form__input-field"
                @click:append="showPassword = !showPassword"
                @keydown.enter="submitLogin"
              ></v-text-field>
            </ValidationProvider>
            <v-row>
              <v-col>
                <v-checkbox
                  v-model="checkbox"
                  label="Remember Me"
                  color="yellow darken-1"
                  class="login__remember"
                  name="remember"
                  id="remember"
                  value="1"
                  dark
                ></v-checkbox>
              </v-col>
              <v-col class="text-right">
                <v-btn text color="primary" href="/password/reset"
                  >Forgot Password?</v-btn
                >
              </v-col>
            </v-row>
            <div class="text-center">
              <v-btn
                rounded
                color="yellow darken-1"
                light
                block
                x-large
                id="index__button"
                @click="submitLogin"
                >Login</v-btn
              >
            </div>
          </v-form>
        </v-card>
      </v-col>
    </v-row>
  </ValidationObserver>
</template>

<script>
require("../../plugins/vee-validate.js");
import { ValidationObserver, ValidationProvider } from "vee-validate";

export default {
  data: () => ({
    snackbar: false,
    snackbarMessage: "",
    snackbarColor: "primary",
    email: "",
    password: "",
    lang: "",
    showPassword: false,
    selectLangModal: false,
    checkbox: "1",
    csrf: document
      .querySelector('meta[name="csrf-token"]')
      .getAttribute("content"),
  }),
  components: {
    ValidationProvider,
    ValidationObserver,
    // SelectLanguage,
  },
  methods: {
    submitLogin() {
      this.$refs.observer.validate().then((formIsValid) => {
        
        if (formIsValid) {
          axios
            .post("/login", {
              _token: this.csrf,
              email: this.email,
              password: this.password,
              remember: this.checkbox,
            })
            .then((response) => {
              if (response.status === 200) {
                this.snackbarColor = "success";
                this.snackbarMessage = "Authentication success. Logging in...";
                this.snackbar = true;
                // this.selectLangModal = true;

                axios
                  .get("/user/location")
                  .then((location) => {
                    if (location.data.countryName == "Italy") {
                      this.lang = "it";
                    } else if (location.data.countryName == "Germany") {
                      this.lang = "de";
                    } else {
                      this.lang = "en";
                    }
                    this.$session.set("local_lang", this.lang);
                    window.location.href = "/lang/" + this.lang;
                  })
                  .catch((_) => {
                    // sets default lang to en
                    this.$session.set("local_lang", "en");
                    window.location.href = "/lang/" + "en";
                  });
              }
            })
            .catch((error) => {
              console.log(error)
              this.snackbarColor = "error";
              this.snackbarMessage = "";

              if (!!error.response.data.errors.email)
                error.response.data.errors.email.map((error) => {
                  this.snackbarMessage =
                    this.snackbarMessage + "* " + error + "<br />";
                });

              if (!!error.response.data.errors.password)
                error.response.data.errors.password.map((error) => {
                  this.snackbarMessage =
                    this.snackbarMessage + "* " + error + "<br />";
                });

              this.snackbar = true;
            });
        }
      });
    },
  },
};
</script>
