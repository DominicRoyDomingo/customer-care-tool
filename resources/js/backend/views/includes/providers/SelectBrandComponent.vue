<template>
  <div class="create">
      <b-modal
        id="select-brand-modal"
        hide-footer
        no-close-on-backdrop
        size="md"
        :title="$t('label.choose_brand')"
      >
        <div class="p-2 margin-top">
                <b-list-group class="mt-3">
                  <b-list-group-item 
                  button 
                  v-for="brand in $this.brands" 
                  :key="brand.id" 
                  @click="changeBrand(brand)"
                  >{{brand.name}}</b-list-group-item>
                </b-list-group>
        </div>
      </b-modal>
  </div>
</template>

<script>
// import Api from "./../../../shared/api.js";

import { createTags } from "./../../../api/tags.js";

import Brand from "./../modals/CreateBrandComponent.vue";

import { mapActions, mapGetters } from "vuex";

export default {
  props: ["$this"],

  data: function() {
    return {
      modal: this.$this.modal.selectBrand,

      modalId: 'select-brand-modal',

      form: this.$this.form,

      formData: this.$this.formData,
    };
  },

  methods: {
    ...mapActions("providers", ["post_provider", "add_provider", "update_brand"]),
    ...mapActions("division", ["get_divisions"]),
    ...mapActions("city", ["get_cities", "reset_cities"]),
    
    modalClose(done) {
      $(".error-provider").remove();

      $("#name, #provider_type").removeAttr(
        "style"
      );

      this.file = "";

      this.$this.$bvModal.hide("provider-add-modal");

      this.form.reset();
      

      this.keep_open = false;
    },

    changeBrand(val) {

      this.$eventBus.$emit('change-brand', val);

      this.$this.$bvModal.hide(this.modalId);
    },

    openProviderTypeModal() {
      // this.modal.isVisible = false;
      this.$this.$bvModal.hide(this.$this.modalId);

      this.$this.$bvModal.show("provider-type-add-modal");
      // this.$this.modal.itemType.isVisible = true;
    },
  },
};
</script>

<style></style>
