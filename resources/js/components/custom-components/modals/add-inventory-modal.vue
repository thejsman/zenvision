<template>
    <b-row>
        <b-col cols="12">
            <div
                class="font-weight-bold font-size-24 text-white mt-4 subscription-header"
            >
                Add Inventory
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mt-4 mb-4 text-white">
                        Please provide how many units you’ve purchased through
                        your supplier
                    </p>
                </div>
                <div class="app-search d-none d-lg-block">
                    <div class="position-relative">
                        <input
                            type="text"
                            class="form-control cogs_filter"
                            placeholder="Filter products"
                            @keyup="handleSearch"
                            v-model="searchText"
                        />
                        <span class="fas fa-search"></span>
                    </div>
                </div>
            </div>
        </b-col>
        <b-col cols="12">
            <div v-if="!hasShopifyStoreCS">
                <div>
                    <div class="d-flex justify-content-center mt-5 mb-5">
                        <b-alert show variant="warning"
                            >Please connect a Shopify store to manage
                            inventory</b-alert
                        >
                        <!-- <b-spinner type="border" label="Loading..."></b-spinner> -->
                    </div>
                </div>
            </div>
            <div v-else>
                <b-table
                    ref="inventoryTable"
                    selectable
                    sticky-header
                    :items="items"
                    :fields="fields"
                    responsive="sm"
                    no-border-collapse
                    head-variant="dark"
                >
                    <template v-slot:cell(cost)="row">
                        <b-input-group
                            class="cogs_cost_width"
                            :class="{ emptyInput: row.item.cost === null }"
                        >
                            <b-input-group-prepend is-text>
                                <b>$</b>
                            </b-input-group-prepend>
                            <b-form-input
                                width="20px"
                                type="number"
                                v-model="row.item.cost"
                                @input="handleCostChange(row.item)"
                                placeholder="Price"
                                min="0"
                                trim
                                size="10"
                            />
                        </b-input-group>
                    </template>

                    <template v-slot:cell(shipping_cost)="row">
                        <b-input-group
                            class="cogs_cost_width"
                            :class="{
                                emptyInput: row.item.shipping_cost === null
                            }"
                        >
                            <b-input-group-prepend is-text>
                                <b>$</b>
                            </b-input-group-prepend>
                            <b-form-input
                                type="number"
                                v-model="row.item.shipping_cost"
                                placeholder="Price"
                                min="0"
                                trim
                            />
                        </b-input-group>
                    </template>
                    <template v-slot:cell(units)="row">
                        <b-input-group
                            class="cogs_cost_width"
                            :class="{
                                emptyInput: row.item.units === null
                            }"
                        >
                            <b-form-input
                                type="number"
                                v-model="row.item.units"
                                placeholder="Units"
                                @input="handleUnitsChange(row.item)"
                                min="0"
                                trim
                            />
                        </b-input-group>
                    </template>
                    <template v-slot:cell(total_inventory)="row">
                        <b-input-group
                            class="cogs_cost_width"
                            :class="{
                                emptyInput: row.item.total_inventory === null
                            }"
                        >
                            <b-input-group-prepend is-text>
                                <b>$</b>
                            </b-input-group-prepend>
                            <b-form-input
                                type="number"
                                v-model="row.item.total_inventory"
                                placeholder="Total Inventory"
                                @input="handleInventoryChange(row.item)"
                                min="0"
                                trim
                            />
                        </b-input-group>
                    </template>
                </b-table>
                <div>
                    <b-alert
                        :show="dismissCountDown"
                        :variant="updateVariant"
                        @dismissed="dismissCountDown = 0"
                        class="mr-5"
                        >{{ updateResult }}</b-alert
                    >
                </div>
                <div
                    class="d-flex mt-4 text-muted justify-content-between align-items-center"
                >
                    <small>*Per unit</small>
                    <div>
                        <b-button
                            type="submit"
                            variant="success"
                            class="btn btn-green ml-2"
                            @click="handleClick"
                            >Add</b-button
                        >
                    </div>
                </div>
            </div>
        </b-col>
        <!-- <b-col cols="12"> </b-col> -->
    </b-row>
