<template>
    <div class="row">
        <div class="col-xl-12 mt-5 d-flex justify-content-between">
            <h3>Cost</h3>
            <h3>{{ totalCost }}</h3>
        </div>
        <div v-for="cost of data" :key="cost.id" class="col-md-4 p-2">
            <Stat
                :title="cost.title"
                :value="cost.value"
                :loading="cost.loading"
                :onClick="cost.onClick"
                :totalSubscriptionCount="cost.totalSubscriptionCount"
                :showIcon="cost.showIcon"
                :iconName="cost.iconName"
                :toolTip="cost.toolTip"
            />
        </div>

        <b-modal
            id="subscription-details"
            size="lg"
            centered
            hide-footer
            hide-header
        >
            <SubscriptionCost @handle-close="handleSubscriptionClose" />
        </b-modal>
        <b-modal id="cogs-details" size="xl" centered hide-footer hide-header>
            <CogsModal @handle-close="handleCogsClose" />
        </b-modal>
    </div>
</template>
<script>
import Stat from "../../widgets/stat";
import { eventBus } from "../../../app";
import SubscriptionCost from "../modals/subscription-cost";
import moment from "moment";
import {
    displayCurrency,
    updateData,
    updateAdData,
    setLoading,
    getDatesBetweenDates,
    getDatesBetweenDatesStandard,
    getDatesBetweenDatesTiktok,
    setLoadingSingle,
    setLoadingAdSingle,
    updateDataMerchantFee
} from "../../../utils";
import CogsModal from "../modals/CogsDetails-modal";
import axios from "axios";
import {
    COGS_TOTAL,
    DISCOUNTS_TOTAL,
    REFUNDS_TOTAL,
    CHARGEBACKS_TOTAL,
    MERCHANT_FEE,
    AD_SPEND_FACEBOOK,
    AD_SPEND_GOOGLE,
    AD_SPEND_SNAPCHAT,
    AD_SPEND_TIKTOK,
    SUBSCRIPTION_COST
} from "../../../constants";
import _ from "lodash";
export default {
    components: { Stat, SubscriptionCost, CogsModal },
    data() {
        return {
            subscriptionData: 0,
            cogsTotal: 0,
            stripeChargebackTotal: 0,
            paypalChargebackTotal: 0,
            shopifyChargebackTotal: 0,
            merchantFees: 0,
            tiktokAdsSpend: 0,
            snapchatAdsSpend: 0,
            facebookAdsSpend: 0,
            googleAdsSpend: 0,
            data: [
                {
                    id: 1,
                    title: COGS_TOTAL,
                    value: `0`,
                    loading: true,
                    onClick: this.handleCogsClick,
                    iconName: "exclamation-icon.svg",
                    showIcon: true
                },
                {
                    id: 2,
                    title: DISCOUNTS_TOTAL,
                    value: `0`,
                    loading: true
                },
                {
                    id: 3,
                    title: REFUNDS_TOTAL,
                    value: `0`,
                    loading: true
                },
                {
                    id: 4,
                    title: CHARGEBACKS_TOTAL,
                    value: `0`,
                    loading: true
                },
                {
                    id: 5,
                    title: MERCHANT_FEE,
                    value: `0`,
                    loading: true,
                    iconName: "data-warning.svg",
                    showIcon: false,
                    toolTip:
                        "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
                },
                {
                    id: 6,
                    title: AD_SPEND_FACEBOOK,
                    value: `-`,
                    loading: true,
                    iconName: "facebook-icon.svg",
                    showIcon: true
                },
                {
                    id: 7,
                    title: AD_SPEND_GOOGLE,
                    value: `-`,
                    loading: true,
                    iconName: "google-icon.svg",
                    showIcon: true
                },
                {
                    id: 8,
                    title: AD_SPEND_SNAPCHAT,
                    value: `-`,
                    loading: true,
                    iconName: "snapchat-icon.svg",
                    showIcon: true
                },
                {
                    id: 9,
                    title: AD_SPEND_TIKTOK,
                    value: `-`,
                    loading: true,
                    iconName: "tiktok-icon.svg",
                    showIcon: true
                },
                {
                    id: 10,
                    title: SUBSCRIPTION_COST,
                    value: `0`,
                    loading: true,
                    onClick: this.handleSubscriptionClick,
                    totalSubscriptionCount: 0
                }
            ],
            totalDiscount: 0,
            hasTiktokAccount: false,
            hasSnapchatAccount: false,
            hasFacebookAccount: false,
            hasGoogleAccount: false,
            hasStripeAccount: false,
            hasPaypalAccount: false,
            paypalFeeTotal: 0,
            hasShopifyAccount: false,
            stripeFeeTotal: 0,
            stripeLoadingStatus: false,
            startDate: moment().subtract(1, "month"),
            endDate: moment()
        };
    },
    computed: {
        totalCost() {
            const totalCost = parseFloat(
                this.totalMerchantFees +
                    this.refundTotal +
                    this.totalDiscount +
                    this.subscriptionData +
                    this.cogsTotal +
                    this.totalChargeback +
                    this.tiktokAdsSpend +
                    this.snapchatAdsSpend +
                    this.facebookAdsSpend +
                    this.googleAdsSpend
            );

            eventBus.$emit("totalCostValue", totalCost);
            return displayCurrency(totalCost);
        },
        anyActiveAccount() {
            return (
                this.hasPaypalAccount ||
                this.hasStripeAccount ||
                this.hasShopifyAccount
            );
        },
        totalMerchantFees() {
            return this.stripeFeeTotal + this.paypalFeeTotal;
        },
        totalChargeback() {
            return (
                this.stripeChargebackTotal +
                this.paypalChargebackTotal +
                this.shopifyChargebackTotal
            );
        }
    },
    props: {
        costData: {
            type: Array,
            default: () => []
        },
        refundTotal: {
            type: Number,
            default: 0
        }
    },
    watch: {
        costData(value, newValue) {
            this.assignData(this.refundTotal, this.costData);
        }
    },
    created() {
        eventBus.$on("updateSubscription", async () => {
            await this.getSubscriptionData();
        });

        eventBus.$on("dateChanged", ({ s_date, e_date }) => {
            this.startDate = s_date;
            this.endDate = e_date;

            // setLoading(this.data);

            this.getMerchantfeesTotal(s_date, e_date);
            this.checkAndShowAdAccountsData(s_date, e_date);
        });

        eventBus.$on("hasTiktokAccount", status => {
            setLoadingAdSingle(this.data, "TIKTOK");
            this.hasTiktokAccount = status;

            if (status) {
                return this.getTiktokAdSpend(this.startDate, this.endDate);
            } else {
                eventBus.$emit("tiktokTransactionEvent", []);
                updateAdData(this.data, "TIKTOK", "-");
            }
        });

        eventBus.$on("hasSnapchatAccount", status => {
            setLoadingAdSingle(this.data, "SNAPCHAT");
            this.hasSnapchatAccount = status;
            if (status) {
                return this.getSnapchatAdSpend(this.startDate, this.endDate);
            } else {
                this.snapchatAdsSpend = 0;
                eventBus.$emit("snapchatTransactionEvent", []);
                updateAdData(this.data, "SNAPCHAT", displayCurrency("-"));
            }
        });

        eventBus.$on("hasFacebookAccount", status => {
            setLoadingAdSingle(this.data, "FACEBOOK");
            this.hasFacebookAccount = status;

            if (status) {
                return this.getFacebookAdSpend(this.startDate, this.endDate);
            } else {
                eventBus.$emit("facebookTransactionEvent", []);
                updateAdData(this.data, "FACEBOOK", displayCurrency("-"));
            }
        });

        eventBus.$on("hasGoogleAccount", status => {
            setLoadingAdSingle(this.data, "GOOGLE");
            this.hasGoogleAccount = status;

            if (status) {
                return this.getGoogleAdSpend(this.startDate, this.endDate);
            } else {
                eventBus.$emit("googleTransactionEvent", []);
                updateAdData(this.data, "GOOGLE", displayCurrency("-"));
            }
        });

        eventBus.$on("hasStripeAccount", status => {
            this.hasStripeAccount = status;
            this.stripeChargebacks = 0;
            this.stripeFeeTotal = 0;

            if (status) {
                this.getStripeTransactions();
                // this.getChargebackTotal();
            } else {
                this.stripeFeeTotal = 0;
                this.stripeChargebackTotal = 0;
                updateDataMerchantFee(
                    this.data,
                    MERCHANT_FEE,
                    displayCurrency(this.totalMerchantFees)
                );
                eventBus.$emit("stripeTransactionEvent", []);
                eventBus.$emit("stripeChargebackEvent", []);
                updateDataMerchantFee(
                    this.data,
                    CHARGEBACKS_TOTAL,
                    displayCurrency(this.totalChargeback)
                );
            }
        });

        eventBus.$on("hasPaypalAccount", status => {
            this.hasPaypalAccount = status;
        });
        eventBus.$on("hasShopifyAccount", status => {
            this.hasShopifyAccount = status;
            // this.assignData(this.refundTotal, this.costData);
        });
    },
    methods: {
        assignData(refundTotal, orders) {
            setTimeout(() => {
                this.getCogsData(orders, refundTotal);

                this.getSubscriptionData();
                this.getChargebackTotal();
            }, 1000);

            // this.getMerchantfeesTotal(s_date, e_date);
        },

        handleSubscriptionClick() {
            this.$bvModal.show("subscription-details");
        },
        handleSubscriptionClose() {
            this.$bvModal.hide("subscription-details");
        },
        handleCogsClick() {
            this.$bvModal.show("cogs-details");
        },

        handleCogsClose() {
            this.$bvModal.hide("cogs-details");
        },

        async getCogsData(orders, refundTotal) {
            if (this.hasShopifyAccount) {
                const discounts = _.sumBy(orders, order =>
                    parseFloat(order.total_discounts)
                );
                this.totalDiscount = discounts;
                updateData(
                    this.data,
                    DISCOUNTS_TOTAL,
                    displayCurrency(discounts)
                );
                updateData(
                    this.data,
                    REFUNDS_TOTAL,
                    displayCurrency(refundTotal)
                );
                try {
                    const result = await axios.get("/cogsicon");
                    const showIcon = result.data === 0 ? false : true;
                    const cogs = _.sumBy(orders, order =>
                        parseFloat(order.total_cost)
                    );
                    this.cogsTotal = cogs;

                    this.updateCogsData(
                        this.data,
                        COGS_TOTAL,
                        this.hasShopifyAccount ? displayCurrency(cogs) : "-",
                        showIcon
                    );
                } catch (err) {
                    this.updateCogsData(
                        this.data,
                        COGS_TOTAL,
                        displayCurrency(0),
                        false
                    );
                }
            } else {
                this.updateCogsData(this.data, COGS_TOTAL, "-", false);
                updateData(this.data, DISCOUNTS_TOTAL, "-");
                updateData(this.data, REFUNDS_TOTAL, "-");
            }
        },
        async getSubscriptionData() {
            try {
                const result = await axios.get("/subscriptioncost");
                const { data } = result;
                if (data.length > 0) {
                    const subTotal2 = this.getSubscriptionTotal(data);
                    this.subscriptionData = subTotal2;
                    this.updateSubscriptionData(
                        this.data,
                        SUBSCRIPTION_COST,
                        displayCurrency(subTotal2),
                        data.length
                    );
                } else {
                    this.subscriptionData = 0;
                    this.updateSubscriptionData(
                        this.data,
                        SUBSCRIPTION_COST,
                        displayCurrency(0),
                        0
                    );
                }
            } catch (error) {
                this.updateSubscriptionData(
                    this.data,
                    SUBSCRIPTION_COST,
                    displayCurrency(0),
                    0
                );
            }
        },
        updateSubscriptionData(data, title, value, totalCount) {
            data.forEach(d => {
                if (d.title === title) {
                    d.value = `${value}`;
                    d.loading = false;
                    d.totalSubscriptionCount = totalCount;
                }
            });
        },
        updateCogsData(data, title, value, showicon) {
            data.forEach(d => {
                if (d.title === title) {
                    d.value = `${value}`;
                    d.loading = false;
                    d.showIcon = showicon;
                }
            });
        },
        getSubscriptionTotal(subscriptions) {
            let subTotal = 0;
            subscriptions.forEach(sub => {
                subTotal += this.calculateSubscription(sub);
            });

            return subTotal;
        },
        calculateSubscription(sub) {
            let total = 0;
            if (sub.billing_period === "Daily") {
                let startDate = new Date(sub.starting_date);
                const endDate =
                    sub.end_date === null ? new Date() : new Date(sub.end_date);
                while (startDate <= endDate) {
                    total += parseFloat(sub.subscription_price);
                    startDate = new Date(
                        startDate.setDate(startDate.getDate() + 1)
                    );
                }
                return total;
            } else if (sub.billing_period === "Weekly") {
                let startDate = new Date(sub.starting_date);
                const endDate =
                    sub.end_date === null ? new Date() : new Date(sub.end_date);
                while (startDate <= endDate) {
                    total += parseFloat(sub.subscription_price);
                    startDate = new Date(
                        startDate.setDate(startDate.getDate() + 7)
                    );
                }
                return total;
            } else if (sub.billing_period === "Monthly") {
                let startDate = new Date(sub.starting_date);
                const endDate =
                    sub.end_date === null ? new Date() : new Date(sub.end_date);
                while (startDate <= endDate) {
                    total += parseFloat(sub.subscription_price);
                    startDate = new Date(
                        startDate.setMonth(startDate.getMonth() + 1)
                    );
                }
                return total;
            } else if (sub.billing_period === "Every 3 months") {
                let startDate = new Date(sub.starting_date);
                const endDate =
                    sub.end_date === null ? new Date() : new Date(sub.end_date);
                while (startDate <= endDate) {
                    total += parseFloat(sub.subscription_price);
                    startDate = new Date(
                        startDate.setMonth(startDate.getMonth() + 3)
                    );
                }
                return total;
            } else if (sub.billing_period === "Every 6 months") {
                let startDate = new Date(sub.starting_date);
                const endDate =
                    sub.end_date === null ? new Date() : new Date(sub.end_date);
                while (startDate <= endDate) {
                    total += parseFloat(sub.subscription_price);
                    startDate = new Date(
                        startDate.setMonth(startDate.getMonth() + 6)
                    );
                }
                return total;
            } else if (sub.billing_period === "Yearly") {
                let startDate = new Date(sub.starting_date);
                const endDate =
                    sub.end_date === null ? new Date() : new Date(sub.end_date);
                while (startDate <= endDate) {
                    total += parseFloat(sub.subscription_price);
                    startDate = new Date(
                        startDate.setFullYear(startDate.getFullYear() + 1)
                    );
                }
                return total;
            }
        },
        async getChargebackTotal() {
            try {
                //Shopify Chargebacks
                if (this.hasShopifyAccount) {
                    this.shopifyChargebackTotal = 0;
                    const result = await axios.get("getshopifydisputes");
                    const { disputes } = result.data;
                    if (disputes.length > 1) {
                        data.forEach(dispute => {
                            this.shopifyChargebackTotal += parseFloat(
                                dispute.amount
                            );
                        });
                    }
                }

                //Stripe Chargebacks

                if (this.hasStripeAccount) {
                    this.stripeChargebackTotal = 0;

                    const stripeResult = await axios.get(
                        "stripeconnect-chargeback"
                    );

                    let stripeChargebacks = stripeResult.data
                        .filter(
                            sc =>
                                sc.status === "charge_refunded" ||
                                sc.status === "lost"
                        )
                        .filter(
                            sc =>
                                new Date(sc.created * 1000) >=
                                    new Date(this.startDate) &&
                                new Date(sc.created * 1000) <=
                                    new Date(this.endDate)
                        );
                    if (stripeChargebacks.length > 0) {
                        eventBus.$emit(
                            "stripeChargebackEvent",
                            stripeChargebacks
                        );
                        stripeChargebacks.forEach(sc => {
                            this.stripeChargebackTotal += parseFloat(
                                sc.amount / 100
                            );
                        });
                    }
                }

                setTimeout(() => {
                    updateData(
                        this.data,
                        CHARGEBACKS_TOTAL,
                        this.hasStripeAccount ||
                            this.hasShopifyAccount ||
                            this.hasPaypalAccount
                            ? displayCurrency(this.totalChargeback)
                            : "-"
                    );
                }, 500);
            } catch (err) {
                this.totalChargeback = 0;
                updateData(this.data, CHARGEBACKS_TOTAL, "-");
            }
        },
        async getMerchantfeesTotal(s_date, e_date) {
            if (this.hasPaypalAccount) {
                await this.getPaypalTransactionsTotal(s_date, e_date);
            }

            if (this.hasStripeAccount) {
                await this.getStripeTransactions();
            }
            setTimeout(() => {
                updateData(
                    this.data,
                    MERCHANT_FEE,
                    this.hasStripeAccount ||
                        this.hasShopifyAccount ||
                        this.hasPaypalAccount
                        ? displayCurrency(this.totalMerchantFees)
                        : "-"
                );
            }, 1000);
        },
        async getPaypalTransactionsTotal(s_date, e_date) {
            paypalFeeTotal = 0;
            const dates = getDatesBetweenDates(s_date, e_date);
            dates.forEach(async date => {
                const paypalResult = await axios.get("paypaltransactions", {
                    params: { s_date: date[0], e_date: date[1] }
                });
                const paypalTransactions = paypalResult.data;

                if (paypalTransactions.length > 0) {
                    paypalTransactions.map(transaction => {
                        if (
                            transaction.transaction_info.hasOwnProperty(
                                "fee_amount"
                            )
                        ) {
                            const orderDate = moment(
                                transaction.transaction_updated_date
                            ).format("MM-DD-YYYY");

                            if (
                                new Date(orderDate) >= new Date(s_date) &&
                                new Date(orderDate) <= new Date(e_date)
                            ) {
                                this.paypalFeeTotal += Math.abs(
                                    parseFloat(
                                        transaction.transaction_info.fee_amount
                                            .value
                                    )
                                );
                            }
                        }
                    });
                }
            });
            updateDataMerchantFee(
                this.data,
                MERCHANT_FEE,
                displayCurrency(this.totalMerchantFees)
            );
        },
        async getTiktokAdSpend(s_date, e_date) {
            this.tiktokAdsSpend = 0;
            const dates = getDatesBetweenDatesTiktok(s_date, e_date);

            let tiktokTotal = 0;
            try {
                dates.forEach(async date => {
                    try {
                        const tiktokResult = await axios.get(
                            "tiktokaccount-adspend",
                            {
                                params: {
                                    s_date: date[0],
                                    e_date: date[1]
                                }
                            }
                        );
                        const tiktokTransactions = tiktokResult.data;

                        if (tiktokTransactions.length > 0) {
                            eventBus.$emit(
                                "tiktokTransactionEvent",
                                tiktokTransactions
                            );
                            tiktokTransactions.map(transaction => {
                                if (transaction.hasOwnProperty("stat_cost")) {
                                    const orderDate = moment(
                                        transaction.stat_datetime
                                    ).format("YYYY-MM-DD");
                                    if (
                                        new Date(orderDate) >=
                                            new Date(s_date) &&
                                        new Date(orderDate) <= new Date(e_date)
                                    ) {
                                        this.tiktokAdsSpend += Math.abs(
                                            parseFloat(transaction.stat_cost)
                                        );
                                    }
                                }
                            });
                        }

                        updateAdData(
                            this.data,
                            "TIKTOK",
                            displayCurrency(this.tiktokAdsSpend)
                        );
                    } catch (err) {
                        updateAdData(this.data, "TIKTOK", displayCurrency(0));
                    }
                });
            } catch (err) {
                updateAdData(this.data, "TIKTOK", displayCurrency(0));
            }
        },
        async getStripeTransactions() {
            setLoadingSingle(this.data, MERCHANT_FEE);
            setTimeout(() => {
                this.stripeLoadingStatus = true;
                const stripeObj = this.data.filter(
                    d => d.title === MERCHANT_FEE
                );
                stripeObj.showicon = true;
            }, 1500);
            this.stripeFeeTotal = 0;
            if (this.hasStripeAccount) {
                try {
                    const result = await axios.get(
                        "stripeconnect-merchantfee",
                        {
                            params: {
                                s_date: moment(this.startDate).format(
                                    "YYYY-MM-DD"
                                ),
                                e_date: moment(this.endDate).format(
                                    "YYYY-MM-DD"
                                )
                            }
                        }
                    );

                    const stripeTransactions = result.data;

                    if (stripeTransactions !== undefined) {
                        eventBus.$emit(
                            "stripeTransactionEvent",
                            stripeTransactions
                        );
                        stripeTransactions.forEach(sTransaction => {
                            this.stripeFeeTotal += parseFloat(sTransaction.fee);
                        });
                    }

                    updateDataMerchantFee(
                        this.data,
                        MERCHANT_FEE,
                        displayCurrency(this.totalMerchantFees)
                    );
                } catch (err) {
                    updateDataMerchantFee(
                        this.data,
                        MERCHANT_FEE,
                        displayCurrency(this.totalMerchantFees)
                    );
                }
            } else {
                this.stripeFeeTotal = 0;
                updateDataMerchantFee(
                    this.data,
                    MERCHANT_FEE,
                    displayCurrency(this.totalMerchantFees)
                );
            }
        },

        async getSnapchatAdSpend(s_date, e_date) {
            try {
                this.snapchatAdsSpend = 0;
                const dates = getDatesBetweenDatesStandard(s_date, e_date);

                dates.forEach(async date => {
                    const result = await axios.get("snapchat-adspend", {
                        params: {
                            s_date: date[0],
                            e_date: moment(date[1])
                                .add("1", "days")
                                .format("YYYY-MM-DD")
                        }
                    });
                    const snapchatStats = _.flatten(result.data);
                    if (snapchatStats.length > 0) {
                        eventBus.$emit(
                            "snapchatTransactionEvent",
                            snapchatStats
                        );
                        snapchatStats.forEach(stats => {
                            this.snapchatAdsSpend += parseFloat(
                                stats.stats.spend / 1000000
                            );
                        });
                        updateAdData(
                            this.data,
                            "SNAPCHAT",
                            displayCurrency(this.snapchatAdsSpend)
                        );
                    } else {
                        updateAdData(this.data, "SNAPCHAT", displayCurrency(0));
                    }
                });
            } catch (err) {
                console.log(err);
                updateAdData(this.data, "SNAPCHAT", displayCurrency(0));
            }
            // return updateAdData(this.data, "SNAPCHAT", displayCurrency(0));
        },
        async getFacebookAdSpend(s_date, e_date) {
            try {
                this.facebookAdsSpend = 0;
                const result = await axios.get("getfacebookadsdata", {
                    params: {
                        s_date: moment(s_date).format("YYYY-MM-DD"),
                        e_date: moment(e_date).format("YYYY-MM-DD")
                    }
                });
                const facebookStats = result.data;

                eventBus.$emit("facebookTransactionEvent", facebookStats);
                facebookStats.forEach(stats => {
                    this.facebookAdsSpend += parseFloat(stats.spend);
                });
                updateAdData(
                    this.data,
                    "FACEBOOK",
                    displayCurrency(this.facebookAdsSpend)
                );
            } catch (err) {
                console.log(err);
            }
        },

        async getGoogleAdSpend(s_date, e_date) {
            try {
                this.googleAdsSpend = 0;
                const result = await axios.get("google-adspend", {
                    params: {
                        s_date: moment(s_date).format("YYYY-MM-DD"),
                        e_date: moment(e_date).format("YYYY-MM-DD")
                    }
                });
                const googleStats = result.data;
                const flatGoogleStats = _.flatten(googleStats);
                if (flatGoogleStats.length > 0) {
                    eventBus.$emit("googleTransactionEvent", flatGoogleStats);
                    flatGoogleStats.forEach(stats => {
                        this.googleAdsSpend += parseFloat(
                            stats.metrics.costMicros / 1000000
                        );
                    });
                    updateAdData(
                        this.data,
                        "GOOGLE",
                        displayCurrency(this.googleAdsSpend)
                    );
                } else {
                    updateAdData(this.data, "GOOGLE", displayCurrency(0));
                }
            } catch (err) {
                console.log(err);
            }
        },
        checkAndShowAdAccountsData(s_date, e_date) {
            this.hasTiktokAccount
                ? this.getTiktokAdSpend(s_date, e_date)
                : updateAdData(this.data, "TIKTOK", "-");
            this.hasSnapchatAccount
                ? this.getSnapchatAdSpend(s_date, e_date)
                : updateAdData(this.data, "SNAPCHAT", "-");
            this.hasFacebookAccount
                ? this.getFacebookAdSpend(s_date, e_date)
                : updateAdData(this.data, "FACEBOOK", "-");
            this.hasGoogleAccount
                ? this.getGoogleAdSpend(s_date, e_date)
                : updateAdData(this.data, "GOOGLE", "-");
        }
    }
};
</script>
