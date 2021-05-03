<script>
/**
 * Stat component -- specify the widget icon, title and value.
 */
import PlaceholderLoader from "../../components/custom-components/placeholder-loader.vue";

export default {
    components: { PlaceholderLoader },
    data() {
        return { showWarning: false };
    },
    computed: {},
    mounted() {
        setTimeout(() => {
            if (this.loading) {
                this.showWarning = true;
            }
        }, 1500);
        // this.$nextTick(() => {
        //     console.log("inside nextTick callback:", this.$el.textContent); // => 'not updated'
        // });
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
            default: null
        },
        iconName: {
            type: String,
            default: ""
        },
        toolTip: {
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
                        class="d-flex justify-content-between align-items-baseline"
                    >
                        <p class="text-muted font-weight-medium">{{ title }}</p>
                        <b-badge
                            variant="primary"
                            class="p-2"
                            v-if="totalSubscriptionCount"
                            >{{ totalSubscriptionCount }}</b-badge
                        >
                        <div
                            v-if="showWarning"
                            v-b-tooltip.hover
                            :title="toolTip"
                        >
                            <img
                                :src="`/images/icons/data-warning.svg`"
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
