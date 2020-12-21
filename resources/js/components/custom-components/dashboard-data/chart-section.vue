<template>
  <div class="row">
    <div class="col-xl-12 mt-4 mb-3 ml-2">
      <h3>Profit Analysis</h3>
    </div>
    <div class="card w-100">
      <div class="card-body">
        <div class="d-flex justify-content-end mt-3 legends">
          <i class="profit-circle" v-tooltip.top-center="msg"></i>
          <span class="mr-3">Profit</span>
          <i class="loss-circle" @click="ClickHandler"></i
          ><span class="mr-3 pr-3">Loss</span>
        </div>
        <!-- <button v-tooltip.top-center="msg">Hover me</button> -->
        <!-- Bi-polar bar -->
        <chartist
          ratio="ct-chart"
          :data="chartData.data"
          :options="chartData.options"
          type="Bar"
          :event-handlers="chartData.eventHandlers"
        ></chartist>
        <!-- <Bar :chart-data="chartdata2.datasets" /> -->
      </div>
    </div>
  </div>
</template>
<script>
import Stat from "../../widgets/stat";
import { Bar } from "vue-chartjs";

export default {
  components: { Stat, Bar },
  data() {
    return {
      msg: "This is the message",
      chartdata2: {
        labels: ["January", "February"],
        datasets: [
          {
            label: "Data One",
            backgroundColor: "#f87979",
            data: [40, 20],
          },
        ],
      },
      options2: {
        responsive: true,
        maintainAspectRatio: false,
      },
      chartData: {
        data: {
          labels: ["A", "B", "C"],
          series: [[1, 3, 2]],
        },
        options: {
          height: 300,
          onClick: function (event, args) {
            alert("No");
          },
        },
        plugins: [
          this.$chartist.plugins.tooltip({
            anchorToPoint: true,
            appendToBody: true,
          }),
        ],
        eventHandlers: [
          {
            event: "draw",
            fn(context) {
              if (context.type === "bar") {
                context.element._node.setAttribute(
                  "v-tooltip.top-center",
                  "msg"
                );

                // context.element._node.addEventListener(
                //   "mouseover",
                //   "ClickHandler"
                // );
              }
            },
          },
        ],
      },
    };
  },
  methods: {
    hightlightme() {
      alert("Hello from handleClick");
    },
    ClickHandler: function (event, points) {
      alert("yes");
      // Here is how to access the chart
      //   const c = this._data._chart;
      //   const datapoint = c.getElementAtEvent(event)[0];
      //   const indexBar = datapoint._index;
      //   const indexSegment = datapoint._datasetIndex;
      // Do whatever with this.my_data, indexBar, and indexSegment
    },
  },
  mounted() {},
  props: {
    performanceData: {
      type: Array,
      default: () => [],
    },
  },
};
</script>



<style lang="scss">
.legends {
  height: 8px;
}
.profit-circle {
  background: #3ec38f;
  border-radius: 50%;
  width: 12px;
  height: 12px;
  margin-right: 6px;
  margin-top: 3px;
}
.loss-circle {
  background: #df1e1e;
  border-radius: 50%;
  width: 12px;
  height: 12px;
  margin-right: 6px;
  margin-top: 3px;
}
.chartist-tooltip {
  position: absolute;
  display: none;
  min-width: 5em;
  padding: 8px 10px;
  background: #383838;
  color: #fff;
  text-align: center;
  pointer-events: none;
  z-index: 100;
  transition: opacity 0.2s linear;
}

.chartist-tooltip:before {
  position: absolute;
  bottom: -14px;
  left: 50%;
  border: solid transparent;
  content: " ";
  height: 0;
  width: 0;
  pointer-events: none;
  border-color: rgba(251, 249, 228, 0);
  border-top-color: #383838;
  border-width: 7px;
  margin-left: -8px;
}

.chartist-tooltip.tooltip-show {
  display: inline-block !important;
}

.tooltip {
  display: block !important;
  z-index: 10000;

  .tooltip-inner {
    background: black;
    color: white;
    border-radius: 16px;
    padding: 5px 10px 4px;
  }

  .tooltip-arrow {
    width: 0;
    height: 0;
    border-style: solid;
    position: absolute;
    margin: 5px;
    border-color: black;
  }

  &[x-placement^="top"] {
    margin-bottom: 5px;

    .tooltip-arrow {
      border-width: 5px 5px 0 5px;
      border-left-color: transparent !important;
      border-right-color: transparent !important;
      border-bottom-color: transparent !important;
      bottom: -5px;
      left: calc(50% - 5px);
      margin-top: 0;
      margin-bottom: 0;
    }
  }

  &[x-placement^="bottom"] {
    margin-top: 5px;

    .tooltip-arrow {
      border-width: 0 5px 5px 5px;
      border-left-color: transparent !important;
      border-right-color: transparent !important;
      border-top-color: transparent !important;
      top: -5px;
      left: calc(50% - 5px);
      margin-top: 0;
      margin-bottom: 0;
    }
  }

  &[x-placement^="right"] {
    margin-left: 5px;

    .tooltip-arrow {
      border-width: 5px 5px 5px 0;
      border-left-color: transparent !important;
      border-top-color: transparent !important;
      border-bottom-color: transparent !important;
      left: -5px;
      top: calc(50% - 5px);
      margin-left: 0;
      margin-right: 0;
    }
  }

  &[x-placement^="left"] {
    margin-right: 5px;

    .tooltip-arrow {
      border-width: 5px 0 5px 5px;
      border-top-color: transparent !important;
      border-right-color: transparent !important;
      border-bottom-color: transparent !important;
      right: -5px;
      top: calc(50% - 5px);
      margin-left: 0;
      margin-right: 0;
    }
  }

  &[aria-hidden="true"] {
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.15s, visibility 0.15s;
  }

  &[aria-hidden="false"] {
    visibility: visible;
    opacity: 1;
    transition: opacity 0.15s;
  }
}
</style>

