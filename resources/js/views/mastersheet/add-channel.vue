<template>
  <b-dropdown variant="primary" class="m-2">
    <template v-slot:button-content>
      Add Channels
      <i class="fas fa-plus pl-1"></i>
    </template>
    <b-dropdown-item href="#" v-b-modal.shopify-connect>
      <img
        src="/images/icons/bank-icon.svg"
        alt
        height="21"
        width="21"
        class="channel-icons"
      />
      Bank accounts
    </b-dropdown-item>
    <b-dropdown-item href="#" v-b-modal.shopify-connect>
      <img
        src="/images/icons/paypal.png"
        alt
        height="21"
        width="21"
        class="channel-icons"
      />
      Paypal
    </b-dropdown-item>
    <b-dropdown-item href="#" v-b-modal.shopify-connect>
      <img
        src="/images/icons/stripe-icon.svg"
        alt
        height="21"
        width="21"
        class="channel-icons"
      />
      Stripe
    </b-dropdown-item>
    <b-dropdown-item href="#" v-b-modal.shopify-connect>
      <img
        src="/images/icons/creditcard-icon.svg"
        alt
        height="21"
        width="21"
        class="channel-icons"
      />
      Credit Card
    </b-dropdown-item>

    <b-dropdown-item href="#" v-b-modal.shopify-connect>
      <img
        src="/images/icons/shopify-icon.svg"
        alt
        height="21"
        width="21"
        class="channel-icons"
      />
      Shopify
    </b-dropdown-item>
  </b-dropdown>
</template>
<script>
export default {};
</script>
