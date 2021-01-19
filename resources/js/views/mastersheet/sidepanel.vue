<script>
import Stat from "../../components/widgets/stat-mastersheet";
import { updateData, displayCurrency, setLoading } from "../../utils";
import { eventBus } from "../../app";
import _ from "lodash";
import {
  NET_EQUITY,
  TOTAL_CASH,
  TOTAL_INVENTORY,
  TOTAL_RESERVES,
  TOTAL_CREDIT_CARD,
  TOTAL_SUPPLIER_PAYABLE,
} from "../../constants";
import axios from "axios";
export default {
  components: { Stat },
  data() {
    return {
      showModal: false,
      totalCash: 0,
      totalInventory: 100,
      totalReserves: 500,
      totalCreditCard: 0,
      totalSupplierPayable: 0,
      netEquityData: [
        {
          icon: "bx bx-copy-alt",
          title: NET_EQUITY,
          value: "$0",
          tooltip:
            "This balance represents the total fluctuation in Net Equity between Yesterday and the day before.",
          loading: true,
        },
      ],
      statData: [
        {
          icon: "bx bx-copy-alt",
          title: TOTAL_CASH,
          value: "$0",
          loading: true,
        },
        {
          icon: "bx bx-archive-in",
          title: TOTAL_INVENTORY,
          value: "$0",
          loading: true,
        },
        {
          icon: "bx bx-purchase-tag-alt",
          title: TOTAL_RESERVES,
          value: "$0",
          loading: true,
        },
      ],
      debtsData: [
        {
          icon: "bx bx-copy-alt",
          title: TOTAL_CREDIT_CARD,
          value: "$0",
          loading: true,
        },
        {
          icon: "bx bx-archive-in",
          title: TOTAL_SUPPLIER_PAYABLE,
          value: "$0",
          loading: true,
        },
      ],
    };
  },
  props: {
    orders: {
      type: Array,
      default: () => [],
    },
  },
  created() {
    eventBus.$on("toggleShopifyStore", () => {
      setLoading(this.statData);
      setLoading(this.netEquityData);
      this.getMastersheetData();
    });
    this.getMastersheetData();
    this.getStripeBalance();
    this.getStripeTransactions();
  },
  methods: {
    async getMastersheetData() {
      const assets = await axios.get("mastersheetdata");
      const cogs = _.sumBy(this.orders, (order) => parseFloat(order.cogs));

      const {
        total_cash,
        total_credit_card,
        total_inventory,
        total_reserves,
        total_supplier_payable,
      } = assets.data;

      updateData(this.statData, TOTAL_CASH, displayCurrency(total_cash));

      updateData(
        this.statData,
        TOTAL_INVENTORY,
        displayCurrency(total_inventory)
      );
      updateData(
        this.statData,
        TOTAL_RESERVES,
        displayCurrency(total_reserves)
      );

      const debts = await axios.get("msdebts");
      const { debts_credit_card, debts_supplier_payable } = debts.data;
      updateData(
        this.debtsData,
        TOTAL_CREDIT_CARD,
        displayCurrency(debts_credit_card)
      );
      updateData(
        this.debtsData,
        TOTAL_SUPPLIER_PAYABLE,
        displayCurrency(debts_supplier_payable + cogs)
      );
      const netEquityTotal =
        total_cash +
        total_credit_card +
        total_inventory +
        total_reserves +
        total_supplier_payable -
        debts_credit_card -
        debts_supplier_payable -
        cogs;
      eventBus.$emit("netEquityTotal", netEquityTotal);
      updateData(
        this.netEquityData,
        NET_EQUITY,
        displayCurrency(netEquityTotal)
      );
    },
    async getStripeBalance() {
      try {
        const result = await axios.get("getstripeaccountsbalance");
        const stripeBalance = result.data.available[0].amount;

        //   "connect_reserved": [
        //     {
        //       "amount": 0,
        //       "currency": "inr"
        //     }
        //   ],
        const stripeReserve = result.data.connect_reserved[0].amount;
      } catch (err) {
        console.log(err);
        return [];
      }
    },
    async getStripeTransactions() {
      try {
        const result = await axios.get("getbalancetransactions");
        const data = result.data;
        console.log(data);
      } catch (err) {
        console.log(err);
        return [];
      }
    },
  },
};
</script>

<template>
  <div class="sheet-leftbar card">
    <div class="mail-list mt-4">
      <div class="row">
        <div
          v-for="stat of netEquityData"
          :key="stat.icon"
          class="col-md-12 my-2 netEquity"
        >
          <Stat
            :icon="stat.icon"
            :title="stat.title"
            :value="stat.value"
            :loading="stat.loading"
            :tooltip="stat.tooltip"
          />
        </div>
      </div>
    </div>
    <div class="mail-list mt-4">
      <div class="row">
        <div class="col-xl-12">
          <h4>Assets</h4>
        </div>
        <div v-for="stat of statData" :key="stat.icon" class="col-md-12 my-2">
          <Stat
            :icon="stat.icon"
            :title="stat.title"
            :value="stat.value"
            :loading="stat.loading"
          />
        </div>
      </div>
    </div>
    <div class="mail-list mt-4">
      <div class="row">
        <div class="col-xl-12">
          <h4>Debts</h4>
        </div>
        <div v-for="stat of debtsData" :key="stat.icon" class="col-md-12 my-2">
          <Stat
            :icon="stat.icon"
            :title="stat.title"
            :value="stat.value"
            :loading="stat.loading"
          />
        </div>
      </div>
    </div>
  </div>
</template>
<style lang="scss">
.sheet-leftbar {
  width: 400px;
  float: left;
  padding: 20px;
  border-radius: 5px;
  background-color: #191e2c;
  border-radius: 10px;
}
.netEquity .card {
  background-color: #2f3863;
}
.netEquity .text-muted {
  color: #556ee7 !important;
}
@media (max-width: 767px) {
  .sheet-leftbar {
    float: none;
    width: 100%;
  }
}
</style>
