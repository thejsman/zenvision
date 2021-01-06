<template>
  <div class="row">
    <div class="col-xl-12 mt-4 d-flex justify-content-between">
      <h3>Cost</h3>
      <h3>{{ totalCost }}</h3>
    </div>
    <div v-for="cost of data" :key="cost.id" class="col-md-4 p-2">
      <Stat :title="cost.title" :value="cost.value" :loading="cost.loading" />
    </div>
  </div>
</template>
<script>
import Stat from "../../widgets/stat";
import { eventBus } from "../../../app";

import {
  displayCurrency,
  displayNumber,
  getSumBy,
  updateData,
} from "../../../utils";
import {
  COGS_TOTAL,
  DISCOUNTS_TOTAL,
  REFUNDS_TOTAL,
  CHARGEBACKS_TOTAL,
  MERCHANT_FEE,
  AD_SPEND_FACEBOOK,
  AD_SPEND_GOOGLE,
} from "../../../constants";

export default {
  components: { Stat },
  data() {
    return {
      data: [
        {
          id: 1,
          title: COGS_TOTAL,
          value: `0`,
          loading: true,
        },
        {
          id: 2,
          title: DISCOUNTS_TOTAL,
          value: `0`,
          loading: true,
        },
        {
          id: 3,
          title: REFUNDS_TOTAL,
          value: `0`,
          loading: true,
        },
        {
          id: 4,
          title: CHARGEBACKS_TOTAL,
          value: `0`,
          loading: true,
        },
        {
          id: 5,
          title: MERCHANT_FEE,
          value: `0`,
          loading: true,
        },
        {
          id: 6,
          title: AD_SPEND_FACEBOOK,
          value: `0`,
          loading: true,
        },
        {
          id: 7,
          title: AD_SPEND_GOOGLE,
          value: `0`,
          loading: true,
        },
      ],
      totalDiscount: 0,
    };
  },
  computed: {
    totalCost() {
      eventBus.$emit(
        "totalCostValue",
        parseFloat(this.merchantFees + this.refundTotal + this.totalDiscount)
      );
      return displayCurrency(
        this.merchantFees + this.refundTotal + this.totalDiscount
      );
    },
  },

  props: {
    costData: {
      type: Array,
      default: () => [],
    },
    refundTotal: {
      type: Number,
      default: 0,
    },
    merchantFees: {
      type: Number,
      default: 0,
    },
  },
  watch: {
    costData(value, newValue) {
      this.assignData(this.refundTotal, this.costData);
    },
  },
  methods: {
    assignData(refundTotal, orders) {
      const cogs = _.sumBy(orders, (order) => parseFloat(order.cogs));
      updateData(this.data, COGS_TOTAL, displayCurrency(cogs));

      const discounts = _.sumBy(orders, (order) =>
        parseFloat(order.total_discounts)
      );
      this.totalDiscount = discounts;

      updateData(this.data, DISCOUNTS_TOTAL, displayCurrency(discounts));

      updateData(this.data, REFUNDS_TOTAL, displayCurrency(refundTotal));

      setTimeout(() => {
        updateData(this.data, CHARGEBACKS_TOTAL, displayCurrency(0));
      }, 3000);
      updateData(this.data, AD_SPEND_FACEBOOK, displayCurrency(0));
      updateData(this.data, AD_SPEND_GOOGLE, displayCurrency(0));
      updateData(this.data, MERCHANT_FEE, displayCurrency(this.merchantFees));
    },
  },
};
</script>
