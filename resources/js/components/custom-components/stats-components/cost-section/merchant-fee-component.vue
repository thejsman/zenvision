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
import { mapGetters, mapActions } from "vuex";
import { MERCHANT_FEE } from "../../../../constants";
import { displayCurrency } from "../../../../utils";
export default {
    components: { Stat },
    data() {
        return {
            data: {
                id: 1,
                title: MERCHANT_FEE + "2",
                value: `0`,
                loading: true,
                toolTip:
                    "Please note that there is a high volume of transaction history that drives this balance.  Accordingly, this information may be delayed by serval minutes"
            }
        };
    },
    computed: {
        ...mapGetters([
            "hasShopifyStorePA",
            "hasStripeAccountCS",
            "stripeTransactions",
            "dateRangeS",
            "stripeFirstLoad"
        ]),
        anyActiveChannel() {
            return this.hasShopifyStorePA || this.hasStripeAccountCS;
        },
        totalChargeback() {
            return this.stripeChargebackTotal;
        }
    },
    methods: {
        ...mapActions(["stripeTransactions", "setMerchantFeeTotal"])
    },
    watch: {
        anyActiveChannel(newVal, oldVal) {
            if (!newVal) {
                this.data.value = "-";
                this.data.loading = false;
            } else {
                this.data.value = displayCurrency(
                    `${this.stripeChargebackTotal}`
                );
                this.data.loading = false;
            }
        },
        stripeChargebackTotal() {
            if (this.anyActiveChannel) {
                if (this.stripeChargebackTotal !== null) {
                    this.data.value = displayCurrency(
                        `${this.stripeChargebackTotal}`
                    );
                    this.data.loading = false;
                }
            } else {
                this.data.value = "-";
                this.data.loading = false;
            }
        },
        async hasStripeAccountCS() {
            if (this.hasStripeAccountCS) {
                await this.getStripeChargeBack();
            }
        },
        async dateRangeS() {
            this.data.loading = true;
            if (this.hasStripeAccountCS) {
                await this.getStripeChargeBack();
            }

            this.data.loading = false;
        }
    }
};
</script>
