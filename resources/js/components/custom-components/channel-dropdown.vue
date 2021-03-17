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
      href="https://www.facebook.com/v6.0/dialog/oauth?client_id=253783359751006&redirect_uri=https%3A%2F%2Fstaging.zenvision.io%2F"
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
    <!-- http%3A%2F%2F127.0.0.1%3A8000%2Fpaypal -->
    <!-- https%3A%2F%2Fstaging.zenvision.io%2Fpaypal -->
    <b-dropdown-item
      href="https://www.paypal.com/connect/?flowEntry=static&client_id=AY8ay9apzuTb7arwPRYfLPlPN1tu9QGIKsEyhDBjLI1FGDwfrtWEvcmOEWgtjXLUrxESYB5jQFXziwlP&response_type=code&scope=openid profile&redirect_uri=https%3A%2F%2Fstaging.zenvision.io%2Fpaypal"
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
      href="https://connect.stripe.com/oauth/authorize?response_type=code&client_id=ca_IXYHhxtBir6EPIAuynBwhmTDeNJLAC0H&scope=read_write&redirect_uri=https://staging.zenvision.io/stripeconnect"
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
