const state = {
    currentChannel: "PA",
    hasBankAccount: false,
    loadingStatus: true
};
const getters = {
    currentChannel: state => state.currentChannel,
    hasBankAccountCS: state => state.hasBankAccount,
    loadingStatus: state => state.loadingStatus
};
const actions = {
    toggleLoadingStatus: ({ commit }, payload) => {
        commit("TOGGGLE_LOADING_STATUS", payload);
    },
    toggleCurrentChannel: ({ commit }, payload) => {
        commit("TOGGGLE_CURRENT_CHANNEL", payload);
    }
};
const mutations = {
    TOGGGLE_LOADING_STATUS: (state, status) => {
        state.loadingStatus = status;
    },
    TOGGGLE_BANK_ACCOUNT_STATUS: (state, status) => {
        state.hasBankAccount = status;
    },
    TOGGGLE_CURRENT_CHANNEL: (state, payload) => {
        state.currentChannel = payload;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
