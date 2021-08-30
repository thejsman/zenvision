<template>
    <div class="flex-start">
        <div class="d-flex flex-row">
            <div v-for="store in stores" :key="store.id" class="dropdown">
                <div
                    class="border rounded p-2 dropbtn mx-1 bg-white channel-icons-grow"
                    :class="{
                        'border-primary': store.enabled_on_dashboard,
                        inactive:
                            currentChannel === 'PA'
                                ? !store.enabled_on_dashboard
                                : null
                    }"
                    @click="disableFeature ? handleClick(store) : null"
                    v-b-tooltip.hover="store.store_name"
                >
                    <img src="/images/icons/shopify-icon.svg" alt height="21" />
                </div>
                <div class="dropdown-content">
                    <a
                        href="#"
                        @click="handleClick(store)"
                        v-if="disableFeature"
                    >
                        {{
                            store.enabled_on_dashboard ? "Disable" : "Enable"
                        }}</a
                    >
                    <a href="#" @click="showMsgBoxOne(store, $event)">Remove</a>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";
import { eventBus } from "../../app";
import { mapGetters, mapActions } from "vuex";
export default {
    name: "StoreIcon",
    data() {
        return {
            stores: []
        };
    },
    computed: { ...mapGetters(["currentChannel"]) },
    props: {
        disableFeature: {
            type: Boolean,
            default: true
        }
    },

    created: function() {
        this.getStores();
    },
    methods: {
        ...mapActions([
            "removeShopifyAccount",
            "getShopifyStores",
            "toggleLoadingStatus",
            "getShopifyCogsTotalPA"
        ]),
        async getStores() {
            try {
                const result = await axios.get("/user/stores");
                this.stores = result.data;
                result.data.length
                    ? this.checkEnabledStatus(result.data)
                    : eventBus.$emit("hasShopifyAccount", false);
            } catch (err) {
                eventBus.$emit("hasShopifyAccount", false);
                this.stores = [];
            }
        },
        checkEnabledStatus(data) {
            const status = data.map(element => element.enabled_on_dashboard);
            status.includes(true)
                ? eventBus.$emit("hasShopifyAccount", true)
                : eventBus.$emit("hasShopifyAccount", false);
        },
        async handleClick(store) {
            try {
                this.toggleLoadingStatus(true);
                await axios.patch("shopifystore", store);
                await this.getShopifyStores(this.currentChannel);
                eventBus.$emit("toggleShopifyStore");

                this.toggleLoadingStatus(false);
                // to remove below line
                this.getStores();
            } catch (error) {
                this.toggleLoadingStatus(false);
                console.log(error);
            }
        },
        async removeChannel(store, event) {
            try {
                this.toggleLoadingStatus(true);

                await axios.patch("shopifystoredelete", store);
                await this.removeShopifyAccount(store);
                await this.getShopifyStores(this.currentChannel);
                eventBus.$emit("toggleShopifyStore");

                this.getStores();
                this.toggleLoadingStatus(false);
            } catch (error) {
                this.toggleLoadingStatus(false);
                console.log(error);
            }
        },
        showMsgBoxOne(store) {
            this.boxOne = "";
            this.$bvModal

                .msgBoxConfirm(
                    "Are you sure you want to remove this account? Please note that removing accounts can cause misleading financial data. We encourage you to include all of your E-Com business financial accounts and Shopify stores in Zenvision."
                )

                .then(value => {
                    this.boxOne = value;

                    if (value) {
                        this.removeChannel(store);
                    }
                })
                .catch(err => {
                    // An error occurred
                });
        }
    }
};
</script>
