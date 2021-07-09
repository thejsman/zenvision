<script>
import Stat from "../../components/widgets/stat-mastersheet";
import {
    updateData,
    displayCurrency,
    setLoading,
    setLoadingSingle
} from "../../utils";
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
import axios from "axios";

import { mapGetters, mapActions } from "vuex";

export default {
    components: { Stat },
    data() {
        return {
            showModal: false,

            totalInventory: 0,
            totalCash: 0,
            totalReserves: 0,
            totalCreditCard: 0,
            totalSupplierPayable: 0,
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
                    loading: true
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
                    loading: true
                }
            ]
        };
    },

    computed: {
        ...mapGetters("MasterSheet", [
            "debtsCreditCardTotal",
            "debtsSupplierPayableTotal",
            "netEquityTotal",
            "assetsReservesTotal",
            "assetsCashTotal"
        ]),
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
        }
    },
    async created() {
        eventBus.$on("stripeChannelRemoved", async () => {
            await this.loadAllChannels();
        });
        eventBus.$on("toggleShopifyStore", () => {
            setTimeout(async () => {
                await this.loadAllChannels();
                setLoading(this.statData);
                setLoading(this.netEquityData);
                this.getMastersheetData();

                this.getBankAccountBalance();
            }, 1000);
        });
        this.getMastersheetData();

        await this.getBankAccountBalance();
    },
    methods: {
        ...mapActions("MasterSheet", ["loadAllChannels"]),
        ...mapActions("BankAccount", ["getBankAccountBalance"]),

        async getMastersheetData() {
            setLoadingSingle(this.netEquityData, NET_EQUITY);

            setTimeout(() => {
                updateData(this.statData, TOTAL_INVENTORY, displayCurrency(0));

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
                updateData(
                    this.debtsData,
                    TOTAL_SUPPLIER_PAYABLE,
                    displayCurrency(this.ShopifyCogsTotal)
                );

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
            }, 7000);
        }
    }
};
</script>

<template>
    <div class="sheet-leftbar card">
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
                    <h4>Assets</h4>
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
                    />
                </div>
            </div>
        </div>
        <div class="mail-list mt-4">
            <div class="row">
                <div class="col-xl-12">
                    <h4>Debts</h4>
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
                    />
                </div>
            </div>
        </div>
    </div>
</template>
<style lang="scss">
.sheet-leftbar {
    width: 400px;
    float: left;
    padding: 20px;
    border-radius: 5px;
    background-color: #191e2c;
    border-radius: 10px;
}
.netEquity .card {
    background-color: #2f3863;
}
.netEquity .text-muted {
    color: #556ee7 !important;
}
@media (max-width: 767px) {
    .sheet-leftbar {
        float: none;
        width: 100%;
    }
}
</style>
