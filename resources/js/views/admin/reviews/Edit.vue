<template>
    <div class="row justify-content-center m-05">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div v-if="content.value" class="card-body">
                    fgh
                    <star-rating :inline=true :star-size=25 :read-only=false :increment=0.5
                                 v-model:rating="content.value.rating"></star-rating>
                    <textarea class="form-control mt-2" style="min-height: 300px" v-model="content.value.review"/>
                    <div class="text-danger mt-1">
                        {{ msg.error }}
                    </div>

                </div>
            </div>
        </div>
        <div>
            <button class="btn btn-primary mt-3" @click="saveReview">Save</button>
        </div>
    </div>
</template>
<script setup>
import {inject, reactive, onMounted} from "vue";
import {useRouter} from 'vue-router'
import {useRoute} from "vue-router";
import StarRating from "vue-star-rating";

const route = useRoute()
const router = useRouter()
const swal = inject('$swal')
const id = route.params.id
const post_id = route.query.post_id
let msg = reactive({error: ''})

const content = reactive({review: '', rating: null})
onMounted(() => {
    axios.get('/api/get-review/' + id)
        .then(response => {
            content.value = response.data.data;
        })
})

async function saveReview() {
    if (content.value.review.length < 50) {
        msg.error = "Please write at least 50 characters.";
        return
    } else {
        msg.error = ""
    }
    axios.put('/api/reviews/' + id, content.value)
        .then(response => {
            router.push({name: 'public-posts.details', params: {id: post_id}})
            swal({
                icon: 'success',
                title: 'Review saved successfully'
            })
        })
        .catch(error => {
            if (error.response?.data) {
                console.log(error)
            }
        })
}


</script>
