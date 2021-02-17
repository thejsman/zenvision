<template>
  <b-row>
    <b-col cols="12">
      <div class="font-weight-bold font-size-24 text-white mt-4 subscription-header">
        Subscription Costs
      </div>
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <p class="mt-4 mb-4 text-white">
            This represents recurring subscription expenses.
          </p>
        </div>
        <b-button
          variant="green"
          class="btn btn-success"
          @click="handleAddSubscriptionCost"
          >Add</b-button
        >
      </div>
    </b-col>
    <b-col cols="12">
      <div v-if="isLoading">
        <div class="col-lg-12 mt-4">
          <div class="d-flex justify-content-center mb-3">
            <b-spinner class="spinner-border" type="border" label="Loading..."></b-spinner>
          </div>
        </div>
      </div>
      <div cols="12" class="mt-4" v-else>
        <b-table
          hover
          :items="items"
          :fields="fields"
          head-variant="dark"
          :sort-by.sync="sortBy"
          :sort-desc.sync="sortDesc"
          :tbody-tr-class="rowClass"
        >
          <template v-slot:cell(action)="row">
            <b-dropdown
              class="actionBtn"
              variant="link"
              text="..."
              v-if="row.item.end_date === null"
            >
              <b-dropdown-item href="#" @click="editSubscription(row)"
                ><i class="fas fa-pencil-alt text-success mr-1" /> Edit
              </b-dropdown-item>
              <b-dropdown-item href="#" @click="endSubscription(row)"
                ><i class="fas fa-stop-circle text-warning mr-1" /> End
                Subscription
              </b-dropdown-item>
              <b-dropdown-item href="#" @click="deleteSubscription(row)"
                ><i class="fas fa-trash text-danger mr-1" /> Delete
              </b-dropdown-item>
            </b-dropdown>
          </template>
        </b-table>
      </div>
    </b-col>

    <b-modal id="subscription-form" size="m" centered hide-footer hide-header>
      <SubscriptionForm
        @handle-close="$bvModal.hide('subscription-form')"
        @updateSubscription="updateSubscription"
      />
    </b-modal>

    <b-modal
      id="editSubscription-form"
      size="m"
      centered
      hide-footer
      hide-header
    >
      <EditSubForm
        :formData="editFormData"
        @handle-close="$bvModal.hide('editSubscription-form')"
      />
    </b-modal>
  </b-row>
</template>

<script>
import axios from "axios";
import SubscriptionForm from "./AddSubscriptionCost-modal";
import EditSubForm from "./editSubscriptionCost-modal";
import { eventBus } from "../../../app";
import moment from "moment";
export default {
  components: { SubscriptionForm, EditSubForm },
  data() {
    return {
      sortBy: "end_date",
      sortDesc: false,
      fields: [
        {
          key: "subscription_name",
          label: "Name",
          tdClass: "tdcenter",
        },
        {
          key: "billing_period",
          label: "Billing Period",
          tdClass: "tdcenter",
        },
        {
          key: "starting_date",
          label: "Start Date",
          tdClass: "tdcenter",
          formatter: "formatDateAssigned",
        },
        {
          key: "end_date",
          label: "End Date",
          tdClass: "tdcenter",
          formatter: "formatDateAssigned",
        },
        {
          key: "subscription_price",
          label: "Price",
          tdClass: "tdcenter",
          formatter: "formatPrice",
        },
        {
          key: "action",
          label: "Action",
          tdClass: "actionbutton",
        },
      ],
      isLoading: true,
      items: [],
      preItems: [],
      searchText: "",
      showMessage: true,
      editFormData: {},
    };
  },
  async created() {
    await this.getSubscriptionCostData();
    eventBus.$on("updateSubscription", async () => {
      await this.getSubscriptionCostData();
    });
  },
  methods: {
    async updateSubscription() {
      await this.getSubscriptionCostData();
    },
    async getSubscriptionCostData() {
      try {
        const result = await axios.get("/subscriptioncost");
        this.items = result.data;
        this.isLoading = false;
      } catch (error) {
        this.items = [];
        this.isLoading = false;
      }
    },
    handleAddSubscriptionCost() {
      this.$bvModal.show("subscription-form");
    },
    formatDateAssigned(value) {
      if (value === null) {
        return "";
      }
      const formattedDate = moment(value).format("LL");
      return formattedDate;
    },
    formatPrice(value) {
      return `$${value}`;
    },
    rowClass(item, type) {
      if (item && type === "row") {
        if (item.end_date !== null) {
          return "text-grey-50";
        }
      } else {
        return null;
      }
    },
    editSubscription(row) {
      this.editFormData = { ...row.item };
      this.$bvModal.show("editSubscription-form");
    },
    async deleteSubscription(row) {
      const result = await axios.delete(`subscriptioncost/${row.item.id}`);
      await this.getSubscriptionCostData();
      eventBus.$emit("updateSubscription");
      await this.updateSubscription();
    },
    async endSubscription(row) {
      const result = await axios.patch(`endSubscriptioncost/${row.item.id}`);
      await this.getSubscriptionCostData();
      eventBus.$emit("updateSubscription");
      await this.updateSubscription();
    },
  },
};
</script>

<style>
.text-grey-50 {
  color: white;
  opacity: 0.5;
}
</style>
