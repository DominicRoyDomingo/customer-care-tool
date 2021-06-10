<style  scoped>
  #imagePreview {
    height: 150px;
    width: 150px;
  }
</style>
<template>
  <div>
    <b-modal
      id="preview-template-modal"
      @hide="hide"
      hide-footer
      size="xl"
      :title="'Previewing template \'' + email_template.template_name + '\''"
    >
        <div>
          <b-embed
            type="iframe"
            :src="email_template.preview"
          ></b-embed>
        </div>
    </b-modal>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
  props: ["parent", "email_template"],

  data() {
    return {
      loading: true,
      vm: this
    };
  },
  
  methods: {
    hide() {
      this.$emit("hide");
    },
  }
};
</script>