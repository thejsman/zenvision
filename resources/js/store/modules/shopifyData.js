import axios from "axios";

import _ from "lodash";

const state = {
    hasShopifyStore: null,
    hasShopifyStorePA: null,
    shopifyStores: [],
    orders: [],
    allOrders: [],
    cogsTotal: null,
    paCogsTotal: 0,
    inventoryTotal: null,
    storeBalance: 0,
    storeReserves: 0,
    shopifyCogsArray: [],
    inventoryChangedProducts: [],
    searchText: "",
    shopifyAbandonedCart: null,
    shopifyAverageUnitsPerOrder: null,
    shopifyRefundTotal: null,
    supplierPayableArray: null
};
const getters = {
    hasShopifyStoreCS: state => state.hasShopifyStore,
    hasShopifyStorePA: state => state.hasShopifyStorePA,
    shopifyOrders: state => state.orders,
    shopifyAllOrders: state => state.allOrders,
    shopifyStores: state => state.shopifyStores,
    ShopifyCogsTotal: state => state.cogsTotal,
    ShopifyCogsTotalPA: state => state.paCogsTotal,
    shopifyStoreBalance: state => state.storeBalance,
    storeReserves: state => state.storeReserves,
    exclamationIconStatus: state => state.exclamationIconStatus,
    numberOfOrders: state => state.orders.length,
    inventoryTotal: state => state.inventoryTotal,
    shopifyCogsArray: state => state.shopifyCogsArray,
    inventoryChangedProducts: state => state.inventoryChangedProducts,
    inventorySearchText: state => state.searchText,
    shopifyRevenue: state =>
        _.sumBy(state.orders, order => parseFloat(order.total_price)),
    shopifyShippingRevenue: state =>
        _.sumBy(state.orders, order =>
            _.sumBy(order.shipping_lines, line => parseFloat(line.price))
        ),
    shopifyTotalTax: state =>
        _.sumBy(state.orders, order => parseFloat(order.total_tax)),
    shopifyDiscounts: state =>
        _.sumBy(state.orders, order => parseFloat(order.total_discounts)),
    shopifyAbandonedCartCount: state => state.shopifyAbandonedCart,
    shopifyAverageUnitsPerOrder: state => state.shopifyAverageUnitsPerOrder,
    shopifyRefundTotal: state => state.shopifyRefundTotal,
    supplierPayableArray: state => state.supplierPayableArray
};
const actions = {
    toggleShopifyStoreStatus: ({ commit }, payload) => {
        commit("TOGGGLE_SHOPIFY_STORE_STATUS", payload);
    },
    getShopifyStores: async ({ commit, dispatch }, section = "PA") => {
        try {
            const result = await axios.get("/user/stores");
            const data = result.data;

            if (data.length > 0) {
                commit("SET_SHOPIFY_STORES", data);
                const status = data.map(
                    element => element.enabled_on_dashboard
                );

                if (section === "MS") {
                    dispatch("getShopifyStoreAllOrders");
                    dispatch("getShopifyStoreBalance");
                    dispatch("getShopifyStoreReserves");
                    dispatch("getShopifyTotalInventory");
                    commit("TOGGGLE_SHOPIFY_STORE_STATUS", true);
                } else {
                    dispatch("getShopifyStoreOrders");
                    dispatch("getShopifyRefundTotal");

                    commit(
                        "TOGGGLE_SHOPIFY_STORE_STATUS_PA",
                        status.includes(true)
                    );
                }
            } else {
                commit("SET_SHOPIFY_STORES", []);
                commit("SET_TOTAL_INVENTORY", 0);
                commit("SET_SHOPIFY_RESERVES", 0);
                commit("SET_SHOPIFY_ORDERS", []);
                commit("SET_COGS_ALL_ORDERS", {
                    data: state.allOrders,
                    supplierPayableData: []
                });
                commit("SET_SHOPIFY_BALANCE", 0);
                commit("SET_ABANDONED_CART_COUNT", 0);
                commit("TOGGGLE_SHOPIFY_STORE_STATUS", false);
                commit("TOGGGLE_SHOPIFY_STORE_STATUS_PA", false);
                if (section === "MS") {
                    dispatch("getSupplierPayableTotal");
                }
            }
        } catch (err) {
            commit("SET_SHOPIFY_STORES", []);
            commit("TOGGGLE_SHOPIFY_STORE_STATUS", false);
            commit("TOGGGLE_SHOPIFY_STORE_STATUS_PA", false);
            console.log({ err });
        }
    },
    getShopifyStoreOrders: async ({ commit, dispatch, rootGetters }) => {
        try {
            const { data } = await axios.get("shopify-orders", {
                params: {
                    s_date: rootGetters.startDateS,
                    e_date: rootGetters.endDateS
                }
            });

            commit("SET_SHOPIFY_ORDERS", data);
            dispatch("getAverageUnitsCount");
            dispatch("getShopifyCogsTotalPA");
        } catch (err) {
            console.log(err);
            commit("SET_SHOPIFY_ORDERS", []);
        }
    },
    getShopifyStoreAllOrders: async ({ commit, dispatch }) => {
        try {
            const { data } = await axios.get("shopify-allorders");
            commit("SET_SHOPIFY_ALL_ORDERS", data);
            setTimeout(() => {
                dispatch("getSupplierPayableTotal");
            }, 1000);
        } catch (err) {
            console.log(err);
            commit("SET_SHOPIFY_ALL_ORDERS", []);
        }
    },
    getSupplierPayableTotal: async ({ state, commit }) => {
        try {
            const supplierPayableResult = await axios.get("supplierpayable");
            const supplierPayableData = supplierPayableResult.data;

            commit("SET_SUPPLIER_PAYABLE", supplierPayableData);

            commit("SET_COGS_ALL_ORDERS", {
                data: state.allOrders,
                supplierPayableData
            });
        } catch (err) {
            commit("SET_SUPPLIER_PAYABLE", []);
            commit("SET_COGS_ALL_ORDERS", {
                data: state.allOrders,
                supplierPayableData: []
            });
            console.log(err);
        }
    },

    getShopifyStoreBalance: async ({ commit }) => {
        try {
            const response = await axios.get("shopify-balance");
            commit("SET_SHOPIFY_BALANCE", response.data);
        } catch (err) {
            console.log(err);
            commit("SET_SHOPIFY_BALANCE", 0);
        }
    },
    getShopifyRefundTotal: async ({ commit, rootGetters }) => {
        try {
            const response = await axios.get("shopify-refunds", {
                params: {
                    start_date: rootGetters.startDateS,
                    end_date: rootGetters.endDateS
                }
            });
            commit("SET_SHOPIFY_REFUND_TOTAL", response.data);
        } catch (err) {
            console.log(err);
            commit("SET_SHOPIFY_REFUND_TOTAL", 0);
        }
    },
    getShopifyStoreReserves: async ({ commit }) => {
        try {
            const response = await axios.get("shopify-reserves");
            commit("SET_SHOPIFY_RESERVES", response.data);

            commit("MasterSheet/SET_ASSETS_RESERVES_TOTAL", response.data, {
                root: true
            });
        } catch (err) {
            console.log(err);
            commit("SET_SHOPIFY_RESERVES", 0);
            commit("MasterSheet/SET_ASSETS_RESERVES_TOTAL", 0, {
                root: true
            });
        }
    },
    getCogsIconStatus: async ({ commit }) => {
        try {
            const { data } = await axios.get("/cogsicon");
        } catch (err) {
            console.log({ err });
        }
    },
    getShopifyCogsTotalPA: async ({ commit, state }) => {
        const cogs = state.orders.reduce((sum, current) => {
            return sum + parseFloat(current.total_cost);
        }, 0);
        commit("SET_COGS_ORDERS_PA", cogs);
    },
    getNumberOfOrders: async ({ commit, state }) => {
        const numberOfOrders = state.orders.length;

        commit("SET_NUMBER_OF_ORDERS", numberOfOrders);
    },
    getShopifyTotalInventory: async ({ commit }) => {
        try {
            const { data } = await axios.get("cogs");
            commit("SET_COGS_ARRAY", data.products);
            const totalInventory = _.sumBy(
                data.products,
                product => parseFloat(product.total_inventory) || 0
            );

            commit("SET_TOTAL_INVENTORY", totalInventory);
        } catch (err) {
            commit("SET_TOTAL_INVENTORY", 0);
            console.log({ err });
        }
    },
    setSearchText: ({ commit }, text) => {
        commit("SET_SEARCH_TEXT", text);
    },
    addToChangedProducts: ({ commit }, product) => {
        commit("SET_CHANGED_PRODUCTS", product);
    },
    getAbandonedCartCount: async ({ commit }) => {
        try {
            const { data } = await axios.get("abandonedcart");
            commit("SET_ABANDONED_CART_COUNT", data);
        } catch (err) {
            console.log({ err });
            commit("SET_ABANDONED_CART_COUNT", 0);
        }
    },
    getAverageUnitsCount: async ({ commit, state }) => {
        try {
            const order_ids = state.orders.map(order => order.order_id);

            if (order_ids.length > 0) {
                const { data } = await axios.get("getavgunitperorder", {
                    params: {
                        order_ids
                    }
                });
                commit("SET_AVERAGE_UNITS_COUNT", data);
            } else {
                commit("SET_AVERAGE_UNITS_COUNT", 0);
            }
        } catch (err) {
            console.log({ err });
            commit("SET_AVERAGE_UNITS_COUNT", 0);
        }
    },
    getDataAfterDateChange: async ({ commit, dispatch }) => {
        await dispatch("getShopifyStoreOrders");
    },
    removeItemfromChangedProducts: ({ commit, dispatch }, product) => {
        commit("REMOVE_ITEM_FORM_CHANGED_PRODUCTS", product);
        dispatch("getShopifyTotalInventory");
    },
    removeShopifyAccount: async ({ commit }, account) => {
        commit("REMOVE_SHOPIFY_ACCOUNT", account);
    }
};
const mutations = {
    TOGGGLE_SHOPIFY_STORE_STATUS: (state, status) => {
        state.hasShopifyStore = status;
    },
    TOGGGLE_SHOPIFY_STORE_STATUS_PA: (state, status) => {
        state.hasShopifyStorePA = status;
    },
    SET_SHOPIFY_STORES: (state, payload) => {
        if (payload.length > 0) {
            state.shopifyStores = payload;
        } else {
            state.shopifyStores = [];
        }
    },
    SET_SHOPIFY_ORDERS: (state, payload) => {
        state.orders = payload;
    },
    SET_SHOPIFY_ALL_ORDERS: (state, payload) => {
        if (payload.length > 0) {
            state.allOrders = payload;
        } else {
            state.allOrders = [];
        }
    },
    SET_COGS_ALL_ORDERS: (state, payload) => {
        const { data, supplierPayableData } = payload;

        const spOrderNumbers = _.map(supplierPayableData, sp =>
            sp.type === "shopify" ? sp.reference_number : null
        ).filter(e => e);

        const ordersCogsTotal = data.reduce((sum, current) => {
            return !spOrderNumbers.includes(current.order_number)
                ? sum + current.total_cost
                : sum + 0;
        }, 0);

        const supplierPayableTotal = supplierPayableData.reduce(
            (sum, current) => {
                return sum + parseFloat(current.amount);
            },
            0
        );
        // console.log(supplierPayableTotal, ordersCogsTotal);
        // console.log("setting it up", supplierPayableTotal + ordersCogsTotal);

        state.cogsTotal = supplierPayableTotal + ordersCogsTotal;
    },
    SET_COGS_ORDERS_PA: (state, payload) => {
        state.paCogsTotal = payload;
    },
    SET_SHOPIFY_BALANCE: (state, payload) => {
        state.storeBalance = payload;
    },
    SET_SHOPIFY_RESERVES: (state, payload) => {
        state.storeReserves = payload;
    },
    SET_EXCLAMATION_ICON_STATUS: (state, payload) => {
        state.exclamationIconStatus = payload === 0 ? false : true;
    },
    SET_NUMBER_OF_ORDERS: (state, payload) => (state.numberOfOrders = payload),
    SET_TOTAL_INVENTORY: (state, payload) => {
        state.inventoryTotal = payload;
    },
    SET_COGS_ARRAY: (state, payload) => {
        const filteredArray = payload.filter(
            product => product.total_inventory !== null
        );
        state.inventoryChangedProducts = filteredArray;
        state.shopifyCogsArray = payload;
    },
    SET_CHANGED_PRODUCTS: (state, payload) => {
        state.inventoryChangedProducts = Array.from(new Set([...payload]));
    },
    SET_SEARCH_TEXT: (state, payload) => {
        state.searchText = payload;
    },
    SET_ABANDONED_CART_COUNT: (state, payload) => {
        state.shopifyAbandonedCart = payload;
    },
    SET_AVERAGE_UNITS_COUNT: (state, payload) => {
        state.shopifyAverageUnitsPerOrder = payload;
    },
    SET_SHOPIFY_REFUND_TOTAL: (state, payload) => {
        state.shopifyRefundTotal = payload;
    },
    SET_SUPPLIER_PAYABLE: (state, payload) =>
        (state.supplierPayableArray = payload),
    REMOVE_ITEM_FORM_CHANGED_PRODUCTS: (state, payload) => {
        state.inventoryChangedProducts = state.inventoryChangedProducts.filter(
            product => product.id !== payload.id
        );
    },
    REMOVE_SHOPIFY_ACCOUNT: (state, payload) => {
        state.shopifyStores = state.shopifyStores.filter(
            account => account.id !== payload.id
        );
        if (state.shopifyStores.length === 0) {
            state.hasShopifyStore = false;
            state.orders = [];
            state.allOrders = [];
            state.cogsTotal = 0;
            state.storeBalance = 0;
            state.storeReserves = 0;
            state.shopifyCogsArray = [];
            state.inventoryChangedProducts = [];
        }
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
