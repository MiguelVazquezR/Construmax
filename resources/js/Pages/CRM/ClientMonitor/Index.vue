<template>
  <AppLayout title="Seguimiento integral">
    <div class="flex justify-between text-lg mx-16 mt-11">
      <span>Seguimiento integral</span>
    </div>

    <div class="flex justify-between mt-5 mx-1 lg:mx-16">
      <div class="w-1/3 relative">
        <input @keyup.enter="handleSearch" v-model="inputSearch" class="input pr-8" placeholder="Buscar" />
        <i class="fa-solid fa-magnifying-glass absolute top-2 right-4 text-xs text-gray2"></i>
      </div>
      <div class="flex items-center">
        <el-dropdown v-if="this.$page.props.auth.user.permissions.includes(
          'Enviar correos en seguimiento integral'
        ) ||
          this.$page.props.auth.user.permissions.includes(
            'Agendar citas en seguimiento integral'
          ) ||
          this.$page.props.auth.user.permissions.includes(
            'Registrar pagos en seguimiento integral'
          )
          " split-button type="primary" @click="$inertia.get(route('crm.email-monitors.create'))">
          Enviar correo
          <template #dropdown>
            <el-dropdown-menu>
              <el-dropdown-item @click="$inertia.get(route('crm.meeting-monitors.create'))" v-if="this.$page.props.auth.user.permissions.includes(
                'Agendar citas en seguimiento integral'
              )
                ">Agendar cita</el-dropdown-item>
              <el-dropdown-item @click="$inertia.get(route('crm.payment-monitors.create'))" v-if="this.$page.props.auth.user.permissions.includes(
                'Registrar pagos en seguimiento integral'
              )
                ">Registar pago</el-dropdown-item>
            </el-dropdown-menu>
          </template>
        </el-dropdown>
      </div>
    </div>


    <!-- ----------- Client monitor table ----------- -->
    <div class="w-11/12 mx-10 my-16 overflow-x-auto">
      <table v-if="filteredTableData.length" class="w-full mx-auto text-xs">
        <thead>
          <tr class="text-left">
            <th class="font-bold pb-5">
              Folio <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
            </th>
            <th class="font-bold pb-5 min-w-[90px]">
              Cliente <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
            </th>
            <th class="font-bold pb-5">
              Tipo <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
            </th>
            <th class="font-bold pb-5">
              Fecha<i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
            </th>
            <th class="font-bold pb-5">
              Concepto <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
            </th>
            <th class="font-bold pb-5">
              Vededor <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr @click="showMonitorType(monitor)" v-for="monitor in filteredTableData" :key="monitor.id"
            class="mb-4 cursor-pointer hover:bg-primarylight">
            <td class="py-2 px-1 rounded-l-full">
              {{ monitor.folio }}
            </td>
            <td class="py-2">
              {{ monitor.customer?.name }}
            </td>
            <td class="py-2">
              {{ monitor.type }}
            </td>
            <td class="py-2">
              {{ monitor.date }}
            </td>
            <td :title="monitor.concept" class="py-2 max-w-[100px] truncate pr-3">
              {{ monitor.concept }}
            </td>
            <td class="py-2" :class="!$page.props.auth.user.permissions.includes('Eliminar seguimiento integral') ? 'rounded-r-full px-2' : null">
              {{ monitor.seller?.name }}
            </td>
            <td v-if="$page.props.auth.user.permissions.includes('Eliminar seguimiento integral')"
              class="py-2 rounded-r-full px-2">
              <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#D90537" title="¿Eliminar?"
                @confirm="deleteClientMonitor(monitor)">
                <template #reference>
                  <i @click.stop=""
                    class="fa-regular fa-trash-can text-primary cursor-pointer p-2 hover:bg-[#FEDBBD] rounded-full"></i>
                </template>
              </el-popconfirm>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else>
        <p class="text-sm text-center text-gray-400">No hay seguimientos para mostrar</p>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
  data() {
    return {
      search: "",
      inputSearch: "",
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    SecondaryButton,
    Link,
  },
  props: {
    client_monitors: Object,
  },
  methods: {
    showMonitorType(monitor) {
      if (monitor.type == 'Correo electrónico') {
        this.$inertia.get(route('crm.email-monitors.show', monitor.emailMonitor?.id));
      } else if (monitor.type == 'Pago') {
        this.$inertia.get(route('crm.payment-monitors.show', monitor.paymentMonitor?.id));
      } else if (monitor.type == 'Reunión') {
        this.$inertia.get(route('crm.meeting-monitors.show', monitor.meetingMonitor?.id));
      }
    },
    handleSearch() {
      this.search = this.inputSearch;
    },
    async deleteClientMonitor(monitor) {
      try {
        const response = await axios.delete(route('crm.client-monitors.destroy', monitor.id));

        if (response.status === 200) {
          this.$notify({
            title: "Correcto",
            message: "Se ha eliminado correctamente",
            type: "success",
          });
          const index = this.client_monitors.data.findIndex(item => item.id === monitor.id);

          if (index !== -1) {
            this.client_monitors.data.splice(index, 1);
          }
        }
      } catch (error) {
        console.log(error);
      }
    },
  },
  computed: {
    filteredTableData() {
      if (!this.search) {
        return this.client_monitors.data;
      } else {
        return this.client_monitors.data.filter((monitor) =>
          monitor.folio.toLowerCase().includes(this.search.toLowerCase()) ||
          monitor.type.toLowerCase().includes(this.search.toLowerCase()) ||
          monitor.concept.toLowerCase().includes(this.search.toLowerCase()) ||
          monitor.customer?.name.toLowerCase().includes(this.search.toLowerCase()) ||
          monitor.seller?.name.toLowerCase().includes(this.search.toLowerCase())
        );
      }
    },
  },

};
</script>

<style></style>
