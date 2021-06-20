const state = {
    hasShopifyStore: false,
    hasStripeAccount: false,
    loadingStatus: false
};
const getters = {
    hasShopifyStoreCS: state => state.hasShopifyStore,
    hasStripeAccountCS: state => state.hasStripeAccount,
    loadingStatus: state => state.loadingStatus
};
const actions = {
    toggleShopifyStoreStatus: ({ commit }, payload) => {
        commit("TOGGGLE_SHOPIFY_STORE_STATUS", payload);
    },
    toggleStripeAccountStatus: ({ commit }, payload) => {
        commit("TOGGGLE_STRIPE_ACCOUNT_STATUS", payload);
    },
    toggleLoadingStatus: ({ commit }, payload) => {
        commit("TOGGGLE_LOADING_STATUS", payload);
    }
};
const mutations = {
    TOGGGLE_SHOPIFY_STORE_STATUS: (state, status) => {
        state.hasShopifyStore = status;
    },
    TOGGGLE_STRIPE_ACCOUNT_STATUS: (state, status) => {
        state.hasStripeAccount = status;
    },
    TOGGGLE_LOADING_STATUS: (state, status) => {
        state.loadingStatus = status;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
