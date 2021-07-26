import _ from "lodash";

const state = {
    hasStripeAccount: false,
    stripeFirstLoad: true,
    stripeAccounts: [],
    stripeAccountsBalance: 0,
    stripeTransactionsArray: [],
    stripeChargebackArray: [],
    stripeChargebackTotal: null,
    merchantFeeTotal: null
};
const getters = {
    hasStripeAccountCS: state => state.hasStripeAccount,
    stripeAccounts: state => state.stripeAccounts,
    stripeTransactions: state => state.stripeTransactionsArray,
    stripeAccountsBalance: state => state.stripeAccountsBalance / 100,
    stripeFirstLoad: state => state.stripeFirstLoad,
    stripeChargebackTotal: state => state.stripeChargebackTotal,
    stripeChargebackArray: state => state.stripeChargebackArray,
    stripeMerchantFeeTotal: state => state.merchantFeeTotal
};
const actions = {
    toggleStripeAccountStatus: ({ commit }, payload) => {
        commit("TOGGGLE_STRIPE_ACCOUNT_STATUS", payload);
    },
    getStripeAccounts: async ({ commit, dispatch }) => {
        const { data } = await axios.get("getstripeaccounts");

        if (data.length > 0) {
            commit("SET_STRIPE_ACCOUNT", data);
            dispatch("getStripeBalance");
            commit("TOGGGLE_STRIPE_ACCOUNT_STATUS", true);
        } else {
            commit("SET_STRIPE_ACCOUNT", []);
            commit("TOGGGLE_STRIPE_ACCOUNT_STATUS", false);
        }
    },
    getStripeAccountsPA: async ({ commit, dispatch }) => {
        const { data } = await axios.get("stripe-accounts");

        if (data.length > 0) {
            commit("SET_STRIPE_ACCOUNT", data);
            const status = data.map(element => element.enabled_on_dashboard);
            commit("TOGGGLE_STRIPE_ACCOUNT_STATUS", status.includes(true));
            dispatch("getStripeChargeBack");
            dispatch("getStripeTransactions");
        } else {
            commit("SET_STRIPE_ACCOUNT", []);
            commit("SET_STRIPE_CHARGEBACK_TOTAL", 0);
            commit("SET_STRIPE_TRANSACTIONS", []);
            commit("TOGGGLE_STRIPE_ACCOUNT_STATUS", false);
        }
    },
    getStripeBalance: async ({ commit }) => {
        try {
            const { data } = await axios.get("stripeaccount-balance");
            let balance = 0;
            if (data.length) {
                balance += _.sumBy(data, "amount");
            }
            commit("SET_STRIPE_ACCOUNT_BALANCE", balance);
        } catch (err) {
            console.log(err);
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
        } else {
            commit("SET_STRIPE_TRANSACTIONS", []);
        }
    },
    getStripeChargeBack: async ({ commit, rootGetters }) => {
        try {
            let chargebackTotal = 0;
            const { data } = await axios.get("stripeconnect-chargeback", {
                params: {
                    s_date: `${rootGetters.startDateS} 00:00:00`,
                    e_date: `${rootGetters.endDateS} 23:59:59`
                }
            });

            const ChargebackArray = data.filter(
                sc => sc.status === "charge_refunded" || sc.status === "lost"
            );
            if (ChargebackArray.length) {
                ChargebackArray.forEach(cb => {
                    chargebackTotal += parseFloat(cb.amount / 100);
                });
                commit("SET_STRIPE_CHARGEBACK_TOTAL", chargebackTotal);
                commit("SET_STRIPE_CHARGEBACK_ARRAY", ChargebackArray);
            } else {
                commit("SET_STRIPE_CHARGEBACK_TOTAL", 0);
                commit("SET_STRIPE_CHARGEBACK_ARRAY", []);
            }
        } catch (err) {
            commit("SET_STRIPE_CHARGEBACK_TOTAL", 0);
            console.log({ err });
        }
    },
    setMerchantFeeTotal: async ({ commit }, payload) => {
        commit("SET_MERCHANT_FEE_TOTAL", payload);
    },
    removeStripeAccount: async ({ commit, dispatch }, account) => {
        try {
            await axios.patch("stripeconnectdelete", account);
            dispatch("getStripeAccounts");
            commit("REMOVE_STRIPE_ACCOUNT", account);
        } catch (err) {
            console.log({ err });
        }
    }
};
const mutations = {
    TOGGGLE_STRIPE_ACCOUNT_STATUS: (state, payload) => {
        state.hasStripeAccount = payload;
    },
    SET_STRIPE_ACCOUNT: (state, payload) => {
        state.stripeAccounts = payload;
    },

    SET_STRIPE_TRANSACTIONS: (state, payload) =>
        (state.stripeTransactionsArray = payload),
    SET_STRIPE_CHARGEBACK_ARRAY: (state, payload) =>
        (state.stripeChargebackArray = payload),
    SET_STRIPE_ACCOUNT_BALANCE: (state, payload) =>
        (state.stripeAccountsBalance = payload),

    SET_STRIPE_CHARGEBACK_TOTAL: (state, payload) =>
        (state.stripeChargebackTotal = payload),
    SET_MERCHANT_FEE_TOTAL: (state, payload) => {
        state.merchantFeeTotal = payload;
    },
    REMOVE_STRIPE_ACCOUNT: (state, payload) => {
        const filteredAccounts = state.stripeAccounts.filter(
            account => account.stripe_user_id !== payload.stripe_user_id
        );

        state.stripeAccounts = [...filteredAccounts];
        if (filteredAccounts.length === 0) {
            state.hasStripeAccount = false;
            state.stripeTransactionsArray = [];
        } else {
            state.stripeTransactionsArray = state.stripeTransactionsArray.filter(
                st => st.stripe_user_id !== payload.stripe_user_id
            );
        }
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
