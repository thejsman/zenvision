const state = {
    supplierPayableArray: null
};
const getters = {
    supplierPayableArray: state => state.supplierPayableArray
};
const actions = {
    getSupplierPayable: async ({ commit }) => {
        try {
            const { data } = await axios.get("supplierpayable");
            console.log("check store: ", data);
            commit("SET_SUPPLIER_PAYABLE", data);
        } catch (error) {
            commit("SET_SUPPLIER_PAYABLE", []);
        }
    }
};
const mutations = {
    SET_SUPPLIER_PAYABLE: (state, payload) =>
        (state.supplierPayableArray = payload)
};

export default {
    state,
    getters,
    actions,
    mutations
};
