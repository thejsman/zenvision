<template>
    <div>
        {{ hasShopifyStorePA }}
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
import { mapGetters, mapActions } from "vuex";
import { NUMBER_OF_ORDERS } from "../../../constants";

export default {
    components: { Stat },
    data() {
        return {
            data: {
                id: 1,
                title: NUMBER_OF_ORDERS,
                value: "0",
                loading: true,
                toolTip:
                    "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
            }
        };
    },
    computed: {
        ...mapGetters(["hasShopifyStorePA", "numberOfOrders"])
    },
    methods: {
        ...mapActions(["getNumberOfOrders"])
    },
    watch: {
        hasShopifyStorePA() {
            if (this.hasShopifyStorePA) {
                this.getNumberOfOrders();
            } else {
                this.data.loading = false;
                this.data.value = "-";
            }
        },
        numberOfOrders(newVal, oldVal) {
            this.data.loading = false;
            this.data.value = `${newVal}`;
        }
    }
};
</script>

<style></style>p
