const state = {};
const getters = {};
const actions = {
    loadAllChannelsPA: async ({ dispatch }) => {
        await Promise.allSettled([
            dispatch("getShopifyStores", "PA", { root: true }),
            dispatch("getStripeAccountsPA", null, { root: true })
        ]);

        dispatch("toggleLoadingStatus", false, { root: true });
    }
};
const mutations = {};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
};
