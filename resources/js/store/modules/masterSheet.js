const state = {
    cogsTotal: 0
};
const getters = {
    cogsTotal: state => state.cogsTotal
};
const actions = {};
const mutations = {
    SET_COGS_TOTAL: (state, palyload) => {
        state.cogsTotal = palyload;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
