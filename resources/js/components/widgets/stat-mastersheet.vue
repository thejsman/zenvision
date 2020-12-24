<script>
/**
 * Stat component -- specify the widget icon, title and value.
 */
import PlaceholderLoader from "../../components/custom-components/placeholder-loader.vue";

export default {
  components: { PlaceholderLoader },
  props: {
    title: {
      type: String,
      default: "",
    },
    value: {
      type: String,
      default: "",
    },
    id: {
      type: Number,
    },
    channelData: {
      type: Array,
      default: () => [],
    },
    loading: {
      type: Boolean,
      default: true,
    },
    tooltip: {
      type: String,
      default: "",
    },
  },
};
</script>

<template>
  <div class="card mini-stats-wid">
    <PlaceholderLoader v-if="loading" />
    <div class="card-body" v-else>
      <div class="media">
        <div class="media-body">
          <div class="d-flex justify-content-between">
            <p class="text-muted font-weight-medium">{{ title }}</p>
            <i
              v-if="tooltip"
              v-b-tooltip.hover="tooltip"
              class="fas fas fa-info-circle"
            ></i>
            <i v-else class="fas fa-angle-down"></i>
          </div>
          <div>-</div>
          <div class="rectangle mt-3">
            <h4 class="mb-0">{{ value }}</h4>
          </div>
        </div>
      </div>
      <div v-for="data of channelData" :key="data.title" class="block">
        <span class="text-muted font-weight-medium pt-1 pb-1">{{
          data.title
        }}</span>
        <span class="float-right">{{ data.value }}</span>
      </div>
    </div>
  </div>
</template>
<style>
.rectangle {
  padding: 1.5px 8px 1px;

  border-radius: 2px;
  background-color: #34c48f66;
  width: 40px;
}
.rectangle h4 {
  color: #b0fbb0;
  font-size: 16px;
  font-weight: 100;
  text-align: center;
}
</style>
