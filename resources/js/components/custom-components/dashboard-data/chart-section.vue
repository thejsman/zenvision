<template>
  <div class="row">
    <div class="col-xl-12 mt-4">
      <h3>Profit Analysis</h3>
    </div>
    <div class="col-xl-12 pa-chart card-body ml-2 mt-2"></div>
  </div>
</template>
<script>
import Stat from "../../widgets/stat";
export default {
  components: { Stat },
  props: {
    performanceData: {
      type: Array,
      default: () => [],
    },
  },
};
</script>
<style>
.pa-chart {
  width: 100%;
  height: 300px;
  background-color: #2a3042;
}
</style>

