<template>
  <div class="row">
    <div class="col-xl-8 mt-4">
      <h3>Profit</h3>
      <div
        class="d-flex flex-row card"
        :style="{ background: '#2f3863', borderRadius: '5px' }"
      >
        <div class="card-body">
          <div class="d-flex flex-row justify-content-between">
            <div class="media">
              <div class="media-body">
                <p class="text-muted font-weight-medium">Profit</p>
                <h4 class="mb-0">${{ profit }}</h4>
              </div>
            </div>
            <div class="align-self-center ml-3 mr-1">
              <apexchart
                class="apex-charts"
                :height="50"
                :width="120"
                :options="ProfitlineChart.chartOptions"
                :series="ProfitlineChart.series"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Stat from "../../widgets/stat";
import _ from "lodash";
import moment from "moment";

export default {
  components: { Stat },
  data() {
    return {
      data: [],
      profit: 0,
      ProfitlineChart: {
        chartOptions: {
          chart: {
            type: "area",
            height: 40,
            sparkline: {
              enabled: true,
            },
          },
          stroke: {
            curve: "smooth",
            width: 2,
          },
          colors: ["#f1b44c"],
          fill: {
            type: "gradient",
            gradient: {
              shadeIntensity: 1,
              inverseColors: false,
              opacityFrom: 0.45,
              opacityTo: 0.05,
              stops: [25, 100, 100, 100],
            },
          },
          tooltip: {
            fixed: {
              enabled: false,
            },
            x: {
              show: false,
            },
            marker: {
              show: false,
            },
          },
        },
      },
    };
  },
  props: {
    profitData: {
      type: Array,
      default: () => [],
    },
  },
  watch: {
    profitData(value, newValue) {
      this.assignData(this.profitData);
    },
  },
  methods: {
    assignData(orders) {
      const revenue = _.sumBy(orders, (order) => parseFloat(order.total_price));
      const cogs = _.sumBy(orders, (order) => parseFloat(order.cogs));
      const profit = parseFloat(revenue - cogs).toFixed(2);

      // Create an array of last 11 days
      const dates = [...Array(11)].map((_, i) => {
        const d = new Date();
        d.setDate(d.getDate() - i);
        return moment(d).format("DD-MM-YYYY");
      });

      let apexData = [];
      //iterate over each date & calculate profit
      dates.forEach((date) => {
        const sum_profit = _.sumBy(orders, (order) => {
          if (moment(order.created_on_shopify).format("DD-MM-YYYY") == date) {
            const profit =
              parseFloat(order.total_price).toFixed(2) - parseFloat(order.cogs);

            return profit;
          } else {
            return 0;
          }
        });
        apexData.push(sum_profit);
      });

      const profit_of_elevendays = _.sum(apexData);

      this.profit = profit_of_elevendays;
      // parse the array
      const sumArray = apexData.map((item) =>
        item == 0 ? 0 : parseFloat(item).toFixed(2)
      );
      //assign data to the chart
      this.ProfitlineChart = {
        ...this.ProfitlineChart,
        series: [
          {
            name: "Profit",
            data: sumArray,
          },
        ],
      };
    },
  },
};
</script>


