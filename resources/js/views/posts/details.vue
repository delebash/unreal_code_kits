<template>
    <div class="container-fluid" style="overflow-x: hidden">
        <div class="row g-5">
            <div class="col-md-10">
                <h3 class="fst-italic mt-2 mb-2">
                    {{ post?.title }}
                </h3>
                <div class="border-bottom m-2"></div>
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

                Categories:
                <strong class="m-1" v-for="category in post?.categories" :key="category.id">
                    <router-link :to="{ name: 'category-posts.index', params: { id: category?.id } }">
                        {{ category.name }}
                    </router-link>
                </strong>
                Versions:
                <strong class="m-1" v-for="version in post?.versions" :key="version.id">
                    <router-link :to="{ name: 'version-posts.index', params: { id: version?.id } }">
                        {{ version.name }}
                    </router-link>
                </strong>
                <div class="m-2">
                    <router-link v-if="can('post-edit') || post?.user_id === user.id || can('post-all')  "
                                 :to="{ name: 'posts.edit', params: { id: post?.id }}"
                                 class="badge bg-primary">Edit Post
                    </router-link>
                    <a href="#" v-if="can('post-delete')  && post?.user_id === user.id || can('post-all')"
                       class="ms-2 badge bg-danger" @click="deletePost(post?.id)">Delete Post</a>
                </div>


                <div class="mb-3">
                    <img :src="getImageUrl(post?.image)" width="200" height="200" alt="image" class="img-fluid">
                </div>
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="post-tab" data-bs-toggle="tab" data-bs-target="#post"
                                type="button" role="tab" aria-controls="post" aria-selected="true">Info
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="review-tab" data-bs-toggle="tab" data-bs-target="#review"
                                type="button" role="tab" aria-controls="review" aria-selected="false">Reviews
                        </button>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="post" role="tabpanel" aria-labelledby="post-tab">
                        <div class="mt-2">
                            <div class="mb-0">
                                <b>Summary:</b>
                            </div>
                            <div style="overflow-y: auto; max-height: 75px">
                                <p>{{ post?.content }}</p>
                            </div>
                        </div>

                        <div class="mt-1">
                            <b>Details:</b>
                        </div>
                        <article class="blog-post mb-2" style="overflow: auto; max-height:225px; min-height:100px;">
                            <div id="preview" v-html="post?.details"></div>
                        </article>
                    </div>
                    <div class="tab-pane m-1" id="review" role="tabpanel" aria-labelledby="review-tab">
                        <div v-if=" post">
                            <router-link v-if="can('review-create')"
                                         :to="{ name: 'review.create', params: { id: post.id }} ">
                                <button class="btn btn-primary m-1">Add Review</button>
                            </router-link>
                        </div>
                        <div style="max-height: 325px; overflow-y: auto" v-if=" post?.reviews">
                            <div v-for="review in post.reviews">
                                <div id="post-content" class="form-control mt-2">
                                    <star-rating :inline=true :star-size=25 :read-only=true :increment=0.5
                                                 :rating="review.rating"></star-rating>
                                    <span class="m-1">By: {{ review.user.name }}  On: {{
                                            formatDate(review?.created_at)
                                        }}</span>
                                    <router-link
                                        v-if="can('review-edit') || review.user_id === user.id || can('review-all')"
                                        :to="{ name: 'review.edit', query: {post_id: post.id}, params: { id: review.id }}"
                                        class="badge bg-primary">Edit
                                    </router-link>
                                    <a href="#"
                                       v-if="can('review-delete')  || review.user_id === user.id  || can('review-all') "
                                       class="ms-2 badge bg-danger" @click="deleteReview(review.id)">Delete</a>
                                    <div class="mt-2">
                                        {{ review.data }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="position-sticky" style="top: 2rem;">
                    <div>
                        <h4 class="fst-italic">Categories</h4>
                        <ol v-if="categories?.length > 0" class="list-unstyled">
                            <li v-for="category in categories" :key="category.id">
                                <router-link :to="{ name: 'category-posts.index', params: { id: category.id } }">
                                    {{ category.name }}
                                </router-link>
                            </li>
                        </ol>
                    </div>
                    <div>
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
import {ref, onMounted, computed, inject} from 'vue';
import {useRoute, useRouter} from "vue-router";
import dayjs from "dayjs";
import StarRating from 'vue-star-rating'
import {useAbility} from "@casl/vue";
import {useStore} from 'vuex'
import usePosts from "@/composables/posts";

const swal = inject('$swal')
const store = useStore()
const user = computed(() => store.state.auth.user)
const {deletePost} = usePosts()

// console.log(user)
const {can} = useAbility();
const post = ref();
const categories = ref();
const versions = ref();
const route = useRoute()
const router = useRouter()
const review = ref('')


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

async function deleteReview(id) {
    axios.delete('/api/reviews/' + id)
        .then(response => {
            //router.push({name: 'public-posts.details', params: { id: post.id }})
            router.go(0)
            swal({
                icon: 'success',
                title: 'Review deleted successfully',
                showConfirmButton: false
            })
        })
        .catch(error => {
            if (error.response?.data) {
                console.log(error)
            }
        })
}
</script>
