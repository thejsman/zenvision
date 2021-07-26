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
import { mapGetters, mapActions } from "vuex";
export default {
    name: "StripeAccount",
    // data() {
    //     return {
    //         stripeAccounts: []
    //     };
    // },
    props: {
        disableFeature: {
            type: Boolean,
            default: true
        }
    },
    created() {
        this.getStripeTransactions();
    },
    computed: {
        ...mapGetters(["stripeAccounts", "currentChannel"])
    },
    methods: {
        ...mapActions([
            "removeStripeAccount",
            "getStripeAccounts",
            "getStripeAccountsPA",
            "toggleLoadingStatus",
            "getStripeTransactions"
        ]),

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
                this.toggleLoadingStatus(true);
                await axios.patch("stripeconnect", account);
                if (this.currentChannel === "PA") {
                    await this.getStripeAccountsPA();
                    eventBus.$emit("stripeAccountToggleStatus");
                } else {
                    await this.getStripeAccounts();
                }

                this.toggleLoadingStatus(false);
            } catch (error) {
                this.toggleLoadingStatus(false);
                console.log(error);
            }
        },

        async removeChannel(account, event) {
            try {
                this.toggleLoadingStatus(true);

                eventBus.$emit("toggleShopifyStore");
                eventBus.$emit("stripeChannelRemoved", account.stripe_user_id);

                await this.removeStripeAccount(account);

                this.toggleLoadingStatus(false);
            } catch (error) {
                this.toggleLoadingStatus(false);
                console.log(error);
            }
        }
    }
};
</script>

<style></style>
