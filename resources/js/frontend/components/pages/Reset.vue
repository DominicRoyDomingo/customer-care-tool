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
          :src="require('../../static/images/reset.png')"
          :lazy-src="require('../../static/images/reset.png')"
          id="reset__image"
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
          <v-form id="reset__form" class="form__container">
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
                @keydown.enter="submitReset"
              ></v-text-field>
            </ValidationProvider>
            <div class="text-center">
              <v-btn
                rounded
                color="yellow darken-1"
                light
                block
                x-large
                id="index__button"
                @click="submitReset"
                >Send Password Reset Link</v-btn
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
    showPassword: false,
    checkbox: "1",
    csrf: document
      .querySelector('meta[name="csrf-token"]')
      .getAttribute("content"),
  }),
  components: {
    ValidationProvider,
    ValidationObserver,
  },
  methods: {
    submitReset() {
      this.$refs.observer.validate().then((formIsValid) => {
        if (formIsValid) {
          axios
            .post("/password/email", {
              _token: this.csrf,
              email: this.email,
            })
            .then((response) => {
              if (response.status === 200) {
                this.snackbarColor = "success";
                this.snackbarMessage =
                  "We've sent you the details on how to reset your password.";
                this.snackbar = true;
              }
            })
            .catch((error) => {
              this.snackbarColor = "error";
              this.snackbarMessage = "";

              if (!!error.response.data.errors.email)
                error.response.data.errors.email.map((error) => {
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
