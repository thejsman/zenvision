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
import { REFUNDS_TOTAL } from "../../../../constants";
import { displayCurrency } from "../../../../utils";
export default {
    components: { Stat },
    data() {
        return {
            data: {
                id: 1,
                title: REFUNDS_TOTAL,
                value: `0`,
                loading: true,
                toolTip:
                    "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
            }
        };
    },
    computed: {
        ...mapGetters(["hasShopifyStorePA", "shopifyRefundTotal"])
    },
    watch: {
        hasShopifyStorePA() {
            if (this.hasShopifyStorePA) {
                this.data.loading = false;
                this.data.value = displayCurrency(`${this.shopifyRefundTotal}`);
            } else {
                this.data.loading = false;
                this.data.value = "-";
            }
        },
        shopifyRefundTotal(newVal, oldVal) {
            if (this.hasShopifyStorePA) {
                this.data.value = this.hasShopifyStorePA
                    ? displayCurrency(`${newVal}`)
                    : "-";
                this.data.loading = false;
            }
        }
    }
};
</script>
