import _ from "lodash";
import moment from "moment";
import {
    NET_EQUITY,
    TOTAL_CASH,
    TOTAL_INVENTORY,
    TOTAL_RESERVES,
    TOTAL_CREDIT_CARD,
    TOTAL_SUPPLIER_PAYABLE
} from "../../constants";
import { displayCurrency } from "../../utils";

const state = {
    assetsData: [],
    netEquityTotal: 0,
    assetsCashTotal: 0,
    assetsInventoryTotal: 0,
    assetsReservesTotal: 0,
    debtsCreditCardTotal: 0,
    debtsSupplierPayableTotal: 0,
    YesterdaysNetEquityFluctuationTotal: 0,
    YesterdaysProfitOrLossTotal: 0,
    OtherExpensesTotal: 0,
    assetsCashTotalLoading: true,
    assetsInventoryTotalLoading: true,
    assetsReservesTotalLoading: true,
    transactionsArray: []
};
const getters = {
    transactionsStartDate: state => state.transStartDate,
    transactionsEndDate: state => state.transEndDate,
    assetsDataArray: state => [
        {
            icon: "bx bx-copy-alt",
            title: TOTAL_CASH,
            value: displayCurrency(state.assetsCashTotal),
            loading: state.assetsCashTotalLoading
        },
        {
            icon: "bx bx-archive-in",
            title: TOTAL_INVENTORY,
            value: displayCurrency(state.assetsInventoryTotal),
            loading: state.assetsInventoryTotalLoading
        },
        {
            icon: "bx bx-purchase-tag-alt",
            title: TOTAL_RESERVES,
            value: displayCurrency(state.assetsReservesTotal),
            loading: state.assetsReservesTotalLoading
        }
    ],
    debtsSupplierPayableTotal: (state, rootGetters) => {
        state.debtsSupplierPayableTotal = rootGetters.cogsTotal;
        return rootGetters.cogsTotal;
    },
    netEquityTotal: state =>
        state.assetsCashTotal +
        state.assetsInventoryTotal +
        state.assetsReservesTotal -
        state.debtsCreditCardTotal -
        state.debtsSupplierPayableTotal
};
const actions = {
    loadAllChannels: async ({ dispatch }) => {
        dispatch("toggleLoadingStatus", true, { root: true });
        dispatch("getShopifyStores", "MS", { root: true });
        dispatch("getStripeAccounts", null, { root: true });
        dispatch("toggleLoadingStatus", false, { root: true });
    },
    setLoadingStatus: ({ commit }, payload) => {
        commit("TOGGLE_LOADING_STATUS", payload);
    }
};
const mutations = {
    TOGGLE_LOADING_STATUS: (state, { channel, status }) => {
        console.log("TOGGLE_LOADING_STATUS has been called", state);
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
};
