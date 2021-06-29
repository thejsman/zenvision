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
            "debtsSupplierPayableTotal",
            "netEquityTotal",
            "assetsCashTotal"
        ]),
        ...mapGetters(["cogsTotal", "stripeAccountsBalance", "storeBalance"])
    },
    props: {
        orders: {
            type: Array,
            default: () => []
        }
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
                displayCurrency(
                    this.netEquityTotal + this.totalCash + this.storeBalance
                )
            );
        },
        storeBalance(newVal, oldVal) {
            console.log(newVal, oldVal);
            updateData(
                this.statData,
                TOTAL_CASH,
                displayCurrency(this.totalCash + this.storeBalance)
            );
            updateData(
                this.netEquityData,
                NET_EQUITY,
                displayCurrency(
                    this.netEquityTotal + this.totalCash + this.storeBalance
                )
            );
        }
    },
    created() {
        eventBus.$on("stripeChannelRemoved", () => {
            this.totalCash = 0;
            setTimeout(() => {
                this.getMastersheetData();
                this.getStripeBalance();
                this.getStripeTransactions();
                this.getBankAccountBalance();
            }, 1000);
        });
        eventBus.$on("toggleShopifyStore", () => {
            this.totalCash = 0;
            setTimeout(async () => {
                await this.loadAllChannels();
                setLoading(this.statData);
                setLoading(this.netEquityData);
                this.getMastersheetData();
                this.getStripeBalance();
                this.getStripeTransactions();
                this.getBankAccountBalance();
            }, 1000);
        });
        this.getMastersheetData();
        this.getStripeBalance();
        this.getStripeTransactions();
        this.getBankAccountBalance();
    },
    methods: {
        ...mapActions("MasterSheet", ["loadAllChannels"]),
        async getBankAccountBalance() {
            setLoadingSingle(this.statData, TOTAL_CASH);

            const result = await axios.get("bankaccount-balance");
            const balance = result.data;
            this.totalCash += parseFloat(balance);
            updateData(
                this.statData,
                TOTAL_CASH,
                displayCurrency(this.totalCash + this.storeBalance)
            );
        },

        async getMastersheetData() {
            setLoadingSingle(this.netEquityData, NET_EQUITY);
            const assets = await axios.get("mastersheetdata");
            const cogs = _.sumBy(this.orders, order =>
                parseFloat(order.total_cost)
            );

            const {
                total_credit_card,
                total_inventory,
                total_reserves,
                total_supplier_payable
            } = assets.data;

            updateData(
                this.statData,
                TOTAL_INVENTORY,
                displayCurrency(total_inventory)
            );
            this.totalReserves = total_reserves;
            updateData(
                this.statData,
                TOTAL_RESERVES,
                displayCurrency(total_reserves)
            );

            const debts = await axios.get("msdebts");
            const { debts_credit_card, debts_supplier_payable } = debts.data;
            updateData(
                this.debtsData,
                TOTAL_CREDIT_CARD,
                displayCurrency(debts_credit_card)
            );
            setTimeout(() => {
                updateData(
                    this.debtsData,
                    TOTAL_SUPPLIER_PAYABLE,
                    displayCurrency(this.cogsTotal)
                );
            }, 1000);

            const netEquityTotal =
                this.totalCash +
                total_credit_card +
                total_inventory +
                total_reserves +
                total_supplier_payable -
                debts_credit_card -
                debts_supplier_payable -
                this.cogsTotal;
            eventBus.$emit("netEquityTotal", this.netEquityTotal);

            updateData(
                this.netEquityData,
                NET_EQUITY,
                displayCurrency(
                    this.netEquityTotal + this.totalCash + this.storeBalance
                )
            );
        },
        async getStripeBalance() {
            try {
                const result = await axios.get("stripeaccount-balance");

                const stripeData = result.data;

                let stripeBalance = 0;
                if (stripeData.length) {
                    stripeData.forEach(element => {
                        stripeBalance += parseFloat(element.amount / 100);
                    });
                }

                this.totalCash += stripeBalance;
            } catch (err) {
                console.log(err);
                return [];
            }
        },
        async getStripeTransactions() {
            try {
                const result = await axios.get("getStripeTransactions");
                const data = result.data;
                return data;
            } catch (err) {
                console.log(err);
                return [];
            }
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
