<template>
    <div class="container">
        <h2 class="text-center my-4">Code Kits By User</h2>

        <div class="row">
            <div v-for="post in posts?.data" :key="post.id" class="col-sm-3">

                <div class="card mb-2" style="width: 20rem;">
                    <img :src="getImageUrl(post)" width="200" height="200" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">{{ post.title }}</h5>
                        <div class="blog-post-meta m-1">Posted: {{ formatDate(post?.created_at) }}
                            <router-link :to="{ name: 'user-posts.index', params: { id: post?.id } }">
                                By: {{ post?.user?.name }}
                            </router-link>
                            <span>
                        -- Average Rating:
                      <star-rating :inline=true :star-size=25 :read-only=true :increment=0.5
                                   :rating="post?.average_rating"></star-rating>
                </span>
                        </div>
                        <p class="card-text">{{ post.content.substring(0, 100) + "..." }}</p>
                        <p class="card-text">
                            Categories:
                            <strong class="" v-for="category in post.categories" :key="category.id">
                                <router-link :to="{ name: 'category-posts.index', params: { id: category?.id } }">
                                    {{ category.name }}
                                </router-link>
                            </strong>
                            Unreal Versions:
                            <strong class="" v-for="version in post.versions" :key="version.id">
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
</template>

<script setup>
import axios from 'axios';
import {ref, onMounted} from 'vue'
import {useRoute} from "vue-router";
import dayjs from "dayjs";
import StarRating from 'vue-star-rating'

const route = useRoute();
const posts = ref();
const review = ref('')
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

onMounted(() => {
    axios.get('/api/get-category-posts/' + route.params.id).then(({data}) => {
        posts.value = data;
    })
})
</script>
