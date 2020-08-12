<script>
import Layout from "../layouts/main";
import PageHeader from "../components/page-header";
import Stat from "../components/widgets/stat";
import ShopifyConnect from "../components/ShopifyConnect-modal";

/**
 * Starter component
 */
export default {
  components: { Layout, PageHeader, Stat, ShopifyConnect },
  data() {
    return {
      title: "Zenvision Dashboard",
      items: [
        {
          text: "Utility",
          href: "/",
        },
        {
          text: "Zenvision Dashboard",
          active: true,
        },
      ],
      statData: [
        {
          id: 1,
          title: "Number of Orders",
          value: "-",
        },
        {
          id: 2,
          title: "Order Revenue",
          value: "-",
        },
        {
          id: 3,
          title: "Shipping Revenue",
          value: "-",
        },
        {
          id: 4,
          title: "Gross Profit",
          value: "-",
        },
        {
          id: 5,
          title: "Net Profit",
          value: "-",
        },
        {
          id: 6,
          title: "Subscription",
          value: "-",
        },
      ],
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
  methods: {
    openModal() {
      this.$refs.modal.ShopifyConnect();
    },
  },
};
</script>
<template>
  <Layout>
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-flex justify-content-between">
          <b-dropdown variant="primary" class="m-2">
            <template v-slot:button-content>
              Add Channels
              <i class="fas fa-plus pl-1"></i>
            </template>
            <b-dropdown-item href="#" v-b-modal.shopify-connect>
              <img src="/images/icons/shopify-icon.svg" alt height="21" class="mr-2" />
              Shopify
            </b-dropdown-item>
            <b-dropdown-item href="#">
              <img src="/images/icons/facebook-icon.svg" alt height="19" class="mr-2" /> Facebook Ads
            </b-dropdown-item>
          </b-dropdown>
          <div class="page-title-right">
            <ol class="text-center m-0">
              <li class="list-unstyled">
                Date Range:
                <b-button variant="outline-light">01/01/2018 - 12/31/2018</b-button>
              </li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-5">
        <div class="row">
          <div class="col-xl-12 mt-4">
            <h3>Revenues</h3>
          </div>
          <div v-for="stat of statData" :key="stat.id" class="col-md-4 p-2">
            <Stat :title="stat.title" :value="stat.value" />
          </div>
        </div>
        <div class="row">
          <div class="col-xl-12 mt-4">
            <h3>Cost</h3>
          </div>
          <div v-for="cost of costData" :key="cost.id" class="col-md-4 p-2">
            <Stat :title="cost.title" :value="cost.value" />
          </div>
        </div>
        <div class="row">
          <div class="col-xl-12 mt-4">
            <h3>Key Performance Metrics</h3>
          </div>
          <div v-for="performance of performanceData" :key="performance.id" class="col-md-4 p-2">
            <Stat :title="performance.title" :value="performance.value" />
          </div>
        </div>
      </div>
      <div class="col-xl-7">
        <div class="row ml-2">
          <div class="col-xl-12 mt-4">
            <h3>Profit Analysis</h3>
          </div>
          <div class="col-xl-12 pa-chart card-body"></div>
        </div>
        <div class="row">
          <div class="col-xl-12 mt-4">
            <h3>Acquisition Channels</h3>
          </div>
          <div v-for="channel of channelsData" :key="channel.id" class="col-md-4 p-2">
            <Stat :title="channel.title" :value="channel.value" :channelData="channel.data" />
          </div>
        </div>
      </div>
    </div>
    <b-modal id="shopify-connect" centered hide-footer hide-header>
      <ShopifyConnect />
    </b-modal>
  </Layout>
</template>
<style scoped>
.pa-chart {
  width: 100%;
  height: 300px;
  background-color: #2a3042;
}
</style>
