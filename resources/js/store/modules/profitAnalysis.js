const state = {};
const getters = {};
const actions = {
    loadAllChannelsPA: async ({ dispatch }) => {
        console.log("PA loadAllChannels");
        await Promise.allSettled([
            dispatch("getShopifyStores", "PA", { root: true }),
            dispatch("getStripeAccounts", null, { root: true })
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
