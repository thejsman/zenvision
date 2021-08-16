<template>
    <div class="flex-start">
        <div class="d-flex flex-row">
            <div
                v-for="googleAccount in googleAccounts"
                :key="googleAccount.id"
                class="dropdown"
            >
                <div
                    class="border rounded p-2 mx-1 dropbtn bg-white channel-icons-grow"
                    :class="{
                        'border-primary': googleAccount.enabled_on_dashboard,
                        inactive:
                            currentChannel === 'PA'
                                ? !googleAccount.enabled_on_dashboard
                                : null
                    }"
                    @click="disableFeature ? handleClick(googleAccount) : null"
                    v-b-tooltip.hover="googleAccount.ad_account_id"
                >
                    <img src="/images/icons/google-icon.svg" alt height="21" />
                </div>

                <div class="dropdown-content">
                    <a
                        href="#"
                        @click="handleClick(googleAccount)"
                        v-if="disableFeature"
                    >
                        {{
                            googleAccount.enabled_on_dashboard
                                ? "Disable"
                                : "Enable"
                        }}</a
                    >
                    <a href="#" @click="showMsgBoxOne(googleAccount, $event)"
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
    name: "googleAccount",
    data() {
        return {
            googleAccounts: []
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
        this.getgoogleAccounts();
    },
    methods: {
        async getgoogleAccounts() {
            try {
                const result = await axios.get("google-connect-getaccounts");
                this.googleAccounts = result.data;
                result.data.length
                    ? this.checkEnabledStatus(result.data)
                    : eventBus.$emit("hasGoogleAccount", false);
            } catch (err) {
                console.log(err);
                this.googleAccounts = [];
            }
        },
        checkEnabledStatus(data) {
            const status = data.map(element => element.enabled_on_dashboard);
            status.includes(true)
                ? eventBus.$emit("hasGoogleAccount", true)
                : eventBus.$emit("hasGoogleAccount", false);
        },
        showMsgBoxOne(account) {
            this.boxOne = "";
            this.$bvModal
                .msgBoxConfirm(
                    "Are you sure you want to remove the Google account?"
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
                await axios.patch("google-connect", account);
                await this.getgoogleAccounts();
                eventBus.$emit("setLoadingFalse");
            } catch (error) {
                eventBus.$emit("setLoadingFalse");
                console.log(error);
            }
        },

        async removeChannel(account, event) {
            try {
                eventBus.$emit("setLoadingTrue");
                await axios.patch("google-connect-delete", account);
                await this.getgoogleAccounts();
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
