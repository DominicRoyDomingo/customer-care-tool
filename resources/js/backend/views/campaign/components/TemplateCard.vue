<template>
  <v-card
    :loading="loading"
    class="mx-4"
    height="100%"
    :elevation="active ? 11 : 0"
    :color="active ? '#385F73' : 'white'"
    :dark="active"
    max-width="250"
    @click="selectTemplate"
  >
    <v-skeleton-loader
      :loading="loading"
      transition="fade-transition"
      class="mx-auto"
      min-height="250"
      type="card"
    >
      <v-img height="250" :src="template_item.preview" @load="loading = false">
        <template v-slot:placeholder>
          <v-row class="fill-height ma-0" align="center" justify="center">
            <v-progress-circular
              indeterminate
              color="grey lighten-5"
            ></v-progress-circular>
          </v-row>
        </template>
      </v-img>

    </v-skeleton-loader>

    <v-card-subtitle v-if="!loading" class="text-center text-subtitle-1">
      {{ template_item.template_name }}
    </v-card-subtitle>
    
    <!--
  <v-card-text>
    <div class="my-4 subtitle-1">
      {{ $t("label.variables") }}
    </div>

    <div>
      {{ template_item.variables_display }}
    </div>
  </v-card-text>
-->
  </v-card>
</template>

<script>
export default {
  name: "TemplateCard",
  props: ["template_item", "active"],

  data: () => ({
    loading: true,
    selection: 1,
  }),

  mounted() {
    setTimeout(e => {
      this.loading = false;
    }, 1000);
  },

  methods: {
    preview() {
      this.$emit("preview-template", this.template_item);
    },

    selectTemplate() {
      this.$emit("select-template", this.template_item);
    },
  },
};
</script>
