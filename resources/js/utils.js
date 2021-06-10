/*
_________________________

=========================
common utility functions
=========================
_________________________

*/

import _ from "lodash";
import moment from "moment";

//fucntion to display numbers in currency on the dashboard
export const displayCurrency = value => {
    if (value === "-") {
        return value;
    }
    if (isNaN(value) || !isFinite(value)) {
        value = 0;
    }
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD"
    }).format(value);
};

//fucntion to display numbers  on the dashboard
export const displayNumber = value => {
    if (isNaN(value) || !isFinite(value)) {
        value = 0;
    }
    return new Intl.NumberFormat("en-US").format(value);
};

// Tempororay Order template
export const tempOrderTemplate = {};

//Caclulate SumBy function
export const getSumBy = (values, filter) => {
    return _.sumBy(values, value => parseFloat(value[filter]));
};

export const updateData = (data, title, value) => {
    data.forEach(d => {
        if (d.title === title) {
            d.value = `${value}`;
            d.loading = false;
        }
    });
};

export const updateNoData = data => {
    data.forEach(d => {
        d.value = `-`;
        d.loading = false;
    });
};

export const updateAdData = (data, provider, value) => {
    data.forEach(d => {
        if (
            d.channelIcon !== undefined &&
            d.channelIcon.includes(provider.toLowerCase())
        ) {
            d.value = `${value}`;
            d.loading = false;
        }
    });
};

export const updateDataMerchantFee = (data, title, value) => {
    data.forEach(d => {
        if (d.title === title) {
            d.value = `${value}`;
            d.loading = false;
            d.iconName = "";
        }
    });
};
export const updateGraphData = (data, title, value, graphData) => {
    data.forEach(d => {
        if (d.title === title) {
            d.value = `${value}`;
            d.series = [{ name: d.series[0].name, data: graphData }];
            d.loading = false;
        }
    });
};

export const setLoading = data => {
    data.forEach(d => (d.loading = true));
};

export const setLoadingSingle = (data, title) => {
    data.forEach(d => {
        if (d.title === title) {
            d.loading = true;
        }
    });
};

export const setLoadingAdSingle = (data, provider) => {
    data.forEach(d => {
        if (
            d.iconName !== undefined &&
            d.iconName.includes(provider.toLowerCase())
        ) {
            d.loading = true;
        }
    });
};

// Function to calculate dates rage of 30days for  paypal transactions api call
export const getDatesBetweenDates = (startDate, endDate) => {
    let dates = [];
    let s_date = moment(startDate);
    let e_date = moment(endDate);

    while (s_date < e_date) {
        const temp_e_date = s_date.clone().add(1, "months");
        const new_e_date = temp_e_date > e_date ? e_date : temp_e_date;

        dates = [
            ...dates,
            [
                moment(s_date).format("YYYY-MM-DDT00:00:00.000") + "Z",
                moment(new_e_date).format("YYYY-MM-DDT23:59:59.000") + "Z"
            ]
        ];
        s_date.add(31, "days");
    }
    return dates;
};
export const getDatesBetweenDatesStandard = (startDate, endDate) => {
    let dates = [];
    let s_date = moment(startDate);
    let e_date = moment(endDate);

    while (s_date < e_date) {
        const temp_e_date = s_date.clone().add(1, "months");
        const new_e_date = temp_e_date > e_date ? e_date : temp_e_date;

        dates = [
            ...dates,
            [
                moment(s_date).format("YYYY-MM-DD"),
                moment(new_e_date).format("YYYY-MM-DD")
            ]
        ];
        s_date.add(31, "days");
    }
    return dates;
};

export const getDatesBetweenDatesTiktok = (startDate, endDate) => {
    let now = moment(startDate).clone(),
        dates = [];

    while (now.isBefore(endDate)) {
        const tempEndDate = now.clone();
        tempEndDate.add(1, "months");

        dates.push([
            now.format("YYYY-MM-DD"),
            tempEndDate.isSameOrBefore(endDate)
                ? tempEndDate.format("YYYY-MM-DD")
                : moment(endDate).format("YYYY-MM-DD")
        ]);
        now.add(1, "months").add(1, "days");
    }
    return dates;
};
