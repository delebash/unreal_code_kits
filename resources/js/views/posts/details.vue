<template>
  <div class="container-fluid" style="overflow-x: hidden">
    <div class="row g-5 mt-4 mb-lg-4">
      <div class="col-md-10">
        <h3 class="pb-4 mb-4 fst-italic border-bottom">
          {{ post?.title }}
        </h3>
        <p class="blog-post-meta">Posted: {{ formatDate(post?.created_at) }} By: {{ post?.user?.name }}</p>
        <article class="blog-post">
          <div>
            <img :src="getImageUrl(post?.image)" width="200" height="200" alt="image" class="img-fluid">
            <div class="mt-4">
              <div class="mb-2">
                <b>Summary:</b>
              </div>
              <p>{{ post?.content }}</p>
            </div>
          </div>
        </article>
        <div class="mt-1">
          <b>Details:</b>
        </div>
        <article class="blog-post mb-2" style="overflow: auto; max-height:400px; min-height:100px;">
          <div id="preview" v-html="post?.details"></div>
        </article>
      </div>

      <div class="col-md-2">
        <div class="position-sticky" style="top: 2rem;">
          <div class="p-4">
            <h4 class="fst-italic">Categories</h4>
            <ol v-if="categories?.length > 0" class="list-unstyled">
              <li v-for="category in categories" :key="category.id">
                <router-link :to="{ name: 'category-posts.index', params: { id: category.id } }">
                  {{ category.name }}
                </router-link>
              </li>
            </ol>
          </div>
          <div class="p-4">
            <h4 class="fst-italic">Unreal Versions</h4>
            <ol v-if="versions?.length > 0" class="list-unstyled">
              <li v-for="version in versions" :key="version.id">
                <router-link :to="{ name: 'version-posts.index', params: { id: version.id } }">
                  {{ version.name }}
                </router-link>
              </li>
            </ol>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import {ref, onMounted} from 'vue';
import {useRoute} from "vue-router";
import dayjs from "dayjs";


const post = ref();
const categories = ref();
const versions = ref();
const route = useRoute()

onMounted(() => {
  axios.get('/api/get-post/' + route.params.id).then(({data}) => {
    post.value = data.data
  })
  axios.get('/api/category-list').then(({data}) => {
    categories.value = data.data
  })
  axios.get('/api/version-list').then(({data}) => {
    versions.value = data.data
  })
})

function formatDate(dateString) {
  const date = dayjs(dateString);
  // Then specify how you want your dates to be formatted
  return date.format('dddd MMMM D, YYYY');
}

function getImageUrl(image) {

  if (!image) {
    image = window.location.origin + "/images/no-image.png"
  }
  return new URL(image, import.meta.url).href
}

</script>
