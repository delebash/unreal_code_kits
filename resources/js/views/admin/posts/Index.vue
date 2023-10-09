<template>
  <div class="row justify-content-center my-2">
    <div class="col-md-12">
      <div class="card border-0">
        <div class="card-header bg-transparent">
          <h5 class="float-start">Posts</h5>
          <router-link v-if="can('post-create')" :to="{ name: 'posts.create' }"
                       class="btn btn-primary btn-sm float-end">
            Create Post
          </router-link>
        </div>
        <div class="card-body shadow-sm">
          <div class="mb-2">
            <span>Search:  </span>
            <input type="text" id="filter-text-box" placeholder="Search all..." v-on:input="onFilterTextBoxChanged()">
          </div>
          <ag-grid-vue
              class="ag-theme-alpine"
              style="height: 400px"
              :columnDefs="columnDefs.value"
              :rowData="rowData.value"
              :defaultColDef="defaultColDef"
              rowSelection="multiple"
              animateRows="true"
              @grid-ready="onGridReady"
              :components="frameworkComponents"
              :context="context"
              cacheQuickFilter="true"
              suppressMenuHide="true"
          >
          </ag-grid-vue>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {reactive, ref, onMounted, watch, onBeforeMount} from "vue";
import usePosts from "@/composables/posts";
import useCategories from "@/composables/categories";
import {useAbility} from '@casl/vue'
import {useRouter} from "vue-router";
import ImageRender from "@/components/agGrid/ImageRender.vue";
import GridActions from "@/components/agGrid/GridActions.vue";

const router = useRouter()

const search_category = ref('')
const search_id = ref('')
const search_title = ref('')
const search_content = ref('')
const search_global = ref('')
const orderColumn = ref('created_at')
const orderDirection = ref('desc')
const {posts, getPosts, deletePost} = usePosts()
// const {categoryList, getCategoryList} = useCategories()
const {can} = useAbility();

let context

onBeforeMount(() => {
  context = {componentParent: deleteItem};
})
onMounted(() => {
  getPosts()
  // getCategoryList()
})


//ag-grid
const gridApi = ref(null); // Optional - for accessing Grid's API

// Obtain API from grid's onGridReady event
const onGridReady = (params) => {
  gridApi.value = params.api;
};

//vue render cell components
const frameworkComponents = {
  ImageRender,
  GridActions
}

// DefaultColDef sets props common to all Columns
const defaultColDef = {
  sortable: true,
  filter: true,
  flex: 1
};

const rowData = reactive({}); // Set rowData to Array of Objects, one Object per Row

// Each Column Definition results in one Column.
const columnDefs = reactive({
  value: [
    {
      headerName: 'ID',
      field: "id",
      icons: {
        menu: '<i class="fa-solid fa-filter"></i>',
      }
    },
    {
      headerName: 'Title',
      field: "title",
      icons: {
        menu: '<i class="fa-solid fa-filter"></i>',
      }
    },
    {
      headerName: 'Description',
      field: "content", icons: {
        menu: '<i class="fa-solid fa-filter"></i>',
      }
    },
    {
      headerName: 'Image',
      autoHeight: true,
      icons: {
        menu: '<i class="fa-solid fa-filter"></i>',
      },
      field: "image", cellRenderer: 'ImageRender'
    },
    {
      headerName: "Created On",
      field: "created_at",
      icons: {
        menu: '<i class="fa-solid fa-filter"></i>',
      }
    },
    {
      headerName: "Categories",
      filter: "agMultiColumnFilter",
      cellStyle: {'white-space': 'normal'},
      autoHeight: true,
      icons: {
        menu: '<i class="fa-solid fa-filter"></i>',
      },
      field: "categories", valueGetter: params => {
        let value = ""
        for (let category of params.data.categories) {
          value = value + category.name + ", "
        }
        value = value.slice(0, -2);
        return value;
      }
    },
    {
      headerName: 'Actions',
      icons: {
        menu: '<i class="fa-solid fa-filter"></i>',
      },
      autoHeight: true,
      field: "id", cellRenderer: 'GridActions',
      cellRendererParams: {
        prefix: 'post',
        prefixPlural: 'posts'
      }
    },
  ],
});

const onFilterTextBoxChanged = () => {
  gridApi.value.setQuickFilter(
      document.getElementById('filter-text-box').value
  );
};
function deleteItem(id){
  deletePost(id)
}

watch(() => posts.value, () => {
  rowData.value = posts.value.data
})
//ag-grid

