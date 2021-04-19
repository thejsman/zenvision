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
                :showCogsWarning="cost.showCogsWarning"
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
export default {
    components: { Stat, SubscriptionCost, CogsModal },
    data() {
        return {
            subscriptionData: 0,
            cogsTotal: 0,
            chargebackTotal: 0,
            merchantFees: 0,
            tiktokAdsSpend: 0,
            data: [
                {
                    id: 1,
                    title: COGS_TOTAL,
                    value: `0`,
                    loading: true,
                    onClick: this.handleCogsClick,
                    showCogsWarning: true
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
                    // iconName: "data-warning.svg",
                    toolTip:
                        "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
                },
                {
                    id: 6,
                    title: AD_SPEND_FACEBOOK,
                    value: `0`,
                    loading: true,
                    iconName: "facebook-icon.svg"
                },
                {
                    id: 7,
                    title: AD_SPEND_GOOGLE,
                    value: `0`,
                    loading: true,
                    iconName: "google-icon.svg"
                },
                {
                    id: 8,
                    title: AD_SPEND_SNAPCHAT,
                    value: `0`,
                    loading: true,
                    iconName: "snapchat-icon.svg"
                },
                {
                    id: 9,
                    title: AD_SPEND_TIKTOK,
                    value: `0`,
                    loading: true,
                    iconName: "tiktok-icon.svg"
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
            totalDiscount: 0
        };
    },
    computed: {
        totalCost() {
            const totalCost = parseFloat(
                this.merchantFees +
                    this.refundTotal +
                    this.totalDiscount +
                    this.subscriptionData +
                    this.cogsTotal +
                    this.chargebackTotal +
                    this.tiktokAdsSpend
            );

            eventBus.$emit("totalCostValue", totalCost);
            return displayCurrency(totalCost);
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
            setLoading(this.data);

            this.getMerchantfeesTotal(s_date, e_date);
            this.getTiktokAdSpend(s_date, e_date);
        });
    },
    methods: {
        assignData(refundTotal, orders) {
            this.getCogsData(orders);
            const discounts = _.sumBy(orders, order =>
                parseFloat(order.total_discounts)
            );
            this.totalDiscount = discounts;
            updateData(this.data, DISCOUNTS_TOTAL, displayCurrency(discounts));
            updateData(this.data, REFUNDS_TOTAL, displayCurrency(refundTotal));
            updateAdData(this.data, "FACEBOOK", displayCurrency(0));
            updateAdData(this.data, "GOOGLE", displayCurrency(0));
            updateAdData(this.data, "SNAPCHAT", displayCurrency(0));
            //   updateData(this.data, MERCHANT_FEE, displayCurrency(this.merchantFees));
            this.getSubscriptionData();
            this.getChargebackTotal();
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

        async getCogsData(orders) {
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
                    displayCurrency(cogs),
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
                    d.showCogsWarning = showicon;
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
        async getChargebackTotal(s_date, e_date) {
            try {
                const result = await axios.get("getshopifydisputes");
                const { disputes } = result.data;
                let totalChargeback = 0;
                if (disputes.length > 1) {
                    data.forEach(dispute => {
                        totalChargeback += parseFloat(dispute.amount);
                    });
                }

                const stripeResult = await axios.get(
                    "stripeconnect-chargeback"
                );

                let stripeChargebacks = stripeResult.data;
                console.log({ stripeChargebacks });
                stripeChargebacks.forEach(sc => {
                    const oDate = new Date(sc.created * 1000);

                    if (
                        new Date(oDate) >= new Date(s_date) &&
                        new Date(oDate) <= new Date(e_date)
                    ) {
                        console.log(oDate);
                        if (
                            sc.status === "charge_refunded" ||
                            sc.status === "lost"
                        ) {
                            totalChargeback += parseFloat(sc.amount / 100);
                        }
                    }
                });

                this.chargebackTotal = totalChargeback;
                updateData(
                    this.data,
                    CHARGEBACKS_TOTAL,
                    displayCurrency(totalChargeback)
                );
            } catch (err) {
                this.chargebackTotal = 0;
                updateData(this.data, CHARGEBACKS_TOTAL, displayCurrency(0));
            }
        },
        async getMerchantfeesTotal(s_date, e_date) {
            let total = 0;
            this.merchantFees = 0;
            //Stripe

            //Paypal
            const paypalTotal = await this.getPaypalTransactionsTotal(
                s_date,
                e_date
            );

            this.merchantFees = total + paypalTotal;
            // updateData(this.data, MERCHANT_FEE, displayCurrency(total));

            //   eventBus.$emit("merchantFeeUpdated", this.merchantFeesTotal);
            await this.getStripeTransactions(s_date, e_date);
            updateData(
                this.data,
                MERCHANT_FEE,
                displayCurrency(this.merchantFees)
            );
        },
        async getPaypalTransactionsTotal(s_date, e_date) {
            let total = 0;
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
                                total += Math.abs(
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
            return total;
        },
        async getTiktokAdSpend(s_date, e_date) {
            this.tiktokAdsSpend = 0;
            const dates = getDatesBetweenDates(s_date, e_date);
            let tiktokTotal = 0;
            dates.forEach(async date => {
                const tiktokResult = await axios.get("tiktokaccount-adspend", {
                    params: {
                        s_date: date[0].substring(0, 10),
                        e_date: date[1].substring(0, 10)
                    }
                });
                const tiktokTransactions = tiktokResult.data;

                if (tiktokTransactions.length > 0) {
                    tiktokTransactions.map(transaction => {
                        if (transaction.hasOwnProperty("stat_cost")) {
                            const orderDate = moment(
                                transaction.stat_datetime
                            ).format("YYYY-MM-DD");
                            if (
                                new Date(orderDate) >= new Date(s_date) &&
                                new Date(orderDate) <= new Date(e_date)
                            ) {
                                this.tiktokAdsSpend += Math.abs(
                                    parseFloat(transaction.stat_cost)
                                );
                            }
                        }
                    });
                }

                // this.tiktokAdsSpend += tiktokTotal;
                updateAdData(
                    this.data,
                    "TIKTOK",
                    displayCurrency(this.tiktokAdsSpend)
                );
            });
        },
        async getStripeTransactions(s_date, e_date) {
            try {
                const result = await axios.get("stripeconnect-merchantfee", {
                    params: {
                        s_date: moment(s_date).format("YYYY-MM-DD"),
                        e_date: moment(e_date).format("YYYY-MM-DD")
                    }
                });

                const stripeTransactions = result.data;

                if (stripeTransactions !== undefined) {
                    stripeTransactions.forEach(sTransaction => {
                        const orderDate = moment(
                            sTransaction.available_on
                        ).format("MM-DD-YYYY");

                        if (
                            new Date(orderDate) >= new Date(s_date) &&
                            new Date(orderDate) <= new Date(e_date)
                        ) {
                            this.merchantFees += parseFloat(sTransaction.fee);
                        }
                    });

                    // updateDataMerchantFee(
                    //     this.data,
                    //     MERCHANT_FEE,
                    //     displayCurrency(this.merchantFees)
                    // );
                }
            } catch (err) {
                return 0;
            }
        }
    }
};
</script>
