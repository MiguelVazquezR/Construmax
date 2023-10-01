<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-[#FD8827] border border-transparent rounded-full font-semibold text-xs text-white tracking-widest hover:bg-[#FD8827] focus:bg-[#FD8827] active:bg-[#FD8827] focus:outline-none focus:ring-2 focus:ring-[#fd8827] focus:ring-offset-2 transition ease-in-out duration-150">
        <slot />
    </button>
</template>
