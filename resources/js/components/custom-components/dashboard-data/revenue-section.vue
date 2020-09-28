<template>
  <div class="row">
    <div class="col-xl-12 mt-4">
      <h3>Revenues</h3>
    </div>
    <div v-for="stat of data" :key="stat.id" class="col-md-4 p-2">
      <Stat :title="stat.title" :value="stat.value" />
    </div>
  </div>
</template>
<script>
import Stat from "../../widgets/stat";
import _ from "lodash";

export default {
  components: { Stat },
  data() {
    return {
      data: [],
    };
  },
  props: {
    revenueData: {
      type: Array,
      default: () => [],
    },
  },
  watch: {
    revenueData(value, newValue) {
      this.assignData(this.revenueData);
    },
  },
  methods: {
    assignData(orders) {
      const number_of_orders = _.size(orders);
      const revenue = _.sumBy(orders, (order) => parseFloat(order.total_price));
      const shipping_revenue = _.sumBy(orders, (order) =>
        _.sumBy(order.shipping_lines, (line) => parseFloat(line.price))
      );
      const total_tax = _.sumBy(orders, (order) => parseFloat(order.total_tax));
      this.data = [
        {
          id: 1,
          title: "Number of Orders",
          value: `${number_of_orders}`,
        },
        {
          id: 2,
          title: "Order Revenue",
          value: `$` + (revenue - shipping_revenue),
        },
        {
          id: 3,
          title: "Shipping Revenue",
          value: `$` + shipping_revenue,
        },
        {
          id: 4,
          title: "Taxes",
          value: `$` + total_tax.toFixed(2),
        },
      ];
    },
  },
};
</script>
