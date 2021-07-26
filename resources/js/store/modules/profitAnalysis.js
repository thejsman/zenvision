const state = {
    subscriptionTotal: null
};
const getters = {
    subscriptionTotal: state => state.subscriptionTotal
};
const actions = {
    loadAllChannelsPA: async ({ dispatch }) => {
        await Promise.allSettled([
            dispatch("getShopifyStores", "PA", { root: true }),
            dispatch("getStripeAccountsPA", null, { root: true })
        ]);

        dispatch("toggleLoadingStatus", false, { root: true });
    },
    setSubscriptionTotal: async ({ commit }, payload) => {
        commit("SET_SUBSCRIPTION_TOTAL", payload);
    }
};
const mutations = {
    SET_SUBSCRIPTION_TOTAL: (state, payload) => {
        state.subscriptionTotal = payload;
    }
};

export default {
    // namespaced: true,
    state,
    getters,
    actions,
    mutations
};
