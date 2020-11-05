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

      const average_order =
        (revenue - shipping_revenue) / number_of_orders || 0;
      const average_unit = number_of_products / number_of_orders || 0;
      const average_profit = parseFloat(
        (revenue - cogs) / number_of_orders
      ).toFixed(2);
      const shipping_country = _.countBy(
        orders,
        (order) => order.shipping_country === "United States"
      );
      const average_us_percentage =
        (shipping_country.true / number_of_orders) * 100;

      this.data = [
        {
          id: 1,
          title: "Conversion Rate",
          value: "-",
        },
        {
          id: 2,
          title: "Abandoned Cart",
          value: `0`,
        },
        {
          id: 3,
          title: "Avg Order Value",
          value:
            `$` + isNaN(average_order)
              ? parseFloat(average_order).toFixed(2)
              : "0",
        },
        {
          id: 4,
          title: "Avg Units Per Order",
          value:
            `$` + isNaN(average_unit)
              ? parseFloat(average_unit).toFixed(2)
              : "0",
        },
        {
          id: 5,
          title: "Avg Profit Per Orders",
          value:
            `$` + isNaN(average_profit)
              ? parseFloat(average_profit).toFixed(2)
              : "0",
        },
        {
          id: 6,
          title: "% US Orders",
          value: `${average_us_percentage}`,
        },
      ];
      // Development saving network requst
      //   this.getAbandonedCartCount();
    },
    async getAbandonedCartCount() {
      let count = "0";
      try {
        const result = await axios.get("abandonedcart", {
          params: {
            store_ids: this.shopifyStores,
          },
        });

        count = await result.data;
      } catch (error) {
        console.log(error);
      }
      this.data[1].value = `${count}`;
    },
  },
};
</script>

