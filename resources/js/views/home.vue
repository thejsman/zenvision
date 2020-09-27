<script>
import Layout from "../layouts/main";
import PageHeader from "../components/page-header";
import axios from "axios";

//Top Nav imports
import ChannelDropdown from "../components/custom-components/channel-dropdown";
import DateRange from "../components/custom-components/date-range";
import StoreIcon from "../components/custom-components/Store-icon";

//Dashboard Data sections import
import Revenue from "../components/custom-components/dashboard-data/revenue-section";
import Costs from "../components/custom-components/dashboard-data/cost-section";
import KeyPerformance from "../components/custom-components/dashboard-data/key-performance-section";
import Chart from "../components/custom-components/dashboard-data/chart-section";

//Modal import
import ShopifyConnect from "../components/custom-components/modals/ShopifyConnect-modal";

export default {
  components: {
    Layout,
    PageHeader,
    ShopifyConnect,
    StoreIcon,
    DateRange,
    ChannelDropdown,
    Revenue,
    Costs,
    KeyPerformance,
    Chart,
  },
  data() {
    return {
      title: "Zenvision Dashboard",
      enabled_on_dashboard: [],
      refund_total: 0,
      number_of_products: 0,
      orders: [],

      costData: [
        {
          id: 1,
          title: "Ad Spend",
          value: "-",
        },
        {
          id: 2,
          title: "COGS",
          value: "-",
        },
        {
          id: 3,
          title: "Merchant Fees",
          value: "-",
        },
        {
          id: 4,
          title: "Refunds",
          value: "-",
        },
        {
          id: 5,
          title: "Chargebacks",
          value: "-",
        },
        {
          id: 6,
          title: "Discounts",
          value: "-",
        },
        {
          id: 7,
          title: "Subscription Costs",
          value: "-",
        },
        {
          id: 8,
          title: "Taxes",
          value: "-",
        },
        {
          id: 9,
          title: "Operating Costs",
          value: "-",
        },
      ],
      performanceData: [
        {
          id: 1,
          title: "Conversion Rate",
          value: "-",
        },
        {
          id: 2,
          title: "Abandoned Cart",
          value: "-",
        },
        {
          id: 3,
          title: "LTV",
          value: "-",
        },
        {
          id: 4,
          title: "Avg Order Value",
          value: "-",
        },
        {
          id: 5,
          title: "Avg Units Per Order",
          value: "-",
        },
        {
          id: 6,
          title: "Avg Profit Per Orders",
          value: "-",
        },
        {
          id: 7,
          title: "% US Orders",
          value: "-",
        },
      ],
      channelsData: [
        {
          id: 1,
          title: "Email Marketing",
          value: "-",
          data: [
            {
              title: "Email sent",
              value: "-",
            },
            {
              title: "Revenue",
              value: "-",
            },
          ],
        },
        {
          id: 2,
          title: "Organic Search",
          value: "-",
          data: [
            {
              title: "Traffic",
              value: "-",
            },
            {
              title: "Revenue",
              value: "-",
            },
          ],
        },
      ],
    };
  },
  created() {
    this.getShopifyStoreData();
  },
  methods: {
    async getShopifyStoreData() {
      try {
        const {
          data: {
            enabled_on_dashboard,
            refund_total,
            orders,
            number_of_products,
          },
        } = await axios.get("shopifystoredata");

        //assign values
        this.enabled_on_dashboard = enabled_on_dashboard;
        this.number_of_products = number_of_products;
        this.refund_total = refund_total;
        this.orders = orders;
      } catch (error) {
        console.log(error);
      }
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
          <DateRange />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-xl-5">
        <Revenue :revenueData="orders" />
        <Costs :costData="orders" />
      </div>
      <div class="col-xl-7">
        <Chart />
        <KeyPerformance :performanceData="performanceData" />
      </div>
    </div>
    <b-modal id="shopify-connect" centered hide-footer hide-header>
      <ShopifyConnect @handle-close="$bvModal.hide('shopify-connect')" />
    </b-modal>
  </Layout>
</template>
