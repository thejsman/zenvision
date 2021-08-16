<template>
    <div class="flex-start">
        <div class="d-flex flex-row">
            <div
                v-for="snapchatAccount in snapchatAccounts"
                :key="snapchatAccount.id"
                class="dropdown"
            >
                <div
                    class="border rounded p-2 mx-1 dropbtn bg-white channel-icons-grow"
                    :class="{
                        'border-primary': snapchatAccount.enabled_on_dashboard,
                        inactive:
                            currentChannel === 'PA'
                                ? !snapchatAccount.enabled_on_dashboard
                                : null
                    }"
                    @click="
                        disableFeature ? handleClick(snapchatAccount) : null
                    "
                    v-b-tooltip.hover="snapchatAccount.ad_account_name"
                >
                    <img
                        src="/images/icons/snapchat-icon.svg"
                        alt
                        height="21"
                    />
                </div>

                <div class="dropdown-content">
                    <a
                        href="#"
                        @click="handleClick(snapchatAccount)"
                        v-if="disableFeature"
                    >
                        {{
                            snapchatAccount.enabled_on_dashboard
                                ? "Disable"
                                : "Enable"
                        }}</a
                    >
                    <a href="#" @click="showMsgBoxOne(snapchatAccount, $event)"
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
import { mapGetters } from "vuex";

export default {
    name: "SnapchatAccount",
    data() {
        return {
            snapchatAccounts: []
        };
    },
    props: {
        disableFeature: {
            type: Boolean,
            default: true
        }
    },
    computed: { ...mapGetters(["currentChannel"]) },
    created() {
        this.getsnapchatAccounts();
    },
    methods: {
        async getsnapchatAccounts() {
            try {
                const result = await axios.get("getsnapchataccounts");
                this.snapchatAccounts = result.data;

                result.data.length
                    ? this.checkEnabledStatus(result.data)
                    : eventBus.$emit("hasSnapchatAccount", false);
            } catch (err) {
                console.log(err);
                this.snapchatAccounts = [];
            }
        },

        checkEnabledStatus(data) {
            const status = data.map(element => element.enabled_on_dashboard);
            status.includes(true)
                ? eventBus.$emit("hasSnapchatAccount", true)
                : eventBus.$emit("hasSnapchatAccount", false);
        },
        showMsgBoxOne(account) {
            this.boxOne = "";
            this.$bvModal
                .msgBoxConfirm(
                    "Are you sure you want to remove the Snapchat Ad account?"
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
                await axios.patch("snapchatadaccount", account);
                await this.getsnapchatAccounts();
                eventBus.$emit("setLoadingFalse");
            } catch (error) {
                eventBus.$emit("setLoadingFalse");
                console.log(error);
            }
        },

        async removeChannel(account, event) {
            try {
                eventBus.$emit("setLoadingTrue");
                await axios.patch("snapchatadaccount-delete", account);
                await this.getsnapchatAccounts();
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
