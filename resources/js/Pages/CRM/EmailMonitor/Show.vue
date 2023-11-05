<template>
  <AppLayout title="Seguimiento de Correo">
        <div class="flex justify-between text-lg mx-2 lg:mx-14 mt-11">
            <span>Seguimiento de Correo electrónico</span>
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
            <Link  v-if="this.$page.props.auth.user.permissions.includes('Enviar correos en seguimiento integral')" :href="route('crm.email-monitors.create')">
              <PrimaryButton class="rounded-full">Enviar correo</PrimaryButton>
            </Link>
                <i v-if="this.$page.props.auth.user.permissions.includes('Eliminar correos en seguimiento integral')" 
                @click="showConfirmModal = true" 
                class="fa-regular fa-trash-can ml-3 text-primary rounded-full p-2 bg-[#FEDBBD] cursor-pointer"></i>
          </div>
        </div>

          <!-- ------------- tabs section starts ------------- -->
      <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
        <div class="flex">
          <p @click="tabs = 1" :class="tabs == 1 ? 'bg-[#FEDBBD] rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Correo electrónico
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- correo Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">

        <p class="font-bold col-span-2 mb-2">Información de la oportunidad</p>

          <span class="text-gray-500 my-2">Folio de oportunidad</span>
          <a class="hover:underline text-primary" :href="route('crm.opportunities.show', email_monitor.data.opportunity?.id)">
            <span>{{ email_monitor.data.opportunity?.folio }} - {{ email_monitor.data.opportunity?.name }}</span></a>
          <span class="text-gray-500 my-2">Vendedor</span>
          <span>{{ email_monitor.data.seller?.name }}</span>
        </div>

        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">

          <p class="font-bold col-span-2 mb-5">Información del correo electrónico</p>
          <span class="text-gray-500">Enviado por</span>
          <span>{{ email_monitor.data.seller.name }}</span>
          <span class="text-gray-500 my-2">Cliente</span>
          <span>{{ email_monitor.data.customer?.name ?? '--' }}</span>
          <span class="text-gray-500 my-2">Sucursal</span>
          <span>{{ email_monitor.data.branch ?? '--' }}</span>
          <span class="text-gray-500 my-2">Enviado a</span>
          <span>{{ email_monitor.data.contact_name }}</span>
          <span class="text-gray-500 my-2">Correo del contacto</span>
          <span>{{ email_monitor.data.contact_email }}</span>
          <span class="text-gray-500 my-2">Asunto</span>
          <span>{{ email_monitor.data.subject }}</span>
          <span class="text-gray-500 my-2">Contenido</span>
          <span>{{ email_monitor.data.content }}</span>
          <span class="text-gray-500 my-2">Enviado el</span>
          <span>{{ email_monitor.data.created_at }}</span>
        </div>
      </div>
      <!-- ------------- correo ends 1 ------------- -->

      <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
        <template #title> Eliminar interacción de correo electrónico </template>
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
    Link,
},  
props:{
    email_monitor: Object,
},
methods:{
    deleteItem() {
      this.$inertia.delete(route('crm.email-monitors.destroy', this.email_monitor.data.id));
    },
}
}
</script>
