<template>
  <div class="row">
    <div class="col-md-4" v-for="(card, ck) in cardBox" :key="ck">
      <div class="card shadow border-left-primary h-90 py-1 border-0">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div
                class="text-sm text-primary mb-2 text-uppercase font-weight-bold"
                v-text="card.title"
              />
              <div class="h6 mb-0 text-gray-800">
                <div
                  class="mb-2"
                  v-for="card in set_card_box_value(card.name)"
                  :key="card.name"
                >
                  <strong v-bind:style="{ color: set_color(card.color) }">
                    {{ card.value }}
                  </strong>
                  <small class="text-muted" v-html="card.title" />
                </div>
              </div>
            </div>
            <div class="col-auto">
              <b-icon
                :icon="card.icon"
                aria-hidden="true"
                class="fa-2x text-muted"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["items", "componentName"],
  data() {
    return {
      cardBox: [
        {
          title: this.$t("card_box_title.average_entries"),
          name: "average_entries",
          icon: "bar-chart-steps",
        },
        {
          title: this.$t("card_box_title.highest_entries"),
          name: "highest_entries",
          icon: "sort-numeric-up-alt",
        },
        {
          title: this.$t("card_box_title.total_entries"),
          name: "total_entries",
          icon: "cloud-upload",
        },
      ],
    };
  },
  methods: {
    set_card_box_value(name) {
      let array = [];
      this.items.forEach((item) => {
        item.summaries.forEach((sum) => {
          if (sum.name === name) {
            array.push({
              title: sum.title,
              value: sum.value,
              color: sum.color,
            });
          }
        });
      });

      return array;
    },

    set_color(color) {
      if (this.componentName === "per_entries") {
        return "#008ffb";
      }
      return color;
    },
  },
};
</script>

<style>
</style>