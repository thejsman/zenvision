<template>
    <div name="fade">
        <div class="">
            <SupplierPayableItem />
        </div>
        <b-button
            variant="primary"
            class="px-5 mt-4 mb-3"
            @click="showSupplierPayableModal"
            >Add</b-button
        >
        <b-modal
            id="supplier-payable-details"
            size="lg"
            centered
            hide-footer
            hide-header
        >
            <SupplierPayableModal @handle-close="handleSupplierPayableClose" />
        </b-modal>
    </div>
</template>

<script>
import SupplierPayableModal from "../../components/custom-components/modals/add-supplier-payable-modal.vue";
import { mapActions, mapGetters } from "vuex";
import SupplierPayableItem from "./supplier-payable-item.vue";

export default {
    created() {
        this.getSupplierPayable();
    },
    components: { SupplierPayableModal, SupplierPayableItem },
    computed: {
        ...mapGetters(["supplierPayableArray"])
    },
    methods: {
        ...mapActions(["getSupplierPayable"]),

        showSupplierPayableModal() {
            this.$bvModal.show("supplier-payable-details");
        },

        handleSupplierPayableClose() {
            this.$bvModal.hide("supplier-payable-details");
        }
    }
};
</script>

<style></style>
