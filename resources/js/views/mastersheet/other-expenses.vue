<template>
    <div>
        <!-- <b-tab active>
            <template v-slot:title>
                <span class="d-inline-block d-sm-none">
                    Other Expenses
                </span>
                <span class="d-none d-sm-inline-block">Other Expenses</span>
            </template>
        </b-tab> -->

        <div class="row">
            <div class="col-md-12 mt-3">
                <div class="list-group-wrapper">
                    <transition name="fade">
                        <div class="loading" v-show="loading">
                            <span class="fa fa-spinner fa-spin"></span>
                            {{ message }}
                        </div>
                    </transition>
                    <div class="list-group" id="infinite-list">
                        <div
                            v-if="!loading && noTransactions && noAccount"
                            class="d-flex flex-column justify-content-center w-100 mt-5"
                        >
                            <div v-if="!noAccount">
                                <p class="text-center">
                                    No bank/credit card account connected.
                                </p>
                            </div>
                            <div v-else>
                                <p class="text-center">
                                    No transactions in between
                                    <span class="font-weight-bold">
                                        {{ transEndDate }}</span
                                    >
                                    and
                                    <span class="font-weight-bold">{{
                                        transStartDate
                                    }}</span
                                    >, load next 14 days transactions
                                </p>

                                <div class="text-center">
                                    <b-button
                                        type="submit"
                                        variant="primary"
                                        class="my-2"
                                        @click="loadMoreTransactions"
                                        >Load more</b-button
                                    >
                                </div>
                            </div>
                        </div>

                        <div
                            v-else
                            v-for="(i, index) in itemsList"
                            :key="index"
                        >
                            <p class="p-2 bg-light sticky mb-0">{{ i }}</p>
                            <div
                                v-for="item in groupedTransactions[i]"
                                :key="item.id"
                            >
                                <TransactionItem :item="item" />
                            </div>
                        </div>
                        <div
                            class="text-center"
                            v-if="!loading && !noTransactions"
                        >
                            <b-button
                                type="submit"
                                variant="primary"
                                class="my-2"
                                @click="loadMoreTransactions"
                                >Load more</b-button
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="col mt-3 pt-3">
                <p class="text-center text-white">
                    Add a <strong>Bank Account, Credit Card, Stripe</strong> to
                    see transactions
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { displayCurrency } from "../../utils";
import moment from "moment";
import { eventBus } from "../../app";
import { mapGetters, mapActions } from "vuex";
import TransactionItem from "./transactions-item.vue";
export default {
    components: { TransactionItem },
    props: {
        set: {
            type: Boolean,
            default: true
        }
    },
    created() {
        this.getBankTransactions();
        eventBus.$on("stripeChannelRemoved", accountId => {
            this.loading = true;

            const filteredStripeTransactions = this.allTransactions.filter(
                e => e.type !== "stripe" && e.stripe_user_id !== accountId
            );

            this.allTransactions = [...filteredStripeTransactions];
            if (filteredStripeTransactions.length > 0) {
                //Group all transaction by date

                var groups = _.groupBy(filteredStripeTransactions, function(
                    transaction
                ) {
                    return transaction.date;
                });
                this.groupedTransactions = groups;

                var ordered = [];

                const keys = _.keys(groups);

                const sortedKeys = keys.sort(function(a, b) {
                    return new Date(b) - new Date(a);
                });

                sortedKeys.forEach(key => {
                    ordered.push({ "`${key}`": groups[key] });
                });

                this.items = sortedKeys;
                if (this.items.length === 0) {
                    this.nextTransButton = true;
                }
                this.loading = false;
            } else {
                this.noTransactions = true;
                this.items = ordered;
                this.loading = false;
            }
        });
        eventBus.$on("bankAccountRemoved", accountId => {
            this.loading = true;
            const filteredTransactions = this.allTransactions.filter(
                e => e.account_id !== accountId && e.type === "bank"
            );

            this.allTransactions = [...filteredTransactions];
            if (filteredTransactions.length > 0) {
                //Group all transaction by date

                var groups = _.groupBy(filteredTransactions, function(
                    transaction
                ) {
                    return transaction.date;
                });
                this.groupedTransactions = groups;

                var ordered = [];

                const keys = _.keys(groups);

                const sortedKeys = keys.sort(function(a, b) {
                    return new Date(b) - new Date(a);
                });

                sortedKeys.forEach(key => {
                    ordered.push({ "`${key}`": groups[key] });
                });

                this.items = sortedKeys;
                if (this.items.length === 0) {
                    this.nextTransButton = true;
                }
                this.loading = false;
            } else {
                this.noTransactions = true;
                this.items = ordered;
                this.loading = false;
            }
        });
    },
    computed: {
        ...mapGetters("BankAccount", [
            "bankAccounts",
            "bankLogos",
            "bankTransactions"
        ]),
        ...mapGetters([
            "hasShopifyStoreCS",
            "hasStripeAccountCS",
            "hasBankAccountCS",
            "stripeTransactions",
            "transStartDate",
            "transEndDate",
            "supplierPayableArray"
        ]),
        transactionArray() {
            if (this.supplierPayableArray) {
                return this.supplierPayableArray
                    .map(sp => sp.reference_number)
                    .filter(e => e);
            } else {
                return [];
            }
        },
        noAccount() {
            console.log(this.hasStripeAccountCS || this.hasBankAccountCS);
            return this.hasStripeAccountCS || this.hasBankAccountCS;
        },
        tagsLenght() {
            return this.chips.length;
        },
        itemsList() {
            return this.items;
        }
    },

    data() {
        return {
            loading: true,
            message: "loading",
            selected: null,
            options: [
                {
                    value: null,
                    text: "Label",
                    disabled: true
                },
                { value: "supplier_payable", text: "Supplier Payable" }
            ],
            items: [],
            allTransactions: [],
            groupedTransactions: [],
            noTransactions: true,
            bankTransactionsLoaded: true,
            bankTransactionsArray: [],
            nextTransButton: false,
            chips: [
                "Shopify",
                "Facebook",
                "Zendrop",
                "Stripe",
                "Paypal",
                "Google",
                "Oberlo",
                "AliExpress"
            ],
            currentInput: ""
        };
    },
    mounted() {
        const listElm = document.querySelector("#infinite-list");

        listElm.addEventListener("scroll", e => {
            if (
                listElm.scrollTop + listElm.clientHeight >=
                listElm.scrollHeight
            ) {
                // this.loadMore();
            }
        });

        // Initially load some items.
        setTimeout(() => {
            this.loadMore();
        }, 3000);
    },
    methods: {
        ...mapActions("BankAccount", ["getBankTransactions"]),

        ...mapActions(["getStripeTransactions", "setNextDatesForTransactions"]),

        saveChip(e) {
            e.preventDefault();
            const { chips, currentInput, set } = this;
            ((set && chips.indexOf(currentInput) === -1) || !set) &&
                chips.push(currentInput);
            this.currentInput = "";
        },
        deleteChip(index) {
            this.chips.splice(index, 1);
        },
        backspaceDelete({ which }) {
            which == 8 &&
                this.currentInput === "" &&
                this.chips.splice(this.chips.length - 1);
        },
        async getPaypalTransactions() {
            try {
                const result = await axios.get("paypaltransactions");
                const data = result.data.reverse();
                const items = data.map(d => d.transaction_info);
                return items;
            } catch (err) {
                console.log(err);
                return [];
            }
        },
        loadMoreTransactions() {
            this.setNextDatesForTransactions();

            this.getTransactions();
        },
        async getTransactions() {
            this.nextTransButton = false;

            this.loading = true;

            if (this.hasStripeAccountCS) {
                await this.getStripeTransactions();
                if (this.stripeTransactions.length > 0) {
                    this.noTransactions = false;
                    this.stripeTransactions.forEach(s =>
                        this.allTransactions.push({
                            type: "stripe",
                            id: s.id,
                            stripe_user_id: s.stripe_user_id,
                            date: moment(s.created).format("LL"),
                            description: s.description,
                            amount: displayCurrency(s.gross)
                        })
                    );
                }
            }

            // paypal.forEach(p =>
            //     allTransactions.push({
            //         type: "paypal",
            //         id: p.transaction_id,
            //         date: moment(p.transaction_initiation_date).format("LL"),
            //         description: this.showName(p),
            //         amount: p.transaction_amount.value
            //     })
            // );

            if (this.hasBankAccountCS) {
                await this.getBankTransactions();

                if (this.bankTransactions.length > 0) {
                    this.noTransactions = false;
                    this.bankTransactions.forEach(bt => {
                        this.allTransactions.push({
                            type: this.bankAccounts.find(
                                bank => bank.bank_user_id === bt.account_id
                            ).bank_type,
                            id: bt.transaction_id,
                            date: moment(bt.date).format("LL"),
                            description: bt.name,
                            amount: displayCurrency(Math.abs(bt.amount)),
                            logo: this.bankLogos.find(
                                o => o.bank_user_id === bt.account_id
                            ).institution_id,
                            account_id: bt.account_id
                        });
                    });
                }
            }

            //Group all transaction by date
            var groups = _.groupBy(this.allTransactions, function(transaction) {
                return transaction.date;
            });
            this.groupedTransactions = groups;

            var ordered = [];

            const keys = _.keys(groups);

            const sortedKeys = keys.sort(function(a, b) {
                return new Date(b) - new Date(a);
            });

            sortedKeys.forEach(key => {
                ordered.push({ "`${key}`": groups[key] });
            });

            this.items = sortedKeys;
            if (this.items.length === 0) {
                this.nextTransButton = true;
            }
            this.loading = false;
        },
        async loadMore() {
            this.getTransactions();
        },
        showName(item) {
            if (item.hasOwnProperty("transaction_subject")) {
                return item.transaction_subject;
            } else if (item.hasOwnProperty("transaction_note")) {
                return item.transaction_note;
            } else return "Transaction";
        }
    }
};
</script>

<style lang="scss">
.loading {
    text-align: center;
    position: absolute;
    color: #fff;
    z-index: 9;
    font-size: 16px;
    padding: 8px 18px;
    border-radius: 5px;
    left: calc(50% - 45px);
    top: calc(50% - 18px);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter,
.fade-leave-to {
    opacity: 0;
}
::-webkit-scrollbar {
    width: 12px;
}

::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(200, 200, 200, 1);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    border-radius: 10px;
    background-color: #32394e;
    -webkit-box-shadow: inset 0 0 6px rgba(90, 90, 90, 0.7);
}

.sticky {
    position: sticky;
    top: 0;
    width: 100%;
}
.list-group-wrapper {
    overflow: auto;
    height: 562px;
}
.label_class {
    width: 100px;
    margin-right: 44px;
}
</style>
