<template>
  <div class="row">
    <div class="col-xl-12 mt-4 d-flex justify-content-between">
      <h3>Revenues</h3>
      <h3>{{ totalRevenue }}</h3>
    </div>
    <div v-for="stat of data" :key="stat.id" class="col-md-4 p-2">
      <Stat :title="stat.title" :value="stat.value" :loading="stat.loading" />
    </div>
  </div>
</template>
<script>
import Stat from "../../widgets/stat";
import _ from "lodash";
import { displayCurrency, getSumBy, updateData } from "../../../utils";
import { eventBus } from "../../../app";

import {
  NUMBER_OF_ORDERS,
  ORDER_REVENUE,
  SHIPPING_REVENUE,
  TAXES_REVENUE,
} from "../../../constants";

export default {
  components: { Stat },
  data() {
    return {
      data: [
        {
          id: 1,
          title: NUMBER_OF_ORDERS,
          value: "0",
          loading: true,
        },
        {
          id: 2,
          title: ORDER_REVENUE,
          value: "0",
          loading: true,
        },
        {
          id: 3,
          title: SHIPPING_REVENUE,
          value: "0",
          loading: true,
        },
        {
          id: 4,
          title: TAXES_REVENUE,
          value: "0",
          loading: true,
        },
      ],
      totalRevenue: 0,
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
      console.log("AllOrders:", this.revenueData);
      this.assignData(this.revenueData);
    },
  },

  methods: {
    assignData(orders) {
      const number_of_orders = _.size(orders);
      const revenue = getSumBy(orders, "total_price");
      const total_tax = getSumBy(orders, "total_tax");
      const shipping_revenue = _.sumBy(orders, (order) =>
        _.sumBy(order.shipping_lines, (line) => parseFloat(line.price))
      );
      this.totalRevenue = displayCurrency(revenue + total_tax);
      eventBus.$emit("totalRevenueValue", parseFloat(revenue + total_tax));
      updateData(this.data, NUMBER_OF_ORDERS, number_of_orders);
      updateData(
        this.data,
        ORDER_REVENUE,
        displayCurrency(revenue - shipping_revenue)
      );
      updateData(
        this.data,
        SHIPPING_REVENUE,
        displayCurrency(shipping_revenue)
      );
      updateData(this.data, TAXES_REVENUE, displayCurrency(total_tax));
    },
  },
};
</script>
