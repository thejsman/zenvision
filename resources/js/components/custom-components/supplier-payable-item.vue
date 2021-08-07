<template>
    <div id="supplier_payable_item" class="changed_inventory">
        <div v-if="supplierPayableArray === null" class="mt-4">
            <b-spinner type="border" label="Loading..."></b-spinner>
        </div>
        <div v-else>
            <div
                class="text-left border-bottom py-2"
                v-for="spItem in supplierPayableArray"
                :key="spItem.id"
            >
                <b-dropdown variant="link" text="..." class="edit_btn">
                    <b-dropdown-item href="#" @click="editInventory(spItem)"
                        ><i class="fas fa-pencil-alt text-success mr-1" />
                        Edit
                    </b-dropdown-item>

                    <b-dropdown-item href="#" @click="deleteInventory(spItem)"
                        ><i class="fas fa-trash text-danger mr-1" />
                        Delete
                    </b-dropdown-item>
                </b-dropdown>
                <div class="d-flex justify-content-between align-items-center">
                    <div>{{ spItem.title }}</div>
                    <div class="pr-2 text-success font-weight-bold">
                        {{
                            new Intl.NumberFormat("en-US", {
                                style: "currency",
                                currency: "USD"
                            }).format(spItem.amount)
                        }}
                    </div>
                </div>
                <div class="opacity5">
                    <div v-if="spItem.type === 'shopify'">
                        {{ spItem.type }}# {{ spItem.reference_number }}
                    </div>
                    <div
                        v-if="
                            spItem.type === 'bank' || spItem.type === 'credit'
                        "
                    >
                        {{ spItem.type }}# {{ spItem.reference_number }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import { eventBus } from "../../app";
import axios from "axios";
export default {
    computed: {
        ...mapGetters(["supplierPayableArray"])
    },

    methods: {
        ...mapActions(["getSupplierPayableTotal"]),
        editInventory(item) {
            this.$bvModal.show("supplier-payable-details");
            setTimeout(() => {
                eventBus.$emit("editSupplierpayable", item);
            }, 10);
        },

        async deleteInventory(item) {
            try {
                await axios.delete(`supplierpayable/${item.id}`);
                await this.getSupplierPayableTotal();
            } catch (err) {
                console.log({ err });
            }
        }
    }
};
</script>

<style>
#supplier_payable_item .dropdown-menu {
    min-width: 5rem !important;
    padding: 0px;
    margin: 0px;
}
#supplier_payable_item .dropdown-item {
    padding: 5px 15px;
}

.opacity5 {
    opacity: 0.5;
}
.changed_inventory {
    overflow-y: auto;
    max-height: 340px;
    min-height: 130px;
}
#supplier_payable_item .btn-link {
    font-size: 1.5rem;
    line-height: 0px;
    padding: 15px 0px;
    opacity: 0.5;
}
#supplier_payable_item .btn-link:hover {
    opacity: 1;
}
</style>
