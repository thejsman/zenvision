<template>
  <div class="row">
    <div class="col-xl-12 mt-4">
      <h3>Cost</h3>
    </div>
    <div v-for="cost of costData" :key="cost.id" class="col-md-4 p-2">
      <Stat :title="cost.title" :value="cost.value" />
    </div>
  </div>
</template>
<script>
import Stat from "../../widgets/stat";
export default {
  components: { Stat },
  props: {
    costData: {
      type: Array,
      default: () => [],
    },
  },
};
</script>
