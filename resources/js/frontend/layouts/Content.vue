<template>
  <div class="content__main">
    <div v-if="page === 'index'">
      <Index />
    </div>
    <div v-if="page === 'login'">
      <Login />
    </div>
    <div v-if="page === 'register'">
      <Register />
    </div>
    <div v-if="page === 'reset'">
      <Reset />
    </div>
  </div>
</template>

<script>
import Index from "../components/pages/Index";
import Login from "../components/pages/Login";
import Register from "../components/pages/Register";
import Reset from "../components/pages/Reset";

export default {
  layout: "Content",
  components: {
    Index,
    Login,
    Register,
    Reset,
  },
  props: ["page"],
};
</script>
