<template>
    <div>
        <div class="account-modal">
            <div class="font-weight-bold font-size-14 text-white">
                Snapchat Ad Accounts
            </div>
            <p class="mt-2 mb-4 text-white">Please select one Ad account</p>
            <div v-if="snapchatError" class="d-flex flex-column">
                <b-alert show variant="danger" class="w-100">
                    Error fetching the details, please try later</b-alert
                >
            </div>
            <div v-if="!snapchatError">
                <div v-if="noAccount">
                    <b-alert show variant="warning" class="w-100"
                        >No Ad account found!</b-alert
                    >
                </div>
                <div v-if="!noAccount">
                    <b-card-group
                        deck
                        v-for="snapchatAdAccount of snapchatData"
                        :key="snapchatAdAccount.id"
                    >
                        <b-card class="mt-2">
                            <b-card-text>
                                <span class="text-muted"> Account Name: </span>
                                <span class="font-weight-bold">
                                    {{ snapchatAdAccount.adaccount.name }}
                                </span>
                            </b-card-text>
                            <b-card-text>
                                <span class="text-muted"> Currency: </span>
                                <span class="font-weight-bold">
                                    {{ snapchatAdAccount.adaccount.currency }}
                                </span>
                            </b-card-text>
                            <b-card-text>
                                <span class="text-muted"> Status: </span>
                                <span class="font-weight-bold">
                                    {{ snapchatAdAccount.adaccount.status }}
                                </span>
                            </b-card-text>
                            <b-button
                                block
                                class="btn btn-primary"
                                :class="{
                                    disabled: alreadyAdded(
                                        snapchatAdAccount.adaccount.id
                                    )
                                }"
                                :disabled="
                                    alreadyAdded(snapchatAdAccount.adaccount.id)
                                "
                                variant="primary"
                                @click="
                                    handleClick(snapchatAdAccount.adaccount)
                                "
                                >{{
                                    alreadyAdded(snapchatAdAccount.id)
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
                    @click="$emit('handle-close')"
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
        snapchatData: {
            type: Array,
            default: () => []
        },
        snapchatError: {
            type: Boolean,
            default: false
        },
        adAccounts: {
            type: Array,
            default: () => []
        }
    },
    created() {
        if (this.snapchatData.length < 1) {
            this.noAccount = true;
        }
    },
    methods: {
        async handleClick(adaccount) {
            try {
                const result = await axios.post(
                    "/snapchatadaccount",
                    adaccount
                );
                this.showMessage = true;
                this.updateVariant = "success";
                this.updateResult = "Snapchat Ad account added successfully";
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
        }
    }
};
</script>
<style>
.fb-card {
    min-width: 500px;
}
.fb-text,
.card-header {
    font-size: 15px;
    font-weight: 400;
    color: white;
}
button:disabled {
    cursor: not-allowed;
    pointer-events: all !important;
}
</style>
