<template>
    <div class="row justify-content-center m-05">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form @submit.prevent="submitForm">
                        <star-rating :inline=true :star-size=25 :read-only=false :increment=1
                                     v-model:rating="review.rating">
                        </star-rating>
                        <!-- Review -->
                        <div class="mb-3">
                            <label for="review-data" class="form-label">
                                Review
                            </label>
                            <textarea class="form-control mt-2" style="min-height: 300px" v-model="review.data"/>
                            <div class="text-danger mt-1">
                                {{ errors.data }}
                            </div>
                            <div class="text-danger mt-1">
                                <div v-for="message in validationErrors?.data">
                                    {{ message }}
                                </div>
                            </div>
                        </div>
                        <!-- Buttons -->
                        <div class="mt-4">
                            <button class="btn btn-primary">
                                <span>Save</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</template>
<script setup>
import {inject, reactive, onMounted, watchEffect} from "vue";
import {useRouter} from 'vue-router'
import {useRoute} from "vue-router";
import StarRating from "vue-star-rating";
import {useForm, useField, defineRule} from "vee-validate";
import {required, min} from "@/validation/rules"
import useReviews from "@/composables/review";
import usePosts from "@/composables/posts";

const route = useRoute()
const router = useRouter()

defineRule('required', required)
defineRule('min', min);

// Define a validation schema
const schema = {
    data: 'required|min:50'
}
// Create a form context with the validation schema
const {validate, errors, resetForm} = useForm({validationSchema: schema})
// Define actual fields for validation
const {value: data} = useField('data', null, {initialValue: ''});
const {value: rating} = useField('rating', null, {initialValue: 0});
const {storeReview, validationErrors, isLoading} = useReviews()
const review = reactive({
    data,
    rating,
})


const id = route.params.id

function submitForm() {
    validate().then(form => {
        if (form.valid) {
            storeReview(review,id)
        }
    })
}

onMounted(() => {

})


</script>
