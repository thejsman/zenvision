<template>
    <div class="col-md-4 mt-4 mb-4">
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
import { US_ORDERS_PERCENTAGE } from "../../../../constants";
import { countBy } from "lodash";

export default {
    components: { Stat },
    data() {
        return {
            data: {
                id: 1,
                title: US_ORDERS_PERCENTAGE,
                value: "0",
                loading: true,
                toolTip:
                    "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
            }
        };
    },
    computed: {
        ...mapGetters(["hasShopifyStorePA", "numberOfOrders", "shopifyOrders"]),
        usOrdersPercentage() {
            const usOrdersCountObj = countBy(
                this.shopifyOrders,
                order => order.shipping_country === "United States"
            );
            if (usOrdersCountObj.true) {
                return (
                    parseFloat(usOrdersCountObj.true / this.numberOfOrders) *
                    100
                ).toFixed(2);
            } else {
                return "0";
            }
        }
    },

    watch: {
        async hasShopifyStorePA() {
            if (this.hasShopifyStorePA) {
                this.data.loading = false;
                this.data.value = `${this.usOrdersPercentage}`;
            } else {
                this.data.loading = false;
                this.data.value = "-";
            }
        },
        usOrdersPercentage(newVal, oldVal) {
            this.data.loading = false;
            this.data.value = `${newVal}`;
        }
    }
};
</script>
