<template>
    <div class="flex-start pl-1">
        <div class="d-flex flex-row dropdown">
            <div
                v-for="tiktokAccount in tiktokAccounts"
                :key="tiktokAccount.id"
            >
                <div
                    class="border rounded p-2 ml-2 dropbtn"
                    :class="{
                        'border-primary': tiktokAccount.enabled_on_dashboard
                    }"
                    @click="disableFeature ? handleClick(tiktokAccount) : null"
                    v-b-tooltip.hover="tiktokAccount.advertiser_name"
                >
                    <img src="/images/icons/tiktok-icon.svg" alt height="21" />
                </div>

                <div class="dropdown-content">
                    <a
                        href="#"
                        @click="handleClick(tiktokAccount)"
                        v-if="disableFeature"
                    >
                        {{
                            tiktokAccount.enabled_on_dashboard
                                ? "Disable"
                                : "Enable"
                        }}</a
                    >
                    <a href="#" @click="showMsgBoxOne(tiktokAccount, $event)"
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
    name: "TiktokAccount",
    data() {
        return {
            tiktokAccounts: []
        };
    },
    props: {
        disableFeature: {
            type: Boolean,
            default: true
        }
    },
    created() {
        this.getTiktokAccounts();
    },
    methods: {
        async getTiktokAccounts() {
            try {
                const result = await axios.get("tiktokaccount");
                this.tiktokAccounts = result.data;

                result.data.length
                    ? this.checkEnabledStatus(result.data)
                    : eventBus.$emit("hasTiktokAccount", false);
            } catch (err) {
                eventBus.$emit("hasTiktokAccount", false);
                this.tiktokAccounts = [];
            }
        },
        checkEnabledStatus(data) {
            const status = data.map(element => element.enabled_on_dashboard);
            status.includes(true)
                ? eventBus.$emit("hasTiktokAccount", true)
                : eventBus.$emit("hasTiktokAccount", false);
        },
        showMsgBoxOne(account) {
            this.boxOne = "";
            this.$bvModal
                .msgBoxConfirm(
                    "Are you sure you want to remove the Tiktok Ad account?"
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
                await axios.patch("tiktokaccount", account);
                await this.getTiktokAccounts();
                eventBus.$emit("setLoadingFalse");
            } catch (error) {
                eventBus.$emit("setLoadingFalse");
                console.log(error);
            }
        },

        async removeChannel(account, event) {
            try {
                eventBus.$emit("setLoadingTrue");
                await axios.patch("tiktokaccount-delete", account);
                await this.getTiktokAccounts();
                eventBus.$emit("setLoadingFalse");
            } catch (error) {
                eventBus.$emit("setLoadingFalse");
                console.log(error);
            }
        }
    }
};
</script>

<style></style>
