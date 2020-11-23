//common utility functions

//fucntion to show currency formated numbers on the dashboard
export const displayCurrency = value => {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD"
    }).format(value);
};
