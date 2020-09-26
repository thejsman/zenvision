<template>
  <div class="row">
    <div class="col-xl-12 mt-4">
      <h3>Key Performance Metrics</h3>
    </div>
    <div
      v-for="performance of performanceData"
      :key="performance.id"
      class="col-md-4 p-2"
    >
      <Stat :title="performance.title" :value="performance.value" />
    </div>
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
