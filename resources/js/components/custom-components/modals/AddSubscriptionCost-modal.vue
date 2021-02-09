<template>
  <div class="px-3">
    <div class="font-weight-bold font-size-18 text-white mt-4 pb-3">
      Add Subscription Cost
    </div>
    <b-form @submit="onSubmit" v-if="show">
      <b-form-group id="input-group-1" label="Name" label-for="name">
        <b-form-input
          id="name"
          v-model="form.subscription_name"
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
          v-model="form.billing_period"
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
          v-model="form.subscription_price"
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

      <b-button type="submit" variant="primary">Add</b-button>
      <b-button class="btn btn-cancel" @click="$emit('handle-close')"
        >Cancel</b-button
      >
    </b-form>
  </div>
</template>

<script>
import axios from "axios";
import { eventBus } from "../../../app";
export default {
  data() {
    return {
      form: {
        subscription_name: "",
        billing_period: null,
        subscription_price: null,
        starting_date: new Date(),
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
  methods: {
    async onSubmit(evt) {
      evt.preventDefault();
      try {
        const result = await axios.post("/subscriptioncost", this.form);
        eventBus.$emit("updateSubscription");
        this.$emit("handle-close");
      } catch (err) {
        eventBus.$emit("updateSubscription");
        this.$emit("handle-close");
      }
    },
   
  },
};
</script>