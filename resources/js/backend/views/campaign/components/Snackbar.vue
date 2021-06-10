<template>
    
    <v-snackbar
      :timeout="snackbarTimeout"
      :v-model="snackbar"
      :color="snackbarColor"
      absolute
      left
      shaped
      top
    >
      {{ snackbarText }}
    </v-snackbar>

</template>

<script>
export default {
    name: "Snackbar",
    props: ["snackbarColor", "snackbarTimeout","snackbarText"],
    data() {
        return {
            snackbar: true
        }
    }
}
</script>