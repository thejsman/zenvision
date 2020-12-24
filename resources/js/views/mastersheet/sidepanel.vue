<script>
import Stat from "../../components/widgets/stat-mastersheet";
export default {
  components: { Stat },
  data() {
    return {
      showModal: false,
      netEquityData: [
        {
          icon: "bx bx-copy-alt",
          title: "Net Equity",
          value: "$0",
          tooltip:
            "This balance represents the total fluctuation in Net Equity between Yesterday and the day before.",
          loading: false,
        },
      ],
      statData: [
        {
          icon: "bx bx-copy-alt",
          title: "Cash",
          value: "$0",
          loading: false,
        },
        {
          icon: "bx bx-archive-in",
          title: "Inventory",
          value: "$0",
          loading: false,
        },
        {
          icon: "bx bx-purchase-tag-alt",
          title: "Reserves",
          value: "$0",
          loading: false,
        },
      ],
      debtsData: [
        {
          icon: "bx bx-copy-alt",
          title: "Credit Cart",
          value: "$0",
          loading: false,
        },
        {
          icon: "bx bx-archive-in",
          title: "Supplier Payable",
          value: "$0",
          loading: false,
        },
      ],
      editorData: "<p>Content of the editor.</p>",
    };
  },
};
</script>

<template>
  <div class="email-leftbar card">
    <div class="mail-list mt-4">
      <div class="row">
        <div
          v-for="stat of netEquityData"
          :key="stat.icon"
          class="col-md-12 my-2 netEquity"
        >
          <Stat
            :icon="stat.icon"
            :title="stat.title"
            :value="stat.value"
            :loading="stat.loading"
            :tooltip="stat.tooltip"
          />
        </div>
      </div>
    </div>
    <div class="mail-list mt-4">
      <div class="row">
        <div class="col-xl-12">
          <h4>Assets</h4>
        </div>
        <div v-for="stat of statData" :key="stat.icon" class="col-md-12 my-2">
          <Stat
            :icon="stat.icon"
            :title="stat.title"
            :value="stat.value"
            :loading="stat.loading"
          />
        </div>
      </div>
    </div>
    <div class="mail-list mt-4">
      <div class="row">
        <div class="col-xl-12">
          <h4>Debts</h4>
        </div>
        <div v-for="stat of debtsData" :key="stat.icon" class="col-md-12 my-2">
          <Stat
            :icon="stat.icon"
            :title="stat.title"
            :value="stat.value"
            :loading="stat.loading"
          />
        </div>
      </div>
    </div>
  </div>
</template>
<style lang="scss">
/* ==============
  Email
===================*/
.email-leftbar {
  width: 400px;
  float: left;
  padding: 20px;
  border-radius: 5px;
  background-color: #191e2c;
  border-radius: 10px;
}
.netEquity .card {
  background-color: #2f3863;
}
.netEquity .text-muted {
  color: #556ee7 !important;
}
@media (max-width: 767px) {
  .email-leftbar {
    float: none;
    width: 100%;
  }
}
</style>
