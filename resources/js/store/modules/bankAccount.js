const state = {
    bankAccountArray: [],
    bankLogoArray: [],
    bankTransactionsArray: []
};
const getters = {
    bankAccounts: state => state.bankAccountArray,
    bankLogos: state => state.bankLogoArray,
    bankTransactions: state => state.bankTransactionsArray
};
const actions = {
    getBankAccounts: async ({ commit }) => {
        try {
            const bankAccounts = await axios.get("/bankaccounts");
            const data = bankAccounts.data;
            if (data.length > 0) {
                commit("SET_BANK_ACCOUNT", data);
                commit("TOGGGLE_BANK_ACCOUNT_STATUS", true, { root: true });
                commit("SET_BANK_LOGO");
            } else {
                commit("TOGGGLE_BANK_ACCOUNT_STATUS", false, { root: true });
            }
        } catch (err) {
            commit("TOGGGLE_BANK_ACCOUNT_STATUS", false, { root: true });
            console.log(err);
        }
    },
    getBankTransactions: async ({ commit, rootGetters }) => {
        try {
            const result = await axios.get("bankaccount-transactions", {
                params: {
                    start_date: rootGetters.transStartDate,
                    end_date: rootGetters.transEndDate
                }
            });
            const data = result.data;
            commit("SET_BANK_TRANSACTIONS", data);
        } catch (err) {
            console.log(err);
        }
    },
    removeBankAccount: async ({ commit }, account) => {
        commit("REMOVE_BANK_ACCOUNT", account);
    }
};
const mutations = {
    SET_BANK_ACCOUNT: (state, payload) => (state.bankAccountArray = payload),
    SET_BANK_LOGO: state => {
        state.bankAccountArray.forEach(account => {
            const { bank_user_id, institution_id } = account;
            state.bankLogoArray.push({ bank_user_id, institution_id });
        });
    },
    SET_BANK_TRANSACTIONS: (state, payload) =>
        (state.bankTransactionsArray = payload),
    REMOVE_BANK_ACCOUNT: (state, payload) => {
        state.bankAccountArray = state.bankAccountArray.filter(
            account => account.id !== payload.id
        );
    }
};
export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
};
