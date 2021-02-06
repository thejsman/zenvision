import dayjs from "dayjs";

const state = {
    startDate: "",
    endDate: ""
};
const getters = {
    startDate: state => state.startDate,
    endDate: state => state.endDate
};
const actions = {
    fetchDateRange({ commit }) {
        commit("setDateRange");
    },
    updateDateRange({ commit }, payload) {
        commit("updateDateRange", payload);
    }
};
const mutations = {
    setDateRange: state => {
        state.startDate = dayjs().subtract(3, "month");
        state.endDate = dayjs();
    },
    updateDateRange: (state, payload) => {
        state.startDate = dayjs(payload.startDate).format("MM/DD/YYYY");
        state.endDate = dayjs(payload.endDate).format("MM/DD/YYYY");
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
