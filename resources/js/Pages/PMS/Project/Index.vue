<template>
  <AppLayout title="Proyectos">
    <div class="flex justify-between text-lg mx-16 mt-11">
      <span>Proyectos</span>
    </div>

    <div class="flex justify-between mt-5 mx-1 lg:mx-16">
      <div class="w-1/3 relative">
        <input @keyup.enter="handleSearch" v-model="inputSearch" class="input pr-8" placeholder="Buscar proyecto" />
        <i class="fa-solid fa-magnifying-glass absolute top-2 right-4 text-xs text-gray2"></i>
      </div>
      <div>
        <PrimaryButton @click="$inertia.get(route('pms.projects.create'))" class="rounded-full">Nuevo proyecto
        </PrimaryButton>
      </div>
    </div>

    <div class="lg:px-16 px-4 py-7 text-sm overflow-x-auto">
      <table v-if="filteredTableData.length" class="w-full mx-auto">
        <thead>
          <tr class="text-left">
            <th class="font-bold pb-5 pl-4">Folio <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
            <th class="font-bold pb-5">Nombre del proyecto <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
            <th class="font-bold pb-5">Tipo de servicio <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
            <th class="font-bold pb-5">Estado <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
            <th class="font-bold pb-5 text-center">Tareas <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
            <th class="font-bold pb-5">Responsable <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
            <th class="font-bold pb-5">Fecha de inicio <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
            <th class="font-bold pb-5">Fecha final <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
            <th class="font-bold pb-5">Completa <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="project in filteredTableData" :key="project.id" class="mb-4 cursor-pointer hover:bg-[#dfdbdba8]"
            @click="$inertia.get(route('pms.projects.show', project.id))">
            <td class="text-left py-2 pr-2 pl-4 rounded-l-full">
              {{ project.folio }}
            </td>
            <td class="text-left py-2">
              {{ project.name }}
            </td>
            <td class="text-left py-2">
              {{ project.service_type }}
            </td>
            <td class="text-left py-2">
              <span
                :class="calculateProjectStatus(project.tasks)?.text_color + ' ' + calculateProjectStatus(project.tasks)?.bg"
                class="py-1 px-2 rounded-full">{{ calculateProjectStatus(project.tasks)?.label }}</span>
            </td>
            <td class="text-left py-2 flex space-x-1 items-center">
              <p class="text-xs">{{ project.tasks.filter(task => task.status === 'Terminada').length }}</p>
              <div class="relative bg-[#D9D9D9] rounded-full h-5 w-24">
                <div
                  :class="(project.tasks.filter(task => task.status === 'Terminada').length / project.tasks.length) * 100 == 100 ? 'rounded-full' : 'rounded-l-full'"
                  class="absolute top-0 left-0 bg-secondary h-5"
                  :style="{ width: (project.tasks.filter(task => task.status === 'Terminada').length / project.tasks.length) * 100 + '%' }">
                </div>
                <p class="text-sm font-bold absolute top-0 right-8 text-white">{{ project.tasks.length != 0 ?
                  Math.round((project.tasks.filter(task => task.status === 'Terminada').length / project.tasks.length) *
                    100) : '0' }}%</p>
              </div>
              <p class="text-xs">{{ project.tasks.length }}</p>
            </td>
            <td class="text-left py-2 px-2">
              {{ project.owner.name }}
            </td>
            <td class="text-left py-2 px-2">
              {{ project.start_date }}
            </td>
            <td class="text-left py-2 px-2">
              {{ project.limit_date }}
            </td>
            <td class="text-left py-2 px-2 rounded-r-full">
              {{ project.finished_at ?? '--' }}
            </td>
          </tr>
        </tbody>
      </table>
      <p v-else class="text-center text-gray2 mt-12">No hay proyectos registrados</p>
      <!-- --- pagination --- -->
      <div class="mt-4">
        <!-- <Pagination :pagination="projects" /> -->
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
    projects: Object
  },
  methods: {
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
          text_color: 'text-red-600',
          bg: 'bg-red-200',
        };
      }

      if (completedTasks === totalTasks) {
        return {
          label: 'Terminado',
          text_color: 'text-green-600',
          bg: 'bg-green-200',
        };
      } else if (inProgressTasks > 0 || completedTasks > 0) {
        return {
          label: 'En proceso',
          text_color: 'text-secondary',
          bg: 'bg-blue-200',
        };
      } else {
        return {
          label: 'Sin iniciar',
          text_color: 'text-orange-600',
          bg: 'bg-orange-200',
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
  