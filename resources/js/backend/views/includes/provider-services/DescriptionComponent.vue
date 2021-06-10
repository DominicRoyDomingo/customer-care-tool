<template>
  <div class="create">
    <b-modal
      id="description-modal"
      hide-footer
      size="md"
      no-close-on-backdrop
      :title="$t('label.description')"
    >
      <v-app id="service-services__container">
        <v-container>
            <p class="my-4">{{form.description}}</p>
        </v-container>
      </v-app>
    </b-modal>
  </div>
</template>

<script>

export default {
  props: ["$this"],

  data: function() {

    return {

      form: this.$this.form,
    };
  },

  methods: {
    modalClose(done) {

      this.$this.$bvModal.hide("description-modal");

      this.form.reset();
    },
  },
};
</script>

<style></style>
