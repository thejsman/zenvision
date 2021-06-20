const state = {
    netEquityTotal: 0,
    assetsCashTotal: 0,
    assetsInventoryTotal: 0,
    assetsReservesTotal: 0,
    debtsCreditCardTotal: 0,
    debtsSupplierPayableTotal: 0,
    YesterdaysNetEquityFluctuationTotal: 0,
    YesterdaysProfitOrLossTotal: 0,
    OtherExpensesTotal: 0
};
const getters = {};
const actions = {
    loadAllChannels: async context => {
        context.dispatch("toggleLoadingStatus", true, { root: true });
        await context.dispatch("getShopifyStores", null, { root: true });
        await context.dispatch("getStripeAccounts", null, { root: true });
        context.dispatch("toggleLoadingStatus", false, { root: true });
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
