<template>
    <div class="ml-auto d-inline-flex">
        <p class="mt-2 mr-2 date-range">Date Range:</p>
        <DateRangePicker
            ref="picker"
            :opens="opens"
            :maxDate="maxDate"
            :locale-data="{ firstDay: 1, format: 'mm-dd-yyyy' }"
            v-model="dateRange"
            @update="handleDateChange"
            :dateFormat="dateFormat"
            :ranges="false"
        >
        </DateRangePicker>
    </div>
</template>
<script>
import DateRangePicker from "vue2-daterange-picker";
import moment from "moment";
import "vue2-daterange-picker/dist/vue2-daterange-picker.css";
import { mapGetters, mapActions } from "vuex";

export default {
    components: { DateRangePicker },
    data() {
        return {
            opens: "left",
            startDateSelected: moment().subtract(1, "month")
        };
    },
    computed: {
        ...mapGetters(["startDateS", "endDateS", "dateRangeS"]),
        maxDate() {
            return moment(this.startDateSelected)
                .add("3", "months")
                .toString();
        },
        dateRange: {
            get() {
                return this.dateRangeS;
            },
            set(value) {
                this.updateDateRange(value);
            }
        }
    },
    methods: {
        ...mapActions(["updateDateRange"]),
        dateFormat(classes, date) {
            if (classes["start-date"]) {
                this.startDateSelected = date;
            }
            if (!classes.disabled) {
                classes.disabled = date.getTime() > new Date();
            }
            return classes;
        },
        handleDateChange() {
            // console.log("we will handle date change here");
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

.daterangepicker .calendar-table .next span,
.daterangepicker .calendar-table .prev span {
    color: #fff;
    border: solid white;
    border-width: 0 2px 2px 0;
    border-radius: 0;
    display: inline-block;
    padding: 3px;
}
</style>
