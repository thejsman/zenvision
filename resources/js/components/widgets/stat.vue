<script>
/**
 * Stat component -- specify the widget icon, title and value.
 */
export default {
  props: {
    title: {
      type: String,
      default: ""
    },
    value: {
      type: String,
      default: ""
    },
    id: {
      type: Number,
      
    }
  }
};
</script>

<template>
  <div class="card mini-stats-wid">
    <div class="card-body">
      <div class="media">
        <div class="media-body">
          <p class="text-muted font-weight-medium">{{title}}</p>
          <h4 class="mb-0">{{value}}</h4>
        </div>

        <!-- <div class="mini-stat-icon avatar-sm align-self-center">
          <span class="">
            <i :class="`${icon} font-size-24`"></i>
          </span>
        </div> -->
      </div>
    </div>
    <!-- end card-body -->
  </div>
  <!-- end card -->
</template>