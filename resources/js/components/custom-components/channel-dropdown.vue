<template>
    <b-dropdown variant="primary" class="m-2 mr-3">
        <template v-slot:button-content>
            <div class="d-flex align-items-center">
                <span class="d-none d-md-block pr-2">Add Channels</span
                ><i class="fas fa-plus"></i>
            </div>
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
        <b-dropdown-item :href="facebookUrl">
            <img
                src="/images/icons/facebook-icon.svg"
                alt
                height="19"
                class="channel-icons"
            />
            Facebook Ads
        </b-dropdown-item>
        <b-dropdown-item :href="googleAdsUrl">
            <img
                src="/images/icons/google-icon.svg"
                alt
                height="19"
                class="channel-icons"
            />
            Google Ads
        </b-dropdown-item>
        <!-- <b-dropdown-item :href="paypalUrl">
            <img
                src="/images/icons/paypal-icon.svg"
                alt
                height="21"
                class="channel-icons"
            />
            Paypal
        </b-dropdown-item> -->
        <b-dropdown-item :href="stripeUrl">
            <img
                src="/images/icons/stripe-icon.svg"
                alt
                height="21"
                class="channel-icons"
            />
            Stripe
        </b-dropdown-item>
        <b-dropdown-item :href="snapchatUrl">
            <img
                src="/images/icons/snapchat-icon.svg"
                alt
                height="21"
                class="channel-icons"
            />
            Snapchat
        </b-dropdown-item>
        <b-dropdown-item :href="tiktokUrl">
            <img
                src="/images/icons/tiktok-icon.svg"
                alt
                height="21"
                class="channel-icons"
            />
            TikTok
        </b-dropdown-item>
    </b-dropdown>
</template>
<script>
export default {
    data() {
        return {
            snapchatUrl: `https://accounts.snapchat.com/login/oauth2/authorize?response_type=code&client_id=${process.env.MIX_SNAPCHAT_CLIENT_ID}&redirect_uri=${process.env.MIX_SNAPCHAT_REDIRECT_URL}&scope=snapchat-marketing-api`,
            tiktokUrl: `https://ads.tiktok.com/marketing_api/auth?app_id=${process.env.MIX_TIKTOK_APP_ID}&redirect_uri=${process.env.MIX_TIKTOK_REDIRECT_URL}`,
            googleAdsUrl: `https://accounts.google.com/o/oauth2/auth?prompt=consent&client_id=${process.env.MIX_GOOGLE_CLIENT_ID}&scope=https://www.googleapis.com/auth/adwords&response_type=code&access_type=offline&redirect_uri=${process.env.MIX_GOOGLE_REDIRECT_URL}`,
            facebookUrl: `https://www.facebook.com/v10.0/dialog/oauth?client_id=${process.env.MIX_FACEBOOK_CLIENT_ID}&redirect_uri=${process.env.MIX_FACEBOOK_REDIRECT_URL}&scope=ads_management,ads_read`,
            stripeUrl: `https://connect.stripe.com/oauth/authorize?response_type=code&client_id=ca_IXYHhxtBir6EPIAuynBwhmTDeNJLAC0H&scope=read_write&redirect_uri=https://app.zenvision.io/stripeconnect&state=profitanalysis-${Math.floor(
                Math.random() * 10000000 + 1
            )}`,
            paypalUrl: `https://www.paypal.com/connect/?flowEntry=static&client_id=AY8ay9apzuTb7arwPRYfLPlPN1tu9QGIKsEyhDBjLI1FGDwfrtWEvcmOEWgtjXLUrxESYB5jQFXziwlP&response_type=code&scope=openid profile&redirect_uri=https%3A%2F%2Fapp.zenvision.io%2Fpaypal&state=profitanalysis-${Math.floor(
                Math.random() * 10000000 + 1
            )}`
        };
    }
};
</script>
