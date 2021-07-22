<template>
    <div class="changed_inventory" id="inventory_item">
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
            <div class="d-flex justify-content-between align-items-center">
                <div>{{ cogsItem.product_title }}</div>
                <div class="pr-2 text-success font-weight-bold">
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
#inventory_item .dropdown-menu {
    min-width: 5rem !important;
    padding: 0px;
    margin: 0px;
}
#inventory_item .dropdown-item {
    padding: 5px 15px;
}

.opacity5 {
    opacity: 0.5;
}
.changed_inventory {
    overflow-y: auto;
    max-height: 320px;
}
#inventory_item .btn-link {
    font-size: 1.5rem;
    line-height: 0px;
    padding: 15px 0px;
    opacity: 0.5;
}
#inventory_item .btn-link:hover {
    opacity: 1;
}
</style>
