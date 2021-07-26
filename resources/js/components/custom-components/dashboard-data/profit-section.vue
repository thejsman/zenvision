<template>
    <div class="row">
        <div class="col-xl-8 mt-4">
            <h3>Profit</h3>
            <div class="d-flex flex-row card profit-card">
                <div class="card-body">
                    <div class="d-flex flex-row justify-content-between">
                        <div class="media">
                            <div class="media-body">
                                <p class="text-muted font-weight-medium">
                                    Profit
                                </p>
                                <h4 class="mb-0">{{ totalProfit }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Stat from "../../widgets/stat";
import { displayCurrency } from "../../../utils";
import { mapGetters } from "vuex";

export default {
    components: { Stat },
    computed: {
        ...mapGetters([
            "shopifyRevenue",
            "shopifyDiscounts",
            "shopifyShippingRevenue",
            "ShopifyCogsTotalPA",
            "shopifyRefundTotal"
        ]),
        totalProfit() {
            return displayCurrency(
                this.shopifyRevenue +
                    this.shopifyShippingRevenue -
                    this.shopifyDiscounts -
                    this.ShopifyCogsTotalPA -
                    this.shopifyRefundTotal
            );
        }
    }
};
</script>
