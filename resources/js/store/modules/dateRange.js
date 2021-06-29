import moment from "moment";

const state = {
    startDateS: moment()
        .subtract(1, "months")
        .format("YYYY-MM-DD"),
    endDateS: moment().format("YYYY-MM-DD"),
    transStartDate: moment()
        .subtract(14, "days")
        .format("YYYY-MM-DD"),
    transEndDate: moment().format("YYYY-MM-DD")
};
const getters = {
    startDateS: state => state.startDateS,
    endDateS: state => state.endDateS,
    transStartDate: state => state.transStartDate,
    transEndDate: state => state.transEndDate
};
const actions = {
    updateDateRange({ commit }, payload) {
        commit("updateDateRange", payload);
    },
    setNextDatesForTransactions: ({ commit }) => {
        commit("SET_NEXT_DATES");
    }
};
const mutations = {
    updateDateRange: (state, payload) => {
        state.startDateS = moment(payload.startDate).format("YYYY-MM-DD");
        state.endDateS = moment(payload.endDate).format("YYYY-MM-DD");
    },
    SET_NEXT_DATES: state => {
        state.transEndDate = moment(state.transStartDate)
            .clone()
            .subtract(1, "days")
            .format("YYYY-MM-DD");
        state.transStartDate = moment(state.transEndDate)
            .clone()
            .subtract(14, "days")
            .format("YYYY-MM-DD");
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
