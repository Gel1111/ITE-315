<script setup>
defineProps({
    type: { type: String, default: 'success' },
    text: { type: String, default: '' },
});
</script>

<template>
    <div v-if="text" class="alert" :class="type">
        {{ text }}
    </div>
</template>