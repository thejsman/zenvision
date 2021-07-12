<template>
    <div class="flex-start">
        <div class="d-flex flex-row">
            <div
                v-for="stripeAccount in stripeAccounts"
                :key="stripeAccount.id"
                class="dropdown"
            >
                <div
                    class="border rounded p-2 mx-1 dropbtn bg-white"
                    :class="{
                        'border-primary': stripeAccount.enabled_on_dashboard
                    }"
                    @click="disableFeature ? handleClick(stripeAccount) : null"
                    v-b-tooltip.hover="stripeAccount.name"
                >
                    <img src="/images/icons/stripe-icon.svg" alt height="21" />
                </div>

                <div class="dropdown-content">
                    <a
                        href="#"
                        @click="handleClick(stripeAccount)"
                        v-if="disableFeature"
                    >
                        {{
                            stripeAccount.enabled_on_dashboard
                                ? "Disable"
                                : "Enable"
                        }}</a
                    >
                    <a href="#" @click="showMsgBoxOne(stripeAccount, $event)"
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
import { mapActions } from "vuex";
export default {
    name: "StripeAccount",
    data() {
        return {
            stripeAccounts: []
        };
    },
    props: {
        disableFeature: {
            type: Boolean,
            default: true
        }
    },
    created() {
        this.getStripeAccounts();
    },

    methods: {
        ...mapActions(["removeStripeAccount"]),
        ...mapActions({ getStripeAccountsFromStore: ["getStripeAccounts"] }),
        async getStripeAccounts() {
            try {
                const result = await axios.get("getstripeaccounts");
                this.stripeAccounts = result.data;

                result.data.length
                    ? this.checkEnabledStatus(result.data)
                    : eventBus.$emit("hasStripeAccount", false);
            } catch (err) {
                console.log(err);
                this.stripeAccounts = [];
            }
        },
        checkEnabledStatus(data) {
            const status = data.map(element => element.enabled_on_dashboard);
            status.includes(true)
                ? eventBus.$emit("hasStripeAccount", true)
                : eventBus.$emit("hasStripeAccount", false);
        },
        showMsgBoxOne(account) {
            this.boxOne = "";
            this.$bvModal
                .msgBoxConfirm(
                    "Are you sure you want to remove the Stripe account?"
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
        },
        async handleClick(account) {
            try {
                eventBus.$emit("setLoadingTrue");
                await axios.patch("stripeconnect", account);
                await this.getStripeAccounts();
                await this.getStripeAccountsFromStore();
                eventBus.$emit("setLoadingFalse");
            } catch (error) {
                eventBus.$emit("setLoadingFalse");
                console.log(error);
            }
        },

        async removeChannel(account, event) {
            try {
                eventBus.$emit("setLoadingTrue");
                eventBus.$emit("toggleShopifyStore");
                eventBus.$emit("stripeChannelRemoved", account.stripe_user_id);
                await axios.patch("stripeconnectdelete", account);
                await this.removeStripeAccount(account);
                await this.getStripeAccounts();
                eventBus.$emit("setLoadingFalse");
            } catch (error) {
                console.log(error);
            }
        }
    }
};
</script>

<style></style>
