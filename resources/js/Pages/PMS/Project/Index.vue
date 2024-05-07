<template>
  <AppLayout title="Tickets">
    <div class="flex justify-between text-lg mx-16 mt-11">
      <span>Tickets</span>
    </div>

    <div class="flex justify-between mt-5 mx-1 lg:mx-16">
      <div class="w-1/3 relative">
        <input @keyup.enter="handleSearch" v-model="inputSearch" class="input pr-8" placeholder="Buscar ticket" />
        <i class="fa-solid fa-magnifying-glass absolute top-2 right-4 text-xs text-gray2"></i>
      </div>
      <div>
        <PrimaryButton v-if="this.$page.props.auth.user.permissions.includes('Crear tickets')"
          @click="$inertia.get(route('pms.projects.create'))" class="rounded-full">Nuevo ticket
        </PrimaryButton>
      </div>
    </div>

    <div class="lg:px-16 px-4 py-7 text-xs overflow-x-auto">
      <table v-if="filteredTableData.length" class="w-full mx-auto">
        <thead>
          <tr class="text-left">
            <th class="font-bold pb-5 pl-4 min-w-[90px]">Folio <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i></th>
            <th class="font-bold pb-5 min-w-[90px]">Nombre <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
            </th>
            <th class="font-bold pb-5 min-w-[120px]">Tipo de servicio <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
            </th>
            <th class="font-bold pb-5 min-w-[90px]">Estado <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i></th>
            <th class="font-bold pb-5 text-center">Tareas <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
            </th>
            <th class="font-bold pb-5 min-w-[120px]">Responsable <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i></th>
            <th class="font-bold pb-5 min-w-[120px]">Fecha de inicio <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i></th>
            <th class="font-bold pb-5 min-w-[120px]">Fecha final <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i></th>
            <th class="font-bold pb-5 min-w-[120px]">Completa <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="project in filteredTableData" :key="project.id" class="mb-4 cursor-pointer hover:bg-primarylight"
            @click="$inertia.get(route('pms.projects.show', project.id))">
            <td class="text-left py-2 pr-2 pl-4 rounded-l-full">
              {{ project.folio }}
            </td>
            <td :title="project.name" class="text-left py-2 max-w-[220px] truncate pr-2">
              {{ project.name }}
            </td>
            <td class="text-left py-2">
              {{ project.service_type }}
            </td>
            <td class="text-left py-2">
              <span
                :class="calculateProjectStatus(project.tasks)?.text_color + ' ' + calculateProjectStatus(project.tasks)?.bg"
                class="py-1 px-2 rounded-full border border-white">{{ calculateProjectStatus(project.tasks)?.label
                }}</span>
            </td>
            <td class="text-left py-2 flex space-x-px items-center">
              <p class="text-[10px] mt-1">{{ project.tasks.filter(task => task.status === 'Terminada').length }}</p>
              <div class="relative bg-gray4 rounded-full h-5 w-24 mt-1 border border-white">
                <div
                  :class="(project.tasks.filter(task => task.status === 'Terminada').length / project.tasks.length) * 100 == 100 ? 'rounded-full' : 'rounded-l-full'"
                  class="absolute top-0 left-0 bg-primary h-5"
                  :style="{ width: (project.tasks.filter(task => task.status === 'Terminada').length / project.tasks.length) * 100 + '%' }">
                </div>
                <p class="text-xs mt-px absolute top-0 right-8 text-black">{{ project.tasks.length != 0 ?
                  Math.round((project.tasks.filter(task => task.status === 'Terminada').length / project.tasks.length) *
                    100) : '0' }}%</p>
              </div>
              <p class="text-[10px] mt-1">{{ project.tasks.length }}</p>
            </td>
            <td class="text-left py-2 px-2 max-w-[120px] truncate">
              {{ project.owner.name }}
            </td>
            <td class="text-left py-2">
              {{ project.start_date }}
            </td>
            <td class="text-left py-2" :class="getTextClass(project)">
              {{ project.limit_date }}
              <el-tooltip v-if="project.finished_at === null && limitDateHasPassed(project)"
                content="La fecha limite ha pasado" placement="top">
                <i class="fa-solid fa-triangle-exclamation"></i>
              </el-tooltip>
            </td>
            <td class="text-left py-2">
              {{ project.finished_at ?? '--' }}
            </td>
            <td v-if="$page.props.auth.user.permissions?.includes('Eliminar tickets')"
              class="text-left py-2 px-2 rounded-r-full">
              <i @click.stop="prepareToDelete(project)" class="fa-regular fa-trash-can text-primary cursor-pointer p-2"></i>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-else class="text-center text-gray2 mt-12">No hay tickets registrados</p>
      <!-- --- pagination --- -->
      <div class="mt-4">
        <!-- <Pagination :pagination="projects" /> -->
      </div>
    </div>
    <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
      <template #title>
        Eliminar ticket
      </template>
      <template #content>
        Al eliminar el ticket se perderán permanentemente las tareas y los archivos relacionados. ¿Deseas continuar?
      </template>
      <template #footer>
        <div class="flex space-x-1">
          <CancelButton @click="showConfirmModal = false">Cancelar</CancelButton>
          <PrimaryButton @click="deleteProject()">Eliminar</PrimaryButton>
        </div>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>
  
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/CancelButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import moment from 'moment';
//   import Pagination from "@/Components/MyComponents/Pagination.vue";

