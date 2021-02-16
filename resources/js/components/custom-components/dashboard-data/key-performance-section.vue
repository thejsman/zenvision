<template>
  <div class="row">
    <div class="col-xl-12 mt-4">
      <h3>Key Performance Metrics</h3>
    </div>
    <div v-for="performance of data" :key="performance.id" class="col-md-3 p-2">
      <Stat
        :title="performance.title"
        :value="performance.value"
        :loading="performance.loading"
      />
    </div>
  </div>
</template>
<script>
import Stat from "../../widgets/stat";
import _ from "lodash";
import { eventBus } from "../../../app";
import {
  displayCurrency,
  displayNumber,
  getSumBy,
  updateData,
} from "../../../utils";
import {
  CONVERSION_RATE,
  AVERAGE_PROFIT_PER_ORDER,
  AVERAGE_ORDER_VALUE,
  AVERAGE_UNITS_PER_ORDER,
  ABANDONED_CART,
  US_ORDERS_PERCENTAGE,
} from "../../../constants";

import { mapGetters } from "vuex";

export default {
  components: { Stat },
  data() {
    return {
      totalProfitValue: 0,
      data: [
        {
          id: 1,
          title: CONVERSION_RATE,
          value: `-`,
          loading: false,
        },
        {
          id: 2,
          title: ABANDONED_CART,
          value: `0`,
          loading: true,
        },
        {
          id: 3,
          title: AVERAGE_ORDER_VALUE,
          value: `0`,
          loading: true,
        },
        {
          id: 4,
          title: AVERAGE_UNITS_PER_ORDER,
          value: `0`,
          loading: true,
        },
        {
          id: 5,
          title: AVERAGE_PROFIT_PER_ORDER,
          value: `0`,
          loading: true,
        },
        {
          id: 6,
          title: US_ORDERS_PERCENTAGE,
          value: `0`,
          loading: true,
        },
      ],
    };
  },
  props: {
    performanceData: {
      type: Array,
      default: () => [],
    },

    totalProducts: {
      type: Number,
      default: 0,
    },
  },
  computed: {
    ...mapGetters["ShopifyProductCount"],
  },
  watch: {
    performanceData(value, newValue) {
      this.assignData(this.performanceData, this.ShopifyProductCount);
    },
  },
  created() {
    eventBus.$on("totalProfitValue", (value) => {
      this.totalProfitValue = value;
    });
  },
  methods: {
    assignData(orders, number_of_products) {
      const number_of_orders = _.size(orders);
      const revenue = getSumBy(orders, "total_price");
      const total_tax = getSumBy(orders, "total_tax");
      const cogs = getSumBy(orders, "cogs");
      const shipping_revenue = _.sumBy(orders, (order) =>
        _.sumBy(order.shipping_lines, (line) => parseFloat(line.price))
      );
      this.totalRevenue = displayCurrency(revenue + total_tax);

      const average_order = displayCurrency(
        (revenue - shipping_revenue) / number_of_orders
      );

      updateData(this.data, AVERAGE_ORDER_VALUE, average_order);

      const average_unit = number_of_products / number_of_orders || 0;

      updateData(
        this.data,
        AVERAGE_UNITS_PER_ORDER,
        displayNumber(average_unit)
      );

      const average_profit = displayCurrency(
        this.totalProfitValue / number_of_orders
      );
      updateData(this.data, AVERAGE_PROFIT_PER_ORDER, average_profit);

      const shipping_country = _.countBy(
        orders,
        (order) => order.shipping_country === "United States"
      );
      const average_us_percentage =
        (shipping_country.true / number_of_orders) * 100;

      updateData(
        this.data,
        US_ORDERS_PERCENTAGE,
        displayNumber(average_us_percentage)
      );
      // Development - saving network requst
      this.getAbandonedCartCount();
    },
    async getAbandonedCartCount() {
      try {
        const result = await axios.get("abandonedcart");
        const count = result.data;
        updateData(this.data, ABANDONED_CART, count);
      } catch (error) {
        console.log(error);
        updateData(this.data, ABANDONED_CART, 0);
      }
    },
  },
};
</script>

