<template>
  <AppLayout title="Ver usuario">
    <div class="flex justify-between items-center text-lg mx-8 mt-8">
      <span>Usuarios</span>
      <Link :href="route('users.index')">
      <p class="flex items-center text-sm text-primary">
        <i class="fa-solid fa-arrow-left-long mr-2"></i>
        <span>Regresar</span>
      </p>
      </Link>
    </div>
    <div class="flex justify-between items-center mx-8 mt-8">
      <el-select v-model="selectedUserId" clearable filterable placeholder="Buscar usuario" class="w-1/3 mr-4"
        no-data-text="No hay proyectos registrados" no-match-text="No se encontraron coincidencias">
        <el-option v-for="item in users" :key="item.id" :label="item.name" :value="item.id" />
      </el-select>
      <div class="flex items-center space-x-2">
        <PrimaryButton v-if="this.$page.props.auth.user.permissions.includes('Crear usuarios')" @click="$inertia.get(route('users.create'))">Agregar usuario</PrimaryButton>
        <SecondaryButton v-if="this.$page.props.auth.user.permissions.includes('Editar usuarios')" @click="$inertia.get(route('users.edit', this.currentUser))" class="!text-lg"><i
            class="fa-solid fa-pen"></i></SecondaryButton>
      </div>
    </div>
    <div class="mx-8 mt-10 grid grid-cols-4 gap-x-4 gap-y-2">
      <div>
        <figure class="rounded-full w-52 h-52 mx-auto">
          <img :src="currentUser?.profile_photo_url" class="rounded-full w-52 h-52">
        </figure>
      </div>
      <div class="col-span-3 grid grid-cols-2 gap-x-4 gap-y-2">
        <span class="text-gray-500">Nombre</span>
        <span>{{ currentUser?.name }}</span>
        <span class="text-gray-500">Departamento</span>
        <span>{{ currentUser?.employee_properties?.department }}</span>
        <span class="text-gray-500">Puesto</span>
        <span>{{ currentUser?.employee_properties?.position }}</span>
        <span class="text-gray-500">Correo electrónico</span>
        <span>{{ currentUser?.email }}</span>
        <span class="text-gray-500">Teléfono</span>
        <span>{{ currentUser?.employee_properties?.phone }}</span>
        <span class="text-gray-500">Estado</span>
        <span :class="{ 'text-green-600': currentUser?.is_active, 'text-red-600': !currentUser?.is_active, }">{{
          currentUser?.is_active ? 'Activo' : 'Inactivo' }}</span>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import CancelButton from "@/Components/CancelButton.vue";
import Checkbox from "@/Components/Checkbox.vue";
import { Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
      selectedUserId: "",
      currentUser: null,
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    SecondaryButton,
    CancelButton,
    InputLabel,
    Checkbox,
    InputError,
    Link,
    //   Pagination
  },
  props: {
    user: Array,
    users: Array,
  },
  methods: {

  },
  watch: {
    selectedUserId(newVal) {
      this.currentUser = this.users.find((item) => item.id == newVal);
    },
  },
  mounted() {
    this.selectedUserId = this.user.id;
    this.currentUser = this.users.find((item) => item.id == this.selectedUserId);
  },
};
</script>