const updateOrdering = (column) => {
  orderColumn.value = column;
  orderDirection.value = (orderDirection.value === 'asc') ? 'desc' : 'asc';
  getPosts(
      1,
      search_category.value,
      search_id.value,
      search_title.value,
      search_content.value,
      search_global.value,
      orderColumn.value,
      orderDirection.value
  );
}
watch(search_category, (current, previous) => {
  getPosts(
      1,
      current,
      search_id.value,
      search_title.value,
      search_content.value,
      search_global.value
  )
})
watch(search_id, (current, previous) => {
  getPosts(
      1,
      search_category.value,
      current,
      search_title.value,
      search_content.value,
      search_global.value
  )
})
watch(search_title, (current, previous) => {
  getPosts(
      1,
      search_category.value,
      search_id.value,
      current,
      search_content.value,
      search_global.value
  )
})
watch(search_content, (current, previous) => {
  getPosts(
      1,
      search_category.value,
      search_id.value,
      search_title.value,
      current,
      search_global.value
  )
})
watch(search_global, _.debounce((current, previous) => {
  getPosts(
      1,
      search_category.value,
      search_id.value,
      search_title.value,
      search_content.value,
      current
  )
}, 200))



</script>
<!--                    <div class="table-responsive mb-4">-->
<!--                        <table class="table" style="position: absolute; z-index: 30">-->
<!--                            <thead>-->
<!--                            <tr>-->
<!--                                <th class="px-6 py-3 bg-gray-50 text-left">-->
<!--                                    <input v-model="search_id" type="text"-->
<!--                                           class="inline-block mt-1 form-control"-->
<!--                                           placeholder="Filter by ID">-->
<!--                                </th>-->
<!--                                <th class="px-6 py-3 bg-gray-50 text-left">-->
<!--                                    <input v-model="search_title" type="text"-->
<!--                                           class="inline-block mt-1 form-control"-->
<!--                                           placeholder="Filter by Title">-->
<!--                                </th>-->
<!--                                <th class="px-6 py-3 bg-gray-50 text-left">-->
<!--                                    <v-select multiple v-model="search_category" :options="categoryList"-->
<!--                                              :reduce="category => category.id" label="name"-->
<!--                                              class="form-control w-10"/>-->
<!--                                </th>-->
<!--                                <th class="px-6 py-3 bg-gray-50 text-left">-->
<!--                                    <input v-model="search_content" type="text"-->
<!--                                           class="inline-block mt-1 form-control"-->
<!--                                           placeholder="Filter by Content">-->
<!--                                </th>-->
<!--                                <th class="px-6 py-3 text-start"></th>-->
<!--                                <th class="px-6 py-3 text-start"></th>-->
<!--                            </tr>-->
<!--                            </thead>-->
<!--                        </table>-->
<!--                    </div>-->

