<template>
<div class="overflow-x-auto">
  <table class="w-full mx-auto">
        <thead>
          <tr class="text-center">
            <th class="font-bold pb-5 px-14 lg:px-2">
              Folio <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-5 px-14 lg:px-2">
              Oportunidad <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-5 px-14 lg:px-2">
              Estatus <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-5 px-14 lg:px-2">
              Fecha inicio <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-5 px-14 lg:px-2">
              Estimación de cierre <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-5 px-14 lg:px-2">
              Cerrada el <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-5 px-14 lg:px-2">
              Pagado el <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="opportunity in opportunities" :key="opportunity"
            class="mb-4 cursor-pointer hover:bg-[#FEDBBD]"
            @click="$inertia.get(route('crm.opportunities.show', opportunity.id))"
          >
            <td class="text-center text-secondary py-2 px-2 rounded-l-full">
              {{ opportunity.folio }}
            </td>
            <td class="text-center py-2 px-2 ">
              {{ opportunity.name }}
            </td>
            <td class="text-center py-2 px-2">
              <span
                class="py-1 px-4 rounded-full"
                :class="getStatusStyles(opportunity)"
                >{{ opportunity.status }}</span
              >
            </td>
            <td class="text-center py-2 px-2">
              <span
                class="py-1 px-2"
                >{{ opportunity.start_date}}</span
              >
            </td>
            <td class="text-center py-2 px-2">
              {{ opportunity.close_date }}
            </td>
            <td class="text-center py-2 px-2">
              {{ opportunity.finished_at ?? "--" }}
            </td>
            <td class="text-center py-2 px-2 rounded-r-full">
              {{ opportunity.paid_at ?? "--" }}
            </td>
          </tr>
        </tbody>
      </table>
  </div>
</template>

<script>
export default {
  data(){
    return{
      
      }
},
components:{

},
props:{
opportunities: Array,
},
methods:{
    getStatusStyles(opportunity){
        if (opportunity.status === 'Nueva') {
            return 'text-[#9A9A9A] bg-[#CCCCCCCC]';
        } else if (opportunity.status === 'Pendiente') {
             return 'text-[#C88C3C] bg-[#F3FD85]';
        } else if (opportunity.status === 'Cerrada') {
             return 'text-[#FD8827] bg-[#FEDBBD]';
        } else if (opportunity.status === 'Pagado') {
             return 'text-[#37951F] bg-[#ADFEB5]';
        } else if (opportunity.status === 'Perdida') {
             return 'text-[#9E0FA9] bg-[#F7B7FC]';
        }
    },
}
}
</script>
