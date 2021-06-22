<template>
    <loading
        :active.sync="loadingStatus"
        :can-cancel="false"
        :is-full-page="true"
        :background-color="'#2F3863'"
    ></loading>
</template>

<script>
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";

export default {
    components: {
        Loading
    },
    props: {
        loadingStatus: {
            type: Boolean,
            default: true
        }
    }
};
</script>
