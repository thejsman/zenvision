<script>
import AddInventoryBtn from "../../components/custom-components/add-inventory.vue";
import AddSupplierPayable from "../../components/custom-components/add-supplier-payable.vue";

export default {
    data() {
        return {
            showInventorySection: false,
            showSupplierPayableSection: false
        };
    },
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
        tooltip2: {
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
        },
        showCaret: {
            type: Boolean,
            default: false
        },
        showCaretDebts: {
            type: Boolean,
            default: false
        }
    },
    methods: {
        clickHandler() {
            this.showInventorySection = !this.showInventorySection;
        },
        clickHandlerSupplierPayable() {
            this.showSupplierPayableSection = !this.showSupplierPayableSection;
        }
    },
    components: { AddInventoryBtn, AddSupplierPayable }
};
</script>

<template>
    <div
        class="card mini-stats-wid"
        :class="{
            cogscard: title === 'Inventory' || title === 'Supplier Payable'
        }"
    >
        <div class="card-body" v-on="showCaret ? { click: clickHandler } : {}">
            <div
                class="media"
                v-on="
                    showCaretDebts ? { click: clickHandlerSupplierPayable } : {}
                "
            >
                <div class="media-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-baseline">
                            <p class="text-muted font-weight-medium">
                                {{ title }}
                            </p>
                            <i
                                v-if="tooltip2"
                                v-b-tooltip.hover="tooltip2"
                                class="fas fas fa-info-circle pl-2"
                            ></i>
                        </div>

                        <i
                            v-if="tooltip"
                            v-b-tooltip.hover="tooltip"
                            class="fas fas fa-info-circle"
                        ></i>
                        <i
                            v-if="showCaret"
                            class="fas"
                            :class="
                                showInventorySection
                                    ? 'fa-angle-up'
                                    : 'fa-angle-down'
                            "
                        ></i>
                        <i
                            v-if="showCaretDebts"
                            class="fas"
                            :class="
                                showSupplierPayableSection
                                    ? 'fa-angle-up'
                                    : 'fa-angle-down'
                            "
                        ></i>
                    </div>
                    <div class="mb-3 mt-1">
                        <b-skeleton
                            v-if="loading"
                            animation="wave"
                            width="30%"
                            class="skeleton-loading"
                        ></b-skeleton>

                        <h4 v-else class="mx-auto">
                            {{ value }}
                        </h4>
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
        <transition name="fade">
            <div v-if="showInventorySection" class="border-top px-3">
                <div class="text-center">
                    <AddInventoryBtn />
                </div>
            </div>
        </transition>
        <transition name="fade">
            <div v-if="showSupplierPayableSection" class="border-top px-3">
                <div class="text-center">
                    <AddSupplierPayable />
                </div>
            </div>
        </transition>
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
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
}
</style>
