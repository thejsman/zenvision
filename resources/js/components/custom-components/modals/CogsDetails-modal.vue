<template>
  <b-row>
    <b-col cols="12">
      <div class="font-weight-bold font-size-24 text-white mt-4 subscription-header">COGS</div>
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <p class="mt-4 mb-4 text-white">
            Please enter the
            <strong>Cost of Goods Sold</strong> for one unit of each product
            listed below:
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
      <div v-if="is_loading">
        <div>
          <div class="d-flex justify-content-center mt-5 mb-5">
            <b-spinner type="border" label="Loading..."></b-spinner>
          </div>
        </div>
      </div>
      <div v-else>
        <b-table
          @row-selected="onRowSelected"
          ref="cogsTable"
          selectable
          sticky-header
          :items="items"
          :fields="fields"
          responsive="sm"
          head-variant="dark"
        >
          <template v-slot:head(selected)>
            <b-checkbox
              checked="false"
              v-model="selectAll"
              @change="handleSelectAll"
            />
          </template>

          <template v-slot:cell(selected)="{ rowSelected }">
            <template v-if="rowSelected">
              <b-checkbox checked="true" @click="onRowSelected" />
            </template>
            <template v-else>
              <b-checkbox checked="false" />
            </template>
          </template>

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
              :class="{ emptyInput: row.item.shipping_cost === null }"
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
        </b-table>
      </div>
      <div>
        <b-alert
          :show="dismissCountDown"
          :variant="updateVariant"
          @dismissed="dismissCountDown = 0"
          class="mr-5"
          >{{ updateResult }}</b-alert
        >
      </div>
    </b-col>
    <b-col cols="12">
      <div class="d-flex mt-4 justify-content-between flex-cloumn">
        <div>
          <div
            v-if="selected.length > 0"
            class="d-flex flex-row justify-content-between"
          >
            <input
              class="form-control cogs_cost_width"
              type="number"
              v-model="bulk_cost"
              name="bulk_cost"
              id="bulk_cost"
              placeholder="Product Cost"
            />

            <input
              class="form-control cogs_cost_width ml-2"
              type="number"
              v-model="bulk_shipping"
              name="bulk_shipping_cost"
              id="bulk_shipping_cost"
              placeholder="Shipping Cost"
            />
            <b-button
              variant="primary"
              class="btn ml-2 border-0"
              @click="handleBulkUpdate"
              >Bulk update pricing</b-button
            >
          </div>
        </div>

        <div>
          <b-button
            variant="cancel"
            class="btn btn-cancel border-0"
            @click="$emit('handle-close')"
            >Cancel</b-button
          >
          <b-button
            type="submit"
            variant="green"
            class="btn btn-green ml-2"
            @click="handleClick"
            >Confirm</b-button
          >
        </div>
      </div>
    </b-col>
  </b-row>
</template>
<script>
import axios from "axios";
import { eventBus } from "../../../app";

export default {
  data() {
    return {
      fields: [
        {
          key: "selected",
          label: "selected",
          tdClass: "tdcenter",
        },
        {
          key: "product_title",
          label: "Product Name",
          tdClass: "tdcenter",
        },
        {
          key: "color",
          label: "Color",
          tdClass: "tdcenter",
        },
        {
          key: "size",
          label: "Size",
          tdClass: "tdcenter",
        },
        {
          key: "sku",
          label: "SKU",
          tdClass: "tdcenter",
        },
        {
          key: "sales_price",
          label: "Sale Price",
          tdClass: "tdcenter",
          formatter: (value) => {
            if (value !== null) {
              return `$${value}`;
            }
          },
        },

        {
          key: "cost",
          label: "Product Cost (Per unit)",

          formatter: (value) => {
            if (value !== null) {
              return `$${value}`;
            }
          },
        },
        {
          key: "shipping_cost",
          label: "Shipping Cost (Per unit)",
        },
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
      selectAll: false,
    };
  },
  async created() {
    await this.getCogsData();
  },
  methods: {
    handleSelectAll() {
      this.selectAll ? this.clearSelected() : this.selectAllRows();
    },
    handleSearch() {
      this.items = this.preItems.filter(
        (item) =>
          item.product_title
            .toLowerCase()
            .includes(this.searchText.toLowerCase()) ||
          item.sku.toLowerCase().includes(this.searchText.toLowerCase())
      );
    },
    handleBulkUpdate() {
      if (this.selected.length < 2) {
        this.showAlert("Please select at least two rows", "danger");
      } else if (this.bulk_cost <= 0 || this.bulk_shipping <= 0) {
        this.showAlert(
          "Product Cost & shipping Cost must be provided",
          "danger"
        );
      } else {
        this.selected.map((item) => {
          item.cost = this.bulk_cost;
          item.shipping_cost = this.bulk_shipping;
        });
        //clean up
        this.bulk_cost = "";
        this.bulk_shipping = "";
        this.selectAll = false;
        this.clearSelected();
      }
    },
    async handleClick() {
      try {
        const updateTable = this.items.filter(
          (item, index) =>
            this.preItems[index].shipping_cost !== item.shipping_cost ||
            this.preItems[index].cost !== item.cost
        );

        if (updateTable.length < 1) {
          this.showAlert("No changes to update", "danger");
        } else {
          const updateResult = await axios.post("cogs", updateTable);
          this.showAlert("COGS updated succesfully", "success");

          setTimeout(() => {
            eventBus.$emit("cogs-updated");
            this.$emit("handle-close");
          }, 2000);
        }
      } catch (error) {
        console.log(error);
        this.showAlert("An error occurred, please try again later.", "danger");
      }
    },
    onRowSelected(items) {
      this.selected = items;
      console.log(this.selected);
    },
    selectAllRows() {
      this.$refs.cogsTable.selectAllRows();
    },
    clearSelected() {
      this.$refs.cogsTable.clearSelected();
    },
    showAlert(message, variant) {
      this.updateResult = message;
      this.updateVariant = variant;
      this.dismissCountDown = 5;
    },
    async getCogsData() {
      try {
        const result = await axios.get("cogs");
        console.log({ result });
        this.items = result.data.products;
        this.is_loading = false;
        this.preItems = JSON.parse(JSON.stringify(this.items));
        console.log(this.items);
      } catch (error) {
        console.log({ error });
        this.products = [];
      }
    },
  },
};
</script>

<style >
.cogs_cost_width {
  width: 120px;
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
