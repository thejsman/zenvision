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
  },
};
</script>

<template>
  <div class="card mini-stats-wid">
    <PlaceholderLoader v-if="loading" />
    <div class="card-body" v-else>
      <div class="media">
        <div class="media-body">
          <p class="text-muted font-weight-medium">{{ title }}</p>
          <h4 class="mb-0">{{ value }}</h4>
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
