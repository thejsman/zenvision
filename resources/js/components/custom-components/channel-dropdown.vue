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
            snapchatUrl: `https://accounts.snapchat.com/login/oauth2/authorize?response_type=code&client_id=${process.env.MIX_SNAPCHAT_CLIENT_ID}&redirect_uri=https://zenvision.io&scope=snapchat-marketing-api`,
            tiktokUrl: `https://ads.tiktok.com/marketing_api/auth?app_id=${process.env.MIX_TIKTOK_APP_ID}&redirect_uri=${process.env.MIX_TIKTOK_REDIRECT_URL}`,
            googleAdsUrl: `https://accounts.google.com/o/oauth2/auth?scope=https://www.googleapis.com/auth/adwords&response_type=code&access_type=offline&redirect_uri=${process.env.MIX_GOOGLE_REDIRECT_URL}&client_id=${process.env.MIX_GOOGLE_CLIENT_ID}`,
            facebookUrl: `https://www.facebook.com/v6.0/dialog/oauth?client_id=${process.env.MIX_FACEBOOK_CLIENT_ID}&redirect_uri=${process.env.MIX_FACEBOOK_REDIRECT_URL}`
        };
    }
};
</script>
