import axios from "axios";

const state = {
    shopifyStores: [],
    orders: [],
    allOrders: []
};
const getters = {
    shopifyOrders: state => state.orders,
    shopifyAllOrders: state => state.allOrders,
    shopifyStores: state => state.shopifyStores
};
const actions = {
    getShopifyStores: async ({ commit, rootState }) => {
        try {
            const result = await axios.get("/user/stores");
            const data = result.data;

            if (data.length > 0) {
                commit("SET_SHOPIFY_STORES", data);
                const status = data.map(
                    element => element.enabled_on_dashboard
                );

                commit("TOGGGLE_SHOPIFY_STORE_STATUS", status.includes(true));
            } else {
                commit("SET_SHOPIFY_STORES", []);
                commit("TOGGGLE_SHOPIFY_STORE_STATUS", false);
            }
        } catch (err) {
            commit("SET_SHOPIFY_STORES", []);
            commit("TOGGGLE_SHOPIFY_STORE_STATUS", false);
            console.log(err);
        }
    },
    getShopifyStoreOrders: async ({ commit, rootState }) => {
        try {
            const response = await axios.get("shopify-orders", {
                params: {
                    start_date: rootState.dateRange.startDateS,
                    end_date: `${rootState.dateRange.endDateS} 23:59:59`
                }
            });

            commit("SET_SHOPIFY_ORDERS", response.data);
        } catch (err) {
            console.log(err);
            commit("SET_SHOPIFY_ORDERS", []);
        }
    },
    getShopifyStoreAllOrders: async ({ commit, rootState }) => {
        try {
            const response = await axios.get("shopify-allorders");

            commit("SET_SHOPIFY_ALL_ORDERS", response.data);
        } catch (err) {
            console.log(err);
            commit("SET_SHOPIFY_ALL_ORDERS", []);
        }
    }
};
const mutations = {
    SET_SHOPIFY_STORES: (state, payload) => {
        if (payload.length > 0) {
            state.shopifyStores = payload;
        } else {
            state.shopifyStores = [];
        }
    },
    SET_SHOPIFY_ORDERS: (state, payload) => {
        if (payload.length > 0) {
            state.orders = payload;
        } else {
            state.orders = [];
        }
    },
    SET_SHOPIFY_ALL_ORDERS: (state, payload) => {
        if (payload.length > 0) {
            state.allOrders = payload;
        } else {
            state.allOrders = [];
        }
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
