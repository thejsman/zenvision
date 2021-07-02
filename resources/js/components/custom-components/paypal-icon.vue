<template>
    <div class="flex-start pl-2">
        <div class="d-flex flex-row">
            <div
                v-for="account in ppAccounts"
                :key="account.id"
                class="dropdown"
            >
                <div
                    class="border rounded p-2  dropbtn ml-2 bg-white"
                    :class="{ 'border-primary': account.enabled_on_dashboard }"
                    @click="disableFeature ? handleClick(account) : null"
                    v-b-tooltip.hover="account.name"
                >
                    <img src="/images/icons/paypal.png" alt height="21" />
                </div>
                <div class="dropdown-content">
                    <a
                        href="#"
                        @click="handleClick(account)"
                        v-if="disableFeature"
                    >
                        {{
                            account.enabled_on_dashboard ? "Disable" : "Enable"
                        }}</a
                    >
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
export default {
    data() {
        return { ppAccounts: [] };
    },
    props: { disableFeature: { type: Boolean, default: true } },
    created() {
        this.getPaypalAccounts();
    },
    methods: {
        async getPaypalAccounts() {
            try {
                const result = await axios.get("getpaypalaccounts");
                this.ppAccounts = result.data;

                result.data.length
                    ? this.checkEnabledStatus(result.data)
                    : eventBus.$emit("hasPaypalAccount", false);
            } catch (err) {
                console.log(err);
                eventBus.$emit("hasPaypalAccount", false);
                this.ppAccounts = [];
            }
        },
        checkEnabledStatus(data) {
            const status = data.map(element => element.enabled_on_dashboard);
            status.includes(true)
                ? eventBus.$emit("hasPaypalAccount", true)
                : eventBus.$emit("hasPaypalAccount", false);
        },
        showMsgBoxOne(account) {
            this.boxOne = "";
            this.$bvModal
                .msgBoxConfirm(
                    "Are you sure you want to remove the Paypal account?"
                )
                .then(value => {
                    this.boxOne = value;
                    if (value) {
                        this.removeChannel(account);
                    }
                })
                .catch(err => {});
        },
        async handleClick(account) {
            this.$emit("togglePaypalAccount", account.id);
            try {
                eventBus.$emit("setLoadingTrue");
                await axios.patch("paypal", account);
                await this.getPaypalAccounts();
                eventBus.$emit("setLoadingFalse");
            } catch (error) {
                console.log(error);
                eventBus.$emit("setLoadingFalse");
            }
        },
        async removeChannel(account, event) {
            try {
                eventBus.$emit("setLoadingTrue");
                await axios.patch("paypaldelete", account);
                await this.getPaypalAccounts();
                eventBus.$emit("setLoadingFalse");
            } catch (error) {
                eventBus.$emit("setLoadingFalse");
                console.log(error);
                this.ppAccounts = [];
            }
        }
    }
};
</script>
