<script>
import Layout from "../../layouts/main";
import AddChannel from "./add-channel";
import Sidepanel from "./sidepanel";
import Mainpanel from "./mainpanel";
import ShopifyConnect from "../../components/custom-components/modals/ShopifyConnect-modal";
import ShopifyStoreIcon from "../../components/custom-components/shopifystore-icon";
import PaypalAccountIcon from "../../components/custom-components/paypal-icon";
import StripeAccount from "../../components/custom-components/stripe-icon";
import BankAccount from "../../components/custom-components/bankaccount-icon.vue";
import { mapGetters, mapActions } from "vuex";

import { eventBus } from "../../app";
export default {
    data() {
        return {
            allOrders: []
        };
    },
    computed: {
        ...mapGetters([
            "stripeAccounts",
            "hasStripeAccountCS",
            "shopifyAllOrders"
        ])
    },
    components: {
        Layout,
        AddChannel,
        Sidepanel,
        Mainpanel,
        ShopifyConnect,
        ShopifyStoreIcon,
        PaypalAccountIcon,
        StripeAccount,
        BankAccount
    },
    async created() {
        await this.getStripeAccounts();
        await this.getShopifyStoreAllOrders();

        eventBus.$on("toggleShopifyStore", () => {
            this.getShopifyData();
        });

        if (new URL(location.href).searchParams.get("shopifyAddAccount")) {
            const result = new URL(location.href).searchParams.get(
                "shopifyAddAccount"
            );
            setTimeout(() => {
                result === "success"
                    ? this.$bvModal.show("shopify-add-account")
                    : this.$bvModal.show("shopify-add-account-error");
            }, 1000);
        }
        if (new URL(location.href).searchParams.get("stripeAddAccount")) {
            const result = new URL(location.href).searchParams.get(
                "stripeAddAccount"
            );
            setTimeout(() => {
                result === "success"
                    ? this.$bvModal.show("stripe-add-account")
                    : this.$bvModal.show("stripe-add-account-error");
            }, 1000);
        }

        this.getShopifyData();
    },
    methods: {
        ...mapActions(["getStripeAccounts", "getShopifyStoreAllOrders"]),
        async getShopifyData() {
            console.log(
                "stripeAccounts",
                this.stripeAccounts,
                "hasStripeAccountCS",
                this.hasStripeAccountCS,
                "shopifyAllOrders",
                this.shopifyAllOrders
            );
            const {
                data: { enabled_on_dashboard, orders }
            } = await axios.get("shopifystoredata");
            this.allOrders = orders;
        },
        handleOk() {
            window.location.href = "/mastersheet";
        }
    }
};
</script>
<template>
    <Layout>
        <div class="row">
            <!-- Right Sidebar -->
            <div class="col-12 my-2 d-flex justify-content-between">
                <div class="d-flex justify-content-start align-items-center">
                    <AddChannel />
                    <ShopifyStoreIcon :disableFeature="false" />
                    <PaypalAccountIcon :disableFeature="false" />
                    <StripeAccount :disableFeature="false" />

                    <BankAccount />
                </div>
                <b-button class="border-0 mr-4 btn-export" variant="dark"
                    >Export</b-button
                >
            </div>
            <div class="col-3 mt-4">
                <Sidepanel :orders="allOrders" />
            </div>
            <div class="col-9 mt-4">
                <Mainpanel :orders="allOrders" />
            </div>
        </div>

        <b-modal
            id="shopify-connect"
            size="lg"
            centered
            hide-footer
            hide-header
        >
            <ShopifyConnect
                :called-from="'mastersheet'"
                @handle-close="$bvModal.hide('shopify-connect')"
            />
        </b-modal>
        <!-- Shopify Modal -->

        <b-modal
            id="shopify-add-account"
            title="Shopify Account"
            ok-only
            ok-variant="primary"
            @ok="handleOk"
            @hide="handleOk"
        >
            <p class="my-2">
                We are importing your Shopify orders and products, this process
                can take a few minutes, please check back later.
            </p>
        </b-modal>

        <!--  Stripe modal -->
        <b-modal
            id="stripe-add-account"
            title="Stripe Account"
            ok-only
            ok-variant="primary"
            @ok="handleOk"
            @hide="handleOk"
        >
            <p class="my-2">
                We are importing your Stripe transactions, this process can take
                a few minutes, please check back later.
            </p>
        </b-modal>
        <b-modal
            id="stripe-add-account-error"
            title="Stripe Account"
            ok-only
            ok-variant="primary"
            @ok="handleOk"
            @hide="handleOk"
        >
            <p class="my-2">
                We are unable to connect your Stripe account, please try again
                later.
            </p>
        </b-modal>
    </Layout>
</template>
<style>
.mastersheet-left {
    background-color: #191e2c;
    opacity: 0.7;
}
.block {
    width: 200px;
    height: 200px;
    background-color: #2f3863;
}
.btn-export {
    height: 40px;
    width: 80px;
}
</style>
