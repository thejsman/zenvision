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

                <highcharts :options="chartOptions"></highcharts>
            </div>
        </div>
    </div>
</template>
<script>
import { Chart } from "highcharts-vue";

import moment from "moment";
import { eventBus } from "../../../app";
import { displayCurrency } from "../../../utils";

export default {
    components: { highcharts: Chart },
    data() {
        return {
            compiledOrders: [],
            dateDifference: 0,
            subscriptionArray: [],
            dateRangeType: "Date Range",
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

                    min: 0,
                    minPadding: 0,
                    startOnTick: true,
                    minRange: 100,
                    min: 0,
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
            }
        };
    },
    methods: {
        assignData(orders) {
            if (this.ChartdateRange.length > 0) {
                const dateDiff = this.ChartdateRange[1].diff(
                    this.ChartdateRange[0],
                    "days"
                );
                this.dateDifference = dateDiff;
                if (dateDiff <= 90) {
                    const dates = [...Array(dateDiff + 1)].map((_, i) => {
                        const d = new Date(this.ChartdateRange[0]);
                        d.setDate(d.getDate() + i);
                        return moment(d).format("M/D/YY");
                    });
                    //   this.polarBarChart.data.labels = dates;
                    // this.chartOptions.xAxis.categories = dates;
                    let dayArray = [];
                    const data_per_day = dates.map(day => {
                        const sum = _.sumBy(this.chartData, order => {
                            if (
                                moment(order.created_on_shopify).format(
                                    "M/D/YY"
                                ) === day
                            ) {
                                return parseFloat(
                                    order.total_price - order.total_cost
                                );
                            } else {
                                return 0;
                            }
                        });
                        dayArray.push(sum);
                    });
                    const final = dayArray.map(day =>
                        parseFloat(day).toFixed(2)
                    );
                    let newCombinedArray = [];
                    for (let i = 0; i < 32; i++) {
                        newCombinedArray.push([
                            Date.parse(dates[i]),
                            parseFloat(final[i])
                        ]);
                    }
                    console.log({ newCombinedArray });
                    // this.polarBarChart.data.series = [final];
                    // console.log({ dates });
                    // this.chartOptions.series[0].data = final.map(e =>
                    //     parseFloat(e)
                    // );
                    this.chartOptions.series[0].data = newCombinedArray;
                } else if (dateDiff <= 120 && dateDiff >= 8) {
                    var result = [];
                    if (
                        this.ChartdateRange[1].isBefore(this.ChartdateRange[0])
                    ) {
                        console.log(
                            "End date must be greated than start date."
                        );
                    }
                    let temp = this.ChartdateRange[0];
                    while (
                        moment(this.ChartdateRange[0]) <=
                        moment(this.ChartdateRange[1])
                    ) {
                        result.push(this.ChartdateRange[0].format("M/D/YY"));

                        this.ChartdateRange[0] = this.ChartdateRange[0].add(
                            1,
                            "weeks"
                        );
                    }
                    this.ChartdateRange[0] = temp;
                    //Week wise dates
                    //   this.polarBarChart.data.labels = result;
                    // this.chartOptions.xAxis.categories = result;

                    let weekArray = [];

                    for (let [index, val] of result.entries()) {
                        const sum = _.sumBy(this.chartData, order => {
                            const orderDate = moment(
                                order.created_on_shopify
                            ).format("M/D/YY");

                            if (
                                moment(orderDate).isBetween(
                                    result[index],
                                    result[index + 1],
                                    undefined,
                                    "[]"
                                )
                            ) {
                                return parseFloat(
                                    order.total_price - order.total_cost
                                );
                            } else {
                                return 0;
                            }
                        });
                        weekArray.push(sum);
                    }

                    const final = weekArray.map(day =>
                        parseFloat(day).toFixed(2)
                    );
                    // this.polarBarChart.data.series = [final];
                    // this.chartOptions.series[0].data = final.map(e =>
                    //     parseFloat(e)
                    // );
                    let newCombinedArray = [];
                    for (let i = 0; i < 32; i++) {
                        newCombinedArray.push([dates[i], final[i]]);
                    }
                    console.log({ newCombinedArray });
                } else {
                    const months = this.getMonths();
                    let monthDataArray = [];
                    const data_per_month = months.map(month => {
                        const sum = _.sumBy(this.chartData, order => {
                            let googleSum = 0;

                            if (
                                moment(order.created_on_shopify).format(
                                    "MMM YYYY"
                                ) === month
                            ) {
                                return parseFloat(
                                    order.total_price - order.total_cost
                                );
                            } else {
                                return 0;
                            }
                        });
                        monthDataArray.push(sum);
                    });
                    const final = monthDataArray.map(month =>
                        parseFloat(month).toFixed(2)
                    );
                    // this.polarBarChart.data.series = [final];

                    this.chartOptions.series[0].data = final.map(e =>
                        parseFloat(e)
                    );
                }
            }
        },
        getMonths() {
            const months = [...Array(Math.round(this.dateDifference / 30))].map(
                (_, i) => {
                    const d = new Date(
                        moment(this.ChartdateRange[0]).format("MM-DD-YYYY")
                    );

                    d.setMonth(d.getMonth() + i);
                    return moment(d).format("MMM YYYY");
                }
            );
            //   this.polarBarChart.data.labels = months;
            // this.chartOptions.xAxis.categories = months;
            return months;
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
