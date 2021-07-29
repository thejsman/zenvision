<template>
    <b-row>
        <b-col cols="12">
            <div
                class="font-weight-bold font-size-24 text-white mt-4 subscription-header"
            >
                {{ supplierModalTitle }}
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
            <div>
                <div>
                    <b-form @submit="onSubmit">
                        <b-form-group id="input-group-1" label-for="title">
                            <b-form-input
                                id="title"
                                v-model.trim="form.title"
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
                                type="number"
                                step="0.01"
                                v-model.trim="form.amount"
                                placeholder="Amount"
                                required
                            ></b-form-input>
                        </b-input-group>
                        <div v-if="isEditing">
                            <b-button
                                variant="success"
                                class="btn btn-green ml-2 mt-4"
                                @click="updateSupplierPayable"
                                >Save</b-button
                            >
                        </div>
                        <div v-else>
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
            updateVariant: "",
            isEditing: false
        };
    },
    async created() {
        eventBus.$on("editSupplierpayable", item => {
            this.handleFormEdit(item);
        });
    },
    computed: {
        ...mapGetters([
            "shopifyCogsArray",
            "hasShopifyStoreCS",
            "inventorySearchText"
        ]),
        supplierModalTitle() {
            return this.isEditing
                ? "Edit supplier payable"
                : "Add supplier payables";
        }
    },
    methods: {
        ...mapActions(["getSupplierPayable", "toggleLoadingStatus"]),
        async onSubmit(e) {
            e.preventDefault();
            console.log(this.form);
            try {
                if (this.form.title === "" || this.form.amount === "") {
                    this.showAlert("All fields are mandatory", "warning");
                    return;
                }
                await axios.post("supplierpayable", { ...this.form });
                this.showAlert(
                    "Supplier payable added successfully",
                    "success"
                );
                this.getSupplierPayable();
                this.reset();
            } catch (err) {
                this.showAlert(
                    "Something went wrong, please try after sometime.",
                    "danger"
                );
                console.log({ err });
            }
        },
        handleFormEdit(item) {
            this.isEditing = true;
            this.form = { ...item };
        },
        async updateSupplierPayable(e) {
            try {
                e.preventDefault();
                await axios.patch("supplierpayable", { ...this.form });
                this.showAlert(
                    "Supplier payable upaded successfully",
                    "success"
                );
                this.getSupplierPayable();
                this.reset();
                setTimeout(() => {
                    this.handleSupplierPayableClose();
                }, 2000);
            } catch (err) {
                console.log({ err });
                this.showAlert(
                    "Something went wrong, please try after sometime",
                    "danger"
                );
                this.reset();
                setTimeout(() => {
                    this.handleSupplierPayableClose();
                }, 2000);
            }
        },

        handleSupplierPayableClose() {
            this.$bvModal.hide("supplier-payable-details");
        },
        showAlert(message, variant) {
            this.updateResult = message;
            this.updateVariant = variant;
            this.dismissCountDown = 5;
        },
        reset() {
            this.form.title = "";
            this.form.amount = "";
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
