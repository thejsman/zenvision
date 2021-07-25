<template>
    <div class="col-md-4 mt-4">
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
import { AVERAGE_PROFIT_PER_ORDER } from "../../../../constants";
import { displayCurrency } from "../../../../utils";

export default {
    components: { Stat },
    data() {
        return {
            data: {
                id: 1,
                title: AVERAGE_PROFIT_PER_ORDER,
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
            "shopifyRevenue",
            "shopifyShippingRevenue",
            "shopifyDiscounts",
            "shopifyTotalTax",
            "ShopifyCogsTotalPA"
        ]),

        averageProfitValue() {
            return displayCurrency(
                parseFloat(
                    (this.shopifyRevenue +
                        this.shopifyShippingRevenue +
                        this.shopifyTotalTax -
                        this.shopifyDiscounts -
                        this.ShopifyCogsTotalPA) /
                        this.numberOfOrders
                )
            );
        }
    },

    watch: {
        async hasShopifyStorePA() {
            if (this.hasShopifyStorePA) {
                this.data.value = `${this.averageProfitValue}`;
                this.data.loading = false;
            } else {
                this.data.value = "-";
                this.data.loading = false;
            }
        },
        averageProfitValue(newVal, oldVal) {
            this.data.loading = false;
            this.data.value = `${newVal}`;
        }
    }
};
</script>

<style></style>p
