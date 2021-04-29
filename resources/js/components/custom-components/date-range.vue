<template>
    <div class="ml-auto d-inline-flex">
        <p class="mt-2 mr-2">Date Range:</p>

        <DateRangePicker
            ref="picker"
            :opens="opens"
            :maxDate="maxDate"
            :locale-data="{ firstDay: 1, format: 'mm-dd-yyyy' }"
            v-model="dateRange"
            @update="selectDate"
            :dateFormat="dateFormat"
            :ranges="false"
        >
        </DateRangePicker>
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
            startDateSelected: moment().subtract(1, "month")
        };
    },
    created() {},
    computed: {
        maxDate() {
            return moment(this.startDateSelected)
                .add("3", "months")
                .toString();
        }
    },
    methods: {
        dateFormat(classes, date) {
            if (classes["start-date"]) {
                this.startDateSelected = date;
            }
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
.daterangepicker td.off {
    visibility: hidden;
}
</style>
