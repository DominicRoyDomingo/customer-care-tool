<template>
  <div id="header__main">
    <v-app-bar flat id="header__app-bar">
      <v-toolbar-title>
        <a href="/">
          <h6><strong>Customer</strong>CareTool</h6>
        </a>
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <div class="header__links">
        <v-btn text large tile href="/login">Login</v-btn>
      </div>
      <div class="header__links">
        <v-btn text large tile outlined href="/register">Register</v-btn>
      </div>
    </v-app-bar>
  </div>
</template>

<script>
export default {
  Layout: "Header",
};
</script>
