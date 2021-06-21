import _ from "lodash";
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
const getters = {
    debtsSupplierPayableTotal: (state, getters, rootState, rootGetters) => {
        // const cogsTotal = _.sumBy(rootState.getters.allOrders, order =>
        //     parseFloat(order.total_cost)
        // );

        const { shopifyData } = rootState;
        const { allOrders } = shopifyData;
        setTimeout(() => {
            console.log({ "Check this": rootGetters.shopifyAllOrders });
        }, 1000);

        // rootState.shopifyData.allOrders.map(order => console.log(order));
        // state.debtsSupplierPayableTotal = cogsTotal;
        // console.log({ cogsTotal });
        return 123;
    }
};
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
