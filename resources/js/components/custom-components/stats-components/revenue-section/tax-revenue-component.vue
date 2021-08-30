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
import { TAXES_REVENUE } from "../../../../constants";
import { displayCurrency } from "../../../../utils";
export default {
    components: { Stat },
    data() {
        return {
            data: {
                id: 1,
                title: TAXES_REVENUE,
                value: "0",
                loading: true,
                toolTip:
                    "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
            }
        };
    },
    computed: {
        ...mapGetters(["hasShopifyStorePA", "shopifyTotalTax"])
    },
    watch: {
        hasShopifyStorePA() {
            if (this.hasShopifyStorePA) {
                this.data.loading = false;
                this.data.value = displayCurrency(`${this.shopifyTotalTax}`);
            } else {
                this.data.loading = false;
                this.data.value = "-";
            }
        },
        shopifyTotalTax(newVal, oldVal) {
            this.data.loading = false;
            this.data.value = this.hasShopifyStorePA
                ? displayCurrency(`${newVal}`)
                : "-";
        }
    }
};
</script>
