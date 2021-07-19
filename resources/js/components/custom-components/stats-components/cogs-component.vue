<template>
    <Stat
        :title="data.title"
        :value="data.value"
        :loading="data.loading"
        :onClick="data.onClick"
        :totalSubscriptionCount="data.totalSubscriptionCount"
        :showIcon="exclamationIconStatus"
        :iconName="data.iconName"
        :channelIcon="data.channelIcon"
        :toolTip="data.toolTip"
    />
</template>

<script>
import Stat from "../../widgets/stat";
import { mapGetters, mapActions } from "vuex";
import CogsModal from "../modals/CogsDetails-modal.vue";
import { COGS_TOTAL } from "../../../constants";
import { displayCurrency } from "../../../utils";

export default {
    components: { CogsModal, Stat },
    data() {
        return {
            data: {
                id: 1,
                title: COGS_TOTAL,
                value: `0`,
                loading: true,
                onClick: this.handleCogsClick,
                iconName: "exclamation-icon.svg",
                showIcon: false,
                toolTip:
                    "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
            }
        };
    },
    computed: {
        ...mapGetters([
            "hasShopifyStorePA",
            "exclamationIconStatus",
            "ShopifyCogsTotalPA",
            "shopifyOrders"
        ])
    },
    methods: {
        ...mapActions(["getShopifyCogsTotalPA"])
    },
    watch: {
        hasShopifyStorePA() {
            if (this.hasShopifyStorePA) {
            } else {
                this.data.loading = false;
                this.data.value = "-";
            }
        },
        ShopifyCogsTotalPA() {
            this.data.value = displayCurrency(this.ShopifyCogsTotalPA);
        },
        shopifyOrders(newVal, oldVal) {
            console.log("Value changed shopifyOrders", newVal, oldVal);
            this.getShopifyCogsTotalPA();
            this.data.loading = false;
            this.data.value = displayCurrency(this.ShopifyCogsTotalPA);
        }
    }
};
</script>

<style></style>
