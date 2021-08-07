<script>
import Stat from "../../components/widgets/stat-mastersheet";
import { updateData, displayCurrency } from "../../utils";
import { eventBus } from "../../app";
import _ from "lodash";
import {
    NET_EQUITY,
    TOTAL_CASH,
    TOTAL_INVENTORY,
    TOTAL_RESERVES,
    TOTAL_CREDIT_CARD,
    TOTAL_SUPPLIER_PAYABLE
} from "../../constants";

import { mapGetters, mapActions } from "vuex";
import { setLoadingSingle } from "../../utils";
export default {
    components: { Stat },
    data() {
        return {
            netEquityData: [
                {
                    icon: "bx bx-copy-alt",
                    title: NET_EQUITY,
                    value: "$0",
                    tooltip:
                        "This balance represents the total fluctuation in Net Equity between Yesterday and the day before.",
                    loading: true
                }
            ],
            statData: [
                {
                    icon: "bx bx-copy-alt",
                    title: TOTAL_CASH,
                    value: "$0",
                    loading: true
                },
                {
                    icon: "bx bx-archive-in",
                    title: TOTAL_INVENTORY,
                    value: "$0",
                    loading: true,
                    showCaret: true
                },
                {
                    icon: "bx bx-purchase-tag-alt",
                    title: TOTAL_RESERVES,
                    value: "$0",
                    loading: true
                }
            ],
            debtsData: [
                {
                    icon: "bx bx-copy-alt",
                    title: TOTAL_CREDIT_CARD,
                    value: "$0",
                    loading: true
                },
                {
                    icon: "bx bx-archive-in",
                    title: TOTAL_SUPPLIER_PAYABLE,
                    value: "$0",
                    loading: true,
                    showCaret: true,
                    tooltip:
                        "In order to get the most accurate supplier payable you will need to provide the product cost and shipping cost on the Profit Analysis page, COGS section."
                }
            ]
        };
    },

    computed: {
        ...mapGetters("MasterSheet", [
            "debtsCreditCardTotal",
            "debtsSupplierPayableTotal",
            "netEquityTotal",
            "assetsInventoryTotal",
            "assetsReservesTotal",
            "assetsCashTotal"
        ]),
        ...mapGetters(["hasShopifyStoreCS"]),
        totalAssets() {
            return displayCurrency(
                this.assetsInventoryTotal +
                    this.assetsReservesTotal +
                    this.assetsCashTotal
            );
        },
        totalDebts() {
            return displayCurrency(
                this.debtsCreditCardTotal + this.debtsSupplierPayableTotal
            );
        },
        ...mapGetters(["ShopifyCogsTotal"]),
        ...mapGetters("BankAccount", ["bankAccountBalance"])
    },

    watch: {
        debtsSupplierPayableTotal(newVal, oldVal) {
            updateData(
                this.debtsData,
                TOTAL_SUPPLIER_PAYABLE,
                displayCurrency(this.debtsSupplierPayableTotal)
            );
        },

        netEquityTotal(newVal, oldVal) {
            eventBus.$emit("netEquityTotal", this.netEquityTotal);
            updateData(
                this.netEquityData,
                NET_EQUITY,
                displayCurrency(this.netEquityTotal)
            );
        },

        debtsCreditCardTotal(newVal, oldVal) {
            updateData(
                this.debtsData,
                TOTAL_CREDIT_CARD,
                displayCurrency(this.debtsCreditCardTotal)
            );
        },
        assetsInventoryTotal(newVal, oldVal) {
            if (this.assetsInventoryTotal === null) {
                if (this.hasShopifyStoreCS) {
                    setLoadingSingle(this.statData, TOTAL_INVENTORY);
                }
            } else {
                updateData(
                    this.statData,
                    TOTAL_INVENTORY,
                    displayCurrency(this.assetsInventoryTotal)
                );
            }
        },
        assetsReservesTotal(newVal, oldVal) {
            updateData(
                this.statData,
                TOTAL_RESERVES,
                displayCurrency(this.assetsReservesTotal)
            );
        },

        assetsCashTotal(newVal, oldVal) {
            updateData(
                this.statData,
                TOTAL_CASH,
                displayCurrency(this.assetsCashTotal)
            );
        },
        ShopifyCogsTotal() {
            if (this.hasShopifyStoreCS) {
                if (this.ShopifyCogsTotal === null) {
                    setLoadingSingle(this.statData, TOTAL_SUPPLIER_PAYABLE);
                } else {
                    updateData(
                        this.debtsData,
                        TOTAL_SUPPLIER_PAYABLE,
                        displayCurrency(this.ShopifyCogsTotal)
                    );
                }
            } else {
                if (this.ShopifyCogsTotal === null) {
                    setLoadingSingle(this.statData, TOTAL_SUPPLIER_PAYABLE);
                } else {
                    updateData(
                        this.debtsData,
                        TOTAL_SUPPLIER_PAYABLE,
                        displayCurrency(this.ShopifyCogsTotal)
                    );
                }
            }
        }
    },
    async created() {
        this.getMastersheetData();
    },
    methods: {
        async getMastersheetData() {
            // console.log("check this", this.assetsInventoryTotal);
            // if (
            //     this.assetsInventoryTotal === null &&
            //     this.hasShopifyStoreCS === true
            // ) {
            //     setLoadingSingle(this.statData, "Inventory");
            // }
            setTimeout(() => {
                // updateData(
                //     this.statData,
                //     TOTAL_INVENTORY,
                //     displayCurrency(this.assetsInventoryTotal)
                // );

                updateData(
                    this.statData,
                    TOTAL_RESERVES,
                    displayCurrency(this.assetsReservesTotal)
                );

                updateData(
                    this.debtsData,
                    TOTAL_CREDIT_CARD,
                    displayCurrency(this.debtsCreditCardTotal)
                );
                // updateData(
                //     this.debtsData,
                //     TOTAL_SUPPLIER_PAYABLE,
                //     displayCurrency(this.ShopifyCogsTotal)
                // );

                eventBus.$emit("netEquityTotal", this.netEquityTotal);

                updateData(
                    this.netEquityData,
                    NET_EQUITY,
                    displayCurrency(this.netEquityTotal)
                );
                updateData(
                    this.statData,
                    TOTAL_CASH,
                    displayCurrency(this.assetsCashTotal)
                );
            }, 1000);
        }
    }
};
</script>

