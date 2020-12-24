<script>
import Stat from "../../components/widgets/stat-mastersheet";
export default {
  components: { Stat },
  data() {
    return {
      statData: [
        {
          icon: "bx bx-copy-alt",
          title: "Yesterday Net Equity Fluctuation",
          value: "$0",
          tooltip:
            "This balance represents the total fluctuation in Net Equity between Yesterday and the day before.",
          loading: false,
        },
        {
          icon: "bx bx-archive-in",
          title: "Yesterday's Profit/Loss",
          tooltip:
            "This represents yesterday's total Profit or Loss for the store(s) that you have connected to Zenvision.",
          value: "$0",
          loading: false,
        },
        {
          icon: "bx bx-purchase-tag-alt",
          title: "Other expenses",
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
        />
      </div>
      <!-- end col-->
    </div>
    <div class="mt-4">
      <h4>
        Transactions
        <i
          class="fas fas fa-info-circle"
          v-b-tooltip.hover="
            'This section represents all transactions that have occurred in the accounts that are linked to Zenvision.'
          "
        ></i>
      </h4>

      <div class="col-md-12 p-0">
        <div class="transaction_section w-100"></div>
      </div>
    </div>
  </div>
</template>
<style lang="scss">
/* ==============
  Email
===================*/

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
