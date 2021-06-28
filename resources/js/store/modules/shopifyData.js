import axios from "axios";
import { sumBy } from "lodash";

const state = {
    hasShopifyStore: false,
    shopifyStores: [],
    orders: [],
    allOrders: [],
    cogsTotal: 0
};
const getters = {
    hasShopifyStoreCS: state => state.hasShopifyStore,
    shopifyOrders: state => state.orders,
    shopifyAllOrders: state => state.allOrders,
    shopifyStores: state => state.shopifyStores,
    cogsTotal: state => state.cogsTotal
};
const actions = {
    toggleShopifyStoreStatus: ({ commit }, payload) => {
        commit("TOGGGLE_SHOPIFY_STORE_STATUS", payload);
    },
    getShopifyStores: async ({ commit, dispatch }, section = "PA") => {
        try {
            const result = await axios.get("/user/stores");
            const data = result.data;

            if (data.length > 0) {
                commit("SET_SHOPIFY_STORES", data);
                const status = data.map(
                    element => element.enabled_on_dashboard
                );
                if (section === "MS") {
                    dispatch("getShopifyStoreAllOrders");
                } else {
                    dispatch("getShopifyStoreOrders");
                }

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
            commit("SET_COGS_ALL_ORDERS", response.data);
        } catch (err) {
            console.log(err);
            commit("SET_SHOPIFY_ALL_ORDERS", []);
            commit("SET_COGS_ALL_ORDERS", []);
        }
    },
    removeShopifyAccount: async ({ commit }, account) => {
        commit("REMOVE_SHOPIFY_ACCOUNT", account);
    }
};
const mutations = {
    TOGGGLE_SHOPIFY_STORE_STATUS: (state, status) => {
        state.hasShopifyStore = status;
    },
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
    },
    SET_COGS_ALL_ORDERS: (state, payload) => {
        state.cogsTotal = sumBy(payload, order => order.total_cost);
    },
    REMOVE_SHOPIFY_ACCOUNT: (state, payload) => {
        state.shopifyStores = state.shopifyStores.filter(
            account => account.id !== payload.id
        );
        if (state.shopifyStores.length === 0) {
            state.hasShopifyStore = false;
            state.orders = [];
            state.allOrders = [];
            state.cogsTotal = 0;
        }
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
