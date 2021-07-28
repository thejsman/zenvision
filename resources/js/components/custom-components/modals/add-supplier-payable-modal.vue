<template>
    <b-row>
        <b-col cols="12">
            <div
                class="font-weight-bold font-size-24 text-white mt-4 subscription-header"
            >
                Add supplier payables
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mt-4 mb-4 text-white">
                        Include how much you have paid your supplier.
                    </p>
                    <p class="mt-4 mb-4 text-white">
                        This is a manual adjustment. Instead we suggest that you
                        label the actual supplier payment transactions in the
                        transactions section as a "Supplier Payable”.
                    </p>
                </div>
            </div>
        </b-col>
        <b-col cols="12">
            <div v-if="!hasShopifyStoreCS">
                <div>
                    <div class="d-flex justify-content-center mt-5 mb-5">
                        <b-alert show variant="warning"
                            >Please connect a Shopify store to manage
                            inventory</b-alert
                        >
                        <!-- <b-spinner type="border" label="Loading..."></b-spinner> -->
                    </div>
                </div>
            </div>
            <div v-else>
                <div>
                    <b-form @submit="onSubmit">
                        <b-form-group id="input-group-1" label-for="title">
                            <b-form-input
                                id="title"
                                v-model="form.title"
                                type="text"
                                placeholder="Title"
                                class="width-1"
                                required
                            ></b-form-input>
                        </b-form-group>

                        <b-input-group
                            id="input-group-2"
                            label-for="amount"
                            class="width-2"
                        >
                            <b-input-group-prepend is-text>
                                <b>$</b>
                            </b-input-group-prepend>
                            <b-form-input
                                id="amount"
                                v-model="form.amount"
                                placeholder="Amount"
                                required
                            ></b-form-input>
                        </b-input-group>
                        <div>
                            <b-button
                                type="submit"
                                variant="success"
                                class="btn btn-green ml-2 mt-4"
                                >Add</b-button
                            >
                        </div>
                    </b-form>
                </div>
                <div>
                    <b-alert
                        :show="dismissCountDown"
                        :variant="updateVariant"
                        @dismissed="dismissCountDown = 0"
                        class="mr-5 mt-4"
                        >{{ updateResult }}</b-alert
                    >
                </div>
                <div
                    class="d-flex mt-4 text-muted justify-content-between align-items-start"
                ></div>
            </div>
        </b-col>
    </b-row>
</template>
<script>
import axios from "axios";
import { mapGetters, mapActions } from "vuex";
import { eventBus } from "../../../app";
import _ from "lodash";

export default {
    data() {
        return {
            form: {
                title: "",
                amount: ""
            },
            dismissCountDown: 0,
            show: true,
            showMessage: true,
            updateResult: "",
            updateVariant: ""
        };
    },
    async created() {
        eventBus.$on("editInventoryText", () => {
            this.searchText = this.inventorySearchText;
            this.handleSearch();
        });
    },
    computed: {
        ...mapGetters([
            "shopifyCogsArray",
            "hasShopifyStoreCS",
            "inventorySearchText"
        ])
    },
    methods: {
        ...mapActions([
            "getShopifyTotalInventory",
            "toggleLoadingStatus",
            "addToChangedProducts"
        ]),
        async onSubmit(e) {
            e.preventDefault();
            try {
                await axios.post("supplierpayable", { ...this.form });
                this.showAlert(
                    "Supplier payable added successfully",
                    "success"
                );
            } catch (err) {
                console.log({ err });
            }
        },
        showAlert(message, variant) {
            this.updateResult = message;
            this.updateVariant = variant;
            this.dismissCountDown = 5;
        }
    }
};
</script>

<style>
.width-1 {
    width: 600px;
}

.width-2 {
    width: 300px;
}
.btn-green {
    width: 100px;
}
</style>
