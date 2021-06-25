const state = {
    bankAccountArray: [],
    bankLogoArray: []
};
const getters = {
    bankAccounts: state => state.bankAccountArray,
    bankLogos: state => state.bankLogoArray
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
    }
};
const mutations = {
    SET_BANK_ACCOUNT: (state, payload) => (state.bankAccountArray = payload),
    SET_BANK_LOGO: (state, payload) => {
        state.bankAccountArray.forEach((account, index) => {
            const { bank_user_id, institution_id } = account;

            state.bankLogoArray.push({ bank_user_id, institution_id });
        });
    }
};
export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
};
