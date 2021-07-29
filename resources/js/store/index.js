import Vuex from "vuex";
import Vue from "vue";
import shopifyData from "./modules/shopifyData";
import dateRange from "./modules/dateRange";
import channelStatus from "./modules/channelStatus";
import StripeAccount from "./modules/stripeAccount";
import MasterSheet from "./modules/masterSheet";
import BankAccount from "./modules/bankAccount";
import SupplierPayable from "./modules/supplierPayable";
import ProfitAnalysis from "./modules/profitAnalysis";

//Load Vuex
Vue.use(Vuex);

//Create store
export default new Vuex.Store({
    modules: {
        shopifyData,
        dateRange,
        channelStatus,
        StripeAccount,
        MasterSheet,
        BankAccount,
        ProfitAnalysis,
        SupplierPayable
    }
});
