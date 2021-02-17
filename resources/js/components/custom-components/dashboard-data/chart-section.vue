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
          <i class="loss-circle"></i><span class="mr-3 pr-3">Loss</span>
        </div>

        <chartist
          ratio="ct-chart"
          :data="polarBarChart.data"
          :options="polarBarChart.options"
          type="Bar"
          :event-handlers="polarBarChart.chartHandlers"
        ></chartist>
      </div>
    </div>
  </div>
</template>
<script>
import Stat from "../../widgets/stat";

import moment from "moment";
import { eventBus } from "../../../app";

export default {
  components: { Stat },
  data() {
    return {
      compiledOrders: [],
      dateDifference: 0,
      subscriptionArray: [],
      polarBarChart: {
        data: {
          labels: [],
          series: [[]],
        },

        options: {
          height: 300,
          legend: {
            position: "bottom",
          },
          plugins: [this.$chartist.plugins.tooltip({})],
        },

        chartHandlers: [
          {
            event: "draw",
            fn(context) {
              let red = "#ff0000";

              if (context.type === "bar") {
                context.element._node.id = "tooltip-" + context.index;

                if (context.value.y < 0) {
                  context.element.attr({
                    style: "stroke: " + red + "; fill: " + red + ";",
                  });
                }
                // console.log(context);
                context.element._node.addEventListener("mouseover", (event) => {
                  //   event.target.id = "tooltip-" + context.index;
                  //   console.log(event.target);

                  eventBus.$emit("fireMethod", context.index);
                });
              }
            },
          },
        ],
      },
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
        if (dateDiff <= 6) {
          const dates = [...Array(dateDiff + 1)].map((_, i) => {
            const d = new Date(this.ChartdateRange[0]);
            d.setDate(d.getDate() + i);
            return moment(d).format("M/D/YY");
          });
          this.polarBarChart.data.labels = dates;
          let dayArray = [];
          const data_per_day = dates.map((day) => {
            const sum = _.sumBy(this.chartData, (order) => {
              if (moment(order.created_on_shopify).format("M/D/YY") === day) {
                return parseFloat(order.total_price - order.cogs);
              } else {
                return 0;
              }
            });
            dayArray.push(sum);
          });
          const final = dayArray.map((day) => parseFloat(day).toFixed(2));
          this.polarBarChart.data.series = [final];
        } else if (dateDiff <= 120 && dateDiff >= 7) {
          var result = [];
          if (this.ChartdateRange[1].isBefore(this.ChartdateRange[0])) {
            console.log("End date must be greated than start date.");
          }
          let temp = this.ChartdateRange[0];
          while (
            moment(this.ChartdateRange[0]) <= moment(this.ChartdateRange[1])
          ) {
            result.push(this.ChartdateRange[0].format("M/D/YY"));

            this.ChartdateRange[0] = this.ChartdateRange[0].add(1, "weeks");
          }
          this.ChartdateRange[0] = temp;

          this.polarBarChart.data.labels = result;

          let weekArray = [];

          for (let [index, val] of result.entries()) {
            const sum = _.sumBy(this.chartData, (order) => {
              const orderDate = moment(order.created_on_shopify).format(
                "M/D/YY"
              );

              if (
                moment(orderDate).isBetween(
                  result[index],
                  result[index + 1],
                  undefined,
                  "[]"
                )
              ) {
                return parseFloat(order.total_price - order.cogs);
              } else {
                return 0;
              }
            });
            weekArray.push(sum);
          }
          //   const data_per_day = result.map((week) => {
          //     const sum = _.sumBy(this.chartData, (order) => {
          //       if (moment(order.created_on_shopify).format("M/D/YY") === week) {
          //         return parseFloat(order.total_price - order.cogs);
          //       } else {
          //         return 0;
          //       }
          //     });
          //     weekArray.push(sum);
          //   });
          const final = weekArray.map((day) => parseFloat(day).toFixed(2));
          this.polarBarChart.data.series = [final];
        } else {
          const months = this.getMonths();
          let monthDataArray = [];
          const data_per_month = months.map((month) => {
            const sum = _.sumBy(this.chartData, (order) => {
              let googleSum = 0;
              if (moment(order.created_on_shopify).format("MMM") === month) {
                return parseFloat(order.total_price - order.cogs);
              } else {
                return 0;
              }
            });
            monthDataArray.push(sum);
          });
          const final = monthDataArray.map((month) =>
            parseFloat(month).toFixed(2)
          );
          this.polarBarChart.data.series = [final];
        }
      }
    },
    getMonths() {
      const months = [...Array(Math.round(this.dateDifference / 30))].map(
        (_, i) => {
          const d = new Date(
            moment(this.ChartdateRange[0]).format("MM-DD-YYYY")
          );
          // const d = moment(this.ChartdateRange[0]).format("DD-MM-YYYY");

          d.setMonth(d.getMonth() + i);
          return moment(d).format("MMM");
        }
      );
      this.polarBarChart.data.labels = months;
      return months;
    },
  },
  mounted() {},
  props: {
    chartData: {
      type: Array,
      default: () => [],
    },
    ChartdateRange: {
      type: Array,
      default: () => [],
    },
    fbSpend: {
      type: Array,
      default: () => [],
    },
    googleData: {
      type: Array,
      default: () => [],
    },
  },
  watch: {
    chartData(value, newValue) {
      this.assignData(this.chartData);
    },
  },
};
</script>



