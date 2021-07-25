<template>
    <div class="col-md-4 mt-4 mt-md-0">
        <Stat
            :title="data.title"
            :value="data.value"
            :loading="data.loading"
            :toolTip="data.toolTip"
        />
    </div>
</template>

<script>
import Stat from "../../../widgets/stat";
import { mapGetters } from "vuex";
import { AVERAGE_ORDER_VALUE } from "../../../../constants";
import { displayCurrency } from "../../../../utils";

export default {
    components: { Stat },
    data() {
        return {
            data: {
                id: 1,
                title: AVERAGE_ORDER_VALUE,
                value: "0",
                loading: true,
                toolTip:
                    "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
            }
        };
    },
    computed: {
        ...mapGetters([
            "hasShopifyStorePA",
            "numberOfOrders",
            "shopifyRevenue"
        ]),
        averageOrderValue() {
            return displayCurrency(
                parseFloat(this.shopifyRevenue / this.numberOfOrders)
            );
        }
    },

    watch: {
        async hasShopifyStorePA() {
            if (this.hasShopifyStorePA) {
                this.data.loading = false;
                this.data.value = `${this.averageOrderValue}`;
            } else {
                this.data.loading = false;
                this.data.value = "-";
            }
        },
        averageOrderValue(newVal, oldVal) {
            this.data.loading = false;
            this.data.value = `${newVal}`;
        }
    }
};
</script>
