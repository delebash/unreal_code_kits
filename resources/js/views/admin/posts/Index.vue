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
const search_version = ref('')
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
  resizable: true,
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
      headerName: "UE Versions",
      filter: "agMultiColumnFilter",
      cellStyle: {'white-space': 'normal'},
      autoHeight: true,
      icons: {
        menu: '<i class="fa-solid fa-filter"></i>',
      },
      field: "versions", valueGetter: params => {
        let value = ""
        for (let version of params.data.versions) {
          value = value + version.name + ", "
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

function deleteItem(id) {
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
      search_version.value,
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
      search_version.value,
      search_id.value,
      search_title.value,
      search_content.value,
      search_global.value
  )
})
watch(search_version, (current, previous) => {
    getPosts(
        1,
        search_category.value,
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
      search_version.value,
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
      search_version.value,
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
      search_version.value,
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
      search_version.value,
      search_id.value,
      search_title.value,
      search_content.value,
      current
  )
}, 200))


</script>
