<template>
    <div class="col-md-4 mt-xs-4">
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
import { mapGetters, mapActions } from "vuex";
import { ABANDONED_CART } from "../../../../constants";

export default {
    components: { Stat },
    data() {
        return {
            data: {
                id: 1,
                title: ABANDONED_CART,
                value: "0",
                loading: true,
                toolTip:
                    "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
            }
        };
    },
    computed: {
        ...mapGetters(["hasShopifyStorePA", "shopifyAbandonedCartCount"])
    },
    methods: {
        ...mapActions(["getAbandonedCartCount"])
    },
    watch: {
        async hasShopifyStorePA() {
            if (this.hasShopifyStorePA) {
                await this.getAbandonedCartCount();
                this.data.loading = false;
                this.data.value = `${this.shopifyAbandonedCartCount}`;
            } else {
                this.data.loading = false;
                this.data.value = "-";
            }
        },
        shopifyAbandonedCartCount(newVal, oldVal) {
            this.data.loading = false;
            this.data.value = `${newVal}`;
        }
    }
};
</script>

<style></style>p
