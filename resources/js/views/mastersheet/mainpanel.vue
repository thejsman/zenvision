<script>
import Stat from "../../components/widgets/stat-mastersheet";
import Transactions from "./transaction-section";
import { eventBus } from "../../app";
import _ from "lodash";
import moment from "moment";
import {
  YESTERDAYS_NET_EQUITY_FLUCTUATION,
  YESTERDAYS_PROFIT_LOSS,
  OTHER_EXPENSES,
} from "../../constants";
import {
  updateData,
  displayCurrency,
  updateGraphData,
  setLoading,
} from "../../utils";
import { mapState, mapGetters, mapActions } from "vuex";

export default {
  components: { Stat, Transactions },
  data() {
    return {
      statData: [
        {
          icon: "bx bx-copy-alt",
          title: YESTERDAYS_NET_EQUITY_FLUCTUATION,
          value: "$0",
          tooltip:
            "This balance represents the total fluctuation in Net Equity between Yesterday and the day before.",
          loading: true,
          showGraph: true,
        },
        {
          icon: "bx bx-archive-in",
          title: YESTERDAYS_PROFIT_LOSS,
          tooltip:
            "This represents yesterday's total Profit or Loss for the store(s) that you have connected to Zenvision.",
          value: "$0",
          loading: true,
          showGraph: true,
          series: [
            {
              name: "Profit",
              data: [0, 0, 0, 0, 0, 0, 0],
            },
          ],
        },
        {
          icon: "bx bx-purchase-tag-alt",
          title: OTHER_EXPENSES,
          tooltip:
            "This balance represents difference between yesterday’s profit/loss and yesterday's fluctuation in net equity. The purpose of this balance is to understand the cash flow differences between your profits/losses and the business's net worth.  PLEASE NOTE that the other expenses section below represents our suggestions for what transactions make up this total difference.",
          value: "$0",
          loading: true,
          showGraph: true,
        },
      ],
      yesterDaysNetEquityTotal: 0,
    };
  },
  created() {
    eventBus.$on("toggleShopifyStore", () => {
      setLoading(this.statData);
      this.getProfitLoss();
    });
    eventBus.$on("netEquityTotal", (value) => {
      this.yesterDaysNetEquityTotal = value;
      updateData(
        this.statData,
        YESTERDAYS_NET_EQUITY_FLUCTUATION,
        displayCurrency(value)
      );
    });
    this.getProfitLoss();
  },
  props: {
    orders: {
      type: Array,
      default: () => [],
    },
  },
  computed: {
    yesterday() {
      const today = new Date();
      const previous_day = new Date(today);
      return moment(previous_day.setDate(previous_day.getDate() - 1)).format(
        "MM-DD-YYYY"
      );
    },
    dayBeforeYesterday() {
      const today = new Date();
      const previous_day = new Date(today);
      return moment(previous_day.setDate(previous_day.getDate() - 2)).format(
        "MM-DD-YYYY"
      );
    },
    lastSevenDaysArray() {
      const today = new Date();
      let weeksArray = [];
      for (let i = 1; i <= 7; i++) {
        weeksArray.unshift(
          moment(new Date(today).setDate(new Date(today).getDate() - i)).format(
            "MM-DD-YYYY"
          )
        );
      }
      return weeksArray;
    },
    yesterdayProfitLoss() {
      let yesterdaysOrder = 0;

      this.orders.forEach((order) => {
        if (
          moment(order.created_on_shopify).format("MM-DD-YYYY") ===
          this.yesterday
        ) {
          yesterdaysOrder +=
            order.total_price - order.cogs - order.total_discounts;
        }
      });

      return parseFloat(yesterdaysOrder);
    },

    profitLossGraphData() {
      return this.lastSevenDaysArray.map((day) => {
        return parseFloat(
          _.sumBy(this.orders, (order) => {
            if (moment(order.created_on_shopify).format("MM-DD-YYYY") === day) {
              return parseFloat(
                order.total_price - order.cogs - order.total_discounts
              );
            } else {
              return 0;
            }
          })
        ).toFixed(2);
      });
    },
  },
  methods: {
    async getProfitLoss() {
      setTimeout(() => {
        updateGraphData(
          this.statData,
          YESTERDAYS_PROFIT_LOSS,
          displayCurrency(this.yesterdayProfitLoss),
          this.profitLossGraphData
        );

        updateData(
          this.statData,
          OTHER_EXPENSES,
          displayCurrency(
            this.yesterDaysNetEquityTotal - this.yesterdayProfitLoss
          )
        );
      }, 2000);
    },
  },
};
</script>

<template>
  <div class="sheet-rightbar mx-4">
    <h4>Master Cash flow reconsiliation</h4>

    <div class="row justify-content-md-between align-items-md-center">
      <!-- end col-->

      <div v-for="stat of statData" :key="stat.icon" class="col-md-4 my-2">
        <Stat
          :icon="stat.icon"
          :title="stat.title"
          :value="stat.value"
          :tooltip="stat.tooltip"
          :loading="stat.loading"
          :showGraph="stat.showGraph"
          :series="stat.series"
        />
      </div>
      <!-- end col-->
    </div>
    <Transactions />
  </div>
</template>
<style lang="scss">
.transaction_section {
  background: #2a3043;
  height: 400px;
  border-radius: 10px;
}
@media (max-width: 767px) {
  .sheet-rightbar {
    margin: 0;
  }
}
</style>
