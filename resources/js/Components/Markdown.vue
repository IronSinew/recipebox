<script setup>
import {computed} from "vue";
import {marked} from "marked";

const props = defineProps({
    body: {type: [String, null], required: false, default: ""}
});

const markdownToHtml = computed(() => marked.parse(props.body || "") )
</script>

<template>
    <div class="markdown-container">
        <div v-html="markdownToHtml"></div>
    </div>
</template>

<style lang="scss" scoped>
.markdown-container::v-deep {
    p {
        @apply mb-2;
    }

    h1, h2, h3, h4, h5 {
        @apply mb-3;
    }
}
</style>
