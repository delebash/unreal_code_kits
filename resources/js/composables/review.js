import {ref, inject} from 'vue'
import {useRouter} from 'vue-router'

export default function useReviews() {

    const review = ref({
        data: '',
        rating: ''
    })
    const router = useRouter()
    const validationErrors = ref({})
    const isLoading = ref(false)
    const swal = inject('$swal')


    const getReview = async (id) => {
        axios.get('/api/get-review/' + id)
            .then(response => {
                review.value = response.data;

            })
    }

    const storeReview = async (review,post_id) => {
        if (isLoading.value) return;
        isLoading.value = true
        validationErrors.value = {}

        axios.post('/api/posts/' + post_id + '/reviews', review, {

        })
            .then(response => {
                router.push({name: 'public-posts.details', params: {id: post_id}})
                swal({
                    icon: 'success',
                    title: 'Review added successfully'
                })
            })
            .catch(error => {
                if (error.response?.data.data) {
                    validationErrors.value = error.response.data.data.errors
                }
            })
            .finally(() => isLoading.value = false)
    }

    const updateReview = async (review,post_id) => {
        if (isLoading.value) return;
        isLoading.value = true
        validationErrors.value = {}

        axios.put('/api/posts/' + post_id + '/reviews/' + review.id,review)
            .then(response => {
                router.push({name: 'public-posts.details', params: {id: post_id}})
                swal({
                    icon: 'success',
                    title: 'Review updated successfully'
                })
            })
            .catch(error => {
                if (error.response?.data.data) {
                    validationErrors.value = error.response.data.data.errors
                }
            })
            .finally(() => isLoading.value = false)
    }

    const deleteReview = async (id) => {
        swal({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this action!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            confirmButtonColor: '#ef4444',
            timer: 20000,
            timerProgressBar: true,
            reverseButtons: true
        })
            .then(result => {
                if (result.isConfirmed) {
                    axios.delete('/api/posts/' + id)
                        .then(response => {
                            getPosts()
                            router.push({name: 'posts.index'})
                            swal({
                                icon: 'success',
                                title: 'Post deleted successfully'
                            })
                        })
                        .catch(error => {
                            swal({
                                icon: 'error',
                                title: 'Something went wrong'
                            })
                        })
                }
            })

    }

    return {
        review,
        getReview,
        storeReview,
        updateReview,
        deleteReview,
        validationErrors,
        isLoading
    }
}
