<template>
  <v-app id="order__container">
    <b-modal
      id="change-order-modal"
      hide-footer
      size="md"
      :title="`${$t(modal.name)} ${$this.class}`"
    >
      <form
        @submit.prevent="onSubmit"
        method="post"
        enctype="multipart/form-data"
        @keydown="form.errors.clear($event.target.name)"
      >
        <b-form-group>
          <v-container>
            <draggable
              class="list-group"
              tag="ul"
              v-model="$this.items"
              v-bind="dragOptions"
              @start="isDragging = true"
              @end="isDragging = false"
            >
              <transition-group type="transition" name="flip-list">
                <span
                  class="list-group-item"
                  v-for="element in $this.items"
                  :key="element.sequence"
                  style="padding: 10px 15px !important; margin-bottom: 5px"
                >
                  {{ element.status }}
                  <v-icon small class="float-right">
                    mdi-arrow-all
                  </v-icon>
                </span>
              </transition-group>
            </draggable>
          </v-container>
        </b-form-group>

        <b-form-group>
          <v-container>
            <span class="float-right">
              <el-button type="danger" size="small" @click="sort" plain>
                {{ $t(modal.button.reset) }}
              </el-button>

              <el-button
                size="small"
                native-type="submit"
                type="success"
                :loading="modal.button.loading"
                style="color: #fff !important"
                >{{ $t(modal.button.update) }}</el-button
              >
            </span>
          </v-container>
        </b-form-group>
      </form>
    </b-modal>
  </v-app>
</template>

<script>
import draggable from "vuedraggable";

import { changeOrder } from "./../../../api/date.js";

export default {
  props: ["$this"],

  components: {
    draggable,
  },

  data: function() {
    return {
      modal: this.$this.modal.order,
    };
  },

  methods: {
    onSubmit() {
      // let resultItems = this.$this.oldItems.map( function(item, i){
      //     console.log(item.status)
      //     console.log(this.$this.items[i].status)
      //     return {id: item.id, sequence: this.$this.items[i].sequence}
      // }.bind(this));

      let resultItems = this.$this.items.map(function(item, i) {
        return { id: item.id };
      });

      this.modal.button.loading = true;

      changeOrder(resultItems).then((resp) => {
        this.$this.makeToast(
          resp.data.type,
          "ORDER CHANGED",
          `${this.$t("successfull_change_order_1")} ${
            this.$this.class
          } ${this.$t("successfull_change_order_2")}`
        );

        this.$this.loadDate();

        this.$this.onChangeOrder(this.$this.class);

        this.modal.button.loading = false;

        if (this.keep_open == true) {
          this.modal.isVisible = true;
        } else {
          this.modal.isVisible = false;

          this.keep_open == false;
        }
      });
    },

    sort() {
      this.$this.items = this.$this.items.sort(
        (a, b) => a.sequence - b.sequence
      );
    },

    compare(a, b) {
      const idA = a.id;
      const idB = b.id;

      let comparison = 0;
      if (idA > idB) {
        comparison = 1;
      } else if (idA < idB) {
        comparison = -1;
      }
      return comparison;
    },
  },

  computed: {
    dragOptions() {
      return {
        animation: 0,
        group: "description",
        disabled: false,
        ghostClass: "ghost",
      };
    },
  },
};
</script>

<style scoped>
.flip-list-move {
  transition: transform 0.5s;
}
.no-move {
  transition: transform 0s;
}
.ghost {
  opacity: 0.5;
  background: #c8ebfb;
}
.list-group {
  min-height: 20px;
}
.list-group-item {
  cursor: move;
}
.list-group-item i {
  cursor: pointer;
}
</style>