<style lang="scss">
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
.chartist-tooltip {
  position: absolute;
  display: none;
  min-width: 5em;
  padding: 8px 10px;
  background: #383838;
  color: #fff;
  text-align: center;
  pointer-events: none;
  z-index: 100;
  transition: opacity 0.2s linear;
}

.chartist-tooltip:before {
  position: absolute;
  bottom: -14px;
  left: 50%;
  border: solid transparent;
  content: " ";
  height: 0;
  width: 0;
  pointer-events: none;
  border-color: rgba(251, 249, 228, 0);
  border-top-color: #383838;
  border-width: 7px;
  margin-left: -8px;
}

.chartist-tooltip.tooltip-show {
  display: inline-block !important;
}

.tooltip {
  display: block !important;
  z-index: 10000;

  .tooltip-inner {
    background: black;
    color: white;
    border-radius: 16px;
    padding: 5px 10px 4px;
  }

  .tooltip-arrow {
    width: 0;
    height: 0;
    border-style: solid;
    position: absolute;
    margin: 5px;
    border-color: black;
  }

  &[x-placement^="top"] {
    margin-bottom: 5px;

    .tooltip-arrow {
      border-width: 5px 5px 0 5px;
      border-left-color: transparent !important;
      border-right-color: transparent !important;
      border-bottom-color: transparent !important;
      bottom: -5px;
      left: calc(50% - 5px);
      margin-top: 0;
      margin-bottom: 0;
    }
  }

  &[x-placement^="bottom"] {
    margin-top: 5px;

    .tooltip-arrow {
      border-width: 0 5px 5px 5px;
      border-left-color: transparent !important;
      border-right-color: transparent !important;
      border-top-color: transparent !important;
      top: -5px;
      left: calc(50% - 5px);
      margin-top: 0;
      margin-bottom: 0;
    }
  }

  &[x-placement^="right"] {
    margin-left: 5px;

    .tooltip-arrow {
      border-width: 5px 5px 5px 0;
      border-left-color: transparent !important;
      border-top-color: transparent !important;
      border-bottom-color: transparent !important;
      left: -5px;
      top: calc(50% - 5px);
      margin-left: 0;
      margin-right: 0;
    }
  }

  &[x-placement^="left"] {
    margin-right: 5px;

    .tooltip-arrow {
      border-width: 5px 0 5px 5px;
      border-top-color: transparent !important;
      border-right-color: transparent !important;
      border-bottom-color: transparent !important;
      right: -5px;
      top: calc(50% - 5px);
      margin-left: 0;
      margin-right: 0;
    }
  }

  &[aria-hidden="true"] {
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.15s, visibility 0.15s;
  }

  &[aria-hidden="false"] {
    visibility: visible;
    opacity: 1;
    transition: opacity 0.15s;
  }
}
</style>
