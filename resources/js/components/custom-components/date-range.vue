<template>
  <div class="ml-auto d-inline-flex">
    <p class="mt-2 mr-2">Date Range:</p>
    <date-range-picker
      ref="picker"
      :opens="opens"
      :ranges="false"
      v-model="dateRange"
      :date-format="dateFormat"
      @update="selectDate"
    ></date-range-picker>
  </div>
</template>
<script>
import DateRangePicker from "vue2-daterange-picker";
import moment from "moment";
import "vue2-daterange-picker/dist/vue2-daterange-picker.css";

export default {
  components: { DateRangePicker },

  data() {
    return {
      dateRange: {
        startDate: moment().add("month", -3),
        endDate: moment(),
      },
      opens: "left",
    };
  },
  methods: {
    dateFormat(classes, date) {
      if (!classes.disabled) {
        classes.disabled = date.getTime() > new Date();
      }
      return classes;
    },
    selectDate() {
      const { startDate, endDate } = this.dateRange;
      console.log(
        moment(startDate).format("DD/MM/YYYY"),
        moment(endDate).format("DD/MM/YYYY")
      );
    },
  },
};
</script>
<style>
.daterangepicker {
  top: 40px;
}
</style>
