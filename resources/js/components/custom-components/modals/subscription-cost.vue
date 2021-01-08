<template>
  <b-row>
    <h1>Subscription cost</h1>
    <b-col cols="10" offset="1">
      <div class="font-weight-bold font-size-18 text-white mt-4">
        Subscription Costs
      </div>
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <p class="mt-4 mb-4 text-white">
            This represents recurring subscription expenses.
          </p>
        </div>
        <b-button
          variant="primary"
          class="btn btn-success"
          @click="handleAddSubscriptionCost"
          >Add</b-button
        >
      </div>
    </b-col>
  </b-row>
</template>

<script>
export default {};
</script>

<style>
</style>
