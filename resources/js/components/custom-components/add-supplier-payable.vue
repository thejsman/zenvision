<template>
    <div name="fade">
        <div class="">
            <SupplierPayableItem :cogsItems="cogsItems" />
        </div>
        <b-button
            variant="primary"
            class="px-5 mt-4 mb-3"
            @click="showInventoryModal"
            >Add</b-button
        >
        <b-modal
            id="inventory-details"
            size="lg"
            centered
            hide-footer
            hide-header
        >
            <SupplierPayableModal @handle-close="handleCogsClose" />
        </b-modal>
    </div>
</template>

<script>
import SupplierPayableModal from "../../components/custom-components/modals/add-supplier-payable-modal.vue";
import { mapGetters } from "vuex";
import SupplierPayableItem from "./supplier-payable-item.vue";
import { Axis } from "highcharts";
export default {
    data() {
        return {
            cogsItems: []
        };
    },
    created() {
        this.getSupplierPayable();
    },
    components: { SupplierPayableModal, SupplierPayableItem },
    computed: {
        ...mapGetters(["shopifyCogsArray"])
    },
    methods: {
        showInventoryModal() {
            this.$bvModal.show("inventory-details");
        },

        handleCogsClose() {
            this.$bvModal.hide("inventory-details");
        },
        async getSupplierPayable() {
            try {
                const { data } = await axios.get("supplierpayable");
                console.log({ data });
                this.cogsItems = data;
            } catch (error) {}
        }
    }
};
</script>

<style></style>
