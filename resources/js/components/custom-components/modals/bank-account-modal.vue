<template>
    <div>
        <div class="account-modal">
            <div class="font-weight-bold font-size-14 text-white">
                Bank Accounts
            </div>
            <p class="mt-2 mb-4 text-white">Please select one account</p>
            <div v-if="bankError" class="d-flex flex-column">
                <b-alert show variant="danger" class="w-100">
                    Error fetching the details, please try later</b-alert
                >
            </div>
            <div v-if="!bankError">
                <div v-if="noAccount">
                    <b-alert show variant="warning" class="w-100"
                        >No account found!</b-alert
                    >
                </div>
                <div v-if="!noAccount">
                    <b-card-group
                        deck
                        v-for="account of bankAccounts"
                        :key="account.id"
                    >
                        <b-card class="mt-2">
                            <b-card-text>
                                <span class="text-muted"> Account Name: </span>
                                <span class="font-weight-bold">
                                    {{ account.name }}
                                </span>
                            </b-card-text>
                            <b-card-text>
                                <span class="text-muted">
                                    Account number:
                                </span>
                                <span class="font-weight-bold">
                                    {{ account.mask }}
                                </span>
                            </b-card-text>
                            <b-card-text>
                                <span class="text-muted"> Account Type: </span>
                                <span class="font-weight-bold">
                                    {{ account.subtype }}
                                </span>
                            </b-card-text>
                            <b-button
                                block
                                class="btn btn-primary"
                                variant="primary"
                                @click="handleClick(account)"
                                >Select</b-button
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
        bankAccounts: {
            type: Array,
            default: () => []
        },
        bankError: {
            type: Boolean,
            default: false
        },
        bankInstitution: {
            type: Object,
            default: () => {}
        },
        plaidPublicToken: {
            type: String,
            default: ""
        }
    },
    created() {
        if (this.bankAccounts.length < 1) {
            this.noAccount = true;
        }
    },
    methods: {
        async handleClick(account) {
            try {
                console.log(this.plaidPublicToken);
                account.institution_name = this.bankInstitution.name;
                account.institution_id = this.bankInstitution.institution_id;
                account.public_token = this.plaidPublicToken;

                await axios.post("/bankaccount", account);
                this.showMessage = true;
                this.updateVariant = "success";
                this.updateResult = "Account added successfully";
                setTimeout(() => {
                    this.showMessage = false;
                    this.updateVariant = "";
                    this.updateResult = "";
                    this.$emit("updateData");
                    this.$emit("handle-close");

                    eventBus.$emit("toggleShopifyStore");
                    window.location.href = "/mastersheet";
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
