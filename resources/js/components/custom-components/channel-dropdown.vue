<template>
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
      <img src="/images/icons/facebook-icon.svg" alt height="19" class="mr-2" />
      Facebook Ads
    </b-dropdown-item>
  </b-dropdown>
</template>
<script>
export default {};
</script>
