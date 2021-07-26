<template>
    <div class="row">
        <div class="col-xl-12 mt-4">
            <h3>Profit Analysis</h3>
        </div>
        <div class="card w-100 ml-2 mr-1">
            <div class="card-body">
                <div class="d-flex justify-content-end mt-3 mb-4 legends">
                    <i class="profit-circle"></i>
                    <span class="mr-3">Profit</span>
                    <i class="loss-circle"></i
                    ><span class="mr-3 pr-3">Loss</span>
                </div>
                <b-skeleton
                    v-if="!showGraph"
                    animation="wave"
                    width="100%"
                    height="297px"
                ></b-skeleton>
                <div v-else>
                    <highcharts
                        :options="chartOptions"
                        ref="lineCharts"
                    ></highcharts>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { Chart } from "highcharts-vue";
import _ from "lodash";
import moment from "moment";
import { eventBus } from "../../../app";
import { displayCurrency } from "../../../utils";
import { mapGetters } from "vuex";

export default {
    components: { highcharts: Chart },
    data() {
        return {
            chartOptions: {
                chart: {
                    backgroundColor: "#2a3042",
                    polar: false,
                    type: "line",
                    height: 300
                },
                title: {
                    text: ""
                },
                legend: {
                    enabled: false
                },
                xAxis: {
                    type: "datetime",
                    dateTimeLabelFormats: {
                        week: "%e' %b"
                    },
                    tickInterval: 24 * 3600 * 1000 * 7,
                    labels: {
                        rotation: -45
                    }
                },

                yAxis: {
                    gridLineColor: "#32394e",

                    minPadding: 0,
                    startOnTick: true,
                    minRange: 100,

                    title: {
                        text: "Profit"
                    },
                    labels: {
                        style: {
                            color: "#FFFFFF"
                        }
                    }
                },
                series: [
                    {
                        name: "Profit",
                        color: "#34c38f",
                        negativeColor: "#FF0000",
                        data: [],
                        type: "line"
                    }
                ],

                tooltip: {
                    formatter: function() {
                        var formatStr =
                            "<b class='tooltip-title'>Profit Analysis</b><hr /><br/>";

                        for (var i = 0; i < this.points.length; i++) {
                            var point = this.points[i];

                            formatStr +=
                                "<b>" +
                                moment(point.x).format("LL") +
                                "<br /><b>" +
                                (point.y >= 0 ? "Profit" : "Loss") +
                                ": " +
                                displayCurrency(point.y) +
                                "</b>";
                        }
                        return formatStr;
                    },
                    shared: true,
                    opacity: 0.7,
                    borderWidth: 1
                }
            },

            showGraph: false,
            stripeTransactionArray: [],
            stripeTransactionStatus: false,

            facebookTransactionArray: [],
            facebookTransationStatus: false,

            snapchatTransactionArray: [],
            snapchatTransationStatus: false,

            googleTransactionArray: [],
            googleTransationStatus: false,

            tiktokTransactionArray: [],
            tiktokTransationStatus: false,

            stripeChargebackArray2: [],
            stripeChargebackStatus: false,

            subscriptionDataStatus: false,
            subscriptionDataArray: [],

            chartSeries: []
        };
    },
    computed: {
        ...mapGetters([
            "startDateS",
            "endDateS",
            "stripeChargebackArray",
            "shopifyOrders"
        ])
    },
    created() {
        //Stripe
        eventBus.$on("stripeTransactionEvent", async stripeData => {
            if (stripeData.length > 0) {
                this.stripeTransationStatus = true;
                this.stripeTransactionArray = stripeData;
                this.assignData();
            } else {
                this.stripeTransationStatus = false;
                this.assignData();
            }
        });
        //Facebook
        eventBus.$on("facebookTransactionEvent", facebookData => {
            if (facebookData.length > 0) {
                this.facebookTransationStatus = true;
                this.facebookTransactionArray = facebookData;
                this.assignData();
            } else {
                this.facebookTransationStatus = false;
                this.assignData();
            }
        });
        //Snapchat
        eventBus.$on("snapchatTransactionEvent", snapchatData => {
            if (snapchatData.length > 0) {
                this.snapchatTransationStatus = true;
                this.snapchatTransactionArray = snapchatData;
                this.assignData();
            } else {
                this.snapchatTransationStatus = false;
                this.assignData();
            }
        });

        //Google
        eventBus.$on("googleTransactionEvent", googleData => {
            if (googleData.length > 0) {
                this.googleTransationStatus = true;
                this.googleTransactionArray = googleData;
                this.assignData();
            } else {
                this.googleTransationStatus = false;
                this.assignData();
            }
        });

        //Tiktok
        eventBus.$on("tiktokTransactionEvent", tiktokData => {
            if (tiktokData.length > 0) {
                this.tiktokTransationStatus = true;
                this.tiktokTransactionArray = tiktokData;
                this.assignData();
            } else {
                this.tiktokTransationStatus = false;
                this.assignData();
            }
        });

        //StripeChargeback
        // eventBus.$on("stripeChargebackEvent", stripeChargeback => {
        //     if (stripeChargeback.length > 0) {
        //         this.stripeChargebackStatus = true;
        //         this.stripeChargebackArray = stripeChargeback;
        //         this.assignData();
        //     } else {
        //         this.stripeChargebackStatus = false;
        //         this.assignData();
        //     }
        // });

        //Subscription
        eventBus.$on("subscriptionDataEvent", subscriptionData => {
            if (subscriptionData.length > 0) {
                this.subscriptionDataStatus = true;
                this.subscriptionDataArray = subscriptionData;
                this.assignData();
            } else {
                this.subscriptionDataStatus = false;
                this.assignData();
            }
        });
    },
    methods: {
        assignData(orders) {
            this.showGraph = false;
            const dates = this.getDaysBetweenDates(
                moment(this.startDateS),
                moment(this.endDateS)
            );

            let dayArray = [];
            dates.map(day => {
                const sum = _.sumBy(this.shopifyOrders, order => {
                    if (
                        moment(order.created_on_shopify).format(
                            "YYYY-MM-DD"
                        ) === day
                    ) {
                        return parseFloat(order.total_price - order.total_cost);
                    } else {
                        return 0;
                    }
                });
                dayArray.push([Date.parse(day), sum]);
            });
            this.chartSeries = dayArray;
            if (this.stripeTransationStatus) {
                this.renderStripeData();
            }

            if (this.facebookTransationStatus) {
                this.renderFacebookData();
            }
            if (this.snapchatTransationStatus) {
                this.renderSnapchatData();
            }
            if (this.googleTransationStatus) {
                this.renderGoogleData();
            }
            if (this.tiktokTransationStatus) {
                this.renderTiktokData();
            }

            if (this.stripeChargebackStatus) {
                this.renderStripeChargeback();
            }

            if (this.subscriptionDataStatus) {
                this.renderSubscriptionData();
            }

            const profitSeries = this.chartSeries.map(cs =>
                parseFloat(cs[1]).toFixed(2)
            );

            eventBus.$emit("profitSeriesData", profitSeries);
            this.updateSeries();
        },

        getDaysBetweenDates(startDate, endDate) {
            let now = startDate.clone(),
                dates = [];

            while (now.isSameOrBefore(endDate)) {
                dates.push(now.format("YYYY-MM-DD"));
                now.add(1, "days");
            }
            return dates;
        },
        renderStripeData() {
            this.chartSeries.forEach(cs => {
                cs[1] -= _.sumBy(this.stripeTransactionArray, stripeTrans =>
                    moment(stripeTrans.created).format("YYYY-MM-DD") ===
                    moment(cs[0]).format("YYYY-MM-DD")
                        ? parseFloat(stripeTrans.fee)
                        : 0
                );
            });
            console.log("chart series: ", this.chartSeries);
            this.updateSeries();
        },
        renderFacebookData() {
            this.chartSeries.forEach(cs => {
                cs[1] -= _.sumBy(this.facebookTransactionArray, facebookTrans =>
                    moment(facebookTrans.date_start).format("YYYY-MM-DD") ===
                    moment(cs[0]).format("YYYY-MM-DD")
                        ? parseFloat(facebookTrans.spend)
                        : 0
                );
            });

            this.updateSeries();
        },
        renderSnapchatData() {
            this.chartSeries.forEach(cs => {
                cs[1] -= _.sumBy(this.snapchatTransactionArray, snapchatTrans =>
                    moment(snapchatTrans.start_time).format("YYYY-MM-DD") ===
                    moment(cs[0]).format("YYYY-MM-DD")
                        ? parseFloat(snapchatTrans.stats.spend / 1000000)
                        : 0
                );
            });

            this.updateSeries();
        },
        renderGoogleData() {
            this.chartSeries.forEach(cs => {
                cs[1] -= _.sumBy(this.googleTransactionArray, googleTrans =>
                    moment(googleTrans.segments.date).format("YYYY-MM-DD") ===
                    moment(cs[0]).format("YYYY-MM-DD")
                        ? parseFloat(googleTrans.metrics.costMicros / 1000000)
                        : 0
                );
            });

            this.updateSeries();
        },

        renderTiktokData() {
            this.chartSeries.forEach(cs => {
                cs[1] -= _.sumBy(this.tiktokTransactionArray, tiktokTrans => {
                    return moment(tiktokTrans.stat_datetime).format(
                        "YYYY-MM-DD"
                    ) === moment(cs[0]).format("YYYY-MM-DD")
                        ? parseFloat(tiktokTrans.stat_cost)
                        : 0;
                });
            });

            this.updateSeries();
        },

        renderStripeChargeback() {
            this.chartSeries.forEach(cs => {
                cs[1] -= _.sumBy(
                    this.stripeChargebackArray2,
                    stripeChargeback => {
                        return moment(stripeChargeback.created * 1000).format(
                            "YYYY-MM-DD"
                        ) === moment(cs[0]).format("YYYY-MM-DD")
                            ? parseFloat(stripeChargeback.amount / 100)
                            : 0;
                    }
                );
            });

            this.updateSeries();
        },

        renderSubscriptionData() {
            this.chartSeries.forEach(cs => {
                cs[1] -= _.sumBy(this.subscriptionDataArray, sub => {
                    return moment(sub.sub_date).format("YYYY-MM-DD") ===
                        moment(cs[0]).format("YYYY-MM-DD")
                        ? parseFloat(sub.amount)
                        : 0;
                });
            });

            this.updateSeries();
        },

        updateSeries() {
            this.showGraph = false;
            setTimeout(() => {
                this.chartOptions.series[0].data = this.chartSeries;
                this.showGraph = true;
            }, 3000);
        }
    },
    mounted() {},

    watch: {
        shopifyOrders() {
            this.assignData();
        },
        stripeChargebackArray(stripeChargeback, oldVal) {
            console.log("we have received chargeback event");
            if (stripeChargeback.length > 0) {
                this.stripeChargebackStatus = true;
                this.stripeChargebackArray2 = stripeChargeback;
                this.assignData();
            } else {
                this.stripeChargebackStatus = false;
                this.assignData();
            }
        }
    }
};
</script>

<style lang="scss">
.highcharts-credits {
    color: #2a3042 !important;
    fill: #2a3042 !important;
}
.legends {
    height: 8px;
}
.profit-circle {
    background: #3ec38f;
    border-radius: 50%;
    width: 12px;
    height: 12px;
    margin-right: 6px;
    margin-top: 3px;
}
.loss-circle {
    background: #df1e1e;
    border-radius: 50%;
    width: 12px;
    height: 12px;
    margin-right: 6px;
    margin-top: 3px;
}
.tooltip-title {
    text-decoration: underline;
}
.highcharts-tooltip {
}
</style>
