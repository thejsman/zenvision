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

export const updateAdData = (data, provider, value) => {
    data.forEach(d => {
        if (
            d.iconName !== undefined &&
            d.iconName.includes(provider.toLowerCase())
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

// Function to calculate dates rage of 30days for  paypal transactions api call
export const getDatesBetweenDates = (startDate, endDate) => {
    let dates = [];
    let s_date = moment(startDate);
    let e_date = moment(endDate);

    while (s_date < e_date) {
        const temp_e_date = s_date.clone().add(30, "days");
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
