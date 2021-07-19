<template>
    <div name="fade">
        <b-button variant="primary" class="px-5" @click="showInventoryModal"
            >Add</b-button
        >
        <b-modal id="cogs-details" size="xl" centered hide-footer hide-header>
            <InventoryModal @handle-close="handleCogsClose" />
        </b-modal>
    </div>
</template>

<script>
import InventoryModal from "../../components/custom-components/modals/add-inventory-modal.vue";
export default {
    components: { InventoryModal },
    methods: {
        showInventoryModal() {
            this.$bvModal.show("cogs-details");
        },

        handleCogsClose() {
            this.$bvModal.hide("cogs-details");
        }
    }
};
</script>

<style></style>
