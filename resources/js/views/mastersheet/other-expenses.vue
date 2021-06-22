<template>
    <div>
        <b-tab active>
            <template v-slot:title>
                <span class="d-inline-block d-sm-none">
                    <i class="fas fa-home"></i>
                </span>
                <span class="d-none d-sm-inline-block">Other Expenses</span>
            </template>
            <div class="row align-items-center">
                <div class="col-sm-12 col-md-10">
                    <form class="app-search d-none d-lg-block">
                        <div class="position-relative">
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Filter transactions"
                            />
                            <span class="bx bx-search-alt"></span>
                        </div>
                    </form>
                </div>
                <div class="col-sm-12 col-md-2">
                    <b-dropdown
                        variant="primary"
                        class="m-2"
                        right
                        text="Right align"
                    >
                        <template v-slot:button-content>
                            Tags ({{ tagsLenght }})
                            <i class="fas fa-angle-down pl-1"></i>
                        </template>
                        <b-dropdown-form>
                            <div class="chip-container align-items-center">
                                <b-form-group @submit.stop.prevent>
                                    <b-form-input
                                        id="dropdown-form-email"
                                        class="text-center text-white chip-input mr-1"
                                        v-model="currentInput"
                                        size="sm"
                                        placeholder="Add tags"
                                        @keypress.enter="saveChip"
                                        @keydown.delete="backspaceDelete"
                                    ></b-form-input>
                                </b-form-group>
                                <div
                                    class="chip"
                                    v-for="(chip, i) of chips"
                                    :key="chip.label"
                                >
                                    {{ chip }}
                                    <i
                                        class="fas fa-times"
                                        @click="deleteChip(i)"
                                    />
                                </div>
                            </div>

                            <b-dropdown-text class="mt-4"
                                ><span
                                    v-b-tooltip.hover="
                                        'Zenvision automatically tags transactions that we believe are Other Expenses.   Please note that while this may not be 100% accurate for you and your business, we recommend that you leave the default settings as is unless you are certain that these transactions are not Other Expenses'
                                    "
                                    >What are tags?</span
                                >
                            </b-dropdown-text>
                        </b-dropdown-form>
                    </b-dropdown>
                </div>
            </div>
        </b-tab>

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
                            v-if="noTransactions"
                            class="d-flex justify-content-center"
                        >
                            <h3>
                                No transactions to show.
                            </h3>
                        </div>
                        <div
                            v-else
                            v-for="(i, index) in itemsList"
                            :key="index"
                        >
                            <p class="p-2 bg-light sticky">{{ i }}</p>
                            <div
                                v-for="item in groupedTransactions[i]"
                                :key="item.id"
                            >
                                <div
                                    class="d-flex flex-row justify-content-start mb-1"
                                >
                                    <img
                                        v-if="item.type === 'paypal'"
                                        src="/images/icons/paypal-transactions.svg"
                                        alt
                                        height="30"
                                        width="30"
                                        class="channel-icons ml-3"
                                    />
                                    <img
                                        v-if="item.type === 'stripe'"
                                        src="/images/icons/stripe-icon.svg"
                                        alt
                                        height="30"
                                        width="30"
                                        class="channel-icons ml-3"
                                    />
                                    <img
                                        v-if="item.type === 'bank'"
                                        src="/images/bank-icons/Chase.svg"
                                        alt
                                        height="30"
                                        width="30"
                                        class="channel-icons ml-3 bg-white rounded"
                                    />

                                    <div
                                        class="d-flex justify-content-between flex-fill align-items-center"
                                    >
                                        <p class="pl-3">
                                            {{ item.description }}
                                        </p>
                                        <p class="pr-3">
                                            {{ item.amount }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-5 pt-5">
                <p class="text-center text-white">
                    Add a <strong>Bank Account, Credit Card </strong> or
                    <strong>Paypal </strong> to see transactions
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
export default {
    props: {
        set: {
            type: Boolean,
            default: true
        }
    },
    created() {
        eventBus.$on("stripeChannelRemoved", accountId => {
            const filteredStripeTransactions = this.allTransactions.filter(
                e => e.stripe_user_id === accountId
            );
            var ordered = {};
            if (filteredStripeTransactions.length > 0) {
                //Group all transaction by date
                var groups = _.groupBy(filteredStripeTransactions, function(
                    transaction
                ) {
                    return transaction.date;
                });

                _(groups)
                    .keys()
                    .sort()
                    .each(function(key) {
                        ordered[key] = groups[key];
                    })
                    .reverse();

                this.items = ordered;
            } else {
                this.noTransactions = true;
                this.items = ordered;
            }
        });
    },
    computed: {
        tagsLenght() {
            return this.chips.length;
        },
        itemsList() {
            return this.items;
        }
    },

    data() {
        return {
            loading: false,
            message: "loading",
            stripePaginatedUrl: "getStripeTransactions",
            items: [],
            allTransactions: [],
            groupedTransactions: [],
            noTransactions: true,
            bankTransactionsLoaded: true,
            bankTransactionsArray: [],
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
                this.loadMore();
            }
        });

        // Initially load some items.
        this.loadMore();
    },
    methods: {
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
        async getBankAccountTransactions() {
            try {
                const result = await axios.get("bankaccount-transactions");
                const data = result.data;

                return data;
            } catch (err) {
                console.log(err);
                return [];
            }
        },
        async getTransactions() {
            this.loading = true;
            // const paypal = await this.getPaypalTransactions();
            const stripe = await axios.get(this.stripePaginatedUrl);

            const stripeData = stripe.data.data;

            if (
                stripe.data.current_page === 1 &&
                stripe.data.data.length === 0
            ) {
                this.noTransactions = true;
                this.loading = false;

                return;
            }
            if (
                stripe.data.current_page !== 1 &&
                stripe.data.next_page_url === null
            ) {
                return;
            } else {
                this.stripePaginatedUrl = stripe.data.next_page_url;
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
            if (stripeData !== undefined) {
                this.noTransactions = false;
                stripeData.forEach(s =>
                    this.allTransactions.push({
                        type: "stripe",
                        id: s.id,
                        date: moment(s.created).format("LL"),
                        description: s.description,
                        amount: displayCurrency(s.gross)
                    })
                );
            }

            if (this.bankTransactionsLoaded) {
                const bankTransactions = await this.getBankAccountTransactions();

                if (bankTransactions.length > 0) {
                    this.noTransactions = false;
                    this.bankTransactionsArray = [...bankTransactions];
                    const arrayLenght = this.bankTransactionsArray.length;
                    if (arrayLenght > 20) {
                        const tempArray = this.bankTransactionsArray.splice(
                            0,
                            20
                        );
                        tempArray.forEach(bt =>
                            this.allTransactions.push({
                                type: "bank",
                                id: bt.transaction_id,
                                date: moment(bt.date).format("LL"),
                                description: bt.name,
                                amount: displayCurrency(Math.abs(bt.amount))
                            })
                        );
                    } else {
                        this.bankTransactionsArray.forEach(bt =>
                            this.allTransactions.push({
                                type: "bank",
                                id: bt.transaction_id,
                                date: moment(bt.date).format("LL"),
                                description: bt.name,
                                amount: displayCurrency(Math.abs(bt.amount))
                            })
                        );
                    }
                }
                this.bankTransactionsLoaded = false;
            } else {
                const arrayLenght = this.bankTransactionsArray.length;
                if (arrayLenght > 20) {
                    const tempArray = this.bankTransactionsArray.splice(0, 20);
                    tempArray.forEach(bt =>
                        this.allTransactions.push({
                            type: "bank",
                            id: bt.transaction_id,
                            date: moment(bt.date).format("LL"),
                            description: bt.name,
                            amount: displayCurrency(Math.abs(bt.amount))
                        })
                    );
                } else {
                    this.bankTransactionsArray.forEach(bt =>
                        this.allTransactions.push({
                            type: "bank",
                            id: bt.transaction_id,
                            date: moment(bt.date).format("LL"),
                            description: bt.name,
                            amount: displayCurrency(Math.abs(bt.amount))
                        })
                    );
                }
            }

            //Group all transaction by date
            var groups = _.groupBy(this.allTransactions, function(transaction) {
                return transaction.date;
            });
            this.groupedTransactions = groups;
            console.log({ groups });
            var ordered = [];

            const keys = _.keys(groups);

            const sortedKeys = keys.sort(function(a, b) {
                return new Date(b) - new Date(a);
            });
            console.log({ sortedKeys });
            sortedKeys.forEach(key => {
                ordered.push({ "`${key}`": groups[key] });
            });
            console.log({ ordered });
            this.items = sortedKeys;

            this.loading = false;
        },
        async loadMore() {
            await this.getTransactions();
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
.app-search .form-control {
    background: #32384c;
}
.app-search span {
    line-height: 48px;
}
.chip-container {
    width: 450px;
    min-height: 34px;
    display: flex;
    flex-wrap: wrap;
    align-content: space-between;
    align-items: center;

    .chip {
        margin: 7px;
        background: #ffaa61;
        padding: 10px 15px;
        font-weight: bold;
        color: white;
        border-radius: 5px;
        display: flex;
        align-items: center;
        i {
            cursor: pointer;
            opacity: 0.56;
            margin-left: 8px;
        }
    }
}
.chip-input {
    width: 120px;
    //   height: 40px;
    margin-top: 15px !important;
    border: 2px dashed #989ba5;
    padding: 4px;
    border-radius: 10px;
    background-color: transparent;
}
.list-group {
    overflow: auto;
    height: 400px;
    width: 1100px;
}
.list-group p {
    font-size: 16px;
}
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
</style>
