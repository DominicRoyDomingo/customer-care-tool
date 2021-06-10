<template>
  <el-button size="small" :type="isActive ? 'primary' : ''" @click="onClick">Code View</el-button>
</template>

<script>
export default {
  name: "CodeViewButton",

  props: ["isActive"],

  methods: {
    onClick(e) {
      this.$emit("click");
    }
  }
};
</script>