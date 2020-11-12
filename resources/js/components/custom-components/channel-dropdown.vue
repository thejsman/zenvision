<template>
  <b-dropdown variant="primary" class="m-2">
    <template v-slot:button-content>
      Add Channels
      <i class="fas fa-plus pl-1"></i>
    </template>
    <b-dropdown-item href="#" v-b-modal.shopify-connect>
      <img src="/images/icons/shopify-icon.svg" alt height="21" class="channel-icons" />
      Shopify
    </b-dropdown-item>
    <b-dropdown-item
      href="https://www.facebook.com/v6.0/dialog/oauth?client_id=2837137499945173&redirect_uri=http://localhost:8000/"
    >
      <img src="/images/icons/facebook-icon.svg" alt height="19" class="channel-icons" />
      Facebook Ads
    </b-dropdown-item>
    <b-dropdown-item
      href="https://accounts.google.com/o/oauth2/auth?scope=https://www.googleapis.com/auth/adwords&response_type=code&redirect_uri=http://localhost:8000/google&client_id=736720769834-eko04svp2029fgs0nt386ic87jakk154.apps.googleusercontent.com"
    >
      <img src="/images/icons/google-icon.svg" alt height="19" class="channel-icons" />
      Google Ads
    </b-dropdown-item>
  </b-dropdown>
</template>
<script>
export default {};
</script>
