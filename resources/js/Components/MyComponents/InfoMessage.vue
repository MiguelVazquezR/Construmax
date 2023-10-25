<template>
  <div class="bg-primarylight text-primary flex items-center space-x-4 rounded-[5px] px-2 py-1 text-sm">
    <div
      class="rounded-full border border-primary w-3 h-3 flex items-center justify-center"
    >
      <i class="fa-solid fa-info text-[7px]"></i>
    </div>
    <p>{{ message }}</p>
    <button @click="$emit('close')" type="button" class="">
      <i class="fa-regular fa-circle-xmark"></i>
    </button>
  </div>
</template>
<script>
export default {
  props: {
    message: String,
  },
  emits: ["close"],
};
</script>
