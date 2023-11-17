<template>
  <AppLayout title="Clientes">
    <div class="flex justify-between text-lg mx-16 mt-11">
      <span>Clientes</span>
    </div>

    <div class="flex justify-between mt-5 mx-1 lg:mx-16">
      <div class="w-1/3 relative">
        <input @keyup.enter="handleSearch" v-model="inputSearch" class="input pr-8" placeholder="Buscar cliente" />
        <i class="fa-solid fa-magnifying-glass absolute top-2 right-4 text-xs text-gray2"></i>
      </div>
      <div v-if="this.$page.props.auth.user.permissions.includes('Crear clientes')" class="flex items-center">
        <el-dropdown v-if="[
          'Enviar correos en seguimiento integral',
          'Agendar citas en seguimiento integral',
          'Registrar pagos en seguimiento integral'
        ].some(permission => this.$page.props.auth.user.permissions.includes(permission))" split-button
          type="primary" @click="$inertia.get(route('crm.customers.create'))">
          Nuevo cliente
          <template #dropdown>
            <el-dropdown-menu>
              <el-dropdown-item v-if="this.$page.props.auth.user.permissions.includes('Enviar correos en seguimiento integral')" @click="update">Enviar correo</el-dropdown-item>
              <el-dropdown-item v-if="this.$page.props.auth.user.permissions.includes('Registrar pagos en seguimiento integral')" @click="showConfirmModal = true">Registar pago</el-dropdown-item>
              <el-dropdown-item v-if="this.$page.props.auth.user.permissions.includes('Agendar citas en seguimiento integral')" @click="showConfirmModal = true">Agendar cita</el-dropdown-item>
            </el-dropdown-menu>
          </template>
        </el-dropdown>
        <PrimaryButton v-else>Nuevo cliente</PrimaryButton>
      </div>
    </div>

    <!-- Customer table ----------------------------------------------------------------------------------------------------->
    <div v-if="customers?.data.length" class="lg:px-16 px-4 py-7 text-xs overflow-x-auto">
      <table class="w-full mx-auto">
        <thead>
          <tr class="text-left">
            <th class="font-bold pb-5 pl-4">ID <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
            </th>
            <th class="font-bold pb-5">Nombre <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
            </th>
            <th class="font-bold pb-5">RFC <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i></th>
            <th class="font-bold pb-5">Fecha de registro <i
                class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i></th>
            <th class="font-bold pb-5">Contactos <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="customer in filteredTableData" :key="customer.id" class="mb-4 cursor-pointer hover:bg-primarylight"
            @click="$inertia.get(route('crm.customers.show', customer.id))">
            <td class="text-left py-2 pr-2 pl-4 rounded-l-full">
              {{ customer.id }}
            </td>
            <td class="text-left py-2">
              {{ customer.name }}
            </td>
            <td class="text-left py-2">
              {{ customer.rfc }}
            </td>
            <td class="text-left py-2">
              {{ customer.created_at }}
            </td>
            <td :title="customer.contacts.map(item => item.name).join(', ')" class="text-left py-2 rounded-r-full">
              <p class="max-w-[80px] truncate">{{ customer.contacts.map(item => item.name).join(', ') }}</p>
            </td>
          </tr>
        </tbody>
      </table>
      <!-- --- pagination --- -->
      <div class="mt-4">
        <!-- <Pagination :pagination="customers" /> -->
      </div>
    </div>
    <div class="text-center mt-12" v-else>
      <p class="text-sm text-gray-400">No hay clientes registrados</p>
      <i class="fa-regular fa-folder-open mt-32 text-gray-400/20 text-[200px]"></i>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
//   import Pagination from "@/Components/MyComponents/Pagination.vue";

export default {
  data() {
    return {
      search: '',
      inputSearch: '',
    }
  },
  components: {
    AppLayout,
    PrimaryButton,
    // Pagination,
  },
  props: {
    customers: Object,
  },
  methods: {
    handleSearch() {
      this.search = this.inputSearch;
    },
  },
  computed: {
    filteredTableData() {
      if (!this.search) {
        return this.customers.data;
      } else {
        return this.customers.data.filter(
          (customer) =>
            customer.id.toString().toLowerCase().includes(this.search.toLowerCase()) ||
            customer.name.toLowerCase().includes(this.search.toLowerCase()) ||
            customer.rfc.toLowerCase().includes(this.search.toLowerCase()) ||
            customer.contacts.map(item => item.name).join(', ').toLowerCase().includes(this.search.toLowerCase())
        )
      }
    }
  },
}
</script>

<style></style>