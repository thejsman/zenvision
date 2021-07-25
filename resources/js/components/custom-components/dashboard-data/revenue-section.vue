<template>
    <div>
        <div class="row p-2 mt-4">
            <div class="col-xl-12">
                <div class="d-flex justify-content-between">
                    <h3>Revenues</h3>
                    <h3>{{ totalRevenue }}</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <NumberOfOrders />
            <OrderRevenue />
            <ShippingRevenue />
            <TaxRevenue />
        </div>
    </div>
</template>
<script>
import NumberOfOrders from "../stats-components/revenue-section/number-of-orders-component.vue";
import OrderRevenue from "../stats-components/revenue-section/order-revenue-component.vue";
import ShippingRevenue from "../stats-components/revenue-section/shipping-revenue-component.vue";
import TaxRevenue from "../stats-components/revenue-section/tax-revenue-component.vue";

import { mapGetters } from "vuex";
import { displayCurrency } from "../../../utils";

export default {
    components: {
        NumberOfOrders,
        OrderRevenue,
        ShippingRevenue,
        TaxRevenue
    },

    computed: {
        ...mapGetters([
            "shopifyRevenue",
            "shopifyShippingRevenue",
            "shopifyTotalTax",
            "shopifyDiscounts"
        ]),
        totalRevenue() {
            return displayCurrency(
                this.shopifyRevenue +
                    this.shopifyShippingRevenue +
                    this.shopifyTotalTax -
                    this.shopifyDiscounts
            );
        }
    }
};
</script>
