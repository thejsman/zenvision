<template>
  <div class="row">
    <div class="col-xl-12 mt-4">
      <h3>Revenues</h3>
    </div>
    <div v-for="stat of revenueData" :key="stat.id" class="col-md-4 p-2">
      <Stat :title="stat.title" :value="stat.value" />
    </div>
  </div>
</template>
<script>
import Stat from "../../widgets/stat";
export default {
  components: { Stat },
  props: {
    revenueData: {
      type: Array,
      default: () => [],
    },
  },
};
</script>
