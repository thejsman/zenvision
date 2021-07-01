<script>
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
            type: Number
        },
        channelData: {
            type: Array,
            default: () => []
        },
        loading: {
            type: Boolean,
            default: true
        },
        tooltip: {
            type: String,
            default: ""
        },
        chartOptions: {
            type: Object,
            default: () => {
                return {
                    chart: {
                        type: "area",
                        height: 40,
                        sparkline: {
                            enabled: true
                        }
                    },
                    stroke: {
                        curve: "smooth",
                        width: 2
                    },
                    colors: ["#f1b44c"],
                    fill: {
                        type: "gradient",
                        gradient: {
                            shadeIntensity: 1,
                            inverseColors: false,
                            opacityFrom: 0.45,
                            opacityTo: 0.05,
                            stops: [25, 100, 100, 100]
                        }
                    },
                    tooltip: {
                        fixed: {
                            enabled: false,
                            color: "#000000"
                        },
                        x: {
                            show: false
                        },
                        y: {
                            formatter: value => {
                                return "$" + value;
                            }
                        },
                        marker: {
                            show: false
                        }
                    }
                };
            }
        },
        series: {
            type: Array,
            default: () => [
                {
                    name: "Profit",
                    data: [0, 0, 0, 0, 0, 0, 0]
                }
            ]
        },
        showGraph: {
            type: Boolean,
            default: false
        }
    }
};
</script>

<template>
    <div class="card mini-stats-wid">
        <div class="card-body">
            <div class="media">
                <div class="media-body">
                    <div class="d-flex justify-content-between">
                        <p class="text-muted font-weight-medium">{{ title }}</p>
                        <i
                            v-if="tooltip"
                            v-b-tooltip.hover="tooltip"
                            class="fas fas fa-info-circle"
                        ></i>
                    </div>
                    <div class="mb-3 mt-1">
                        <b-skeleton
                            v-if="loading"
                            animation="wave"
                            width="30%"
                            class="skeleton-loading"
                        ></b-skeleton>
                        <h4 v-else class="mx-auto">{{ value }}</h4>
                    </div>
                    <div class="d-flex justify-content-between">
                        <!-- <div class="rectangle mt-3" v-if="loading">
                            <b-skeleton
                                animation="wave"
                                width="100%"
                            ></b-skeleton>
                        </div>
                        <div class="rectangle mt-3" v-else>
                            <h4 class="mb-0 mx-auto">0%</h4>
                        </div> -->

                        <apexchart
                            v-if="showGraph"
                            class="apex-charts"
                            :height="30"
                            :width="120"
                            :options="chartOptions"
                            :series="series"
                        />
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
    padding: 6px 8px 6px;

    border-radius: 2px;
    background-color: #34c48f66;
    width: 60px;
}
.rectangle h4 {
    color: #b0fbb0;
    font-size: 16px;
    font-weight: 100;
    text-align: center;
}
.apexcharts-tooltip {
    color: #191e2c;
}
.skeleton-loading {
    padding: 12px;
}
</style>
