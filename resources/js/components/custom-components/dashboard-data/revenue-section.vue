<template>
    <div class="row">
        <div class="col-xl-12 mt-4 d-flex justify-content-between">
            <h3>Revenues</h3>
            <h3>{{ totalRevenueinUSD }}</h3>
        </div>
        <div v-for="stat of data" :key="stat.id" class="col-md-4 p-2">
            <Stat
                :title="stat.title"
                :value="stat.value"
                :loading="stat.loading"
            />
        </div>
    </div>
</template>
<script>
import Stat from "../../widgets/stat";
import _ from "lodash";
import {
    displayCurrency,
    getSumBy,
    updateData,
    updateNoData,
    setLoading
} from "../../../utils";
import { eventBus } from "../../../app";

import {
    NUMBER_OF_ORDERS,
    ORDER_REVENUE,
    SHIPPING_REVENUE,
    TAXES_REVENUE
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
                    loading: true
                },
                {
                    id: 2,
                    title: ORDER_REVENUE,
                    value: "0",
                    loading: true
                },
                {
                    id: 3,
                    title: SHIPPING_REVENUE,
                    value: "0",
                    loading: true
                },
                {
                    id: 4,
                    title: TAXES_REVENUE,
                    value: "0",
                    loading: true
                }
            ],
            totalRevenue: 0,
            hasShopifyAccount: false
        };
    },
    props: {
        revenueData: {
            type: Array,
            default: () => []
        }
    },
    computed: {
        totalRevenueinUSD() {
            return displayCurrency(this.totalRevenue);
        }
    },
    watch: {
        revenueData(value, newValue) {
            this.assignData(this.revenueData);
        }
    },
    created() {
        eventBus.$on("hasShopifyAccount", status => {
            this.hasShopifyAccount = status;
            this.assignData(this.revenueData);
        });
    },
    methods: {
        assignData(orders) {
            setLoading(this.data);
            if (this.hasShopifyAccount) {
                const number_of_orders = _.size(orders);
                const revenue = getSumBy(orders, "total_price");
                const total_tax = getSumBy(orders, "total_tax");
                const discounts = _.sumBy(orders, order =>
                    parseFloat(order.total_discounts)
                );
                const shipping_revenue = _.sumBy(orders, order =>
                    _.sumBy(order.shipping_lines, line =>
                        parseFloat(line.price)
                    )
                );
                this.totalRevenue = parseFloat(revenue + discounts);
                eventBus.$emit(
                    "totalRevenueValue",
                    parseFloat(revenue + discounts)
                );
                updateData(this.data, NUMBER_OF_ORDERS, number_of_orders);
                updateData(
                    this.data,
                    ORDER_REVENUE,
                    displayCurrency(
                        revenue - shipping_revenue - total_tax + discounts
                    )
                );
                updateData(
                    this.data,
                    SHIPPING_REVENUE,
                    displayCurrency(shipping_revenue)
                );
                updateData(
                    this.data,
                    TAXES_REVENUE,
                    displayCurrency(total_tax)
                );
            } else {
                eventBus.$emit("totalRevenueValue", 0);
                updateNoData(this.data);
            }
        }
    }
};
</script>
