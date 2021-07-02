<template>
    <div class="flex-start pl-2">
        <div class="d-flex flex-row">
            <div
                v-for="account in bankAccounts"
                :key="account.id"
                class="dropdown"
            >
                <div
                    class="border rounded p-2 dropbtn border-primary bg-white  ml-2"
                    v-b-tooltip.hover="bankName(account)"
                >
                    <img
                        :src="
                            `/images/bank-icons/${account.institution_id}.png`
                        "
                        alt
                        height="21"
                    />
                </div>
                <div class="dropdown-content">
                    <a href="#" @click="showMsgBoxOne(account, $event)"
                        >Remove</a
                    >
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";
import { eventBus } from "../../app";
import { mapGetters, mapActions } from "vuex";
export default {
    name: "BankAccountIcon",
    data() {
        return {
            // bankAccounts: []
        };
    },
    computed: {
        ...mapGetters("BankAccount", ["bankAccounts", "bankLogos"]),
        bankName() {
            return account =>
                this.titleCase(
                    `${account.bank_name} - ${account.bank_subtype}`
                );
        }
    },
    props: {
        disableFeature: {
            type: Boolean,
            default: true
        }
    },
    created: function() {
        this.getBankAccounts();
    },
    methods: {
        ...mapActions("BankAccount", ["getBankAccounts", "removeBankAccount"]),

        titleCase(str) {
            str = str.toLowerCase().split(" ");
            for (var i = 0; i < str.length; i++) {
                str[i] = str[i].charAt(0).toUpperCase() + str[i].slice(1);
            }
            return str.join(" ");
        },
        async handleClick(store) {
            try {
                await axios.patch("shopifystore", store);
                eventBus.$emit("toggleShopifyStore");
                this.getBankAccounts();
            } catch (error) {
                console.log(error);
            }
        },
        async removeChannel(account, event) {
            try {
                // eventBus.$emit("removeShopifyaccount", account.id);
                await axios.patch("bankaccountdelete", account);
                await this.removeBankAccount(account);
                this.getBankAccounts();
                eventBus.$emit("bankAccountRemoved", account.bank_user_id);
                eventBus.$emit("toggleShopifyStore");
            } catch (error) {
                console.log(error);
            }
        },
        showMsgBoxOne(account) {
            this.boxOne = "";
            this.$bvModal
                .msgBoxConfirm(
                    "Are you sure you want to remove this account? Please note that removing accounts can cause misleading financial data. We encourage you to include all of your E-Com business financial accounts and Shopify stores in Zenvision."
                )
                .then(value => {
                    this.boxOne = value;

                    if (value) {
                        this.removeChannel(account);
                    }
                })
                .catch(err => {
                    // An error occurred
                });
        }
    }
};
</script>
