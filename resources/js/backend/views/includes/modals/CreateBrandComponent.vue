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
      :title="form.action == 'Add' ? $t('label.add') : $t('button.update')"
    >
      <form
        class="form"
        @submit.prevent="onSave"
        @keyup="form.errors.clear($event.target.name)"
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
            v-model="form.name"
            class="form-control"
            :placeholder="$t('label.required')"
            autocomplete="off"
            aria-describedby="brand"
          />
          <small
            id="job"
            v-if="form.errors.has('name')"
            v-text="form.errors.get('name')"
            class="text-danger"
          />
        </div>

        <div class="form-group">
          <label for="website">{{$t('label.website')}}</label>

          <input
            id="website"
            type="text"
            name="website"
            v-model="form.website"
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
              :variant="form.action == 'Add' ? 'primary' : 'success'"
              :disabled="btnloading"
              class="mb-2 mt-2 text-capitalize mt-1"
            >
              <span v-if="!btnloading">
                <i class="fas fa-save"></i>
                {{ form.action == "Add" ? $t('button.save') : $t('button.save_change') }}
              </span>
              <b-spinner v-else small label="Small Spinner"></b-spinner>
            </b-button>
            <b-button
              variant="secondary"
              size="sm"
              @click="modalClose"
            >{{$t('cancel')}}</b-button>
          </span>
        </div>
      </form>
    </b-modal>
  </div>
</template>

<script>
import Form from "./../../../shared/form.js";
import toast from "./../../../helpers/toast.js";
import { getBrandsAndPlatformTypes } from "./../../../api/content_item.js";
import { mapActions, mapGetters } from "vuex";
export default {
    props: ["parent", "this_cbc"],
    mixins: [toast],
    data() {
        return {
        file: null,
        loading: true,
        btnloading: false,
        form: new Form({
            id: "",
            name: "",
            website: "",
            logo: "",
            action: "Add"
        }),
        };
    },

    computed: {
        ...mapGetters("brand", {
        responseStatus: "get_response_status"
        })
    },

    methods: {
        ...mapActions("brand", ["post_brand","add_brand","update_brand"]),
        onGetFile(event) {
        this.file = event.target.files[0];
        },
        hide() {
        this.$emit("hide");
        },
        modalClose() {
            this.$bvModal.hide('brand-modal')
            this.this_cbc.$bvModal.show(this.this_cbc.modalId);
        },
        onSave() {
        let vm = this;
        this.btnloading = true;

        let formData = new FormData();

        formData.append("id", this.form.id);
        formData.append("api_token", this.$user.api_token);
        formData.append("action", this.form.action);
        formData.append("name", this.form.name);
        formData.append("website", this.form.website);
        formData.append("file", this.file);

        this.post_brand(formData)
            .then(resp => {
            this.btnloading = false;
            getBrandsAndPlatformTypes().then(resp => {

                this.parent.brands = resp.brands;
                this.parent.platform_type = resp.platformTypes
                this.$bvModal.hide("brand-modal");
                this.this_cbc.$bvModal.show(this.this_cbc.modalId);
    
            });
            
            if (this.responseStatus) {
                //this.loadItems();

                if(this.form.action == "Add"){
                this.add_brand(this.responseStatus);
                }
                else{
                let brand = vm.responseStatus;
                brand.brand_index = vm.editingIndex;
                vm.update_brand(brand);
                }

                let message = {
                create: `${this.form.name}` + this.$t( 'created_message' ),
                update: this.$t( 'updated_message1' ) + this.$t( 'label.brands' ) + ` ID: ${this.form.id} ` + this.$t( 'updated_message2' ),
                title: {
                    create: this.$t( 'new_record_created' ),
                    update: this.$t( 'record_updated' )
                }
                };
                
                this.makeToast(
                this.form.action === "Add" ? "success" : "warning",
                this.form.action === "Add"
                    ? message.title.create
                    : message.title.update,
                this.form.action === "Add"
                    ? message.create
                    : message.update
                );

                
            }
            })
            .catch(error => {
            console.log(error);
            this.form.errors.record(error.response.data.errors);
            this.btnloading = false;
            });
        }
    }
};
</script>
 
