import axios from "axios";

const state = {
    orders: [],
    enabledStores: [],
    numberOfProducts: 0,
    refundTotal: 0
};
const getters = {
    ShopifyOrders: state => state.orders,
    ShopifyStores: state => state.enabledStores,
    ShopifyProductCount: state => state.numberOfProducts,
    ShopifyRefundTotal: state => state.refundTotal
};
const actions = {
    async fetchShopifyData({ commit }) {
        try {
            const response = await axios.get("shopifydata");
            console.log({ response });
            commit("setShopifyData", response.data);
        } catch (err) {
            console.log(err);
        }
    }
};
const mutations = {
    setShopifyData: (state, data) => {
        state.orders = data.orders;
        state.enabledStores = data.enabled_on_dashboard;
        state.numberOfProducts = data.number_of_products;
        state.refundTotal = data.refund_total;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
