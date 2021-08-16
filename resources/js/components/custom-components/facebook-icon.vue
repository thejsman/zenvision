<template>
    <div class="flex-start">
        <div class="d-flex flex-row">
            <div
                v-for="facebookAccount in facebookAccounts"
                :key="facebookAccount.id"
                class="dropdown"
            >
                <div
                    class="border rounded p-2 dropbtn bg-white mx-1 channel-icons-grow"
                    :class="{
                        'border-primary': facebookAccount.enabled_on_dashboard,
                        inactive:
                            currentChannel === 'PA'
                                ? !facebookAccount.enabled_on_dashboard
                                : null
                    }"
                    @click="
                        disableFeature ? handleClick(facebookAccount) : null
                    "
                    v-b-tooltip.hover="facebookAccount.ad_account_name"
                >
                    <img
                        src="/images/icons/facebook-icon.svg"
                        alt
                        height="21"
                    />
                </div>

                <div class="dropdown-content">
                    <a
                        href="#"
                        @click="handleClick(facebookAccount)"
                        v-if="disableFeature"
                    >
                        {{
                            facebookAccount.enabled_on_dashboard
                                ? "Disable"
                                : "Enable"
                        }}</a
                    >
                    <a href="#" @click="showMsgBoxOne(facebookAccount, $event)"
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
    name: "FacebookAccount",
    data() {
        return {
            facebookAccounts: []
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
        this.getfacebookAccounts();
    },
    methods: {
        async getfacebookAccounts() {
            try {
                const result = await axios.get("getfacebookaccounts");
                this.facebookAccounts = result.data;
                result.data.length
                    ? this.checkEnabledStatus(result.data)
                    : eventBus.$emit("hasFacebookAccount", false);
            } catch (err) {
                console.log(err);
                eventBus.$emit("hasFacebookAccount", false);
                this.facebookAccounts = [];
            }
        },
        checkEnabledStatus(data) {
            const status = data.map(element => element.enabled_on_dashboard);
            status.includes(true)
                ? eventBus.$emit("hasFacebookAccount", true)
                : eventBus.$emit("hasFacebookAccount", false);
        },
        showMsgBoxOne(account) {
            this.boxOne = "";
            this.$bvModal
                .msgBoxConfirm(
                    "Are you sure you want to remove the Facebook Ad account?"
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
                await axios.patch("fbconnect", account);
                await this.getfacebookAccounts();
                eventBus.$emit("setLoadingFalse");
            } catch (error) {
                eventBus.$emit("setLoadingFalse");
                console.log(error);
            }
        },

        async removeChannel(account, event) {
            try {
                eventBus.$emit("setLoadingTrue");
                await axios.patch("fbconnectdelete", account);
                await this.getfacebookAccounts();
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
