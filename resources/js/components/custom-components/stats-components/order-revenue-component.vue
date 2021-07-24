<template>
    <div class="col-md-4  mt-xs-4">
        <Stat
            :title="data.title"
            :value="data.value"
            :loading="data.loading"
            :toolTip="data.toolTip"
        />
    </div>
</template>

<script>
import Stat from "../../widgets/stat";
import { mapGetters } from "vuex";
import { ORDER_REVENUE } from "../../../constants";
import { displayCurrency } from "../../../utils";
export default {
    components: { Stat },
    data() {
        return {
            data: {
                id: 2,
                title: ORDER_REVENUE,
                value: "0",
                loading: true,
                toolTip:
                    "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
            }
        };
    },
    computed: {
        ...mapGetters(["hasShopifyStorePA", "shopifyRevenue"])
    },
    watch: {
        hasShopifyStorePA() {
            if (this.hasShopifyStorePA) {
                this.data.loading = false;
                this.data.value = displayCurrency(`${this.shopifyRevenue}`);
            } else {
                this.data.loading = false;
                this.data.value = "-";
            }
        },
        shopifyRevenue(newVal, oldVal) {
            this.data.loading = false;
            this.data.value = displayCurrency(`${newVal}`);
        }
    }
};
</script>

<style></style>p
