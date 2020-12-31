<script>
import Stat from "../../components/widgets/stat-mastersheet";
import Transactions from "./transaction-section";
import { eventBus } from "../../app";
import {
  YESTERDAYS_NET_EQUITY_FLUCTUATION,
  YESTERDAYS_PROFIT_LOSS,
  OTHER_EXPENSES,
} from "../../constants";
import { updateData, displayCurrency } from "../../utils";

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
          loading: false,
        },
        {
          icon: "bx bx-archive-in",
          title: YESTERDAYS_PROFIT_LOSS,
          tooltip:
            "This represents yesterday's total Profit or Loss for the store(s) that you have connected to Zenvision.",
          value: "$0",
          loading: false,
          showGraph: true,
        },
        {
          icon: "bx bx-purchase-tag-alt",
          title: OTHER_EXPENSES,
          tooltip:
            "This balance represents difference between yesterday’s profit/loss and yesterday's fluctuation in net equity. The purpose of this balance is to understand the cash flow differences between your profits/losses and the business's net worth.  PLEASE NOTE that the other expenses section below represents our suggestions for what transactions make up this total difference.",
          value: "$0",
          loading: false,
        },
      ],
      debtsData: [
        {
          icon: "bx bx-copy-alt",
          title: "Credit Cart",
          value: "$0",
          loading: false,
        },
        {
          icon: "bx bx-archive-in",
          title: "Supplier Payable",
          value: "$0",
          loading: false,
        },
      ],
    };
  },
};
</script>

<template>
  <div class="email-rightbar mx-4">
    <h4>Master cash flow reconsiliation</h4>

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
  .email-rightbar {
    margin: 0;
  }
}
</style>
