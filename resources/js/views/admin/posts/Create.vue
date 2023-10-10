<template>
  <div class="row justify-content-center m-0">
    <div class="col-md-10">
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
              <textarea v-model="post.content" id="post-content" class="form-control"></textarea>
              <div class="text-danger mt-1">
                {{ errors.content }}
              </div>
              <div class="text-danger mt-1">
                <div v-for="message in validationErrors?.content">
                  {{ message }}
                </div>
              </div>
            </div>
            <div>
              Details
              <div>
                <ckeditor style="max-height: 100px" :editor="editor" v-model="post.details" :config="editorConfiguration"></ckeditor>
                <div id="word-count" class="mb-2"></div>
              </div>
            </div>

            <!-- Category -->
            <div class="mb-3">
              <label for="post-category" class="form-label">
                Category
              </label>
              <v-select multiple v-model="post.categories" :options="categoryList"
                        :reduce="category => category.id" label="name" class="form-control"/>
              <div class="text-danger mt-1">
                {{ errors.categories }}
              </div>
              <div class="text-danger mt-1">
                <div v-for="message in validationErrors?.categories">
                  {{ message }}
                </div>
              </div>
            </div>
            <!-- Versions -->
            <div class="mb-3">
              <label for="post-version" class="form-label">
                Unreal Version
              </label>
              <v-select multiple v-model="post.versions" :options="versionList"
                        :reduce="version => version.id" label="name" class="form-control"/>
              <div class="text-danger mt-1">
                {{ errors.versions }}
              </div>
              <div class="text-danger mt-1">
                <div v-for="message in validationErrors?.versions">
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
import {onMounted, reactive} from "vue";
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
  versions: 'required'
}
// Create a form context with the validation schema
const {validate, errors} = useForm({validationSchema: schema})
// Define actual fields for validation
const {value: title} = useField('title', null, {initialValue: ''});
const {value: details} = useField('details', null, {initialValue: ''});
const {value: content} = useField('content', null, {initialValue: ''});
const {value: categories} = useField('categories', null, {initialValue: '', label: 'category'});
const {value: versions} = useField('versions', null, {initialValue: '', label: 'version'});
const {categoryList, getCategoryList} = useCategories()
const {versionList, getVersionList} = useVersions()
const {storePost, validationErrors, isLoading} = usePosts()
const post = reactive({
  title,
  content,
  categories,
  versions,
  thumbnail: '',
  details
})

function submitForm() {
  validate().then(form => {
    if (form.valid) storePost(post)
  })
}

onMounted(() => {
  getCategoryList()
  getVersionList()
  console.log(versions)
})
</script>