export default {
  data() {
    return {
      search: '',
      inputSearch: '',
      showConfirmModal: false,
      projectToDelete: null,
    }
  },
  components: {
    AppLayout,
    PrimaryButton,
    CancelButton,
    SecondaryButton,
    ConfirmationModal,
    //   Pagination
  },
  props: {
    projects: Object
  },
  methods: {
    limitDateHasPassed(project) {
      const today = moment();
      const limitDate = moment(project.raw_limit_date);

      if (limitDate.isBefore(today, 'day')) {
        return true;
      }

      return false;
    },
    prepareToDelete(project) {
      this.projectToDelete = project.id;
      this.showConfirmModal = true;
    },
    deleteProject() {
      this.$inertia.delete(route('pms.projects.destroy', this.projectToDelete));
      this.$notify({
        title: "Éxito",
        message: "Ticket eliminado",
        type: "success",
      });
      this.projectToDelete = null;
      this.showConfirmModal = false;
    },
    getTextClass(project) {
      const today = moment();
      const createdDate = moment(project.raw_created_at);
      const limitDate = moment(project.raw_limit_date);

      if (project.finished_at === null) {
        if (limitDate.isSame(today, 'day') || limitDate.isBefore(today, 'day')) {
          return 'text-red-600';
        } else if (createdDate.isBefore(today, 'day') && limitDate.isAfter(today, 'day')) {
          return 'text-amber-600';
        } else if (createdDate.isSame(today, 'day')) {
          return 'text-green-600';
        } else {
          return 'text-black'; // Clase vacía si no se cumple ninguna condición
        }
      }
    },
    handleSearch() {
      this.search = this.inputSearch;
    },
    handlePageChange(newPage) {
      this.$inertia.get(route('pms.projects.index', { page: newPage }));
    },
    calculateProjectStatus(tasks) {
      const totalTasks = tasks.length;
      const completedTasks = tasks.filter(task => task.status === 'Terminada').length;
      const inProgressTasks = tasks.filter(task => task.status === 'En curso').length;

      if (totalTasks === 0) {
        return {
          label: 'Sin tareas',
          text_color: 'text-[#FB2A2A]',
          bg: 'bg-[#FEBEBE]',
        };
      }

      if (completedTasks === totalTasks) {
        return {
          label: 'Terminado',
          text_color: 'text-[#37951F]',
          bg: 'bg-[#ADFEB5]',
        };
      } else if (inProgressTasks > 0 || completedTasks > 0) {
        return {
          label: 'En proceso',
          text_color: 'text-primary',
          bg: 'bg-primarylight',
        };
      } else {
        return {
          label: 'Sin iniciar',
          text_color: 'text-gray2',
          bg: 'bg-gray4',
        };
      }
    },
  },
  computed: {
    filteredTableData() {
      if (!this.search) {
        return this.projects.data;
      } else {
        return this.projects.data.filter(
          (project) =>
            project.folio.toLowerCase().includes(this.search.toLowerCase()) ||
            project.name.toLowerCase().includes(this.search.toLowerCase()) ||
            project.service_type.toLowerCase().includes(this.search.toLowerCase()) ||
            project.owner.name.toLowerCase().includes(this.search.toLowerCase()) ||
            project.start_date.toLowerCase().includes(this.search.toLowerCase()) ||
            project.limit_date.toLowerCase().includes(this.search.toLowerCase())
        )
      }
    }
  },
}
</script>
  