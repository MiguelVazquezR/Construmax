<template>
<div class="overflow-x-auto">
    <table class="w-full mx-auto">
        <thead>
          <tr class="text-left">
            <th class="font-bold pb-5 min-w-[90px]">Folio <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
            </th>
            <th class="font-bold pb-5 min-w-[170px]">Tipo de interacción <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
            </th>
            <th class="font-bold pb-5 min-w-[90px]">Fecha <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
            </th>
            <th class="font-bold pb-5 min-w-[90px]">Concepto <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
            </th>
            <th class="font-bold pb-5 min-w-[90px]">Vendedor <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="monitor in client_monitors" :key="monitor"
            class="mb-4 cursor-pointer hover:bg-[#FEDBBD]"
            @click="showMonitorType(monitor)"
          >
            <td class="text-left text-secondary py-2 px-2 rounded-l-full">
              {{ monitor.folio }}
            </td>
            <td class="text-left py-2 px-2 ">
              {{ monitor.type }}
            </td>
            <td class="text-left py-2 px-2">
              <span
                class="py-1 rounded-full"
                >{{ monitor.date }}</span
              >
            </td>
            <td class="text-left py-2 px-2">
              <span
                class="py-1 px-2"
                >{{ monitor.concept}}</span
              >
            </td>
            <td class="text-left py-2 px-2 rounded-r-full">
              {{ monitor.seller?.name }}
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
client_monitors: Array,
},
methods:{
showMonitorType(monitor) {
      if (monitor.type == 'Correo electrónico') {
        this.$inertia.get(route('crm.email-monitors.show', monitor.emailMonitor?.id));
      } else if (monitor.type == 'Pago') {
        this.$inertia.get(route('crm.payment-monitors.show', monitor.paymentMonitor?.id));
      } else if (monitor.type == 'Reunión') {
        this.$inertia.get(route('crm.meeting-monitors.show', monitor.meetingMonitor?.id));
      }
    },
}
}
</script>
