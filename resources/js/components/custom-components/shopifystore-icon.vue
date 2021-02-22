<template>
  <div class="flex-start pl-2">
    <div class="d-flex flex-row dropdown">
      <div v-for="store in stores" :key="store.id">
        <div
          class="border rounded p-2 dropbtn"
          :class="{ 'border-primary': store.enabled_on_dashboard }"

          @click="disableFeature ? handleClick(store) : null"
          v-b-tooltip.hover="store.store_name"
        >
          <img src="/images/icons/shopify-icon.svg" alt height="21" />
        </div>
        <div class="dropdown-content">
          <a href="#" @click="handleClick(store)" v-if="disableFeature">

            {{ store.enabled_on_dashboard ? "Disable" : "Enable" }}</a
          >
          <a href="#" @click="showMsgBoxOne(store, $event)">Remove</a>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
import { eventBus } from "../../app";
export default {
  name: "StoreIcon",
  data() {
    return {
      stores: [],
    };
  },

  props: {
    disableFeature: {
      type: Boolean,
      default: true,
    },
  },

  created: function () {
    this.getStores();
  },
  methods: {
    async getStores() {
      const stores = await axios.get("/user/stores");
      this.stores = stores.data;
    },
    async handleClick(store) {
      try {
        await axios.patch("shopifystore", store);
        eventBus.$emit("toggleShopifyStore");
        this.getStores();
      } catch (error) {
        console.log(error);
      }
    },
    async removeChannel(store, event) {
      try {
        // eventBus.$emit("removeShopifyStore", store.id);
        await axios.patch("shopifystoredelete", store);
        eventBus.$emit("toggleShopifyStore");
        this.getStores();
      } catch (error) {
        console.log(error);
      }
    },
    showMsgBoxOne(store) {
      this.boxOne = "";
      this.$bvModal

        .msgBoxConfirm(
          "Are you sure you want to remove this account? Please note that removing accounts can cause misleading financial data. We encourage you to include all of your E-Com business financial accounts and Shopify stores in Zenvision."
        )

        .then((value) => {
          this.boxOne = value;
         
          if (value) {
            this.removeChannel(store);
          }
        })
        .catch((err) => {
          // An error occurred
        });
    },
  },
};
</script>

