<template>
    <div name="fade">
        <div class="">
            <InventoryItem :cogsItem="cogsItem" />
        </div>
        <b-button
            variant="primary"
            class="px-5 mt-4 mb-3"
            @click="showInventoryModal"
            >Add</b-button
        >
        <b-modal
            id="inventory-details"
            size="xl"
            centered
            hide-footer
            hide-header
        >
            <InventoryModal @handle-close="handleCogsClose" />
        </b-modal>
    </div>
</template>

<script>
import InventoryModal from "../../components/custom-components/modals/add-inventory-modal.vue";
import { mapGetters } from "vuex";
import InventoryItem from "./inventory-item.vue";
export default {
    components: { InventoryModal, InventoryItem },
    computed: {
        ...mapGetters(["shopifyCogsArray"]),
        cogsItem() {
            return this.shopifyCogsArray.find(
                cogs => cogs.total_inventory !== null
            );
        }
    },
    methods: {
        showInventoryModal() {
            this.$bvModal.show("inventory-details");
        },

        handleCogsClose() {
            this.$bvModal.hide("inventory-details");
        }
    }
};
</script>

<style></style>
