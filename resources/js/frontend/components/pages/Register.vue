<template>
  <div>
    <v-snackbar
        v-if="createOrgModalSnackbar == false"
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
    <ValidationObserver ref="observer" v-if="show">
      <v-row class="pre-auth-form__container" align="center" justify="center">
        <v-col class="pre-auth-form__container">
          <v-card
            dark
            color="rgba(0,0,0,0.5)"
            class="pre-auth-form__card"
            elevation="4"
          >
            <v-form id="register__form" ref="form">
              <v-text-field
                type="hidden"
                name="_token"
                :value="csrf"
                class="form__hidden-field"
              ></v-text-field>
              <v-row>
                <v-col>
                  <ValidationProvider
                    v-slot="{ errors }"
                    name="First Name"
                    rules="required|max:191"
                  >
                    <v-text-field
                      v-model="firstName"
                      label="First Name"
                      name="first_name"
                      id="first_name"
                      append-icon="mdi-account-circle"
                      outlined
                      rounded
                      required
                      dark
                      filled
                      :counter="191"
                      :error-messages="errors"
                      class="form__input-field"
                      @keydown.enter="submitRegister"
                    ></v-text-field>
                  </ValidationProvider>
                </v-col>
                <v-col>
                  <ValidationProvider
                    v-slot="{ errors }"
                    name="Last Name"
                    rules="required|max:191"
                  >
                    <v-text-field
                      v-model="lastName"
                      label="Last Name"
                      name="last_name"
                      id="last_name"
                      append-icon="mdi-account-supervisor-circle"
                      outlined
                      rounded
                      required
                      dark
                      filled
                      :counter="191"
                      :error-messages="errors"
                      class="form__input-field"
                      @keydown.enter="submitRegister"
                    ></v-text-field>
                  </ValidationProvider>
                </v-col>
              </v-row>
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
                  @keydown.enter="submitRegister"
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider
                v-slot="{ errors }"
                name="Organization"
                rules="required|max:191"
            >
                <v-text-field
                v-model="org_name"
                label="Organization"
                name="org_name"
                id="org_name"
                append-icon="mdi-account-circle"
                outlined
                rounded
                required
                dark
                filled
                :counter="191"
                :error-messages="errors"
                class="form__input-field"
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider
                vid="password"
                v-slot="{ errors }"
                name="Password"
                rules="required|min:8"
              >
                <v-text-field
                  v-model="password"
                  ref="password"
                  :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                  :type="showPassword ? 'text' : 'password'"
                  label="Password"
                  hint="At least 8 characters"
                  name="password"
                  id="password"
                  autocomplete="new-password"
                  counter
                  outlined
                  rounded
                  required
                  dark
                  filled
                  :error-messages="errors"
                  class="form__input-field"
                  @click:append="showPassword = !showPassword"
                  @keydown.enter="submitRegister"
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider
                v-slot="{ errors }"
                name="Password"
                rules="required|min:8|password:@password"
              >
                <v-text-field
                  v-model="confirmPassword"
                  type="password"
                  label="Confirm Password"
                  hint="Must be the same with password above"
                  name="password_confirmation"
                  id="password_confirmation"
                  autocomplete="new-password"
                  append-icon="mdi-asterisk"
                  counter
                  outlined
                  rounded
                  required
                  dark
                  filled
                  :error-messages="errors"
                  class="form__input-field"
                  @keydown.enter="submitRegister"
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
                  @click="submitRegister"
                  >Register
                  </v-btn>
              </div>
            </v-form>
          </v-card>
        </v-col>
        <v-col>
          <v-img
            :src="require('../../static/images/register-fg.png')"
            id="register__image-fg"
          ></v-img>
          <v-img
            :src="require('../../static/images/register-bg.png')"
            :lazy-src="require('../../static/images/register-bg.png')"
            id="register__image"
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
      </v-row>
    </ValidationObserver>
    <transition name="fade" >
    <v-row class="pre-auth-form__container" align="center" justify="center" v-if="show == false">
      <v-col class="pre-auth-form__container">
        <v-card
          dark
          color="rgba(0,0,0,0.5)"
          class="pre-auth-form__card"
          elevation="4"
        >
          <v-form id="register__form" ref="form">
            <div class="text-center">
              <p>ORGANIZATION INFORMATION</p>
            </div>
            <v-text-field
              type="hidden"
              name="_token"
              :value="csrf"
              class="form__hidden-field"
            ></v-text-field>
              <v-text-field
              v-model="org_location"
              label="Location"
              name="org_location"
              id="org_location"
              append-icon="mdi-account-circle"
              outlined
              rounded
              required
              dark
              filled
              class="form__input-field"
              ></v-text-field>
          
              <v-text-field
              v-model="org_other_info"
              label="Other info"
              name="org_other_info"
              id="org_other_info"
              append-icon="mdi-account-circle"
              outlined
              rounded
              required
              dark
              filled
              class="form__input-field"
              ></v-text-field>
            <v-row>
              <v-col> 
                <div class="text-center">
                  <v-btn
                    rounded
                    color="yellow darken-1"
                    light
                    block
                    x-large
                    id="index__button"
                    @click="skip"
                    >Skip
                    </v-btn>
                </div>
              </v-col>
              <v-col>
                <div class="text-center">
                  <v-btn
                    rounded
                    color="yellow darken-1"
                    light
                    block
                    x-large
                    id="index__button"
                    @click="additionalInfo"
                    >Save
                    </v-btn>
                </div>
              </v-col>
            </v-row>
          </v-form>
        </v-card>
      </v-col>
      <v-col>
        <v-img
          :src="require('../../static/images/register-fg.png')"
          id="register__image-fg"
        ></v-img>
        <v-img
          :src="require('../../static/images/register-bg.png')"
          :lazy-src="require('../../static/images/register-bg.png')"
          id="register__image"
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
    </v-row>
    </transition>
  </div>
