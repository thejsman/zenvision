const state = {
    hasShopifyStore: false,
    currentChannel: "PA",
    hasBankAccount: false,
    loadingStatus: true
};
const getters = {
    hasShopifyStoreCS: state => state.hasShopifyStore,
    currentChannel: state => state.currentChannel,
    hasBankAccountCS: state => state.hasBankAccount,
    loadingStatus: state => state.loadingStatus
};
const actions = {
    toggleShopifyStoreStatus: ({ commit }, payload) => {
        commit("TOGGGLE_SHOPIFY_STORE_STATUS", payload);
    },

    toggleLoadingStatus: ({ commit }, payload) => {
        commit("TOGGGLE_LOADING_STATUS", payload);
    },
    toggleCurrentChannel: ({ commit }, payload) => {
        console.log("Channel Status change", payload);
        commit("TOGGGLE_CURRENT_CHANNEL", payload);
    }
};
const mutations = {
    TOGGGLE_SHOPIFY_STORE_STATUS: (state, status) => {
        state.hasShopifyStore = status;
    },

    TOGGGLE_LOADING_STATUS: (state, status) => {
        state.loadingStatus = status;
    },
    TOGGGLE_BANK_ACCOUNT_STATUS: (state, status) => {
        state.hasBankAccount = status;
    },
    TOGGGLE_CURRENT_CHANNEL: (state, payload) => {
        state.currentChannel = payload;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
