<template>
  <div>
    <v-row align="center">
      <v-col>
        <v-img
          :src="require('../../static/images/index.png')"
          :lazy-src="require('../../static/images/index.png')"
          id="index__image"
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
        <div id="index__title">
          <h5>
            <strong>Customers, connected.</strong>
          </h5>
          <h6>Relax... You can now manage your clients with ease.</h6>
        </div>
      </v-col>
    </v-row>
    <v-divider dark></v-divider>
    <v-row align="center">
      <v-spacer></v-spacer>
      <v-col class="text-center" cols="12" sm="4">
        <div>
          <v-btn text large tile href="/login" class="landing__button"
            >Login</v-btn
          >
        </div>
        <div>
          <v-btn
            text
            large
            tile
            outlined
            href="/register"
            class="landing__button"
            >Register</v-btn
          >
        </div>
      </v-col>
      <v-spacer></v-spacer>
    </v-row>
  </div>
</template>
