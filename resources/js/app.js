import './bootstrap';

import {createApp} from 'vue';
import store from './store'
import router from './routes/index'
import VueSweetalert2 from "vue-sweetalert2";
import {abilitiesPlugin} from '@casl/vue';
import ability from './services/ability';
import vSelect from "vue-select";
import useAuth from './composables/auth';
import i18n from "./plugins/i18n";
import '@fortawesome/fontawesome-free/css/all.css'
import '@fortawesome/fontawesome-free/js/all.js'
import 'sweetalert2/dist/sweetalert2.min.css';
import 'vue-select/dist/vue-select.css';
import "ag-grid-community/styles/ag-grid.css";
import "ag-grid-community/styles/ag-theme-alpine.css";
import {AgGridVue} from "ag-grid-vue3";
const app = createApp({
    created() {
        useAuth().getUser()
    }
});


app.use(router)
app.use(store)
app.use(VueSweetalert2)
app.use(i18n)
app.use(abilitiesPlugin, ability)
app.component("v-select", vSelect);
app.component('AgGridVue', AgGridVue);
app.mount('#app')
