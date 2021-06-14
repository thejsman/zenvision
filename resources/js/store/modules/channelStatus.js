const state = {
    hasShopifyStore1: false
};
const getters = {
    hasShopifyStoreCS: state => state.hasShopifyStore1
};
const actions = {
    toggleShopifyStoreStatus: ({ commit }) => {
        commit("toggleShopifyStoreStatus");
    }
};
const mutations = {
    TOGGGLE_SHOPIFY_STORE_STATUS: (state, status) => {
        state.hasShopifyStore1 = status;
        console.log("I have set the state", state, status);
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
