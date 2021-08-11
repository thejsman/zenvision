<template>
    <div>
        <div class="account-modal">
            <div class="font-weight-bold font-size-14 text-white">
                Google Ad Accounts
            </div>
            <p class="mt-2 mb-4 text-white">Please select one Ad account</p>
            <div v-if="googleError" class="d-flex flex-column">
                <b-alert show variant="danger" class="w-100">
                    Error fetching the details, please try later</b-alert
                >
            </div>
            <div v-if="!googleError">
                <div v-if="noAccount">
                    <b-alert show variant="warning" class="w-100"
                        >No Ad account found!</b-alert
                    >
                </div>
                <div v-if="!noAccount">
                    <b-card-group
                        deck
                        v-for="googleAdAccount of googleData"
                        :key="googleAdAccount"
                    >
                        <b-card class="mt-2">
                            <b-card-text>
                                <span class="text-muted">Customer Id:</span>
                                <span class="font-weight-bold">
                                    {{
                                        googleAdAccount.replace(
                                            "customers/",
                                            ""
                                        )
                                    }}
                                </span>
                            </b-card-text>

                            <b-button
                                block
                                class="btn btn-primary"
                                :class="{
                                    disabled: alreadyAdded(googleAdAccount)
                                }"
                                :disabled="alreadyAdded(googleAdAccount)"
                                variant="primary"
                                @click="handleClick(googleAdAccount)"
                                >{{
                                    alreadyAdded(googleAdAccount)
                                        ? "Account alreay added"
                                        : "Select"
                                }}</b-button
                            >
                        </b-card>
                    </b-card-group>
                </div>
                <div class="mt-2">
                    <b-alert :show="showMessage" :variant="updateVariant">{{
                        updateResult
                    }}</b-alert>
                </div>
                <b-button
                    block
                    class="btn btn-cancel text-center"
                    @click="handleCancel"
                    >Cancel</b-button
                >
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";
import { eventBus } from "../../../app";

export default {
    data() {
        return {
            showMessage: true,
            updateResult: "",
            updateVariant: "",
            noAccount: false
        };
    },
    props: {
        googleData: {
            type: Array,
            default: () => []
        },
        googleError: {
            type: Boolean,
            default: false
        },
        adAccounts: {
            type: Array,
            default: () => []
        }
    },
    created() {
        if (this.googleData.length < 1) {
            this.noAccount = true;
        }
    },
    methods: {
        async handleClick(adaccount) {
            try {
                const result = await axios.post("google-connect", {
                    ad_account_id: adaccount
                });
                this.showMessage = true;
                this.updateVariant = "success";
                this.updateResult = "Google Ad account added successfully";
                setTimeout(() => {
                    this.showMessage = false;
                    this.updateVariant = "";
                    this.updateResult = "";
                    this.$emit("updateData");
                    this.$emit("handle-close");
                    eventBus.$emit("toggleShopifyStore");
                    window.location.href = "/";
                }, 2000);
            } catch (error) {
                console.log(error);
                this.showMessage = true;
                this.updateVariant = "danger";
                this.updateResult = "Something went wrong, please try later";
                setTimeout(() => {
                    this.showMessage = false;
                    this.updateVariant = "";
                    this.updateResult = "";
                    this.$emit("handle-close");
                }, 2000);
            }
        },
        alreadyAdded(id) {
            return this.adAccounts.some(
                account => account.ad_account_id === id
            );
        },
        handleCancel() {
            window.location.href = "/";
        }
    }
};
</script>
<style>
button:disabled {
    cursor: not-allowed;
    pointer-events: all !important;
}
</style>
