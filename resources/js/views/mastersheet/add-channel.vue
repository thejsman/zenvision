<template>
    <b-dropdown variant="primary" class="m-2">
        <template v-slot:button-content>
            <div class="d-flex align-items-center">
                <span class="d-none d-md-block pr-2">Add Channels</span
                ><i class="fas fa-plus"></i>
            </div>
        </template>
        <PlaidLink
            clientName="Zenvision"
            env="development"
            :link_token="plaidLinkToken"
            :products="['auth', 'transactions']"
            :onLoad="onLoad"
            :onSuccess="onSuccess"
            :onExit="onExit"
            :onEvent="onEvent"
        >
            <b-dropdown-item
                href="#"
                id="teller-connect"
                @click="handleBankAccountClick"
            >
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

        <!-- <b-dropdown-item :href="paypalUrl">
            <img
                src="/images/icons/paypal-icon.svg"
                alt
                height="21"
                width="21"
                class="channel-icons"
            />
            Paypal
        </b-dropdown-item> -->
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
        <PlaidLink
            clientName="Zenvision"
            env="development"
            :link_token="plaidLinkToken"
            :products="['auth', 'transactions']"
            :onLoad="onLoad"
            :onSuccess="onSuccess"
            :onExit="onExit"
            :onEvent="onEvent"
        >
            <b-dropdown-item
                href="#"
                id="teller-connect"
                @click="handleCreditCardClick"
            >
                <img
                    src="/images/icons/creditcard-icon.svg"
                    alt
                    height="21"
                    width="21"
                    class="channel-icons"
                />
                Credit Card
            </b-dropdown-item>
        </PlaidLink>

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
        <b-modal id="plaid-connect" centered hide-footer hide-header>
            <BankConnect
                :bankAccounts="plaidAccounts"
                :bankInstitution="plaidInstitution"
                :plaidPublicToken="plaidPublicToken"
                :plaidClickType="plaidClickType"
                @handle-close="$bvModal.hide('plaid-connect')"
            />
        </b-modal>
    </b-dropdown>
</template>
<script>
import axios from "axios";
import PlaidLink from "vue-plaid-link2";
import { mapMutations } from "vuex";
import BankConnect from "../../components/custom-components/modals/bank-account-modal.vue";

export default {
    data() {
        return {
            stripeUrl: `https://connect.stripe.com/oauth/authorize?response_type=code&client_id=ca_IXYHhxtBir6EPIAuynBwhmTDeNJLAC0H&scope=read_write&redirect_uri=https://app.zenvision.io/stripeconnect&state=mastersheet-${Math.floor(
                Math.random() * 10000000 + 1
            )}`,
            paypalUrl: `https://www.paypal.com/connect/?flowEntry=static&client_id=AY8ay9apzuTb7arwPRYfLPlPN1tu9QGIKsEyhDBjLI1FGDwfrtWEvcmOEWgtjXLUrxESYB5jQFXziwlP&response_type=code&scope=openid profile&redirect_uri=https%3A%2F%2Fapp.zenvision.io%2Fpaypal&state=mastersheet-${Math.floor(
                Math.random() * 10000000 + 1
            )}`,
            plaidAccounts: [],
            plaidLinkToken: "",
            plaidPublicToken: "",
            plaidInstitution: "",
            plaidClickType: "",
            showModal: true
        };
    },
    components: { PlaidLink, BankConnect },
    methods: {
        ...mapMutations(["TOGGGLE_LOADING_STATUS"]),

        handleBankAccountClick() {
            this.TOGGGLE_LOADING_STATUS(true);
            this.plaidClickType = "depository";
            setTimeout(() => {
                this.TOGGGLE_LOADING_STATUS(false);
            }, 1000);
        },
        handleCreditCardClick() {
            this.TOGGGLE_LOADING_STATUS(true);
            this.plaidClickType = "credit";
            setTimeout(() => {
                this.TOGGGLE_LOADING_STATUS(false);
            }, 1000);
        },
        async onLoad() {},
        onSuccess(public_token, metadata) {
            try {
                const accounts = metadata.accounts.filter(account =>
                    this.plaidClickType === "depository"
                        ? account.type === "depository"
                        : account.type === "credit"
                );
                this.plaidAccounts = accounts;
                this.plaidPublicToken = public_token;
                this.plaidInstitution = metadata.institution;
            } catch (err) {
                console.log(err);
            }
        },
        onExit(err, metadata) {},
        onEvent(eventName, metadata) {
            console.log({ eventName });
            if (eventName === "HANDOFF") {
                this.$bvModal.show("plaid-connect");
            }
            if (eventName === "ERROR") {
                this.$bvModal.show("bankaccount-error");
            }
        },
        async getPlaidLinkToken() {
            try {
                const result = await axios.get("bankaccount-link-token");
                const linkToken = result.data;
                if (linkToken) {
                    this.plaidLinkToken = linkToken;
                }
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
