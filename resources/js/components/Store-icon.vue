<template>
  <div class="d-flex flex-row">
    <div v-for="store in stores" :key="store.id">
      <div class="p-1">
        <img src="/images/icons/shopify-icon.svg" alt height="21" class="mr-2" />
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
export default {
  name: "StoreIcon",
  data() {
    return {
      stores: [],
    };
  },
  created: function () {
    this.getStores();
  },
  methods: {
    async getStores() {
      const stores = await axios.get("/user/stores");
      console.log("check", stores.data);
      this.stores = stores.data;
    },
  },
};
</script>
