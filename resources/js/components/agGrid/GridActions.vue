<template>
<span>
  <router-link v-if="can(`${prefix}-edit`)"
               :to="{ name: `${prefixPlural}.edit`, params: { id: id } }"
               class="badge bg-primary">Edit
  </router-link>
  <a href="#" v-if="can(`${prefix}-edit`)" @click.prevent="deletePost(id)" class="ms-2 badge bg-danger">Delete</a>
</span>
</template>
<script setup>
import {useAbility} from '@casl/vue';
const {can} = useAbility();

const props = defineProps({})

function deletePost(id){
  props.params.context.componentParent(id)
}
const id = props.params.value
const prefix = props.params.prefix
const prefixPlural = props.params.prefixPlural


</script>
<style scoped>

</style>
