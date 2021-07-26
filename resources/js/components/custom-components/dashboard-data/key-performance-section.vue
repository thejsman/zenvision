<template>
    <div>
        <div class="row p-2 mt-4">
            <div class="col-xl-12">
                <div class="d-flex justify-content-between">
                    <h3>Key Performance Metrics</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <AbandonedCart />
            <AverageOrderValue />
            <AverageUnitCount />
            <AverageProfitValue />
            <UsOrdersPercentage />
        </div>
    </div>
</template>
<script>
import AbandonedCart from "../../custom-components/stats-components/key-performance-section/abandoned-cart-component.vue";
import AverageOrderValue from "../../custom-components/stats-components/key-performance-section/average-order-component.vue";
import AverageUnitCount from "../../custom-components/stats-components/key-performance-section/average-units-component.vue";
import AverageProfitValue from "../../custom-components/stats-components/key-performance-section/average-profit-component.vue";
import UsOrdersPercentage from "../../custom-components/stats-components/key-performance-section/us-orders-component.vue";

export default {
    components: {
        AbandonedCart,
        AverageOrderValue,
        AverageUnitCount,
        AverageProfitValue,
        UsOrdersPercentage
    }
};
</script>
