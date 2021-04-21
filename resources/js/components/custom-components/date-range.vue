<template>
    <div class="ml-auto d-inline-flex">
        <p class="mt-2 mr-2">Date Range:</p>
        <date-range-picker
            ref="picker"
            :opens="opens"
            :maxDate="maxDate"
            :singleDatePicker="false"
            v-model="dateRange"
            @update="selectDate"
            :dateFormat="dateFormat"
            :ranges="false"
        >
        </date-range-picker>
    </div>
</template>
<script>
import DateRangePicker from "vue2-daterange-picker";
import moment from "moment";
import { eventBus } from "../../app";
import "vue2-daterange-picker/dist/vue2-daterange-picker.css";

export default {
    components: { DateRangePicker },

    data() {
        return {
            dateRange: {
                startDate: moment().subtract(1, "month"),
                endDate: moment()
            },
            opens: "left",
            minDate: moment()
                .subtract(3, "month")
                .toString(),
            maxDate: moment().toString(),
            singleDatePicker: "range",
            ranges: {
                Today: [moment(), moment()],
                Yesterday: [
                    moment().subtract(1, "days"),
                    moment().subtract(1, "days")
                ],
                "Last week": [
                    moment()
                        .subtract(1, "week")
                        .startOf("week"),
                    moment()
                        .subtract(1, "week")
                        .endOf("week")
                ],
                "This month": [
                    moment().startOf("month"),
                    moment().endOf("month")
                ],

                "Last month": [
                    moment()
                        .subtract(1, "month")
                        .startOf("month"),
                    moment()
                        .subtract(1, "month")
                        .endOf("month")
                ],
                "Last 3 months": [moment(), moment().subtract(3, "month")]
            }
        };
    },
    created() {},
    methods: {
        dateFormat(classes, date) {
            if (!classes.disabled) {
                classes.disabled = date.getTime() > new Date();
            }
            return classes;
        },
        selectDate() {
            const { startDate, endDate } = this.dateRange;
            //   this.$emit("changeDateRange", this.dateRange);
            eventBus.$emit("changeDateRange", this.dateRange);
        }
    }
};
</script>
<style>
.daterangepicker {
    top: 40px;
    background-color: #262b3c;
}
.reportrange-text[data-v-267f4ee2] {
    background: #262b3c;
    padding: 0.47rem 0.75rem;
}
.daterangepicker .calendar-table,
.daterangepicker .drp-buttons {
    border: none;
    background-color: none;
}
.daterangepicker td.in-range {
    background-color: #ebf4f82b;
}
</style>
