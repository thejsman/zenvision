import axios from "axios";

const state = {
    orders: []
};
const getters = {
    ShopifyOrders: state => state.orders
};
const actions = {
    getShopifyStoreOrders: async ({ commit, rootState }) => {
        try {
            const response = await axios.get("shopify-orders", {
                params: {
                    start_date: rootState.dateRange.startDateS,
                    end_date: `${rootState.dateRange.endDateS} 23:59:59`
                }
            });

            commit("setShopifyStoreOrders", response.data);
            return response.data;
        } catch (err) {
            console.log(err);
            commit("setShopifyStoreOrders", []);
        }
    }
};
const mutations = {
    setShopifyStoreOrders: (state, payload) => {
        state.orders = payload.orders;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