</template>

<script>
require("../../plugins/vee-validate.js");
import { mapActions, mapGetters } from "vuex";
import { ValidationObserver, ValidationProvider } from "vee-validate";
import SelectOrganization from "./../../modals/SelectOrganization.vue";
import CreateOrganization from "./../../modals/CreateOrganization.vue";

export default {
  data: () => ({
    snackbar: false,
    snackbarMessage: "",
    snackbarColor: "primary",
    firstName: "",
    lastName: "",
    email: "",
    password: "",
    org_name: "",
    org_location: "",
    org_other_info: "",
    org_selected: "",
    org_id: "",
    showPassword: false,
    show: true,
    selectOrgModal: false,
    createOrgModal: false,
    createOrgModalSnackbar: false,
    confirmPassword: "",
    organizations: [],
    organizationCategories: [],
    csrf: document
      .querySelector('meta[name="csrf-token"]')
      .getAttribute("content"),
  }),
  components: {
    ValidationProvider,
    ValidationObserver,
    CreateOrganization,
  },
  methods: {
  
    submitRegister() {
      this.$refs.observer.validate().then((formIsValid) => {
        if (formIsValid) {
          this.finishRegister()
        }
      });
    },
    additionalInfo() {
      axios
      .put(`/organization/${this.org_id}`, {
      _token: this.csrf,
      location: this.org_location,
      other_info: this.org_other_info,
      })
      .then((response) => {
      if (response.status === 200) {
          window.location.href = '/login';
        }
      })
    },

    skip(){ 
      window.location.href = '/login';
    },
    finishRegister() {
      axios
      .post("/register", {
      _token: this.csrf,
      first_name: this.firstName,
      last_name: this.lastName,
      email: this.email,
      password: this.password,
      password_confirmation: this.confirmPassword,
      organization: this.org_name,
      })
      .then((response) => {
      if (response.status === 200) {
          this.org_id = response.data
          this.snackbarColor = "success";
          this.snackbarMessage =
          "You have created your account";
          this.snackbar = true;
          this.show = false
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

      if (!!error.response.data.errors.password)
          error.response.data.errors.password.map((error) => {
          this.snackbarMessage =
              this.snackbarMessage + "* " + error + "<br />";
          });
      if (!!error.response.data.errors.organization)
          error.response.data.errors.organization.map((error) => {
          this.snackbarMessage =
              this.snackbarMessage + "* " + error + "<br />";
          });
      if (!!error.response.data.errors.category)
          error.response.data.errors.category.map((error) => {
          this.snackbarMessage =
              this.snackbarMessage + "* " + error + "<br />";
          });

      this.snackbar = true;
      });
    },
  },
};
</script>

<style scoped>

.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}

</style>
