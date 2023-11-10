<template>
  <AppLayout title="Ver usuario">
    <SkeletonLoading v-if="loading" />
    <div v-else>
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
          <PrimaryButton v-if="this.$page.props.auth.user.permissions.includes('Crear usuarios')"
            @click="$inertia.get(route('users.create'))">Agregar usuario</PrimaryButton>
          <SecondaryButton
            v-if="this.$page.props.auth.user.permissions.includes('Editar usuarios') && user.employee_properties !== null"
            @click="$inertia.get(route('users.edit', this.user.id))" class="!text-lg"><i class="fa-solid fa-pen"></i>
          </SecondaryButton>
        </div>
      </div>
      <div class="mx-8 mt-10 grid grid-cols-4 gap-x-4 gap-y-2">
        <div>
          <figure class="rounded-full w-52 h-52 mx-auto">
            <img :src="user.profile_photo_url" class="object-cover rounded-full w-52 h-52">
          </figure>
        </div>
        <div class="col-span-3 grid grid-cols-2 gap-x-4 gap-y-2">
          <span class="text-gray-500">Nombre</span>
          <span>{{ user.name }}</span>
          <span class="text-gray-500">Departamento</span>
          <span>{{ user.employee_properties?.department ?? 'Dirección' }}</span>
          <span class="text-gray-500">Puesto</span>
          <span>{{ user.employee_properties?.position ?? 'Dirección' }}</span>
          <span class="text-gray-500">Correo electrónico</span>
          <span>{{ user.email }}</span>
          <span class="text-gray-500">Teléfono</span>
          <span>{{ user.employee_properties?.phone ?? '--' }}</span>
          <span class="text-gray-500">Estado</span>
          <span :class="{ 'text-green-600': user.is_active, 'text-red-600': !user.is_active, }">{{
            user.is_active ? 'Activo' : 'Inactivo' }}</span>
        </div>
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
import SkeletonLoading from "@/Components/MyComponents/SkeletonLoading/Show.vue";
import { Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
      selectedUserId: this.user.id,
      loading: false,
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
    SkeletonLoading,
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
      this.loading = true;
      if (this.currentTab > 1) {
        this.$inertia.get(route('users.show', { customer: newVal, defaultTab: this.currentTab }));
      } else {
        this.$inertia.get(route('users.show', newVal));
      }
    },
  },
  mounted() {
    // tab to open
    // if (this.defaultTab !== null) {
    //   this.currentTab = this.defaultTab;
    // }
  },
};
</script>
