<template>
  <b-dropdown variant="primary" class="m-2">
    <template v-slot:button-content>
      Add Channels
      <i class="fas fa-plus pl-1"></i>
    </template>
    <b-dropdown-item href="#" v-b-modal.shopify-connect>
      <img
        src="/images/icons/shopify-icon.svg"
        alt
        height="21"
        class="channel-icons"
      />
      Shopify
    </b-dropdown-item>
    <b-dropdown-item
      href="https://www.facebook.com/v6.0/dialog/oauth?client_id=2837137499945173&redirect_uri=http://localhost:8000/"
    >
      <img
        src="/images/icons/facebook-icon.svg"
        alt
        height="19"
        class="channel-icons"
      />
      Facebook Ads
    </b-dropdown-item>
    <b-dropdown-item
      href="https://accounts.google.com/o/oauth2/auth?scope=https://www.googleapis.com/auth/adwords&response_type=code&redirect_uri=http://localhost:8000/google&client_id=736720769834-eko04svp2029fgs0nt386ic87jakk154.apps.googleusercontent.com"
    >
      <img
        src="/images/icons/google-icon.svg"
        alt
        height="19"
        class="channel-icons"
      />
      Google Ads
    </b-dropdown-item>
    <b-dropdown-item
      href="https://www.sandbox.paypal.com/connect/?flowEntry=static&client_id=AdZIt50i5iAVT5V688yGmgm-ZHAyLyncY0NlIPW9Y4zkQOfsbJ2m4-7BJa7U6EeGivB09Xnu-5xLjs2J&response_type=code&scope=openid email https://uri.paypal.com/services/paypalattributes&redirect_uri=https%3A%2F%2Fstaging.zenvision.io%2Fpaypal"
    >
      <img
        src="/images/icons/paypal.png"
        alt
        height="21"
        class="channel-icons"
      />
      Paypal
    </b-dropdown-item>
    <b-dropdown-item
      href="https://connect.stripe.com/oauth/authorize?response_type=code&client_id=ca_IVj0p0IFc5yVVUGsGdtvL8aX0kQocmvQ&scope=read_write&redirect_uri=https://staging.zenvision.io/stripeconnect"
    >
      <img
        src="/images/icons/stripe-icon.svg"
        alt
        height="21"
        class="channel-icons"
      />
      Stripe
    </b-dropdown-item>
  </b-dropdown>
</template>
<script>
export default {};
</script>
