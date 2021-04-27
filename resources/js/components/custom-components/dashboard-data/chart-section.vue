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
                        :updateArgs="[true, false]"
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
            updateArgs: [true, true, true],
            showGraph: false,
            stripeTransactionArray: [],
            stripeTransationStatus: false,
            chartSeries: []
        };
    },
    created() {
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
    },
    methods: {
        assignData(orders) {
            this.showGraph = false;
            const dates = this.getDaysBetweenDates(
                this.ChartdateRange[0],
                this.ChartdateRange[1]
            );

            let dayArray = [];
            dates.map(day => {
                const sum = _.sumBy(this.chartData, order => {
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
                // dayArray.push([day, sum]);
            });
            this.chartSeries = dayArray;
            if (this.stripeTransationStatus) {
                this.renderStripeData();
            }
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
                    moment(stripeTrans.available_on).format("YYYY-MM-DD") ===
                    moment(cs[0]).format("YYYY-MM-DD")
                        ? parseFloat(stripeTrans.fee)
                        : 0
                );
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
    props: {
        chartData: {
            type: Array,
            default: () => []
        },
        ChartdateRange: {
            type: Array,
            default: () => []
        },
        fbSpend: {
            type: Array,
            default: () => []
        },
        googleData: {
            type: Array,
            default: () => []
        }
    },
    watch: {
        chartData(value, newValue) {
            this.assignData(this.chartData);
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
