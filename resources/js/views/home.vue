<script>
import Layout from "../layouts/main";
import PageHeader from "../components/page-header";
import axios from "axios";

//Top Nav imports
import ChannelDropdown from "../components/custom-components/channel-dropdown";
import DateRange from "../components/custom-components/date-range";
import StoreIcon from "../components/custom-components/shopifystore-icon";
import PaypalAccount from "../components/custom-components/paypal-icon";
import StripeAccount from "../components/custom-components/stripe-icon";
import FacebookAccount from "../components/custom-components/facebook-icon";
import SnapchatAccount from "../components/custom-components/snapchat-icon";
import TiktokAccount from "../components/custom-components/tiktok-icon";
import GoogleAccount from "../components/custom-components/google-icon";

//Dashboard Data sections import
import Profit from "../components/custom-components/dashboard-data/profit-section";
import Revenue from "../components/custom-components/dashboard-data/revenue-section";
import Costs from "../components/custom-components/dashboard-data/cost-section";
import KeyPerformance from "../components/custom-components/dashboard-data/key-performance-section";
import Chart from "../components/custom-components/dashboard-data/chart-section";

//Modal import
import ShopifyConnect from "../components/custom-components/modals/ShopifyConnect-modal";
import FacebookConnect from "../components/custom-components/modals/facebook-adaccount-modal";
import SnapchatConnect from "../components/custom-components/modals/snapchat-adaccount-modal";
import GoogleConnect from "../components/custom-components/modals/google-adaccount-modal";
import TiktokConnect from "../components/custom-components/modals/tiktok-adaccount-modal";
import Profile from "../components/custom-components/modals/profile-modal.vue";
import moment from "moment";
import { mapState, mapGetters, mapActions } from "vuex";

import { eventBus } from "../app";

import Loading from "vue-loading-overlay";
// Import stylesheet
import "vue-loading-overlay/dist/vue-loading.css";

