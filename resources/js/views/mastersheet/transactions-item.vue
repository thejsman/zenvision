<template>
    <div class="d-flex justify-content-between align-items-center my-1">
        <div class="bgred">
            <div class="d-flex align-items-center px-3 py-2">
                <img
                    v-if="item.type === 'paypal'"
                    src="/images/icons/paypal-transactions.svg"
                    alt
                    height="30"
                    width="30"
                    class="channel-icons"
                />
                <img
                    v-if="item.type === 'stripe'"
                    src="/images/icons/stripe-icon.svg"
                    alt
                    height="30"
                    width="30"
                    class="channel-icons"
                />
                <img
                    v-if="
                        [
                            'depository',
                            'bank',
                            'credit',
                            'investment',
                            'loan',
                            'other'
                        ].includes(item.type)
                    "
                    :src="`/images/bank-icons/${item.logo}.png`"
                    alt
                    height="30"
                    width="30"
                    class="channel-icons bg-white rounded"
                />
            </div>
            {{ item.description }}
        </div>
        <div class="bggreen">
            <div class="d-flex justify-content-end align-items-center">
                <div class="transaction_select">
                    <b-dropdown
                        variant="outline-light"
                        class="align-items-left"
                        right
                    >
                        <template #button-content>
                            {{
                                transactionArray.includes(item.id)
                                    ? "Supplier Payable"
                                    : "Label"
                            }}
                            <i class="fas fa-angle-down pl-2"></i>
                        </template>

                        <b-dropdown-item
                            @click="onChange($event, item)"
                            v-if="!transactionArray.includes(item.id)"
                            >Supplier Payable</b-dropdown-item
                        >
                        <b-dropdown-item
                            @click="onChange($event, item, true)"
                            v-if="transactionArray.includes(item.id)"
                            >Label</b-dropdown-item
                        >
                    </b-dropdown>
                </div>

                <div class="transaction_amount">{{ item.amount }}</div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
export default {
    props: {
        item: {
            type: Object,
            default: () => {}
        }
    },
    computed: {
        ...mapGetters(["supplierPayableArray"]),
        transactionArray() {
            if (this.supplierPayableArray) {
                return this.supplierPayableArray
                    .map(sp => sp.reference_number)
                    .filter(e => e);
            } else {
                return [];
            }
        }
    },
    methods: {
        ...mapActions(["getSupplierPayableTotal"]),
        async onChange(e, item, removeItem) {
            try {
                if (removeItem === true) {
                    await axios.delete(`supplierpayable-txn/${item.id}`);
                    await this.getSupplierPayableTotal();
                } else {
                    const form = {};
                    form.title = item.description;
                    form.type = item.type;
                    form.amount = -parseFloat(
                        item.amount.replace(/[^0-9.-]+/g, "")
                    );
                    form.reference_number = item.id;

                    await axios.post("supplierpayable", { ...form });

                    this.getSupplierPayableTotal();
                }
            } catch (err) {
                console.log({ err });
            }
        }
    }
};
</script>

<style>
.bgred {
    display: flex;
    align-items: center;
    flex-grow: 1;
}
.bggreen {
    flex-grow: 1;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex-grow: 1;
}

.transaction_amount {
    min-width: 100px;
    text-align: right;
    padding-right: 30px;
}
.transaction_select {
    min-width: 80px;
}
.transaction_select .dropdown-item {
    padding: 3px 20px;
    text-align: right;
}
@media (max-width: 600px) {
    .transaction_select {
        min-width: 0px;
    }
    .transaction_amount {
        min-width: 0px;
        padding: 0px 5px;
    }
}
</style>
