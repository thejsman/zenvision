<template>
  <div class="row">
    <div class="col-xl-12 mt-4">
      <h3>Cost</h3>
    </div>
    <div v-for="cost of data" :key="cost.id" class="col-md-4 p-2">
      <Stat :title="cost.title" :value="cost.value" />
    </div>
  </div>
</template>
<script>
import Stat from "../../widgets/stat";
export default {
  components: { Stat },
  data() {
    return {
      data: [],
    };
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
  },
  watch: {
    costData(value, newValue) {
      this.assignData(this.refundTotal, this.costData);
    },
  },
  methods: {
    assignData(refundTotal, orders) {
      const cogs = _.sumBy(orders, (order) => parseFloat(order.cogs));
      const discounts = _.sumBy(orders, (order) =>
        parseFloat(order.total_discounts)
      );

      this.data = [
        {
          id: 1,
          title: "Ad Spend",
          value: "-",
        },
        {
          id: 2,
          title: "COGS",
          value: `$` + cogs,
        },
        {
          id: 3,
          title: "Merchant Fees",
          value: "-",
        },
        {
          id: 4,
          title: "Refunds",
          value: `$` + refundTotal,
        },
        {
          id: 5,
          title: "Chargebacks",
          value: "-",
        },
        {
          id: 6,
          title: "Discounts",
          value: `$` + discounts,
        },
      ];
    },
  },
};
</script>
