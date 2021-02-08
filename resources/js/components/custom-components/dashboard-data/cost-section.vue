<template>
  <div class="row">
    <div class="col-xl-12 mt-4 d-flex justify-content-between">
      <h3>Cost</h3>
      <h3>{{ totalCost }}</h3>
    </div>
    <div v-for="cost of data" :key="cost.id" class="col-md-4 p-2">
      <Stat
        :title="cost.title"
        :value="cost.value"
        :loading="cost.loading"
        :onClick="cost.onClick"
        :totalSubscriptionCount="cost.totalSubscriptionCount"
      />
    </div>

    <b-modal
      id="subscription-details"
      size="lg"
      centered
      hide-footer
      hide-header
    >
      <SubscriptionCost @handle-close="handleSubscriptionClose" />
    </b-modal>
  </div>
</template>
<script>
import Stat from "../../widgets/stat";
import { eventBus } from "../../../app";
import SubscriptionCost from "../modals/subscription-cost";
import { moment } from "moment";
import { displayCurrency, updateData } from "../../../utils";
import {
  COGS_TOTAL,
  DISCOUNTS_TOTAL,
  REFUNDS_TOTAL,
  CHARGEBACKS_TOTAL,
  MERCHANT_FEE,
  AD_SPEND_FACEBOOK,
  AD_SPEND_GOOGLE,
  SUBSCRIPTION_COST,
} from "../../../constants";
export default {
  components: { Stat, SubscriptionCost },
  data() {
    return {
      subscriptionData: [],
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
        {
          id: 8,
          title: SUBSCRIPTION_COST,
          value: `0`,
          loading: true,
          onClick: this.handleSubscriptionClick,
          totalSubscriptionCount: 0,
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
  created() {
    eventBus.$on("updateSubscription", async () => {
      await this.getSubscriptionData();
    });
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
      updateData(this.data, AD_SPEND_FACEBOOK, displayCurrency(0));
      updateData(this.data, AD_SPEND_GOOGLE, displayCurrency(0));
      updateData(this.data, MERCHANT_FEE, displayCurrency(this.merchantFees));
      this.getSubscriptionData();
      this.getChargebackTotal();
    },
    handleSubscriptionClick() {
      this.$bvModal.show("subscription-details");
    },
    handleSubscriptionClose() {
      this.$bvModal.hide("subscription-details");
    },
    async getSubscriptionData() {
      try {
        const result = await axios.get("/subscriptioncost");
        const { data } = result;
        if (data.length > 0) {
          const subTotal2 = this.getSubscriptionTotal(data);
          this.updateSubscriptionData(
            this.data,
            SUBSCRIPTION_COST,
            displayCurrency(subTotal2),
            data.length
          );
        } else {
          this.updateSubscriptionData(
            this.data,
            SUBSCRIPTION_COST,
            displayCurrency(0),
            0
          );
        }
      } catch (error) {
        this.updateSubscriptionData(
          this.data,
          SUBSCRIPTION_COST,
          displayCurrency(0),
          0
        );
      }
    },
    updateSubscriptionData(data, title, value, totalCount) {
      data.forEach((d) => {
        if (d.title === title) {
          d.value = `${value}`;
          d.loading = false;
          d.totalSubscriptionCount = totalCount;
        }
      });
    },
    getSubscriptionTotal(subscriptions) {
      let subTotal = 0;
      subscriptions.forEach((sub) => {
        subTotal += this.calculateSubscription(sub);
      });
      console.log("SubTotal", subTotal);
      return subTotal;
    },
    calculateSubscription(sub) {
      let total = 0;
      if (sub.billing_period === "Daily") {
        let startDate = new Date(sub.starting_date);
        const endDate =
          sub.end_date === null ? new Date() : new Date(sub.end_date);
        while (startDate <= endDate) {
          total += parseFloat(sub.subscription_price);
          startDate = new Date(startDate.setDate(startDate.getDate() + 1));
        }
        return total;
      } else if (sub.billing_period === "Weekly") {
        let startDate = new Date(sub.starting_date);
        const endDate =
          sub.end_date === null ? new Date() : new Date(sub.end_date);
        while (startDate <= endDate) {
          total += parseFloat(sub.subscription_price);
          startDate = new Date(startDate.setDate(startDate.getDate() + 7));
        }
        return total;
      } else if (sub.billing_period === "Monthly") {
        let startDate = new Date(sub.starting_date);
        const endDate =
          sub.end_date === null ? new Date() : new Date(sub.end_date);
        while (startDate <= endDate) {
          total += parseFloat(sub.subscription_price);
          startDate = new Date(startDate.setMonth(startDate.getMonth() + 1));
          console.log(startDate);
        }
        return total;
      } else if (sub.billing_period === "Every 3 months") {
        let startDate = new Date(sub.starting_date);
        const endDate =
          sub.end_date === null ? new Date() : new Date(sub.end_date);
        while (startDate <= endDate) {
          total += parseFloat(sub.subscription_price);
          startDate = new Date(startDate.setMonth(startDate.getMonth() + 3));
        }
        return total;
      } else if (sub.billing_period === "Every 6 months") {
        let startDate = new Date(sub.starting_date);
        const endDate =
          sub.end_date === null ? new Date() : new Date(sub.end_date);
        while (startDate <= endDate) {
          total += parseFloat(sub.subscription_price);
          startDate = new Date(startDate.setMonth(startDate.getMonth() + 6));
        }
        return total;
      } else if (sub.billing_period === "Yearly") {
        let startDate = new Date(sub.starting_date);
        const endDate =
          sub.end_date === null ? new Date() : new Date(sub.end_date);
        while (startDate <= endDate) {
          total += parseFloat(sub.subscription_price);
          startDate = new Date(
            startDate.setFullYear(startDate.getFullYear() + 1)
          );
        }
        return total;
      }
    },
    async getChargebackTotal() {
      try {
        const result = await axios.get("getshopifydisputes");
        const { disputes } = result.data;
        let totalChargeback = 0;
        if (disputes.length > 1) {
          data.forEach((dispute) => {
            totalChargeback += parseFloat(dispute.amount);
          });
        }
        updateData(
          this.data,
          CHARGEBACKS_TOTAL,
          displayCurrency(totalChargeback)
        );
      } catch (err) {
        console.log(err);
        updateData(this.data, CHARGEBACKS_TOTAL, displayCurrency(0));
      }
    },
  },
};
</script>