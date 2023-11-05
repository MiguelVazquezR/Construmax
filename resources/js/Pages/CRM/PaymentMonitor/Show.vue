<template>
   <AppLayout title="Seguimiento de pagos">
        <div class="flex justify-between text-lg mx-2 lg:mx-14 mt-11">
            <span>Seguimiento de pagos</span>
            <Link :href="route('crm.client-monitors.index')">
            <p class="flex items-center text-sm text-primary">
                <i class="fa-solid fa-arrow-left-long mr-2"></i>
                <span>Regresar</span>
            </p>
            </Link>
        </div>
        <div class="flex justify-between items-center">
            <span></span>
          <div class="mx-4 my-5">
            <Link  v-if="this.$page.props.auth.user.permissions.includes('Registrar pagos en seguimiento integral')" :href="route('crm.payment-monitors.create')">
              <PrimaryButton class="rounded-full">Registrar pago</PrimaryButton>
            </Link>
                <i v-if="this.$page.props.auth.user.permissions.includes('Editar pagos en seguimiento integral')" 
                @click="$inertia.get(route('crm.payment-monitors.edit', payment_monitor.data.id))" 
                class="fa-solid fa-pencil ml-3 text-primary rounded-full p-2 bg-[#FEDBBD] cursor-pointer"></i>
                <i v-if="this.$page.props.auth.user.permissions.includes('Eliminar pagos en seguimiento integral')" 
                @click="showConfirmModal = true" 
                class="fa-regular fa-trash-can ml-3 text-primary rounded-full p-2 bg-[#FEDBBD] cursor-pointer"></i>
          </div>
        </div>

         <!-- ------------- tabs section starts ------------- -->
      <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
        <div class="flex">
          <p @click="tabs = 1" :class="tabs == 1 ? 'bg-[#FEDBBD] rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Pagos
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

       <!-- ------------- Pago Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">

        <p class="font-bold col-span-2 mb-2">Información de la oportunidad</p>

          <span class="text-gray-500 my-2">Folio de oportunidad</span>
          <a class="hover:underline text-primary" :href="route('crm.opportunities.show', payment_monitor.data.opportunity?.id)">
            <span>{{ payment_monitor.data.opportunity?.folio }} - {{ payment_monitor.data.opportunity?.name }}</span></a>
          <span class="text-gray-500 my-2">Vendedor</span>
          <span>{{ payment_monitor.data.seller?.name }}</span>

          <p class="font-bold col-span-2 mb-2">Datos del pago</p>

            <span class="text-gray-500 my-2">Monto</span>
            <span>${{ payment_monitor.data.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
            <span class="text-gray-500 my-2">Método de pago</span>
            <span>{{ payment_monitor.data.payment_method }}</span>
            <span class="text-gray-500 my-2">Concepto</span>
            <span>{{ payment_monitor.data.concept }}</span>
            <span class="text-gray-500 my-2">Fecha de pago</span>
            <span>{{ payment_monitor.data.paid_at }}</span>
            <span class="text-gray-500 my-2">Observaciones</span>
            <span>{{ payment_monitor.data.notes }}</span>
        </div>

        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
          <p class="font-bold col-span-2 mb-2">Información del cliente</p>

            <span class="text-gray-500 my-2">Cliente</span>
          <span>{{ payment_monitor.data.customer?.name ?? '--' }}</span>
          <span class="text-gray-500 my-2">Sucursal</span>
          <span>{{ payment_monitor.data.branch ?? '--' }}</span>
          <span class="text-gray-500 my-2">Contacto</span>
          <span>{{ payment_monitor.data.contact_name }}</span>
          <span class="text-gray-500 my-2">Teléfono del contacto</span>
          <span>{{ payment_monitor.data.contact_phone }}</span>

          <p class="font-bold col-span-2 mb-2 mt-5">Archivos adjuntos</p>
          <div v-if=" payment_monitor.data.media?.length">
            <li v-for="file in payment_monitor.data.media" :key="file" class="flex items-center justify-between col-span-full">
              <a :href="file.original_url" target="_blank" class="flex items-center">
                <i :class="getFileTypeIcon(file.file_name)"></i>
                <span class="ml-2">{{ file.file_name }}</span>
              </a>
            </li>
          </div>
          <p class="text-sm text-gray-400" v-else><i class="fa-regular fa-file-excel mr-3"></i>No hay archivos adjuntos</p>
        </div>
      </div>
      <!-- ------------- Pago ends 1 ------------- -->

      <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
        <template #title> Eliminar registro de pago </template>
        <template #content> ¿Continuar con la eliminación? </template>
        <template #footer>
          <div>
            <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
            <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
          </div>
        </template>
      </ConfirmationModal>

    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import CancelButton from "@/Components/CancelButton.vue";
import { Link } from "@inertiajs/vue3";

export default {
data(){
    return {
        showConfirmModal: false,
        tabs: 1,
    }
},
components:{
    AppLayout,
    PrimaryButton,
    SecondaryButton,
    ConfirmationModal,
    CancelButton,
    Link
},
props:{
    payment_monitor: Object,
},
methods:{
    deleteItem() {
      this.$inertia.delete(route('crm.payment-monitors.destroy', this.payment_monitor.data.id));
    },
    getFileTypeIcon(fileName) {
      // Asocia extensiones de archivo a iconos
      const fileExtension = fileName.split('.').pop().toLowerCase();
      switch (fileExtension) {
        case 'pdf':
          return 'fa-regular fa-file-pdf text-red-700';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
          return 'fa-regular fa-image text-blue-300';
        default:
          return 'fa-regular fa-file-lines';
      }
    },
},
}
</script>
