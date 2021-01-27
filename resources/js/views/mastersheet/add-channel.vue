<template>
  <b-dropdown variant="primary" class="m-2">
    <template v-slot:button-content>
      Add Channels
      <i class="fas fa-plus pl-1"></i>
    </template>
    <b-dropdown-item href="#" id="teller-connect">
      <img
        src="/images/icons/bank-icon.svg"
        alt
        height="21"
        width="21"
        class="channel-icons"
      />
      Bank Account
    </b-dropdown-item>
    <b-dropdown-item
      href="https://www.sandbox.paypal.com/connect/?flowEntry=static&client_id=AdZIt50i5iAVT5V688yGmgm-ZHAyLyncY0NlIPW9Y4zkQOfsbJ2m4-7BJa7U6EeGivB09Xnu-5xLjs2J&response_type=code&scope=openid email https://uri.paypal.com/services/paypalattributes&redirect_uri=http%3A%2F%2F127.0.0.1%3A8000%2Fpaypal"
    >
      <img
        src="/images/icons/paypal.png"
        alt
        height="21"
        width="21"
        class="channel-icons"
      />
      Paypal
    </b-dropdown-item>
    <b-dropdown-item
      href="https://connect.stripe.com/oauth/authorize?response_type=code&client_id=ca_IVj0p0IFc5yVVUGsGdtvL8aX0kQocmvQ&scope=read_write&redirect_uri=http://localhost:8000/stripeconnect"
    >
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
import axios from "axios";

export default {
  created() {
    document.addEventListener("DOMContentLoaded", function () {
      var tellerConnect = TellerConnect.setup({
        applicationId: "app_ne846be906r2t6156a000",
        onInit: function () {
          console.log("Teller Connect has initialized");
        },
        // Part 3. Handle a successful enrollment's accessToken
        onSuccess: async function (enrollment) {
          console.log("User enrolled successfully", enrollment);

          try {
            const result = await axios.post("bankaccount", {
              user: enrollment.user.id,
              institution_name: "Citi",
              access_token: enrollment.accessToken,
            });
            console.log(result);
          } catch (err) {
            console.log(err);
          }
        },
        onExit: function () {
          console.log("User closed Teller Connect");
        },
      });

      // Part 4. Hook user actions to start Teller Connect
      var el = document.getElementById("teller-connect");
      el.addEventListener("click", function () {
        tellerConnect.open();
      });
    });
  },
  methods: {
    async addBankAccount(enrollment) {},
  },
};
</script>
