<script>
import Layout from "../layouts/main";
import PageHeader from "../components/page-header";
import axios from "axios";

//Top Nav imports
import ChannelDropdown from "../components/custom-components/channel-dropdown";
import DateRange from "../components/custom-components/date-range";
import StoreIcon from "../components/custom-components/Store-icon";

import PaypalAccount from "../components/custom-components/Paypal-icon";

//Dashboard Data sections import
import Profit from "../components/custom-components/dashboard-data/profit-section";
import Revenue from "../components/custom-components/dashboard-data/revenue-section";
import Costs from "../components/custom-components/dashboard-data/cost-section";
import KeyPerformance from "../components/custom-components/dashboard-data/key-performance-section";
import Chart from "../components/custom-components/dashboard-data/chart-section";

//Modal import
import ShopifyConnect from "../components/custom-components/modals/ShopifyConnect-modal";

import moment from "moment";
import { mapState, mapGetters, mapActions } from "vuex";
import dayjs from "dayjs";
import { eventBus } from "../app";

export default {
  components: {
    Layout,
    PageHeader,
    ShopifyConnect,
    StoreIcon,
    DateRange,
    ChannelDropdown,
    Profit,
    Revenue,
    Costs,
    KeyPerformance,
    Chart,
    PaypalAccount,
  },
  data() {
    return {
      title: "Zenvision Dashboard",
      allOrders: [],
      enabled_on_dashboard: [],
      refund_total: 0,
      number_of_products: 0,
      orders: [],
      chargebacks: 0,
      merchantFees: [],
      merchantFeesTotal: 0,
      chargebacksTotal: 0,
      backupOrders: [],
      dateRangeSelected: [],
      facebookData: [],
      googleData: [],
      facebookError: false,
      facebookAdAccounts: [],
      googleAdAccounts: [],
      fb_spend: [],
      google_ads_data: [],
      paypalChargebacks: [],
      stripeChargebacks: [],
      paypalAccounts: [],
      stripeAccounts: [],
      stripeTransactions: [],
      paypalTransactions: [],
    };
  },
  mounted() {},
  computed: {
    ...mapGetters([
      "ShopifyOrders",
      "ShopifyStores",
      "ShopifyProductCount",
      "ShopifyRefundTotal",
      "startDate",
      "endDate",
    ]),
  },
  created() {
    // this.fetchShopifyData();

    this.getShopifyStoreData();

    eventBus.$on("changeDateRange", (date) => this.handleDateChange(date));
  },
  methods: {
    ...mapActions(["fetchShopifyData"]),

    async getShopifyStoreData() {
      try {
        const {
          data: {
            enabled_on_dashboard,
            refund_total,
            orders,
            number_of_products,
            paypalAccounts,
            paypalTransactions,
          },
        } = await axios.get("shopifystoredata");

        //assign values

        this.backupOrders = [...orders];
        this.paypalAccounts = paypalAccounts;
        this.paypalTransactions = paypalTransactions;

        let d = new Date();
        this.handleDateChange({
          startDate: moment(d).add(-3, "months"),
          endDate: moment(d),
        });
      } catch (error) {
        console.log(error);
      }
    },
    getMerchantfeesTotal(s_date, e_date) {
      let total = 0;

      //   this.paypalTransactions.map((transaction) => {
      //     const orderDate = moment(transaction.transaction_updated_date).format(
      //       "MM-DD-YYYY"
      //     );
      //     console.log(transaction.transaction_info.fee_amount.value);
      //     if (
      //       new Date(orderDate) >= new Date(s_date) &&
      //       new Date(orderDate) <= new Date(e_date)
      //     ) {
      //       total += Math.abs(
      //         parseFloat(transaction.transaction_info.fee_amount.value)
      //       );
      //     }
      //   });

      this.merchantFeesTotal = total;
    },
    handleDateChange(dateRange) {
      const { startDate, endDate } = dateRange;
      const s_date = moment(startDate).format("MM-DD-YYYY");
      const e_date = moment(endDate).format("MM-DD-YYYY");
      console.log("Called");
      const filteredOrders = this.backupOrders.filter((order) => {
        const orderDate = moment(order.created_on_shopify).format("MM-DD-YYYY");
        return (
          new Date(orderDate) >= new Date(s_date) &&
          new Date(orderDate) <= new Date(e_date)
        );
      });
      this.dateRangeSelected = [moment(s_date), moment(e_date)];
      this.allOrders = filteredOrders;
      this.getMerchantfeesTotal(s_date, e_date);
    },
  },
};
</script>
<template>
  <Layout>
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-flex align-items-center">
          <ChannelDropdown />
          <StoreIcon />
          <PaypalAccount :ppAccounts="paypalAccounts" />
          <DateRange @changeDateRange="handleDateChange" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-xl-5">
        <Profit
          :profitData="allOrders"
          :startDate="dateRangeSelected"
          :fbSpend="fb_spend"
        />
        <Revenue :revenueData="allOrders" :dateRange="dateRangeSelected" />
        <Costs
          :costData="allOrders"
          :refundTotal="refund_total"
          :merchantFees="merchantFeesTotal"
        />
      </div>
      <div class="col-xl-7">
        <Chart
          :chartData="allOrders"
          :ChartdateRange="dateRangeSelected"
          :fbSpend="fb_spend"
          :googleData="google_ads_data"
        />
        <KeyPerformance
          :performanceData="allOrders"
          :shopifyStores="enabled_on_dashboard"
          :totalProducts="number_of_products"
        />
      </div>
    </div>
    <b-modal id="shopify-connect" centered hide-footer hide-header>
      <ShopifyConnect @handle-close="$bvModal.hide('shopify-connect')" />
    </b-modal>
  </Layout>
</template>