</template>
<script>
import axios from "axios";
import { mapGetters, mapActions } from "vuex";
import { eventBus } from "../../../app";
import _ from "lodash";

export default {
    data() {
        return {
            changedProducts: [],
            fields: [
                {
                    key: "product_title",
                    label: "Product Name",
                    tdClass: "tdcenter"
                },
                {
                    key: "color",
                    label: "Color",
                    tdClass: "tdcenter"
                },
                {
                    key: "size",
                    label: "Size",
                    tdClass: "tdcenter"
                },
                {
                    key: "cost",
                    label: "Product Cost*",

                    formatter: value => {
                        if (value !== null) {
                            return `$${value}`;
                        }
                    }
                },

                {
                    key: "units",
                    label: "Units",
                    tdClass: "tdcenter"
                },
                {
                    key: "total_inventory",
                    label: "Total Inventory",
                    tdClass: "tdcenter"
                }
            ],

            is_loading: true,
            items: [],
            preItems: [],
            searchText: "",
            showMessage: true,
            updateResult: "",
            updateVariant: "",
            dismissCountDown: 0,
            selected: [],
            bulk_cost: "",
            bulk_shipping: "",
            selectAll: false
        };
    },
    async created() {
        eventBus.$on("editInventoryText", () => {
            this.searchText = this.inventorySearchText;
            this.handleSearch();
        });
        await this.getCogsData();
    },
    computed: {
        ...mapGetters([
            "shopifyCogsArray",
            "hasShopifyStoreCS",
            "inventorySearchText"
        ])
    },
    methods: {
        ...mapActions([
            "getShopifyTotalInventory",
            "toggleLoadingStatus",
            "addToChangedProducts"
        ]),
        handleSearch() {
            this.items = this.preItems.filter(
                item =>
                    item.product_title
                        .toLowerCase()
                        .includes(this.searchText.toLowerCase()) ||
                    item.sku
                        .toLowerCase()
                        .includes(this.searchText.toLowerCase())
            );
        },
        handleUnitsChange(item) {
            this.changedProducts.push(item);
            item.total_inventory = parseFloat(item.cost * item.units).toFixed(
                2
            );
        },
        handleInventoryChange(item) {
            this.changedProducts.push(item);
            item.units = parseInt(item.total_inventory / item.cost);
        },
        handleCostChange(item) {
            this.changedProducts.push(item);
            item.total_inventory = parseFloat(item.cost * item.units);
        },
        async handleClick() {
            try {
                this.toggleLoadingStatus(true);
                this.addToChangedProducts(this.changedProducts);
                const updateTable = this.items.filter(
                    (item, index) =>
                        this.preItems[index].shipping_cost !==
                            item.shipping_cost ||
                        this.preItems[index].cost !== item.cost ||
                        this.preItems[index].units !== item.units ||
                        this.preItems[index].total_inventory !==
                            item.total_inventory
                );

                if (updateTable.length < 1) {
                    this.showAlert("No changes to update", "danger");
                } else {
                    await axios.post("cogs", updateTable);
                    this.getShopifyTotalInventory();
                    this.toggleLoadingStatus(false);
                    this.showAlert("Inventory updated succesfully", "success");

                    setTimeout(() => {
                        this.$emit("handle-close");
                    }, 2000);
                }
            } catch (error) {
                console.log(error);
                this.showAlert(
                    "An error occurred, please try again later.",
                    "danger"
                );
            }
        },

        showAlert(message, variant) {
            this.updateResult = message;
            this.updateVariant = variant;
            this.dismissCountDown = 5;
        },
        async getCogsData() {
            try {
                // const { data } = await axios.get("cogs");

                this.items = this.shopifyCogsArray;
                this.is_loading = false;
                this.preItems = JSON.parse(JSON.stringify(this.items));
            } catch (error) {
                console.log({ error });
                this.products = [];
            }
        }
    }
};
</script>

<style>
.cogs_cost_width {
    width: 125px;
}
.table-active,
.table-active > th,
.table-active > td {
    background-color: #222736 !important;
}
.tdcenter {
    vertical-align: middle !important;
}
</style>
