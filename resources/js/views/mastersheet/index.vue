<script>
import Layout from "../../layouts/main";
import AddChannel from "./add-channel";
import Sidepanel from "./sidepanel";
import Mainpanel from "./mainpanel";
import ShopifyConnect from "../../components/custom-components/modals/ShopifyConnect-modal";
import ShopifyStoreIcon from "../../components/custom-components/shopifystore-icon";
import PaypalAccountIcon from "../../components/custom-components/paypal-icon";
import StripeAccount from "../../components/custom-components/stripe-icon";

import { eventBus } from "../../app";
export default {
  data() {
    return {
      allOrders: [],
    };
  },
  components: {
    Layout,
    AddChannel,
    Sidepanel,
    Mainpanel,
    ShopifyConnect,
    ShopifyStoreIcon,
    PaypalAccountIcon,
    StripeAccount,
  },
  created() {
    eventBus.$on("toggleShopifyStore", () => {
      this.getShopifyData();
    });
    this.getShopifyData();
  },
  methods: {
    async getShopifyData() {
      const {
        data: { enabled_on_dashboard, orders },
      } = await axios.get("shopifystoredata");
      this.allOrders = orders;
    },
  },
};
</script>
<template>
  <Layout>
    <div class="row">
      <!-- Right Sidebar -->
      <div class="col-12 my-2 d-flex justify-content-between">
        <div class="d-flex justify-content-start align-items-center">
          <AddChannel />
          <ShopifyStoreIcon :disableFeature="false" />
          <PaypalAccountIcon :disableFeature="false" />
          <StripeAccount :disableFeature="false" />
        </div>
        <b-button class="border-0 mr-4 btn-export" variant="dark"
          >Export</b-button
        >
      </div>
      <div class="col-3 mt-4">
        <Sidepanel :orders="allOrders" />
      </div>
      <div class="col-9 mt-4">
        <Mainpanel :orders="allOrders" />
      </div>
    </div>
    <b-modal id="shopify-connect" centered hide-footer hide-header>
      <ShopifyConnect @handle-close="$bvModal.hide('shopify-connect')" />
    </b-modal>
  </Layout>
</template>
<style>
.mastersheet-left {
  background-color: #191e2c;
  opacity: 0.7;
}
.block {
  width: 200px;
  height: 200px;
  background-color: #2f3863;
}
.btn-export {
  height: 40px;
  width: 80px;
}
</style>
