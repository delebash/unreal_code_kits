<template>
    <div class="row justify-content-center m-05">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form @submit.prevent="submitForm">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="post-title" class="form-label">
                                Title
                            </label>
                            <input v-model="post.title" id="post-title" type="text" class="form-control">
                            <div class="text-danger mt-1">
                                {{ errors.title }}
                            </div>
                            <div class="text-danger mt-1">
                                <div v-for="message in validationErrors?.title">
                                    {{ message }}
                                </div>
                            </div>
                        </div>
                        <!-- Content -->
                        <div class="mb-3">
                            <label for="post-content" class="form-label">
                                Description
                            </label>
                            <textarea style="min-height: 200px" v-model="post.content" id="post-content"
                                      class="form-control"></textarea>
                            <div class="text-danger mt-1">
                                {{ errors.content }}
                            </div>
                            <div class="text-danger mt-1">
                                <div v-for="message in validationErrors?.content">
                                    {{ message }}
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="post-details" class="form-label">
                                Details
                            </label>
                            <div>
                                <ckeditor id="post-details" style="max-height: 100px" :editor="editor"
                                          v-model="post.details"
                                          :config="editorConfiguration"></ckeditor>
                                <div id="word-count" class="mb-2"></div>
                            </div>
                            <div class="text-danger mt-1">
                                {{ errors.details }}
                            </div>
                            <div class="text-danger mt-1">
                                <div v-for="message in validationErrors?.details">
                                    {{ message }}
                                </div>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="post-category" class="form-label">
                                Category
                            </label>
                            <v-select multiple v-model="post.categories" :options="categoryList"
                                      :reduce="category => category.id"
                                      label="name" class="form-control"/>
                            <div class="text-danger mt-1">
                                {{ errors.categories }}
                            </div>
                            <div class="text-danger mt-1">
                                <div v-for="message in validationErrors?.categories">
                                    {{ message }}
                                </div>
                            </div>
                        </div>
                        <!-- Unreal Versions -->
                        <div class="mb-3">
                            <label for="post-category" class="form-label">
                                Unreal Versions
                            </label>
                            <v-select multiple v-model="post.versions" :options="versionList"
                                      :reduce="version => version.id"
                                      label="name" class="form-control"/>
                            <div class="text-danger mt-1">
                                {{ errors.versions }}
                            </div>
                            <div class="text-danger mt-1">
                                <div v-for="message in validationErrors?.versions">
                                    {{ message }}
                                </div>
                            </div>
                        </div>
                        <!-- Post Type -->
                        <div class="mb-3">
                            <label for="post-category" class="form-label">
                                Post Type
                            </label>
                            <v-select v-model="post.type" :options="typeList" :reduce="type => type.id"
                                      label="type" class="form-control"/>
                            <div class="text-danger mt-1">
                                {{ errors.type }}
                            </div>
                            <div class="text-danger mt-1">
                                <div v-for="message in validationErrors?.type">
                                    {{ message }}
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="thumbnail" class="form-label">
                                Thumbnail
                            </label>
                            <input @change="post.thumbnail = $event.target.files[0]" type="file" class="form-control"
                                   id="thumbnail"/>
                            <div class="text-danger mt-1">
                                <div v-for="message in validationErrors?.thumbnail">
                                    {{ message }}
                                </div>
                            </div>
                            <div>
                                <img :src="post.thumbnail" alt="" height="100">
                            </div>
                        </div>
                        <!-- Buttons -->
                        <div class="mt-4">
                            <button :disabled="isLoading" class="btn btn-primary">
                                <div v-show="isLoading" class=""></div>
                                <span v-if="isLoading">Processing...</span>
                                <span v-else>Save</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import {onMounted, reactive, watchEffect} from "vue";
import {useRoute} from "vue-router";
import useCategories from "@/composables/categories";
import useVersions from "@/composables/versions";
import usePosts from "@/composables/posts";
import {useForm, useField, defineRule} from "vee-validate";
import {required, min} from "@/validation/rules"
import {shallowRef} from 'vue';

//ckeditor
import CKEditor from '@ckeditor/ckeditor5-vue';
// removing the /build/ckeditor, so you must keep all the content of the online build tool
import Editor from 'ckeditor5-custom-build';
import {editorConfiguration} from '@/constants'

const editor = shallowRef(Editor.Editor);
const ckeditor = shallowRef(CKEditor.component);

//ckeditor

defineRule('required', required)
defineRule('min', min);

// Define a validation schema
const schema = {
    title: 'required|min:5',
    content: 'required|min:50',
    categories: 'required',
    // details: 'min:50',
    versions: 'required',
    type: 'required'
}
// Create a form context with the validation schema
const {validate, errors, resetForm} = useForm({validationSchema: schema})
// Define actual fields for validation
const {value: title} = useField('title', null, {initialValue: ''});
const {value: details} = useField('details', null, {initialValue: ''});
const {value: content} = useField('content', null, {initialValue: ''});
const {value: categories} = useField('categories', null, {initialValue: '', label: 'category'});
const {value: versions} = useField('versions', null, {initialValue: '', label: 'version'});
const {value: type} = useField('type', null, {initialValue: '', label: 'type'});
const {categoryList, getCategoryList} = useCategories()
const {versionList, getVersionList} = useVersions()
const typeList = [{id: 1, type: "Code Kit"}, {id: 2, type: "Other"}]
const {post: postData, getPost, updatePost, validationErrors, isLoading} = usePosts()
const post = reactive({
    title,
    content,
    categories,
    versions,
    details,
    type,
    thumbnail: ''
})


const route = useRoute()

function submitForm() {
    validate().then(form => {
        if (form.valid) updatePost(post)
    })
}

onMounted(() => {
    getPost(route.params.id)
    getCategoryList()
    getVersionList()
})
// https://vuejs.org/api/reactivity-core.html#watcheffect
watchEffect(() => {
    post.id = postData.value.id
    post.title = postData.value.title
    post.content = postData.value.content
    post.thumbnail = postData.value.image
    post.categories = postData.value.categories
    post.versions = postData.value.versions
    post.details = postData.value.details
    post.type = postData.value.type
})
</script>
