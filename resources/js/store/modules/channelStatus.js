const state = {
    hasShopifyStore: false
};
const getters = {
    hasShopifyStore: state => state.hasShopifyStore
};
const actions = {
    toggleShopifyStoreStatus: ({ commit }) => {
        commit("toggleShopifyStoreStatus");
    }
};
const mutations = {
    toggleShopifyStoreStatus: state =>
        (state.hasShopifyStore = !state.hasShopifyStore)
};