export default {
    components: {
        Layout,
        PageHeader,
        ShopifyConnect,
        StoreIcon,
        DateRange,
        ChannelDropdown,
        Profit,
        Revenue,
        Costs,
        KeyPerformance,
        Chart,
        PaypalAccount,
        StripeAccount,
        FacebookConnect,
        FacebookAccount,
        SnapchatAccount,
        SnapchatConnect,
        TiktokAccount,
        GoogleConnect,
        GoogleAccount,
        TiktokConnect,
        Loading,
        Profile
    },
    data() {
        return {
            title: "Zenvision Dashboard",
            allOrders: [],
            enabled_on_dashboard: [],
            refund_total: 0,
            number_of_products: 0,
            orders: [],
            chargebacks: 0,
            merchantFees: [],
            merchantFeesTotal: 0,
            chargebacksTotal: 0,
            backupOrders: [],
            dateRangeSelected: [],
            facebookData: [],
            snapchatAdAccounts: [],
            tiktokAdAccounts: [],
            googleAdAccounts: [],
            googleData: [],
            facebookError: false,
            snapchatError: false,
            tiktokError: false,
            googleError: false,
            facebookAdAccounts: [],

            fb_spend: [],
            google_ads_data: [],
            paypalChargebacks: [],
            stripeChargebacks: [],

            stripeAccounts: [],
            stripeTransactions: [],

            isLoading: false
        };
    },

    computed: {
        ...mapGetters([
            "shopifyStores",
            "startDateS",
            "endDateS",
            "shopifyOrders",
            "hasShopifyStoreCS"
        ])
    },
    created() {
        if (new URL(location.href).searchParams.get("listSnapchatAccount")) {
            this.getSnapchatAdAccounts();
        }

        if (new URL(location.href).searchParams.get("listGoogleAccounts")) {
            this.getGoogleAdAccounts();
        }
        if (new URL(location.href).searchParams.get("listTiktokAccounts")) {
            this.getTiktokAccounts();
        }
        if (new URL(location.href).searchParams.get("listFacebookAdAccounts")) {
            this.getFacebookAdsAccounts();
        }
        if (new URL(location.href).searchParams.get("stripeAddAccount")) {
            const result = new URL(location.href).searchParams.get(
                "stripeAddAccount"
            );
            setTimeout(() => {
                if (result === "success") {
                    this.$bvModal.show("stripe-add-account");
                    eventBus.$emit(
                        "triggerStripeCheck",
                        new URL(location.href).searchParams.get("record_id")
                    );
                } else {
                    this.$bvModal.show("stripe-add-account-error");
                }
            }, 1000);
        }

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

        this.getShopifyStoreData();

        eventBus.$on("changeDateRange", date => this.handleDateChange(date));
        eventBus.$on("toggleShopifyStore", async () => {
            this.getShopifyStoreData();
        });
        eventBus.$on("cogs-updated", async () => {
            this.getShopifyStoreData();
        });

        eventBus.$on("setLoadingTrue", () => {
            this.isLoading = true;
        });
        eventBus.$on("setLoadingFalse", () => {
            this.isLoading = false;
        });
    },
    methods: {
        ...mapActions(["getShopifyStoreOrders", "getShopifyStores"]),

        showModal(modalId) {
            this.$bvModal.show(modalId);
        },
        handleOk() {
            window.location.href = "/";
        },
        async getShopifyStoreData() {
            await this.getShopifyStores();
            await this.getShopifyStoreOrders();
            try {
                const orders = this.shopifyOrders;
                //assign values
                this.allOrders = _.sortBy(orders, "created_on_shopify");
            } catch (error) {
                console.log(error);
            }
        },
        async getFacebookAdsAccounts(code) {
            try {
                const result = await axios.get("facebook-listadaccounts", {
                    params: {
                        access_token: new URL(location.href).searchParams.get(
                            "listFacebookAdAccounts"
                        )
                    }
                });
                this.facebookData = result.data;

                this.$bvModal.show("facebook-connect");
            } catch (error) {
                console.log(error);
                this.facebookError = true;
            }
        },

        async getSnapchatAdAccounts() {
            try {
                if (
                    new URL(location.href).searchParams.get(
                        "listSnapchatAccount"
                    ) === "noaccount"
                ) {
                    setTimeout(() => {
                        this.showModal("snapchat-noaccount");
                    }, 100);
                    return;
                }
                const result = await axios.get("snapchat-listadaccounts", {
                    params: {
                        organization_id: new URL(
                            location.href
                        ).searchParams.get("listSnapchatAccount")
                    }
                });
                this.snapchatAdAccounts = result.data;

                this.$bvModal.show("snapchat-connect");
            } catch (error) {
                console.log(error);
                this.snapchatError = true;
            }
        },
        async getTiktokAccounts() {
            try {
                const accessToken = new URL(location.href).searchParams.get(
                    "listTiktokAccounts"
                );

                if (accessToken === "noaccount") {
                    setTimeout(() => {
                        this.showModal("tiktok-noaccount");
                    }, 100);
                } else {
                    const result = await axios.get(
                        "tiktokaccount-listaccount",
                        {
                            params: {
                                access_token: new URL(
                                    location.href
                                ).searchParams.get("listTiktokAccounts")
                            }
                        }
                    );
                    this.tiktokAdAccounts = result.data;

                    this.$bvModal.show("tiktok-connect");
                }
            } catch (error) {
                console.log(error);
                this.tiktokError = true;
            }
        },

        async getGoogleAdAccounts() {
            try {
                const result = await axios.get("google-connect-listaccounts", {
                    params: {
                        access_token: new URL(location.href).searchParams.get(
                            "listGoogleAccounts"
                        )
                    }
                });
                this.googleAdAccounts = result.data;

                this.$bvModal.show("google-connect");
            } catch (error) {
                console.log(error);
                this.snapchatError = true;
            }
        },
        async handleDateChange() {
            const s_date = moment(this.startDateS).format("MM-DD-YYYY");
            const e_date = moment(this.endDateS).format("MM-DD-YYYY");

            await this.getShopifyStoreData();
            this.dateRangeSelected = [moment(s_date), moment(e_date)];

            eventBus.$emit("dateChanged", { s_date, e_date });
        }
    }
};
</script>
<template>
    <Layout>
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center">
                    <ChannelDropdown />
                    <StoreIcon />
                    <PaypalAccount />
                    <StripeAccount />
                    <FacebookAccount />
                    <SnapchatAccount />
                    <TiktokAccount />
                    <GoogleAccount />
                    <DateRange @changeDateRange="handleDateChange" />
                </div>
            </div>
        </div>
        <loading
            :active.sync="isLoading"
            :can-cancel="false"
            :is-full-page="true"
            :background-color="'#2F3863'"
        ></loading>
        <div class="row">
            <div class="col-xl-5">
                <Profit :profitData="allOrders" />
                <Revenue :revenueData="allOrders" />
                <Costs :costData="allOrders" :refundTotal="refund_total" />
            </div>
            <div class="col-xl-7">
                <Chart :chartData="allOrders" />
                <KeyPerformance :performanceData="allOrders" />
            </div>
        </div>
        <b-modal
            id="shopify-connect"
            size="lg"
            centered
            hide-footer
            hide-header
        >
            <ShopifyConnect @handle-close="$bvModal.hide('shopify-connect')" />
        </b-modal>
        <b-modal id="profile-update" size="xl" centered hide-footer hide-header>
            <Profile @handle-close="$bvModal.hide('profile-update')" />
        </b-modal>
        <b-modal id="facebook-connect" centered hide-footer hide-header>
            <FacebookConnect
                :facebookData="facebookData"
                :facebookError="facebookError"
                :adAccounts="facebookAdAccounts"
                :startDate="dateRangeSelected"
                @handle-close="$bvModal.hide('facebook-connect')"
            />
        </b-modal>
        <b-modal id="snapchat-connect" centered hide-footer hide-header>
            <SnapchatConnect
                :snapchatData="snapchatAdAccounts"
                :snapchatError="snapchatError"
                @handle-close="$bvModal.hide('snapchat-connect')"
            />
        </b-modal>
        <b-modal id="google-connect" centered hide-footer hide-header>
            <GoogleConnect
                :googleData="googleAdAccounts"
                :googleError="googleError"
                @handle-close="$bvModal.hide('google-connect')"
            />
        </b-modal>
        <b-modal id="tiktok-connect" centered hide-footer hide-header>
            <TiktokConnect
                :tiktokData="tiktokAdAccounts"
                :tiktokError="tiktokError"
                @handle-close="$bvModal.hide('tiktok-connect')"
            />
        </b-modal>
        <!-- Tiktok No ad account -->
        <b-modal
            id="tiktok-noaccount"
            title="TikTok Ad Account"
            ok-only
            ok-variant="primary"
            @ok="handleOk"
            @hide="handleOk"
        >
            <p class="my-2">No TikTok Ad Account found!</p>
        </b-modal>

        <!-- Snapchat No Organization -->
        <b-modal
            id="snapchat-noaccount"
            title="Snapchat Ad Account"
            ok-only
            ok-variant="primary"
            @ok="handleOk"
            @hide="handleOk"
        >
            <p class="my-2">No Snapchat Ad Account found!</p>
        </b-modal>

        <!--  Stripe modal -->
        <b-modal
            id="stripe-add-account"
            title="Stripe Account"
            ok-only
            ok-variant="primary"
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
    </Layout>
</template>
