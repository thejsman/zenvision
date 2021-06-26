const state = {
    stripeAccounts: [],
    stripePaginatedUrl: "getStripeTransactions",
    stripeTransactionsArray: []
};
const getters = {
    stripeAccounts: state => state.stripeAccounts,
    stripeTransactions: state => state.stripeTransactionsArray
};
const actions = {
    getStripeAccounts: async ({ commit }) => {
        const result = await axios.get("getstripeaccounts");
        if (result.data.length > 0) {
            commit("SET_STRIPE_ACCOUNT", result.data);
            commit("TOGGGLE_STRIPE_ACCOUNT_STATUS", true, { root: true });
        } else {
            commit("SET_STRIPE_ACCOUNT", []);
            commit("TOGGGLE_STRIPE_ACCOUNT_STATUS", false, { root: true });
        }
    },
    getStripeTransactions: async ({
        commit,
        state,
        rootState,
        rootGetters
    }) => {
        const { data: stripeTransactions } = await axios.get(
            "stripeaccount-transactions",
            {
                params: {
                    start_date: rootGetters.transStartDate,
                    end_date: `${rootGetters.transEndDate} 23:59:59`
                }
            }
        );
        if (stripeTransactions.length > 0) {
            commit("SET_STRIPE_TRANSACTIONS", stripeTransactions);
        }
    }
};
const mutations = {
    SET_STRIPE_ACCOUNT: (state, payload) => {
        state.stripeAccounts = payload;
    },
    SET_STRIPE_TRANSACTIONS: (state, payload) =>
        (state.stripeTransactionsArray = payload)
};

export default {
    state,
    getters,
    actions,
    mutations
};
