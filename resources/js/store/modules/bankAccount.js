const state = {
    bankAccountArray: [],
    bankAccountLoadingStatus: true,
    bankLogoArray: [],
    bankBalance: 0,
    bankTransactionsArray: [],
    creditCardLiabilities: 0
};
const getters = {
    bankAccounts: state => state.bankAccountArray,
    bankLogos: state => state.bankLogoArray,
    bankTransactions: state => state.bankTransactionsArray,
    bankAccountBalance: state => state.bankBalance,
    creditCardLiabilities: state => state.creditCardLiabilities,
    bankAccountLoadingStatus: state => state.bankAccountLoadingStatus
};
const actions = {
    getBankAccounts: async ({ commit, dispatch }) => {
        try {
            const bankAccounts = await axios.get("/bankaccounts");
            const data = bankAccounts.data;
            if (data.length > 0) {
                commit("SET_BANK_ACCOUNT", data);
                dispatch("getCreditCardBalance");
                dispatch("getBankAccountBalance");
                commit("TOGGGLE_BANK_ACCOUNT_STATUS", true, { root: true });
                commit("SET_BANK_LOGO");
            } else {
                commit("TOGGGLE_BANK_ACCOUNT_STATUS", false, { root: true });
                commit("MasterSheet/SET_DEBTS_CREDIT_CARD_TOTAL", 0, {
                    root: true
                });
                commit("SET_CREDIT_CARD_LIABILITIES", 0);
                commit("SET_BANK_TRANSACTIONS", []);
                commit("SET_ICON_LOADER", false);
            }
        } catch (err) {
            commit("TOGGGLE_BANK_ACCOUNT_STATUS", false, { root: true });
            console.log(err);
        }
    },
    getBankAccountBalance: async ({ commit }) => {
        try {
            const { data } = await axios.get("bankaccount-balance");
            commit("SET_BANK_ACCOUNT_BALANCE", data);
        } catch (err) {
            console.log({ err });
        }
    },
    getCreditCardBalance: async ({ commit }) => {
        try {
            const result = await axios.get("creditcard-liabilities");
            const liabilities = result.data;
            commit("MasterSheet/SET_DEBTS_CREDIT_CARD_TOTAL", liabilities, {
                root: true
            });
            commit("SET_CREDIT_CARD_LIABILITIES", liabilities);
        } catch (err) {
            commit("MasterSheet/SET_DEBTS_CREDIT_CARD_TOTAL", 0, {
                root: true
            });
            commit("SET_CREDIT_CARD_LIABILITIES", 0);
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
    removeBankAccount: async ({ commit, dispatch }, account) => {
        commit("REMOVE_BANK_ACCOUNT", account);
        dispatch("getBankAccounts");
    }
};
const mutations = {
    SET_BANK_ACCOUNT: (state, payload) => {
        state.bankAccountArray = payload;
    },
    SET_BANK_LOGO: state => {
        state.bankAccountArray.forEach(account => {
            const { bank_user_id, institution_id } = account;
            state.bankLogoArray.push({ bank_user_id, institution_id });
        });
    },
    SET_BANK_TRANSACTIONS: (state, payload) =>
        (state.bankTransactionsArray = payload),
    SET_CREDIT_CARD_LIABILITIES: (state, payload) => {
        state.creditCardLiabilities = payload;
    },
    SET_BANK_ACCOUNT_BALANCE: (state, payload) => {
        state.bankBalance = payload;
        state.bankAccountLoadingStatus = false;
    },
    REMOVE_BANK_ACCOUNT: (state, payload) => {
        state.bankAccountArray = state.bankAccountArray.filter(
            account => account.id !== payload.id
        );
    },
    SET_ICON_LOADER: (state, payload) =>
        (state.bankAccountLoadingStatus = payload)
};
export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
};