<!--                    <div class="table-responsive">-->
<!--                        <table class="table">-->
<!--                            <thead>-->
<!--                            <tr>-->
<!--                                <th class="px-6 py-3 text-start">-->
<!--                                    <div class="flex flex-row"-->
<!--                                         @click="updateOrdering('id')">-->
<!--                                        <div class="font-medium text-uppercase"-->
<!--                                             :class="{ 'font-bold text-blue-600': orderColumn === 'id' }">-->
<!--                                            ID-->
<!--                                        </div>-->
<!--                                        <div class="select-none">-->
<!--                                <span :class="{-->
<!--                                  'text-blue-600': orderDirection === 'asc' && orderColumn === 'id',-->
<!--                                  'hidden': orderDirection !== '' && orderDirection !== 'asc' && orderColumn === 'id',-->
<!--                                }">&uarr;</span>-->
<!--                                            <span :class="{-->
<!--                                  'text-blue-600': orderDirection === 'desc' && orderColumn === 'id',-->
<!--                                  'hidden': orderDirection !== '' && orderDirection !== 'desc' && orderColumn === 'id',-->
<!--                                }">&darr;</span>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </th>-->
<!--                                <th class="px-6 py-3 text-left">-->
<!--                                    <div class="flex flex-row"-->
<!--                                         @click="updateOrdering('title')">-->
<!--                                        <div class="font-medium text-uppercase"-->
<!--                                             :class="{ 'font-bold text-blue-600': orderColumn === 'title' }">-->
<!--                                            Title-->
<!--                                        </div>-->
<!--                                        <div class="select-none">-->
<!--                                <span :class="{-->
<!--                                  'text-blue-600': orderDirection === 'asc' && orderColumn === 'title',-->
<!--                                  'hidden': orderDirection !== '' && orderDirection !== 'asc' && orderColumn === 'title',-->
<!--                                }">&uarr;</span>-->
<!--                                            <span :class="{-->
<!--                                  'text-blue-600': orderDirection === 'desc' && orderColumn === 'title',-->
<!--                                  'hidden': orderDirection !== '' && orderDirection !== 'desc' && orderColumn === 'title',-->
<!--                                }">&darr;</span>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </th>-->
<!--                                <th class="px-6 py-3 text-left">-->
<!--                                    <div class="flex flex-row">-->
<!--                                        <div class="font-medium text-uppercase">-->
<!--                                            Thumbnail-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </th>-->
<!--                                <th class="px-6 py-3 bg-gray-50 text-left">-->
<!--                                    <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Category</span>-->
<!--                                </th>-->
<!--                                <th class="px-6 py-3 bg-gray-50 text-left">-->
<!--                                    <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Content</span>-->
<!--                                </th>-->
<!--                                <th class="px-6 py-3 bg-gray-50 text-left">-->
<!--                                    <div class="flex flex-row items-center justify-between cursor-pointer"-->
<!--                                         @click="updateOrdering('created_at')">-->
<!--                                        <div class="leading-4 font-medium text-gray-500 uppercase tracking-wider"-->
<!--                                             :class="{ 'font-bold text-blue-600': orderColumn === 'created_at' }">-->
<!--                                            Created at-->
<!--                                        </div>-->
<!--                                        <div class="select-none">-->
<!--                                <span :class="{-->
<!--                                  'text-blue-600': orderDirection === 'asc' && orderColumn === 'created_at',-->
<!--                                  'hidden': orderDirection !== '' && orderDirection !== 'asc' && orderColumn === 'created_at',-->
<!--                                }">&uarr;</span>-->
<!--                                            <span :class="{-->
<!--                                  'text-blue-600': orderDirection === 'desc' && orderColumn === 'created_at',-->
<!--                                  'hidden': orderDirection !== '' && orderDirection !== 'desc' && orderColumn === 'created_at',-->
<!--                                }">&darr;</span>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </th>-->
<!--                                <th class="px-6 py-3 bg-gray-50 text-left">-->
<!--                                    Actions-->
<!--                                </th>-->
<!--                            </tr>-->
<!--                            </thead>-->
<!--                            <tbody>-->
<!--                            <tr v-for="post in posts.data" :key="post.id">-->
<!--                                <td class="px-6 py-4 text-sm" width="20">-->
<!--                                    {{ post.id }}-->
<!--                                </td>-->
<!--                                <td class="px-6 py-4 text-sm">-->
<!--                                    {{ post.title }}-->
<!--                                </td>-->
<!--                                <td class="px-6 py-4 text-sm">-->
<!--                                    <img v-if="post.image" :src="getImageUrl(post)" alt="image" height="70">-->
<!--                                </td>-->
<!--                                <td class="px-6 py-4 text-sm">-->
<!--                                    <div v-for="category in post.categories">-->
<!--                                        {{ category.name }}-->
<!--                                    </div>-->
<!--                                </td>-->
<!--                                <td class="px-6 py-4 text-sm">-->
<!--                                    {{ post.content }}-->
<!--                                </td>-->
<!--                                <td class="px-6 py-4 text-sm">-->
<!--                                    {{ post.created_at }}-->
<!--                                </td>-->
<!--                                <td class="px-6 py-4 text-sm">-->
<!--                                                        <router-link v-if="can('post-edit')"-->
<!--                                                                     :to="{ name: 'posts.edit', params: { id: 1 } }"-->
<!--                                                                     class="badge bg-primary">Edit-->
<!--                                                        </router-link>-->
<!--                                    <a href="#" v-if="can('post-delete')" @click.prevent="deletePost(post.id)"-->
<!--                                       class="ms-2 badge bg-danger">Delete</a>-->
<!--                                </td>-->
<!--                            </tr>-->
<!--                            </tbody>-->
<!--                        </table>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="card-footer">-->
<!--                    <Pagination :data="posts" :limit="3"-->
<!--                                @pagination-change-page="page => getPosts(page, search_category, search_id, search_title, search_content, search_global, orderColumn, orderDirection)"-->
<!--                                class="mt-4"/>-->
<!--                  <GridActions></GridActions>-->
