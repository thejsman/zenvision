<script>
import OtherExpenses from "./other-expenses";
import AllTransactions from "./all-transactions";

export default {
    components: { OtherExpenses, AllTransactions }
};
</script>

<template>
    <div class="mt-2">
        <div class="d-flex">
            <h4>
                Transactions
            </h4>
            <i
                class="fas fas fa-info-circle ml-1 t_line"
                v-b-tooltip.hover="
                    'This section represents all transactions that have occurred in the accounts that are linked to Zenvision.'
                "
            ></i>
        </div>

        <div class="col mt-1 p-0">
            <div>
                <!-- Hiding tabs  -->
                <!-- <b-card no-body>
                    <b-tabs
                        nav-class="nav-tabs-custom m-2 pt-2"
                        content-class="p-3 text-muted"
                    >
                        <OtherExpenses />
                        <AllTransactions />
                    </b-tabs>
                </b-card> -->

                <b-card no-body>
                    <OtherExpenses />
                </b-card>

                <div class="text-center text-muted mt-2">
                    <p class="align-items-center">
                        <i
                            class="fas fas fa-info-circle learn-more"
                            v-b-tooltip.hover="
                                'Learn more about the Mastersheet page...'
                            "
                        ></i>
                        Learn more about the <a href="#">Mastersheet page</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.t_line {
    line-height: 25px;
}
</style>
