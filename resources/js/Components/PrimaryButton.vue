<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-full font-semibold text-xs text-white tracking-widest hover:bg-primary focus:bg-primary active:bg-primary focus:outline-none focus:ring-2 focus:ring-primary transition ease-in-out duration-150 disabled:cursor-not-allowed disabled:opacity-50">
        <slot />
    </button>
</template>
