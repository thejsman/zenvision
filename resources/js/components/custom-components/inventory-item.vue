<template>
    <div class="changed_inventory">
        <div
            class="text-left border-bottom py-2"
            v-for="cogsItem in inventoryChangedProducts"
            :key="cogsItem.variant_id"
        >
            <b-dropdown variant="link" text="..." class="edit_btn">
                <b-dropdown-item href="#" @click="editInventory(cogsItem)"
                    ><i class="fas fa-pencil-alt text-success mr-1" />
                    Edit
                </b-dropdown-item>

                <b-dropdown-item href="#" @click="deleteInventory(cogsItem)"
                    ><i class="fas fa-trash text-danger mr-1" />
                    Delete
                </b-dropdown-item>
            </b-dropdown>
            <div class="d-flex justify-content-between">
                <div>{{ cogsItem.product_title }}</div>
                <div class="mr-2">
                    {{
                        new Intl.NumberFormat("en-US", {
                            style: "currency",
                            currency: "USD"
                        }).format(cogsItem.total_inventory)
                    }}
                </div>
            </div>
            <div class="opacity5">
                {{ cogsItem.sku }}
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
        ...mapGetters(["inventoryChangedProducts"])
    },
    methods: {
        ...mapActions(["removeItemfromChangedProducts"]),
        editInventory(item) {
            setTimeout(() => {
                eventBus.$emit("editInventoryText", item.sku);
            }, 100);
            this.$bvModal.show("inventory-details");
        },

        async deleteInventory(item) {
            try {
                await axios.patch("inventory", item);
                this.removeItemfromChangedProducts(item);
            } catch (err) {
                console.log({ err });
            }
        }
    }
};
</script>

<style>
.opacity5 {
    opacity: 0.5;
}
.changed_inventory {
    overflow-y: scroll;
    max-height: 320px;
}
.edit_btn {
    z-index: 99;
}
</style>
