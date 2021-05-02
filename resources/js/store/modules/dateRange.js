import moment from "moment";

const state = {
    startDateS: moment()
        .subtract(1, "months")
        .format("YYYY-MM-DD"),
    endDateS: moment().format("YYYY-MM-DD")
};
const getters = {
    startDateS: state => state.startDateS,
    endDateS: state => state.endDateS
};
const actions = {
    updateDateRange({ commit }, payload) {
        console.log("We are done now");
        console.log({ payload });
        commit("updateDateRange", payload);
    }
};
const mutations = {
    updateDateRange: (state, payload) => {
        state.startDateS = moment(payload.startDate).format("YYYY-MM-DD");
        state.endDateS = moment(payload.endDate).format("YYYY-MM-DD");
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
