<template>
    <div name="fade">
        <b-button variant="primary" class="px-5" @click="showInventoryModal"
            >Add</b-button
        >
        <b-modal id="cogs-details" size="xl" centered hide-footer hide-header>
            <CogsModal @handle-close="handleCogsClose" />
        </b-modal>
    </div>
</template>

<script>
import CogsModal from "../../components/custom-components/modals/CogsDetails-modal.vue";
export default {
    components: { CogsModal },
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
