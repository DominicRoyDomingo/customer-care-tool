<style  scoped>
#imagePreview {
  height: 150px;
  width: 150px;
}
</style>
<template>
  <div>
    <b-modal
      id="brand-modal"
      @hide="hide"
      hide-footer
      size="md"
      :title="parent.form.action == 'Add' ? $t('label.add') : $t('button.update')"
    >
      <form
        class="form"
        @submit.prevent="onSave"
        @keyup="parent.form.errors.clear($event.target.name)"
      >
        <div class="form-group">
          <label for="name">
            {{$t('label.brand_name')}}
            <strong class="text-danger">*</strong>
          </label>

          <input
            id="name"
            type="text"
            name="name"
            v-model="parent.form.name"
            class="form-control"
            :placeholder="$t('label.required')"
            autocomplete="off"
            aria-describedby="brand"
          />
          <small
            id="job"
            v-if="parent.form.errors.has('name')"
            v-text="parent.form.errors.get('name')"
            class="text-danger"
          />
        </div>

        <div class="form-group">
          <label for="website">{{$t('label.website')}}</label>

          <input
            id="website"
            type="text"
            name="website"
            v-model="parent.form.website"
            class="form-control"
            autocomplete="off"
            aria-describedby="brand"
          />
        </div>

        <div class="form-group">
          <label for="logo">{{$t('label.logo')}}</label>
          <b-form-file id="img-file" @change="onGetFile" accept=".png, .jpg, .jpeg" plain></b-form-file>
        </div>

        <div class="form-group">
          <span class="float-right">
            <b-button
              type="submit"
              :variant="parent.form.action == 'Add' ? 'primary' : 'success'"
              :disabled="parent.btnloading"
              class="mb-2 mt-2 text-capitalize mt-1"
            >
              <span v-if="!parent.btnloading">
                <i class="fas fa-save"></i>
                {{ parent.form.action == "Add" ? $t('button.save') : $t('button.save_change') }}
              </span>
              <b-spinner v-else small label="Small Spinner"></b-spinner>
            </b-button>
            <b-button
              variant="secondary"
              size="sm"
              @click="$bvModal.hide('brand-modal')"
            >{{$t('cancel')}}</b-button>
          </span>
        </div>
      </form>
    </b-modal>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
export default {
  props: ["parent"],

  data() {
    return {
      file: null,
      loading: true
    };
  },

  computed: {
    ...mapGetters("brand", {
      responseStatus: "get_response_status"
    })
  },

  methods: {
    ...mapActions("brand", ["post_brand"]),
    onGetFile(event) {
      this.file = event.target.files[0];
    },
    hide() {
      this.$emit("hide");
    },
    onSave() {
      this.parent.btnloading = true;

      let formData = new FormData();

      formData.append("api_token", this.$user.api_token);
      formData.append("action", this.parent.form.action);
      formData.append("name", this.parent.form.name);
      formData.append("website", this.parent.form.website);
      formData.append("file", this.file);

      this.post_brand(formData)
        .then(resp => {
          this.parent.btnloading = false;
          this.$bvModal.hide("brand-modal");
          if (this.responseStatus) {
            this.parent.loadItems();

            let message = {
              create: `${this.parent.form.name}` + this.$t( 'created_message' ),
              update: this.$t( 'updated_message1' ) + this.$t( 'label.brands' ) + ` ID: ${this.parent.form.id} ` + this.$t( 'updated_message2' ),
              title: {
                create: this.$t( 'new_record_created' ),
                update: this.$t( 'record_updated' )
              }
            };
            
            this.parent.makeToast(
              this.parent.form.action === "Add" ? "success" : "warning",
              this.parent.form.action === "Add"
                ? message.title.create
                : message.title.update,
              this.parent.form.action === "Add"
                ? message.create
                : message.update
            );
          }
        })
        .catch(error => {
          console.log(error);
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnloading = false;
        });
    }
  }
};
</script>
 
