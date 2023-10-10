<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-3 py-2 bg-primarylight text-primary rounded-[15px] focus:outline-none focus:ring-2 focus:ring-primarylight disabled:opacity-25 transition ease-in-out duration-150">
        <slot />
    </button>
</template>
