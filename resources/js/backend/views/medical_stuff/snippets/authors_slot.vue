<template>
  <span>
    <div v-for="(typeF, typeI) in parent.parent.authorSlotForm" :key="typeI">
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
            @click.prevent="onRemoveAuthorsSlot(typeI)"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </div>
      </div>
      <div class="form-group">
        <label for="actor" class="text-uppercase">
            {{ $t("type_of_author") }}
        </label>
        <v-select
            id="actor"
            :options="author_type"
            v-model="typeF.actor_type"
            name="actor_type"
            @input="selectAuthorType()"
            label="name"
            required
        >
            <template #list-header>
              <li style="margin-left: 20px" class="mb-2">
                  <b-link
                  v-b-tooltip.hover
                  :title="`${$t('label.new')} ${$t(
                      'new_type_of_author'
                  )}`"
                  href="javascript:;"
                  @click="parent.showChildModal('add-author-type-modal')"
                  >
                  <i class="fa fa-plus" aria-hidden="true"></i>
                  {{ $t("label.new") }} {{ $t("new_type_of_author") }}
                  </b-link>
              </li>
            </template>
            <template v-slot:option="option">
              {{ option.name }}
              <small
                v-if="option.convertion == true"
                style="color:red"
              >
                (No {{ option.language }} translation yet)</small
              >
            </template>
        </v-select>
      </div>
      <div class="form-group">
        <label for="actor" class="text-uppercase">
            {{ $t("publishing_author_idea") }}
        </label>
        <v-select
            id="actor"
            :options="
            parent.parent.actors.data ? parent.parent.actors.data : parent.parent.actors
            "
            v-model="typeF.actors"
            name="actor"
            label="full_name"
            multiple
            required
        >
            <template #list-header>
            <li style="margin-left: 20px" class="mb-2">
                <b-link
                v-b-tooltip.hover
                :title="`${$t('label.new')} ${$t(
                    'content_author_idea'
                )}`"
                href="javascript:;"
                @click="parent.createActor()"
                >
                <i class="fa fa-plus" aria-hidden="true"></i>
                {{ $t("label.new") }} {{ $t("content_author_idea") }}
                </b-link>
            </li>
            </template>
        </v-select>
      </div>
    </div>
  </span>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import Datepicker from "vuejs-datepicker";
export default {
  props: ["parent"],
  components: {
    Datepicker,
  },
   data: function() {
      return {
        type_array: [],
        authorsTypeList: this.parent.parent.authorSlotForm,
        author_type: this.parent.parent.type_authors
      }
   },
  computed: {
    ...mapGetters("categ_terms", {
        type_authors: "get_type_author_items",
    }),
  },
  methods: {
    ...mapActions("categ_terms", [
      "get_type_authors",
    ]),

    loadAutorType() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: '',
        brand_id: this.$brand.id,
      };
      this.get_type_authors(params).then((_) => {
      });
    },

    selectAuthorType() {
      this.loadAutorType();
      let array = [];
      JSON.parse( JSON.stringify(this.parent.setAuthorSlotId())).forEach((data,index)=>{
        let type =  data.actor_type;
        for(var j= 0; j< this.type_authors.length;j++) {
          if (type === this.type_authors[j].id) {
            this.type_authors.splice(j,1);
            break;
          }
        }
        this.author_type = this.type_authors;
      })
    }, 

    onRemoveAuthorsSlot(index) {
      let vm = this;
      vm.parent.parent.authorSlotForm.splice(index, 1);
      if (vm.parent.parent.authorSlotForm.length < 1) {
        vm.parent.parent.isAddAuthor = false;
      }
      this.selectAuthorType();
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
      //         vm.parent.parent.authorSlotForm.splice(index, 1);
      //         if (vm.parent.parent.authorSlotForm.length < 1) {
      //           vm.parent.parent.isAddAuthor = false;
      //         }
      //       },
      //     },
      //     cancel: function () {},
      //   },
      // });
    },

  },
};
</script>

 