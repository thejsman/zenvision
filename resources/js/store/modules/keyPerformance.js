import moment from "moment";
import axios from "axios";
import {
    AVERAGE_PROFIT_PER_ORDER,
    AVERAGE_ORDER_VALUE,
    AVERAGE_UNITS_PER_ORDER,
    ABANDONED_CART,
    US_ORDERS_PERCENTAGE
} from "../../constants";

const state = {
    data: [
        {
            id: 1,
            title: ABANDONED_CART,
            value: `0`,
            loading: false,
            toolTip:
                "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
        },

        {
            id: 2,
            title: AVERAGE_ORDER_VALUE,
            value: `0`,
            loading: false,
            toolTip:
                "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
        },
        {
            id: 3,
            title: AVERAGE_UNITS_PER_ORDER,
            value: `0`,
            loading: false,
            toolTip:
                "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
        },
        {
            id: 4,
            title: AVERAGE_PROFIT_PER_ORDER,
            value: `0`,
            loading: false,
            toolTip:
                "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
        },
        {
            id: 5,
            title: US_ORDERS_PERCENTAGE,
            value: `0`,
            loading: false,
            toolTip:
                "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
        }
    ]
};
const getters = {
    keyPerformanceData: state => state.data
};
const actions = {
    checkFunciton({ commit, rootState }) {
        console.log("action from KP State", rootState);
    }
};
const mutations = {};

export default {
    state,
    getters,
    actions,
    mutations
};