<template>
    <div class="px-2">
        <div class="mail-list mt-4">
            <div class="row">
                <div
                    v-for="stat of netEquityData"
                    :key="stat.icon"
                    class="col-md-12 my-2 netEquity"
                >
                    <Stat
                        :icon="stat.icon"
                        :title="stat.title"
                        :value="stat.value"
                        :loading="stat.loading"
                        :tooltip="stat.tooltip"
                    />
                </div>
            </div>
        </div>
        <div class="mail-list mt-4">
            <div class="row">
                <div class="col-xl-12">
                    <div class="d-flex justify-content-between">
                        <h4>Assets</h4>
                        <h4>{{ totalAssets }}</h4>
                    </div>
                </div>
                <div
                    v-for="stat of statData"
                    :key="stat.icon"
                    class="col-md-12 my-2"
                >
                    <Stat
                        :icon="stat.icon"
                        :title="stat.title"
                        :value="stat.value"
                        :loading="stat.loading"
                        :showCaret="stat.showCaret"
                    />
                </div>
            </div>
        </div>
        <div class="mail-list mt-4">
            <div class="row">
                <div class="col-xl-12">
                    <div class="d-flex justify-content-between">
                        <h4>Debts</h4>
                        <h4>{{ totalDebts }}</h4>
                    </div>
                </div>
                <div
                    v-for="stat of debtsData"
                    :key="stat.icon"
                    class="col-md-12 my-2"
                >
                    <Stat
                        :icon="stat.icon"
                        :title="stat.title"
                        :value="stat.value"
                        :loading="stat.loading"
                        :showCaretDebts="stat.showCaret"
                        :tooltip2="stat.tooltip"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
<style lang="scss">
.sheet-leftbar {
    background-color: #191e2c;
    border-radius: 10px;
}
.netEquity .card {
    background-color: #2f3863;
}
.netEquity .text-muted {
    color: #556ee7 !important;
}
// @media (max-width: 767px) {
//     .sheet-leftbar {
//         float: none;
//         width: 100%;
//     }
// }
</style>
