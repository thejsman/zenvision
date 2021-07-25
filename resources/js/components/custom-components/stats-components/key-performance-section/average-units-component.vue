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
import { mapGetters, mapActions } from "vuex";
import { AVERAGE_UNITS_PER_ORDER } from "../../../../constants";

export default {
    components: { Stat },
    data() {
        return {
            data: {
                id: 1,
                title: AVERAGE_UNITS_PER_ORDER,
                value: "0",
                loading: true,
                toolTip:
                    "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
            }
        };
    },
    computed: {
        ...mapGetters(["hasShopifyStorePA", "shopifyAverageUnitsPerOrder"])
    },
    methods: {
        ...mapActions(["getAverageUnitsCount"])
    },
    watch: {
        async hasShopifyStorePA() {
            if (this.hasShopifyStorePA) {
                await this.getAverageUnitsCount();
                this.data.value = `${this.shopifyAverageUnitsPerOrder}`;
                this.data.loading = false;
            } else {
                this.data.loading = false;
                this.data.value = "-";
            }
        },
        shopifyAverageUnitsPerOrder(newVal, oldVal) {
            this.data.value = `${newVal}`;
            this.data.loading = false;
        }
    }
};
</script>

<style></style>p
