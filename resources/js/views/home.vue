<script>
import Layout from "../layouts/main";
import PageHeader from "../components/page-header";
import Stat from "../components/widgets/stat";

/**
 * Starter component
 */
export default {
  components: { Layout, PageHeader, Stat },
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
    };
  },
};
</script>
<template>
  <Layout>
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
          <b-button variant="primary">
            Add Channels
            <i class="fas fa-plus pl-1"></i>
          </b-button>
          <div class="page-title-right">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item active">Date Range:</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-6">
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
      </div>
      <div class="col-xl-6">Char here</div>
    </div>
  </Layout>
</template>
