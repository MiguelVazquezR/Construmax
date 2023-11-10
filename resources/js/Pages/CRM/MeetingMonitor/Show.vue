<template>
  <AppLayout title="Seguimiento de reuniones">
    <div class="flex justify-between text-lg mx-2 lg:mx-14 mt-11">
      <span>Seguimiento de reuniones</span>
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
        <Link v-if="this.$page.props.auth.user.permissions.includes('Agendar citas en seguimiento integral')"
          :href="route('crm.meeting-monitors.create')">
        <PrimaryButton class="rounded-full">Agendar cita</PrimaryButton>
        </Link>
        <i v-if="this.$page.props.auth.user.permissions.includes('Editar citas en seguimiento integral')"
          @click="$inertia.get(route('crm.meeting-monitors.edit', meeting_monitor.data.id))"
          class="fa-solid fa-pencil ml-3 text-primary rounded-full p-2 bg-[#FEDBBD] cursor-pointer"></i>
        <i v-if="this.$page.props.auth.user.permissions.includes('Eliminar citas en seguimiento integral')"
          @click="showConfirmModal = true"
          class="fa-regular fa-trash-can ml-3 text-primary rounded-full p-2 bg-[#FEDBBD] cursor-pointer"></i>
      </div>
    </div>

    <!-- ------------- currentTab section starts ------------- -->
    <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
      <div class="flex">
        <p @click="currentTab = 1" :class="currentTab == 1 ? 'bg-[#FEDBBD] rounded-xl text-primary' : ''
          " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
          Citas
        </p>
      </div>
    </div>
    <!-- ------------- currentTab section ends ------------- -->

    <!-- ------------- Cita Starts 1 ------------- -->
    <div v-if="currentTab == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
      <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">

        <p class="font-bold col-span-2 mb-2">Información de la oportunidad</p>

        <span class="text-gray-500 my-2">Folio de oportunidad</span>
        <a class="hover:underline text-primary"
          :href="route('crm.opportunities.show', meeting_monitor.data.opportunity?.id)">
          <span>{{ meeting_monitor.data.opportunity?.folio }} ({{ meeting_monitor.data.opportunity?.name }})</span></a>
        <span class="text-gray-500 my-2">Vendedor</span>
        <span>{{ meeting_monitor.data.seller?.name }}</span>

        <p class="font-bold col-span-2 my-2">Información de la cita</p>
        <span class="text-gray-500">Creado por</span>
        <span>{{ meeting_monitor.data.seller.name }}</span>
        <span class="text-gray-500 my-2">Fecha</span>
        <span>{{ meeting_monitor.data.meeting_date }}</span>
        <span class="text-gray-500 my-2">Hora</span>
        <span>{{ meeting_monitor.data.time }}</span>
        <span class="text-gray-500 my-2">Cita</span>
        <span>{{ meeting_monitor.data.meeting_via }}</span>
        <span class="text-gray-500 my-2">Ubicación</span>
        <span>{{ meeting_monitor.data.location }}</span>
        <span class="text-gray-500 my-2">Descripción</span>
        <span>{{ meeting_monitor.data.description }}</span>
      </div>

      <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
        <p class="font-bold col-span-2 mb-2">Información del cliente</p>

        <span class="text-gray-500 my-2">Cliente</span>
        <span>{{ meeting_monitor.data.customer?.name ?? '--' }}</span>
        <span class="text-gray-500 my-2">Sucursal</span>
        <span>{{ meeting_monitor.data.branch ?? '--' }}</span>
        <span class="text-gray-500 my-2">Contacto</span>
        <span>{{ meeting_monitor.data.contact_name }}</span>
        <span class="text-gray-500 my-2">Teléfono del contacto</span>
        <span>{{ meeting_monitor.data.contact_phone }}</span>
      </div>
    </div>
    <!-- ------------- Cita ends 1 ------------- -->


    <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
      <template #title> Eliminar registro de cita </template>
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
  data() {
    return {
      showConfirmModal: false,
      currentTab: 1,
    }
  },
  components: {
    AppLayout,
    PrimaryButton,
    SecondaryButton,
    ConfirmationModal,
    CancelButton,
    Link,
  },
  props: {
    meeting_monitor: Object,
  },
  methods: {
    deleteItem() {
      this.$inertia.delete(route('crm.meeting-monitors.destroy', this.meeting_monitor.data.id));
    },
  }
}
</script>
