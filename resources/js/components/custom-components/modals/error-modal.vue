<template>
    <b-modal
        id="error-modal"
        :title="title"
        ok-only
        ok-variant="primary"
        hide-header
    >
        <slot></slot>
    </b-modal>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            default: ""
        }
    }
};
</script>

<style></style>
