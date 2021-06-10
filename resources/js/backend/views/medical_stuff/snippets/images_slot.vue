<template>
  <span>
    <div v-for="(typeF, typeI) in parent.parent.imageSlotForm" :key="typeI">
      <div class="row mb-0">
        <div class="col-md-11">
          <hr />
        </div>
        <div class="col-md-1">
          <v-btn
            fab
            small
            color="error"
            class="bg-danger text-white"
            @click.prevent="onRemoveImageSlot(typeI)"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </div>
      </div>
      <div class="form-group">
        <v-row>
          <v-col md="4">
            <label for="image" class="text-uppercase">
                {{ $t("image") }}
            </label>
            <div style="height: 156px; max-width: 200px;">
              <v-img
                v-if="typeF.imagePlaceholder != null"
                height="156"
                :src="typeF.imagePlaceholder"
              ></v-img>

              <v-img
                v-else
                height="156"
                src="https://customer-care-tool.s3.us-east-2.amazonaws.com/image-placeholder/image-placeholder.png"
              ></v-img>
            </div><br>
            <input 
              id="fileUpload" 
              type="file" 
              @change="onGetFile(typeF, $event)"
              accept=".png, .jpg, .jpeg"
              plain
            >
          </v-col>
          <v-col md="8">
            <label for="htmlTag" class="text-uppercase">
                {{ $t("html_tag_priority") }}
            </label>
            <v-select
                id="htmlTag"
                :options="parent.htmlTags"
                v-model="typeF.htmlTags"
                name="htmlTag"
                label="description"
                multiple
                required
            >
                <template #list-header>
                <li style="margin-left: 20px" class="mb-2">
                    <b-link
                    v-b-tooltip.hover
                    :title="$t('html_tag_new')"
                    href="javascript:;"
                    @click="parent.showChildModal('html-tag-modal')"
                    >
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    {{ $t('html_tag_new') }}
                    </b-link>
                </li>
                </template>
            </v-select>
          </v-col>
        </v-row>
      </div>
    </div>
  </span>
</template>

<script>
export default {
  props: ["parent"],
  methods: {
    onRemoveImageSlot(index) {
      let vm = this;
      vm.parent.parent.imageSlotForm.splice(index, 1);
      if (vm.parent.parent.imageSlotForm.length < 1) {
        vm.parent.parent.isAddImage = false;
      }
      // let vm = this;
      // $.confirm({
      //   title: this.$t("confirm_action"),
      //   content: vm.$t("label.delete_author_slot_confirmation"),
      //   type: "red",
      //   typeAnimated: true,
      //   buttons: {
      //     tryAgain: {
      //       text: this.$t("confirm"),
      //       btnClass: "btn-red",
      //       action: function () {
      //         vm.parent.parent.imageSlotForm.splice(index, 1);
      //       },
      //     },
      //     cancel: function () {},
      //   },
      // });
    },

    

    onGetFile(typeF,event) {
			if(event.target.files[0] == undefined) return;
      typeF.image = event.target.files[0];
  
      typeF.imagePlaceholder = URL.createObjectURL(typeF.image);
    },
  },
};
</script>

 