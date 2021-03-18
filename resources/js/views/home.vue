<script>
import Layout from "../layouts/main";
import PageHeader from "../components/page-header";
import axios from "axios";

//Top Nav imports
import ChannelDropdown from "../components/custom-components/channel-dropdown";
import DateRange from "../components/custom-components/date-range";
import StoreIcon from "../components/custom-components/shopifystore-icon";
import PaypalAccount from "../components/custom-components/paypal-icon";
import StripeAccount from "../components/custom-components/stripe-icon";
import FacebookAccount from "../components/custom-components/facebook-icon";
import SnapchatAccount from "../components/custom-components/snapchat-icon";

//Dashboard Data sections import
import Profit from "../components/custom-components/dashboard-data/profit-section";
import Revenue from "../components/custom-components/dashboard-data/revenue-section";
import Costs from "../components/custom-components/dashboard-data/cost-section";
import KeyPerformance from "../components/custom-components/dashboard-data/key-performance-section";
import Chart from "../components/custom-components/dashboard-data/chart-section";

//Modal import
import ShopifyConnect from "../components/custom-components/modals/ShopifyConnect-modal";
import FacebookConnect from "../components/custom-components/modals/facebook-adaccount-modal";

import moment from "moment";
import { mapState, mapGetters, mapActions } from "vuex";

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
    StripeAccount,
    FacebookConnect,
    FacebookAccount,
    SnapchatAccount,
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
    if (new URL(location.href).searchParams.get("code")) {
      this.getFacebookAds();
    }

    this.getShopifyStoreData();

    eventBus.$on("changeDateRange", (date) => this.handleDateChange(date));
    eventBus.$on("toggleShopifyStore", async () => {
      this.getShopifyStoreData();
    });
    eventBus.$on("cogs-updated", async () => {
      this.getShopifyStoreData();
    });
  },
  methods: {
    ...mapActions(["fetchShopifyData"]),

    async getShopifyStoreData() {
      try {
        const {
          data: { orders, paypalAccounts, paypalTransactions },
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
    async getFacebookAds(code) {
      try {
        const result = await axios.get("fbconnect", {
          params: {
            code: new URL(location.href).searchParams.get("code"),
          },
        });
        this.facebookData = result.data;
        console.log({ Checkthis: this.facebookData });
        this.$bvModal.show("facebook-connect");
      } catch (error) {
        console.log(error);
        this.facebookError = true;
      }
    },
    handleDateChange(dateRange) {
      const { startDate, endDate } = dateRange;
      const s_date = moment(startDate).format("MM-DD-YYYY");
      const e_date = moment(endDate).format("MM-DD-YYYY");

      const filteredOrders = this.backupOrders.filter((order) => {
        const orderDate = moment(order.created_on_shopify).format("MM-DD-YYYY");
        order.created_on_shopify = orderDate;
        order.total_price = parseFloat(order.total_price);
        return (
          new Date(orderDate) >= new Date(s_date) &&
          new Date(orderDate) <= new Date(e_date)
        );
      });
      this.dateRangeSelected = [moment(s_date), moment(e_date)];
      //   this.getMerchantfeesTotal(s_date, e_date);
      this.allOrders = _.sortBy(filteredOrders, "created_on_shopify");

      eventBus.$emit("dateChanged", { s_date, e_date });
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
          <StripeAccount />
          <FacebookAccount />
          <SnapchatAccount />
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
    <b-modal id="shopify-connect" size="lg" centered hide-footer hide-header>
      <ShopifyConnect @handle-close="$bvModal.hide('shopify-connect')" />
    </b-modal>
    <b-modal id="facebook-connect" size="lg" centered hide-footer hide-header>
      <FacebookConnect
        :facebookData="facebookData"
        :facebookError="facebookError"
        :adAccounts="facebookAdAccounts"
        :startDate="dateRangeSelected"
        @handle-close="$bvModal.hide('facebook-connect')"
      />
    </b-modal>
  </Layout>
</template>
