const state = {
    stripeAccounts: []
};
const getters = {
    stripeAccounts: state => state.stripeAccounts
};
const actions = {
    getStripeAccounts: async ({ commit }) => {
        const result = await axios.get("getstripeaccounts");
        if (result.data.length > 0) {
            commit("SET_STRIPE_ACCOUNT", result.data);
            commit("TOGGGLE_STRIPE_ACCOUNT_STATUS", true);
        } else {
            commit("SET_STRIPE_ACCOUNT", []);
            commit("TOGGGLE_STRIPE_ACCOUNT_STATUS", false);
        }
    }
};
const mutations = {
    SET_STRIPE_ACCOUNT: (state, payload) => {
        state.stripeAccounts = payload;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
