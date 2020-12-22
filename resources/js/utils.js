/*
_________________________

=========================
common utility functions
=========================
_________________________

*/

import _, { values } from "lodash";

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
    console.log(title, value);
    data.forEach(d => {
        if (d.title === title) {
            d.value = `${value}`;
            d.loading = false;
        }
    });
};
