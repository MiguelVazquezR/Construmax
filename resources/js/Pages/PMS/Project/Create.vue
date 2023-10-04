<template>
    <AppLayout title="Crear proyecto">
      <div class="flex justify-between text-lg mx-8 mt-8">
        <b>Nuevo proyectos</b>
        <p class="flex items-center text-sm text-primary">
            <i class="fa-solid fa-arrow-left-long mr-2"></i>
            <span>Regresar</span>
        </p>
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
    //   projects: Object
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
              project.project_name.toLowerCase().includes(this.search.toLowerCase())
          )
        }
      }
    },
  }
  </script>
  