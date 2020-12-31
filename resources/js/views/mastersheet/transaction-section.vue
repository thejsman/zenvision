<script>
import OtherExpenses from "./other-expenses";
import AllTransactions from "./all-transactions";

export default {
  components: { OtherExpenses, AllTransactions },
};
</script>

<template>
  <div class="mt-4">
    <h4>
      Transactions
      <i
        class="fas fas fa-info-circle"
        v-b-tooltip.hover="
          'This section represents all transactions that have occurred in the accounts that are linked to Zenvision.'
        "
      ></i>
    </h4>

    <div class="col-md-12 p-0">
      <div>
        <b-card no-body>
          <b-tabs
            nav-class="nav-tabs-custom m-2 p-3"
            content-class="p-3 text-muted"
          >
            <OtherExpenses />
            <AllTransactions />
          </b-tabs>
        </b-card>
        <div class="text-center text-muted mt-2">
          <p class="align-items-center">
            <i
              class="fas fas fa-info-circle"
              v-b-tooltip.hover="'Learn more about the Mastersheet page...'"
            ></i>
            Learn more about the <a href="#">Mastersheet page</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

