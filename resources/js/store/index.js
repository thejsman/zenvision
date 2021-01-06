import Vuex from "vuex";
import Vue from "vue";
import shopifyData from "./modules/shopifyData";
import dateRange from "./modules/dateRange";

//Load Vuex
Vue.use(Vuex);

//Create store
export default new Vuex.Store({
    modules: {
        shopifyData,
        dateRange
    }
});
