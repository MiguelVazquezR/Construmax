<template>
    <AppLayout title="Usuarios">
        <div class="flex justify-between text-lg mx-16 mt-11">
            <span>Usuarios</span>
        </div>

        <div class="flex justify-between mt-5 mx-1 lg:mx-16">
            <div class="w-1/3 relative">
                <input @keyup.enter="handleSearch" v-model="inputSearch" class="input pr-8" placeholder="Buscar usuario" />
                <i class="fa-solid fa-magnifying-glass absolute top-2 right-4 text-xs text-gray2"></i>
            </div>
            <div>
                <PrimaryButton v-if="this.$page.props.auth.user.permissions.includes('Crear usuarios')" @click="$inertia.get(route('users.create'))" class="rounded-full">Agregar usuario
                </PrimaryButton>
            </div>
        </div>

        <div class="lg:px-16 px-4 py-7 text-xs overflow-x-auto">
            <table v-if="filteredTableData.length" class="w-full mx-auto">
                <thead>
                    <tr class="text-left">
                        <th class="font-bold pb-5 pl-4">ID <button><i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i></button></th>
                        <th class="font-bold pb-5">Usuario <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i></th>
                        <th class="font-bold pb-5">Departamento <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i></th>
                        <th class="font-bold pb-5">Puesto <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i></th>
                        <th class="font-bold pb-5">Correo de trabajo <i
                                class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i></th>
                        <th class="font-bold pb-5">Teléfono <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i></th>
                        <th class="font-bold pb-5">Status <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in filteredTableData" :key="user.id" class="mb-4 cursor-pointer hover:bg-primarylight"
                        @click="$inertia.get(route('users.show', user.id))">
                        <td class="text-left py-2 pr-2 pl-4 rounded-l-full min-w-[50px]">
                            {{ user.id }}
                        </td>
                        <td class="text-left py-2 min-w-[120px] pr-2">
                            {{ user.name }}
                        </td>
                        <td class="text-left py-2 min-w-[110px]">
                            {{ user.employee_properties?.department ?? 'Dirección' }}
                        </td>
                        <td class="text-left py-2">
                            {{ user.employee_properties?.position ?? 'Dirección' }}
                        </td>
                        <td class="text-left py-2 max-w-[160px] truncate px-2">
                            {{ user.email }}
                        </td>
                        <td class="text-left py-2 min-w-[80px]">
                            {{ user.employee_properties?.phone ?? '--' }}
                        </td>
                        <td class="text-left py-2 px-2 rounded-r-full" :class="{ 'text-red-600': !user.is_active }">
                            {{ user.is_active ? 'Activo' : 'Inactivo' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <p v-else class="text-center text-gray2 mt-12">No hay usuarios registrados</p>
            <!-- --- pagination --- -->
            <div class="mt-4">
                <!-- <Pagination :pagination="users" /> -->
            </div>
        </div>
    </AppLayout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
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
        SecondaryButton,
        //   Pagination
    },
    props: {
        users: Object
    },
    methods: {
        handleSearch() {
            this.search = this.inputSearch;
        },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.users.data;
            } else {
                const employees = this.users.data.filter(item => item.employee_properties);
                return this.users.data.filter(
                    (user) =>
                            user.id.toString().toLowerCase().includes(this.search.toLowerCase()) ||
                            user.name.toLowerCase().includes(this.search.toLowerCase()) ||
                            user.employee_properties?.department.toLowerCase().includes(this.search.toLowerCase()) ||
                            user.employee_properties?.phone.toLowerCase().includes(this.search.toLowerCase()) ||
                            user.employee_properties?.position.toLowerCase().includes(this.search.toLowerCase()) ||
                            user.email.toLowerCase().includes(this.search.toLowerCase())   
                )
            }
        }
    },
}
</script>
  