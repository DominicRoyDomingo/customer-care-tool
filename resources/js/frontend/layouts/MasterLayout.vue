<template>
  <v-app id="app__container" dark>
    <Header />
    <Background />
    <Content :page="page" />
  </v-app>
</template>

<script>
import Header from "./Header";
import Background from "../components/Background";
import Content from "./Content";

export default {
  layout: "MasterLayout",
  components: {
    Header,
    Background,
    Content,
  },
  props: ["page"],
};
</script>
