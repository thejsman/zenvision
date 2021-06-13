import axios from "axios";

const state = {
    shopifyStores: [],
    orders: []
};
const getters = {
    shopifyOrders: state => state.orders,
    shopifyStores: state => state.shopifyStores
};
const actions = {
    getShopifyStores: async ({ commit, rootState }) => {
        try {
            const result = await axios.get("/user/stores");
            const data = result.data;

            commit("SHOPIFY_STORES", data);
        } catch (err) {
            commit("SHOPIFY_STORES", []);
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

            commit("SHOPIFY_ORDERS", response.data);
        } catch (err) {
            console.log(err);
            commit("setShopifyStoreOrders", []);
        }
    }
};
const mutations = {
    SHOPIFY_STORES: (state, payload) => {
        if (payload.length > 0) {
            state.shopifyStores = payload;
        } else {
            state.shopifyStores = [];
        }
    },
    SHOPIFY_ORDERS: (state, payload) => {
        if (payload.length > 0) {
            state.orders = payload;
        } else {
            state.orders = [];
        }
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
