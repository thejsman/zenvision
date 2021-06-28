<template>
    <div>
        <div v-if="!facebookError" class="m-4 d-flex flex-column">
            <div class="font-weight-bold font-size-14 text-white">
                Facebook Ad Accounts
            </div>
            <p class="mt-4 mb-4 text-white">Please select one Ad account</p>

            <b-card-group class="flex justify-content-center" v-if="!noAccount">
                <div
                    v-for="fbdata of facebookData"
                    :key="fbdata.id"
                    class="d-flex justify-content-center d-flex flex-row"
                >
                    <b-card
                        bg-variant="light"
                        :header="`Account Name: ` + fbdata.name"
                        class="m-2 fb-card"
                    >
                        <b-card-text>
                            <p class="fb-text">
                                Currency: {{ fbdata.currency }}
                            </p>

                            <b-button
                                block
                                class="btn btn-primary"
                                :class="{ disabled: alreadyAdded(fbdata.id) }"
                                :disabled="alreadyAdded(fbdata.id)"
                                variant="primary"
                                @click="handleClick(fbdata)"
                                >{{
                                    alreadyAdded(fbdata.id)
                                        ? "Account alreay added"
                                        : "Select this account"
                                }}</b-button
                            >
                        </b-card-text>
                    </b-card>
                </div>
            </b-card-group>
            <div v-else>
                <b-alert show variant="warning" class="w-100"
                    >No Ad account found!</b-alert
                >
            </div>

            <div>
                <b-alert :show="showMessage" :variant="updateVariant">{{
                    updateResult
                }}</b-alert>
            </div>
            <b-button
                variant="primary"
                class="btn btn-cancel align-self-end"
                @click="$emit('handle-close')"
                >Cancel</b-button
            >
        </div>

        <div v-else class="m-4 d-flex flex-column">
            <p>Error fetching the details, please try later</p>
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
        facebookData: {
            type: Array,
            default: () => []
        },
        facebookError: {
            type: Boolean,
            default: false
        },
        adAccounts: {
            type: Array,
            default: () => []
        }
    },
    created() {
        if (this.facebookData.length < 1) {
            this.noAccount = true;
        }
    },
    methods: {
        async handleClick(fbData) {
            try {
                const result = await axios.post(
                    "/facebook-addadaccounts",
                    fbData
                );
                this.showMessage = true;
                this.updateVariant = "success";
                this.updateResult = "Facebook Ad account added successfully";
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
