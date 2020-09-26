<template>
  <div class="flex-start pl-2">
    <div class="d-flex flex-row">
      <div v-for="store in stores" :key="store.id">
        <div class="border border-primary rounded mr-2 p-2">
          <img src="/images/icons/shopify-icon.svg" alt height="18" />
        </div>
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
      this.stores = stores.data;
    },
  },
};
</script>
