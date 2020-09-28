<template>
  <div class="row">
    <div class="col-xl-12 mt-4">
      <h3>Key Performance Metrics</h3>
    </div>
    <div v-for="performance of data" :key="performance.id" class="col-md-3 p-2">
      <Stat :title="performance.title" :value="performance.value" />
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
    performanceData: {
      type: Array,
      default: () => [],
    },
    shopifyStores: {
      type: Array,
      default: () => [],
    },
    totalProducts: {
      type: Number,
      default: 0,
    },
  },
  watch: {
    performanceData(value, newValue) {
      this.assignData(this.performanceData, this.totalProducts);
    },
  },
  methods: {
    assignData(orders, number_of_products) {
      const number_of_orders = _.size(orders);
      const revenue = _.sumBy(orders, (order) => parseFloat(order.total_price));
      const shipping_revenue = _.sumBy(orders, (order) =>
        _.sumBy(order.shipping_lines, (line) => parseFloat(line.price))
      );
      const cogs = _.sumBy(orders, (order) => parseFloat(order.cogs));
      const total_tax = _.sumBy(orders, (order) => parseFloat(order.total_tax));

      const average_order = (revenue - shipping_revenue) / number_of_orders;
      const average_unit = number_of_products / number_of_orders;
      const average_profit = parseFloat(
        (revenue - cogs) / number_of_orders
      ).toFixed(2);
      const shipping_country = _.countBy(
        orders,
        (order) => order.shipping_country === "United States"
      );
      const average_us_percentage =
        (shipping_country.true / number_of_orders) * 100;
      console.log({ average_us_percentage });
      this.data = [
        {
          id: 1,
          title: "Conversion Rate",
          value: "-",
        },
        {
          id: 3,
          title: "Avg Order Value",
          value: `$` + average_order,
        },
        {
          id: 4,
          title: "Avg Units Per Order",
          value: `${average_unit}`,
        },
        {
          id: 5,
          title: "Avg Profit Per Orders",
          value: `$` + average_profit,
        },
        {
          id: 6,
          title: "% US Orders",
          value: `${average_us_percentage}`,
        },
      ];
      this.getAbandonedCartCount();
    },
    async getAbandonedCartCount() {
      let count = 0;
      try {
        const result = await axios.get("abandonedcart", {
          params: {
            store_ids: this.shopifyStores,
          },
        });
        count = await result.data;
        console.log({ count });
      } catch (error) {
        console.log(error);
      }

      // Development saving network requst
      this.data.splice(2, 0, {
        id: 2,
        title: "Abandoned Cart",
        value: `${count}`,
      });
    },
  },
};
</script>

