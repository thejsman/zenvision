<template>
  <div class="row">
    <div class="col-xl-8 mt-4">
      <h3>Profit</h3>
      <div class="d-flex flex-row card profit-card">
        <div class="card-body">
          <div class="d-flex flex-row justify-content-between">
            <div class="media">
              <div class="media-body">
                <p class="text-muted font-weight-medium">Profit</p>
                <h4 class="mb-0">{{ totalProfit }}</h4>
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
import { eventBus } from "../../../app";
import { displayCurrency } from "../../../utils";

export default {
  components: { Stat },
  data() {
    return {
      data: [],
      profit: 0,
      totalCost: 0,
      totalRevenue: 0,

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
  computed: {
    totalProfit() {
      eventBus.$emit(
        "totalProfitValue",
        parseFloat(this.totalRevenue - this.totalCost)
      );

      return displayCurrency(this.totalRevenue - this.totalCost);
    },
    profitSeries() {
      if (this.profitData.length > 0) {
        const profitSeriesData = _(this.profitData)
          .groupBy("created_on_shopify")
          .map((objs, key) => _.sumBy(objs, "total_price"))
          .value();

        return profitSeriesData.map((ps) => parseFloat(ps).toFixed(2));
      } else {
        return [0, 0, 0, 0, 0];
      }
    },
  },
  created() {
    eventBus.$on("totalCostValue", (value) => (this.totalCost = value));
    eventBus.$on("totalRevenueValue", (value) => (this.totalRevenue = value));
  },
  props: {
    profitData: {
      type: Array,
      default: () => [],
    },
  },
  watch: {
    profitData(value, newValue) {
      this.ProfitlineChart = {
        ...this.ProfitlineChart,
        series: [
          {
            name: "Profit",
            data: this.profitSeries,
          },
        ],
      };
    },
  },
};
</script>
