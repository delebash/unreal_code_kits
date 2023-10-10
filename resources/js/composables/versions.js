import { ref, inject } from 'vue'
import { useRouter } from 'vue-router'

export default function useVersions() {
    const versions = ref([])
    const versionList = ref([])
    const version = ref({
        name: ''
    })

    const router = useRouter()
    const validationErrors = ref({})
    const isLoading = ref(false)
    const swal = inject('$swal')

    const getVersions = async (
        page = 1,
        search_id = '',
        search_title = '',
        search_global = '',
        order_column = 'created_at',
        order_direction = 'desc'
    ) => {
        axios.get('/api/versions?page=' + page +
            '&search_id=' + search_id +
            '&search_title=' + search_title +
            '&search_global=' + search_global +
            '&order_column=' + order_column +
            '&order_direction=' + order_direction)
            .then(response => {
                versions.value = response.data;
            })
    }

    const getVersion = async (id) => {
        axios.get('/api/versions/' + id)
            .then(response => {
                version.value = response.data.data;
            })
    }

    const storeVersion = async (version) => {
        if (isLoading.value) return;

        isLoading.value = true
        validationErrors.value = {}

        axios.post('/api/versions', version)
            .then(response => {
                router.push({name: 'versions.index'})
                swal({
                    icon: 'success',
                    title: 'Version saved successfully'
                })
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors
                }
            })
            .finally(() => isLoading.value = false)
    }

    const updateVersion = async (version) => {
        if (isLoading.value) return;

        isLoading.value = true
        validationErrors.value = {}

        axios.put('/api/versions/' + version.id, version)
            .then(response => {
                router.push({name: 'versions.index'})
                swal({
                    icon: 'success',
                    title: 'Version updated successfully'
                })
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors
                }
            })
            .finally(() => isLoading.value = false)
    }

    const deleteVersion = async (id) => {
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
                    axios.delete('/api/versions/' + id)
                        .then(response => {
                            getVersions()
                            router.push({name: 'versions.index'})
                            swal({
                                icon: 'success',
                                title: 'Version deleted successfully'
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

    const getVersionList = async () => {
        axios.get('/api/version-list')
            .then(response => {
                versionList.value = response.data.data;
            })
    }

    return {
        versionList,
        versions,
        version,
        getVersions,
        getVersionList,
        getVersion,
        storeVersion,
        updateVersion,
        deleteVersion,
        validationErrors,
        isLoading
    }
}
