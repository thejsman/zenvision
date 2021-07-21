<template>
    <div name="fade">
        <div class="border-bottom text-left pb-2">
            <b-dropdown variant="link" text="...">
                <b-dropdown-item href="#" @click="editSubscription(row)"
                    ><i class="fas fa-pencil-alt text-success mr-1" />
                    Edit
                </b-dropdown-item>

                <b-dropdown-item href="#" @click="deleteSubscription(row)"
                    ><i class="fas fa-trash text-danger mr-1" />
                    Delete
                </b-dropdown-item>
            </b-dropdown>
            <div class="d-flex justify-content-between">
                <div>{{ cosgsItem.product_title }}</div>
                <div>
                    {{
                        new Intl.NumberFormat("en-US", {
                            style: "currency",
                            currency: "USD"
                        }).format(cosgsItem.total_inventory)
                    }}
                </div>
            </div>
            <div class="footer-link">
                {{ cosgsItem.sku }}
            </div>
        </div>
        <b-button
            variant="primary"
            class="px-5 mt-4"
            @click="showInventoryModal"
            >Add</b-button
        >
        <b-modal id="cogs-details" size="xl" centered hide-footer hide-header>
            <InventoryModal @handle-close="handleCogsClose" />
        </b-modal>
        <b-modal id="cogs-details2" size="xl" centered hide-footer hide-header>
            <InventoryModal @handle-close="handleCogsClose" :showedit="true" />
        </b-modal>
    </div>
</template>

<script>
import InventoryModal from "../../components/custom-components/modals/add-inventory-modal.vue";
import { mapGetters } from "vuex";

export default {
    components: { InventoryModal },
    computed: {
        ...mapGetters(["shopifyCogsArray"]),
        cosgsItem() {
            return this.shopifyCogsArray.find(
                cogs => cogs.total_inventory !== null
            );
        }
    },
    methods: {
        handleCogsEdit() {
            alert("Show modal here");
        },
        showInventoryModal() {
            this.$bvModal.show("cogs-details");
        },
        showInventoryModal2() {
            this.$bvModal.show("cogs-details2");
        },

        handleCogsClose() {
            this.$bvModal.hide("cogs-details");
        }
    }
};
</script>

<style></style>
