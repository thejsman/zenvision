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
import { DISCOUNTS_TOTAL } from "../../../../constants";
import { displayCurrency } from "../../../../utils";
export default {
    components: { Stat },
    data() {
        return {
            data: {
                id: 1,
                title: DISCOUNTS_TOTAL,
                value: `0`,
                loading: true,
                toolTip:
                    "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
            }
        };
    },
    computed: {
        ...mapGetters(["hasShopifyStorePA", "shopifyDiscounts"])
    },
    watch: {
        hasShopifyStorePA() {
            if (this.hasShopifyStorePA) {
                this.data.value = displayCurrency(`${this.shopifyDiscounts}`);
                this.data.loading = false;
            } else {
                this.data.value = "-";
                this.data.loading = false;
            }
        },
        shopifyDiscounts(newVal, oldVal) {
            if (this.hasShopifyStorePA) {
                this.data.value = displayCurrency(`${newVal}`);
                this.data.loading = false;
            }
        }
    }
};
</script>
