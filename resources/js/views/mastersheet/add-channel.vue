<template>
    <b-dropdown variant="primary" class="m-2">
        <template v-slot:button-content>
            Add Channels
            <i class="fas fa-plus pl-1"></i>
        </template>
        <PlaidLink
            clientName="Zenvision"
            env="sandbox"
            :link_token="plaidLinkToken"
            :products="['auth', 'transactions']"
            :onLoad="onLoad"
            :onSuccess="onSuccess"
            :onExit="onExit"
            :onEvent="onEvent"
        >
            <b-dropdown-item href="#" id="teller-connect">
                <img
                    src="/images/icons/bank-icon.svg"
                    alt
                    height="21"
                    width="21"
                    class="channel-icons"
                />

                Bank accounts
            </b-dropdown-item>
        </PlaidLink>

        <b-dropdown-item :href="paypalUrl">
            <img
                src="/images/icons/paypal.png"
                alt
                height="21"
                width="21"
                class="channel-icons"
            />
            Paypal
        </b-dropdown-item>
        <b-dropdown-item :href="stripeUrl">
            <img
                src="/images/icons/stripe-icon.svg"
                alt
                height="21"
                width="21"
                class="channel-icons"
            />
            Stripe
        </b-dropdown-item>
        <b-dropdown-item href="#">
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
        <b-modal id="plaid-connect" size="lg" centered hide-footer hide-header>
            <BankConnect
                :bankAccounts="plaidAccounts"
                :bankInstitutionName="plaidInstitutionName"
                @handle-close="$bvModal.hide('facebook-connect')"
            />
        </b-modal>
    </b-dropdown>
</template>
<script>
import axios from "axios";

import PlaidLink from "vue-plaid-link2";
import BankConnect from "../../components/custom-components/modals/bank-account-modal.vue";
export default {
    data() {
        return {
            stripeUrl: `https://connect.stripe.com/oauth/authorize?response_type=code&client_id=ca_IXYHhxtBir6EPIAuynBwhmTDeNJLAC0H&scope=read_write&redirect_uri=https://staging.zenvision.io/stripeconnect&state=mastersheet-${Math.floor(
                Math.random() * 10000000 + 1
            )}`,
            paypalUrl: `https://www.paypal.com/connect/?flowEntry=static&client_id=AY8ay9apzuTb7arwPRYfLPlPN1tu9QGIKsEyhDBjLI1FGDwfrtWEvcmOEWgtjXLUrxESYB5jQFXziwlP&response_type=code&scope=openid profile&redirect_uri=https%3A%2F%2Fstaging.zenvision.io%2Fpaypal&state=mastersheet-${Math.floor(
                Math.random() * 10000000 + 1
            )}`,
            plaidAccounts: [],
            plaidLinkToken: "",
            plaidInstitutionName: ""
        };
    },
    components: { PlaidLink, BankConnect },
    methods: {
        async onLoad() {
            console.log("Onload event tiggered");
        },
        onSuccess(public_token, metadata) {
            console.log("OnSuccess event :", { public_token, metadata });
            const accounts = metadata.accounts.filter(
                account => account.type === "depository"
            );
            this.plaidAccounts = accounts;
            this.plaidPublicToken = public_token;
            this.plaidInstitutionName = metadata.institution.name;
            console.log(this.plaidInstitutionName);
        },
        onExit(err, metadata) {
            console.log("OnExit : ", { err, metadata });
        },
        onEvent(eventName, metadata) {
            console.log("OnEvent: ", { eventName, metadata });
            if (eventName === "HANDOFF") {
                this.$bvModal.show("plaid-connect");
            }
        },
        async getPlaidLinkToken() {
            try {
                const result = await axios.get("bankaccount-link-token");
                const linkToken = result.data;
                if (linkToken) {
                    this.plaidLinkToken = linkToken;
                }
                console.log("LinkToken generated: ", this.plaidLinkToken);
            } catch (err) {
                console.log(err);
            }
        }
    },
    created() {
        this.getPlaidLinkToken();
    }
};
</script>
