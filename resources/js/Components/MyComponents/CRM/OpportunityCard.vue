<template>
  <div @click="$inertia.get(route('crm.opportunities.show', opportunity.id))"
    class="border border-[#D9D9D9] text-left rounded-md py-2 px-7 shadow-md shadow-gray-400/100 h-32 relative cursor-pointer w-72 lg:w-[360px]"
  >
    <div class="flex items-center absolute top-2 left-2 cursor-move p-1">
      <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
      <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
    </div>
    <el-tooltip :content="'Prioridad: ' + opportunity.priority.label" placement="top">
        <i :class="opportunity.priority.color" class="fa-solid fa-circle text-[9px] absolute top-3 right-2 p-1"></i>
    </el-tooltip>
<p class="font-bold truncate">{{ opportunity?.name }}</p>
    <p><strong>Cliente:</strong> {{ opportunity.customer?.name }}</p>
    <p><strong>Sucursal:</strong> {{ opportunity.branch }}</p>
    <p>${{ opportunity?.amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
    <div class="flex justify-between mt-3">
      <p class="text-gray-400">Actividades <span class="text-white rounded-full px-2 bg-primary text-xs">{{ opportunity?.opportunityTasksQuantity }}</span></p>
      <p>{{ opportunity?.created_at?.diffForHumans }}</p>
    </div>
  </div>
</template>

<script>
export default {
    data(){

        return {

        }
    },
    components:{

    },
    methods:{

    },
    props:{
        opportunity: Object
    },
};
</script>
