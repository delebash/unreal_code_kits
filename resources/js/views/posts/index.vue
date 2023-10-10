<template>
  <div class="container-fluid">
    <h2 class="text-center my-6">Code Kits</h2>
    <div class="row mb-2">
      <table class="table">
        <thead>
        <tr>
          <th class="px-6 py-3 bg-gray-50 text-left">
            <input v-model="search_title" type="text"
                   class="inline-block mt-1 form-control"
                   placeholder="Filter by Title">
          </th>
          <th class="px-8 py-3 bg-gray-50 text-left">
            Category:
            <v-select multiple v-model="search_category" :options="categoryList"
                      :reduce="category => category.id" label="name"/>
          </th>
          <th class="px-6 py-3 bg-gray-50 text-left">
            UE Version:
            <v-select multiple v-model="search_version" :options="versionList"
                      :reduce="version => version.id" label="name"/>
          </th>
          <th class="px-6 py-3 bg-gray-50 text-left">
            <input v-model="search_content" type="text"
                   class="inline-block mt-1 form-control"
                   placeholder="Filter by Description">
          </th>
          <th class="px-6 py-3 bg-gray-50 text-left">
            Order By:
            <v-select :clearable="false" v-model="orderColumn" @update:modelValue="updateOrdering"
                      :options="sortColumnsOptions"
            />
          </th>
          <th class="px-6 py-3 bg-gray-50 text-left">
            Order Direction:
            <v-select :clearable="false" @update:modelValue="updateOrdering" v-model="orderDirection"
                      :options="sortOrderOptions"
            />
          </th>
        </tr>
        </thead>
      </table>
    </div>
    <div class="overflow-y-auto overflow-hidden" style="min-height:600px;max-height:600px">
      <div class="row">
        <div v-for="post in posts?.data" :key="post.id" class="col-3">

          <div class="card mb-2" style="width: 24rem;">
            <img :src="getImageUrl(post)" width="250" height="250" class="card-img-top" alt="...">
            <div class="card-body">
              <h5 class="card-title"><b>{{ post.title }}</b></h5>
              <div class="card-text small">On: {{ formatDate(post.created_at) }}</div>
              <div class="card-text small mb-1">By: <a href="#"
                                                       @click.prevent="getUserPosts(post?.user?.id)">{{
                  post?.user?.name
                }}</a></div>
              <p class="card-text">{{ post.content.substring(0, 100) + "..." }}</p>
              <p class="card-text">
                Categories:
                <strong class="m-1" v-for="category in post.categories" :key="category.id">
                  <router-link :to="{ name: 'category-posts.index', params: { id: category.id } }">
                    {{ category.name }}
                  </router-link>
                </strong>
              </p>
              <p class="card-text">
                Unreal Versions:
                <strong class="m-1" v-for="version in post.versions" :key="version.id">
                  <router-link :to="{ name: 'version-posts.index', params: { id: version.id } }">
                    {{ version.name }}
                  </router-link>
                </strong>
              </p>
              <router-link :to="{ name: 'public-posts.details', params: { id: post.id } }"
                           class="btn btn-primary">View
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import {ref, onMounted, watch} from 'vue'
import usePosts from "@/composables/posts";
import _ from "lodash";
import useCategories from "@/composables/categories";
import useVersions from "@/composables/versions";
import dayjs from "dayjs";

const search_category = ref('')
const search_version = ref('')
const search_id = ref('')
const search_title = ref('')
const search_content = ref('')
const search_global = ref('')
const search_user_id = ref('')
const orderColumn = ref('tile')
const orderDirection = ref('desc')
const {posts, getDisplayPosts, getPostsByUser} = usePosts()
const {categoryList, getCategoryList} = useCategories()
const {versionList, getVersionList} = useVersions()


const sortColumnsOptions = ['title', 'created_at']
const sortOrderOptions = ['asc', 'desc']


onMounted(() => {
  getDisplayPosts()
  getCategoryList()
  getVersionList()
})

function formatDate(dateString) {
  const date = dayjs(dateString);
  // Then specify how you want your dates to be formatted
  return date.format('dddd MMMM D, YYYY');
}

function getImageUrl(post) {
  let image
  if (post.image) {
    image = post.image
  } else {
    image = window.location.origin + "/images/no-image.png"
  }
  return new URL(image, import.meta.url).href
}

const updateOrdering = () => {
  getDisplayPosts(
      1,
      search_category.value,
      search_version.value,
      search_id.value,
      search_title.value,
      search_content.value,
      search_global.value,
      orderColumn.value,
      orderDirection.value
  );
}
watch(search_category, (current, previous) => {
  getDisplayPosts(
      1,
      current,
      search_id.value,
      search_title.value,
      search_content.value,
      search_global.value
  )
})
watch(search_version, (current, previous) => {
  getDisplayPosts(
      1,
      current,
      search_id.value,
      search_title.value,
      search_content.value,
      search_global.value
  )
})
watch(search_id, (current, previous) => {
  getDisplayPosts(
      1,
      search_category.value,
      search_version.value,
      current,
      search_title.value,
      search_content.value,
      search_global.value
  )
})
watch(search_title, (current, previous) => {
  getDisplayPosts(
      1,
      search_category.value,
      search_version.value,
      search_id.value,
      current,
      search_content.value,
      search_global.value
  )
})
watch(search_content, (current, previous) => {
  getDisplayPosts(
      1,
      search_category.value,
      search_version.value,
      search_id.value,
      search_title.value,
      current,
      search_global.value
  )
})
watch(search_global, _.debounce((current, previous) => {
  getDisplayPosts(
      1,
      search_category.value,
      search_version.value,
      search_id.value,
      search_title.value,
      search_content.value,
      current
  )
}, 200))

function getUserPosts(id) {
  search_user_id.value = id
  getDisplayPosts(
      1,
      search_category.value,
      search_version.value,
      search_id.value,
      search_title.value,
      search_content.value,
      search_global.value,
      search_user_id.value
  )
}
</script>
