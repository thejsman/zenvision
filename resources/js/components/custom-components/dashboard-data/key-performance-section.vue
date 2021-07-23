<template>
    <div class="row">
        <div class="col-xl-12 mt-4 header-key-perf">
            <h3>Key Performance Metrics</h3>
        </div>
        <div
            v-for="performance of data"
            :key="performance.id"
            class="col-md-3 p-2"
        >
            <Stat
                :title="performance.title"
                :value="performance.value"
                :loading="performance.loading"
                :toolTip="performance.toolTip"
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
    updateNoData,
    setLoading
} from "../../../utils";
import {
    AVERAGE_PROFIT_PER_ORDER,
    AVERAGE_ORDER_VALUE,
    AVERAGE_UNITS_PER_ORDER,
    ABANDONED_CART,
    US_ORDERS_PERCENTAGE
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
                    title: ABANDONED_CART,
                    value: `0`,
                    loading: true,
                    toolTip:
                        "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
                },

                {
                    id: 2,
                    title: AVERAGE_ORDER_VALUE,
                    value: `0`,
                    loading: true,
                    toolTip:
                        "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
                },
                {
                    id: 3,
                    title: AVERAGE_UNITS_PER_ORDER,
                    value: `0`,
                    loading: true,
                    toolTip:
                        "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
                },
                {
                    id: 4,
                    title: AVERAGE_PROFIT_PER_ORDER,
                    value: `0`,
                    loading: true,
                    toolTip:
                        "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
                },
                {
                    id: 5,
                    title: US_ORDERS_PERCENTAGE,
                    value: `0`,
                    loading: true,
                    toolTip:
                        "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
                }
            ],
            hasShopifyAccount: false
        };
    },
    props: {
        performanceData: {
            type: Array,
            default: () => []
        }
    },
    computed: {
        ...mapGetters(["keyPerformanceData"])
    },
    watch: {
        performanceData(value, newValue) {
            this.assignData(this.performanceData);
        }
    },
    created() {
        eventBus.$on("totalProfitValue", value => {
            this.totalProfitValue = value;
        });
        eventBus.$on("hasShopifyAccount", status => {
            this.hasShopifyAccount = status;
            this.assignData(this.performanceData);
        });
    },
    methods: {
        async assignData(orders) {
            setLoading(this.data);
            if (this.hasShopifyAccount) {
                const number_of_orders = _.size(orders);
                const revenue = getSumBy(orders, "total_price");
                const total_tax = getSumBy(orders, "total_tax");
                this.totalRevenue = displayCurrency(revenue + total_tax);

                const average_order = displayCurrency(
                    revenue / number_of_orders
                );

                updateData(this.data, AVERAGE_ORDER_VALUE, average_order);

                const average_profit = displayCurrency(
                    this.totalProfitValue / number_of_orders
                );
                updateData(this.data, AVERAGE_PROFIT_PER_ORDER, average_profit);

                const shipping_country = _.countBy(
                    orders,
                    order => order.shipping_country === "United States"
                );
                const average_us_percentage =
                    (shipping_country.true / number_of_orders) * 100;

                updateData(
                    this.data,
                    US_ORDERS_PERCENTAGE,
                    displayNumber(average_us_percentage)
                );
                // Avg Unit Per Order
                this.getAvgUnitCount();

                // Development - saving network requst
                this.getAbandonedCartCount();
            } else {
                updateNoData(this.data);
            }
        },
        async getAbandonedCartCount() {
            try {
                const result = await axios.get("abandonedcart");
                const count = result.data;
                if (this.hasShopifyAccount) {
                    updateData(this.data, ABANDONED_CART, count);
                } else {
                    updateData(this.data, ABANDONED_CART, "-");
                }
            } catch (error) {
                console.log(error);
                updateData(this.data, ABANDONED_CART, 0);
            }
        },
        async getAvgUnitCount() {
            try {
                const result = await axios.get("getavgunitperorder");
                const count = result.data;
                if (this.hasShopifyAccount) {
                    updateData(this.data, AVERAGE_UNITS_PER_ORDER, count);
                } else {
                    updateData(this.data, AVERAGE_UNITS_PER_ORDER, "-");
                }
            } catch (error) {
                console.log(error);
                updateData(this.data, AVERAGE_UNITS_PER_ORDER, 0);
            }
        }
    }
};
</script>
