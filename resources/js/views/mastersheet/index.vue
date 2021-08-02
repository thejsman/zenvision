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
import { mapGetters, mapActions, mapMutations, mapState } from "vuex";
import Loading from "vue-loading-overlay";
import { eventBus } from "../../app";
export default {
    data() {
        return {
            allOrders: []
        };
    },
    computed: {
        ...mapGetters([
            "shopifyStores",
            "stripeAccounts",
            "hasStripeAccountCS",
            "shopifyAllOrders",
            "loadingStatus"
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
        BankAccount,
        Loading
    },
    async created() {
        this.toggleCurrentChannel("MS");

        await this.loadAllChannels();

        eventBus.$on("toggleShopifyStore", async () => {
            console.log("toggleShopifyStore triggered");
            await this.loadAllChannels();
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
    },
    methods: {
        ...mapActions("MasterSheet", ["loadAllChannels"]),
        ...mapActions(["toggleCurrentChannel"]),

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
            </div>
            <div class="col-md-3 mt-4">
                <Sidepanel />
            </div>
            <div class="col-md-9 mt-4">
                <Mainpanel />
            </div>

            <loading
                :active.sync="loadingStatus"
                :can-cancel="false"
                :is-full-page="true"
                :background-color="'#191e2c'"
                :opacity="0.8"
            ></loading>
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
