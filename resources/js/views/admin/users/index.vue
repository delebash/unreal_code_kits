<template>
    <div class="row justify-content-center my-2">
        <div class="col-md-12">
            <div class="card border-0">
                <div class="card-header bg-transparent">
                    <h5 class="float-start">Users</h5>
                    <router-link v-if="can('user-list')" :to="{ name: 'users.create' }"
                                 class="btn btn-primary btn-sm float-end">
                        Add New
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
import {ref, onMounted, watch, onBeforeMount, reactive} from "vue";
import useUsers from "../../../composables/users";
import {useAbility} from '@casl/vue'
import GridActions from "@/components/agGrid/GridActions.vue";

const search_id = ref('')
const search_title = ref('')
const search_global = ref('')
const orderColumn = ref('created_at')
const orderDirection = ref('desc')
const {users, getUsers, deleteUser} = useUsers()
const {can} = useAbility()

let context

onBeforeMount(() => {
  context = {componentParent: deleteItem};
})

onMounted(() => {
    getUsers()
})

//ag-grid
const gridApi = ref(null); // Optional - for accessing Grid's API

// Obtain API from grid's onGridReady event
const onGridReady = (params) => {
  gridApi.value = params.api;
};

//vue render cell components
const frameworkComponents = {
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
      headerName: 'Name',
      field: "name",
      icons: {
        menu: '<i class="fa-solid fa-filter"></i>',
      }
    },
    {
      headerName: 'Email',
      field: "email",
      icons: {
        menu: '<i class="fa-solid fa-filter"></i>',
      }
    },
    {
      headerName: "Created On",
      field: "created_at",
      icons: {
        menu: '<i class="fa-solid fa-filter"></i>',
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
        prefix: 'user',
        prefixPlural: 'users'
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
  deleteUser(id)
}
watch(() => users.value, () => {
  rowData.value = users.value.data
})
//ag-grid

const updateOrdering = (column) => {
    orderColumn.value = column;
    orderDirection.value = (orderDirection.value === 'asc') ? 'desc' : 'asc';
    getUsers(
        1,
        search_id.value,
        search_title.value,
        search_global.value,
        orderColumn.value,
        orderDirection.value
    );
}
watch(search_id, (current, previous) => {
    getUsers(
        1,
        current,
        search_title.value,
        search_global.value
    )
})
watch(search_title, (current, previous) => {
    getUsers(
        1,
        search_id.value,
        current,
        search_global.value
    )
})
watch(search_global, _.debounce((current, previous) => {
    getUsers(
        1,
        search_id.value,
        search_title.value,
        current
    )
}, 200))
</script>
