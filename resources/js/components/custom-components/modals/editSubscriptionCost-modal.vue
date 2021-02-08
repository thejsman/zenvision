<template>
  <div class="px-3">
    <div class="font-weight-bold font-size-18 text-white mt-4 pb-3">
      Edit Subscription Cost
    </div>
    <b-form @submit="onSubmit" v-if="show">
      <b-form-group id="input-group-1" label="Name" label-for="name">
        <b-form-input
          id="name"
          v-model="formData.subscription_name"
          required
          placeholder="Product Name"
        ></b-form-input>
      </b-form-group>

      <b-form-group
        id="input-group-2"
        label="Billing period"
        label-for="billing_period"
      >
        <b-form-select
          id="billing_period"
          v-model="formData.billing_period"
          :options="periods"
          required
        ></b-form-select>
      </b-form-group>
      <b-form-group
        id="input-group-3"
        label="Price"
        label-for="price"
        prepend="$"
      >
        <b-form-input
          id="price"
          v-model="formData.subscription_price"
          required
          placeholder="0.00"
          prepend="$"
        ></b-form-input>
      </b-form-group>

      <b-form-group id="input-group-4" label="Starting date" label-for="date">
        <!-- <b-form-input
          id="starting_date"
          v-model="form.starting_date"
          required
          placeholder="Starting Date"
        ></b-form-input> -->

        <b-form-datepicker
          id="starting_date"
          placeholder="Starting Date"
          :date-format-options="{
            year: 'numeric',
            month: 'numeric',
            day: 'numeric',
          }"
          locale="en"
          v-model="form.starting_date"
        ></b-form-datepicker>
      </b-form-group>
      <!-- <date-range-picker
        ref="picker"
        :opens="opens"
        :ranges="false"
        v-model="form.starting_date"
        :singleDatePicker="singleDatePicker"
      ></date-range-picker> -->
      <b-button type="submit" variant="primary">Save</b-button>
      <b-button class="btn btn-cancel" @click="$emit('handle-close')"
        >Cancel</b-button
      >
    </b-form>
    <!-- <b-card class="mt-3" header="Form Data Result">
      <pre class="m-0">{{ formData }}</pre>
    </b-card> -->
  </div>
</template>

<script>
import axios from "axios";
import DateRangePicker from "vue2-daterange-picker";
import { eventBus } from "../../../app";
export default {
  components: { DateRangePicker },
  data() {
    return {
      form: {
        subscription_name: "",
        billing_period: null,
        subscription_price: null,
        starting_date: null,
      },
      periods: [
        { text: "Select One", value: null },
        "Daily",
        "Weekly",
        "Monthly",
        "Every 3 months",
        "Every 6 months",
        "Yearly",
      ],
      show: true,
    };
  },
  props: {
    formData: {
      type: Object,
      default: () => {},
    },
  },
  methods: {
    async onSubmit(evt) {
      evt.preventDefault();
      const result = await axios.patch(
        `/subscriptioncost/${this.formData.id}`,
        this.formData
      );
      eventBus.$emit("updateSubscription");
      this.$emit("handle-close");
      eventBus.$emit("subscriptionUpdate");
    },
  },
};
</script>