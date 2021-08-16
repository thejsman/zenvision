<script>
/**
 * Stat component -- specify the widget icon, title and value.
 */
import PlaceholderLoader from "../../components/custom-components/placeholder-loader.vue";

export default {
    components: { PlaceholderLoader },
    data() {
        return {
            showWarning: false
        };
    },
    mounted() {
        setTimeout(() => {
            if (this.loading === true) {
                this.showWarning = true;
            }
        }, 15000);
    },
    watch: {
        loading(value, newValue) {
            if (!this.loading) {
                this.showWarning = false;
            }

            if (this.loading) {
                setTimeout(() => {
                    if (this.loading === true) {
                        this.showWarning = true;
                    }
                }, 15000);
            }
        }
    },
    props: {
        title: {
            type: String,
            default: ""
        },
        value: {
            type: String,
            default: ""
        },
        id: {
            type: Number
        },
        channelData: {
            type: Array,
            default: () => []
        },
        loading: {
            type: Boolean,
            default: true
        },
        onClick: {
            type: Function,
            default: () => {}
        },
        totalSubscriptionCount: {
            type: Number,
            default: null
        },
        showIcon: {
            type: Boolean,
            default: false
        },
        iconName: {
            type: String,
            default: ""
        },
        toolTip: {
            type: String,
            default: ""
        },
        channelIcon: {
            type: String,
            default: ""
        }
    }
};
</script>

<template>
    <div
        class="card mini-stats-wid"
        :class="{ cogscard: title === 'COGS' || title === 'Subscriptions' }"
    >
        <!-- <PlaceholderLoader v-if="loading" /> -->
        <div class="card-body" @click="onClick">
            <div class="media">
                <div class="media-body">
                    <div
                        class="d-flex justify-content-between align-items-center mb-3"
                    >
                        <div class="font-weight-medium">
                            {{ title }}
                        </div>

                        <div class="d-flex justify-content-end">
                            <b-badge
                                variant="primary"
                                class="mr-2 mt-0"
                                v-if="totalSubscriptionCount"
                                >{{ totalSubscriptionCount }}</b-badge
                            >
                            <img
                                v-if="showWarning"
                                v-b-tooltip.hover
                                :title="toolTip"
                                :src="`/images/icons/data-warning.svg`"
                                alt
                                height="19"
                                class="channel-icons"
                            />
                            <img
                                v-if="channelIcon"
                                :src="`/images/icons/${this.channelIcon}`"
                                alt
                                height="19"
                                class="channel-icons ml-2"
                            />

                            <img
                                v-if="showIcon"
                                :src="`/images/icons/${this.iconName}`"
                                alt
                                height="19"
                                class="channel-icons"
                            />
                        </div>
                    </div>

                    <div class="mb-0 mt-1" v-if="loading">
                        <b-skeleton animation="wave" width="40%"></b-skeleton>
                    </div>
                    <h4 class="mb-0" v-else>
                        {{ value }}
                    </h4>
                </div>
            </div>
            <div v-for="data of channelData" :key="data.title" class="block">
                <span class="text-muted font-weight-medium pt-1 pb-1">{{
                    data.title
                }}</span>
                <span class="float-right">{{ data.value }}</span>
            </div>
        </div>
    </div>
</template>
<style>
.b-skeleton-text {
    height: 1rem;
}
</style>
