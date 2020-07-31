<template>
    <component :is="component_name" />
</template>
<script>
export default {
    props: {
        component: {
            type: String,
            required: true
        }
    },
    computed: {
        component_name() {
            return !!this.$root.$options.components[this.component] ? this.component : "pages-404";
        }
    }
};
</script>
