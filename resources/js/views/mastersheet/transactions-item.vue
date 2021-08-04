<template>
    <div class="d-flex justify-content-between align-items-center">
        <div class="bgred">
            <div class="d-flex align-items-center px-3 py-1">
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
                    v-if="item.type === 'bank'"
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
            <div class="row">
                <div class="col-6">
                    <select
                        class="form-control label_class"
                        @change="onChange($event, item)"
                    >
                        <option value="null">Label</option>
                        <option
                            value="supplier_payable"
                            :selected="transactionArray.includes(item.id)"
                            >Supplier Payable</option
                        >
                    </select>
                </div>
                <div class="col-6">{{ item.amount }}</div>
            </div>
        </div>
    </div>
    <!-- <div class="d-flex justify-content-between align-items-center">
        <div>
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
                v-if="item.type === 'bank'"
                :src="`/images/bank-icons/${item.logo}.png`"
                alt
                height="30"
                width="30"
                class="channel-icons bg-white rounded"
            />

            {{ item.description }}
        </div>
        <div class="d-flex">
            <select
                class="form-control label_class"
                @change="onChange($event, item)"
            >
                <option value="null">Label</option>
                <option
                    value="supplier_payable"
                    :selected="transactionArray.includes(item.id)"
                    >Supplier Payable</option
                >
            </select>
            <p class="pt-3">{{ item.amount }}</p>
        </div>
    </div> -->
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
        async onChange(e, item) {
            try {
                if (e.target.value === "null") {
                    console.log({ item });
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
</style>
