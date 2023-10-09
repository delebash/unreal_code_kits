<template>
  <img :src="imageSource"   :style="{width: width, height: height}">
</template>

<script setup>
const props = defineProps({
  width: {
    type: String,
    default: '100px'
  },
  height: {
    type: String,
    default: 'auto'
  }
})

const imageSource = props.params.value;

</script>

<style scoped>

</style>
