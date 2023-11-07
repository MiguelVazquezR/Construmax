<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-gray3 rounded-full font-semibold text-xs text-white tracking-widest focus:bg-gray2 active:bg-gray2 focus:outline-none focus:ring-2 focus:ring-gray3 transition ease-in-out duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
        <slot />
    </button>
</template>
