<template>
  <div @click="$inertia.get(route('crm.opportunity-tasks.show', opportunityTask))"
    class="rounded-md shadow-md shadow-gray-400/100 group relative h-[196px] cursor-pointer w-80">
    <el-tooltip :content="'Prioridad: ' + opportunityTask?.priority" placement="top">
      <i :class="getPriorityStyles()" class="fa-solid fa-circle text-[9px] absolute top-3 right-2 p-1"></i>
    </el-tooltip>
    <div class="py-3 px-4">
      <p class="truncate" :class="opportunityTask?.finished_at ? 'line-through' : ''">{{ opportunityTask?.name }}</p>
      <div class="flex justify-between items-center">
        <p class="text-gray-400 mt-3 mb-2">Responsable</p>
        <el-tooltip v-if="opportunityTask?.media?.length" content="Archivos adjuntos" placement="top">
          <i @click.stop="" class="fa-solid fa-paperclip rounded-full p-2"></i>
        </el-tooltip>
      </div>
      <figure>
        <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full items-center">
          <img class="h-11 w-11 rounded-full object-cover" :src="opportunityTask?.asigned?.profile_photo_url"
            :alt="opportunityTask?.asigned?.name" />
          <p class="ml-3">{{ opportunityTask?.asigned?.name }}</p>
        </div>
      </figure>
      <div class="flex items-center justify-between mt-3">
        <p :class="opportunityTask?.deadline_status === 'Atrasadas' ? 'bg-[#FEB2C4]' : 'bg-[#F6F89E]'"
          class="rounded-full px-3 text-sm">{{ opportunityTask?.deadline_status !== 'Terminar hoy' ?
            opportunityTask?.limit_date + ', ' + opportunityTask?.time : opportunityTask?.time }}</p>
        <div class="flex items-center space-x-3">
          <i v-if="opportunityTask?.finished_at" class="fa-solid fa-check text-lg text-green-500"></i>
          <p class="text-xs text-gray-400">{{ opportunityTask?.comments?.length }}<i
              class="fa-regular fa-comments text-lg rounded-full ml-2"></i></p>
        </div>
      </div>
    </div>
    <button @click.stop="$emit('task-done', opportunityTask?.id)" v-if="!opportunityTask?.finished_at"
      class="text-sm text-white bg-red-600 w-full py-1 rounded-b-md opacity-0 group-hover:opacity-100 transition-opacity duration-300 ease-in-out">
      Hecho
    </button>
  </div>
</template>
<script>

export default {
  data() {
    return {
    }
  },
  props: {
    opportunityTask: Object,
  },
  methods: {
    getPriorityStyles() {
      if (this.opportunityTask?.priority === 'Baja') {
        return 'text-[#87CEEB]';
      } else if (this.opportunityTask?.priority === 'Media') {
        return 'text-[#F2C940]';
      } else if (this.opportunityTask?.priority === 'Alta') {
        return 'text-[#FB2A2A]';
      }
    },
  },
}
</script>


