<template>
  <AppLayout title="Crear proyecto">
    <div class="flex justify-between items-center text-lg mx-8 mt-8">
      <b>Nuevo proyecto</b>
      <Link :href="route('pms.projects.index')">
      <p class="flex items-center text-sm text-primary">
        <i class="fa-solid fa-arrow-left-long mr-2"></i>
        <span>Regresar</span>
      </p>
      </Link>
    </div>
    <form @submit.prevent="store" class="mx-8 mt-3 grid grid-cols-2 gap-x-4 gap-y-2">
      <div>
        <InputLabel value="Título del proyecto *" class="ml-2" />
        <input v-model="form.name" type="text" class="input mt-1" placeholder="Asignar un nombre al proyecto">
      </div>
      <div>
        <InputLabel value="Responsable *" class="ml-2" />
        <input v-model="form.owner_id" type="text" class="input mt-1" placeholder="Quien responde por el proyecto">
      </div>
      <div>
        <InputLabel value="Fecha de inicio *" class="ml-2" />
        <el-date-picker v-model="form.start_date" type="date" placeholder="Inicio *" format="YYYY/MM/DD"
          value-format="YYYY-MM-DD" />
      </div>
      <div>
        <InputLabel value="Fecha de límite *" class="ml-2" />
        <el-date-picker v-model="form.limit_date" type="date" placeholder="Límite *" format="YYYY/MM/DD"
          value-format="YYYY-MM-DD" />
      </div>
      <div class="col-span-full ml-2 text-sm mt-3 flex">
        <label class="flex items-center cursor-pointer flex-shrink-0 flex-grow-0">
          <Checkbox v-model:checked="form.is_strict" name="strict" class="bg-transparent" />
          <span class="mx-2">Proyecto estricto</span>
          <el-tooltip content="Las tareas no pueden comenzar ni finalizar fuera de las fechas programadas de un proyecto"
            placement="right">
            <!-- <i class="fa-solid fa-circle-info text-primary text-xs ml-2"></i> -->
            <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center">
              <i class="fa-solid fa-info text-primary text-[7px]"></i>
            </div>
          </el-tooltip>
        </label>
      </div>
      <div class="mt-5 col-span-full">
        <InputLabel value="Descripción" class="ml-2" />
        <RichText @content="updateDescription($event)" />
      </div>
      <div class="ml-2 mt-2 col-span-full flex">
        <p class="flex items-center space-x-2 text-sm text-primary cursor-pointer flex-shrink-0 flex-grow-0"><i
            class="fa-solid fa-paperclip"></i> <span>Adjuntar archivos</span></p>
      </div>
      <div class="mt-5 col-span-full w-[calc(50%-16px)]">
        <div class="flex justify-between items-center mx-2">
          <InputLabel value="Etiquetas" />
          <button type="button" class="rounded-full border border-primary w-4 h-4 flex items-center justify-center">
            <i class="fa-solid fa-plus text-primary text-[9px]"></i>
          </button>
        </div>
        <input v-model="form.tags" type="text" class="input mt-1" placeholder="Escriba un nombre de etiqueta">
      </div>
      <div class="col-span-full ml-2 text-sm mt-3 flex">
        <label class="flex items-center cursor-pointer flex-shrink-0 flex-grow-0">
          <Checkbox v-model:checked="form.is_strict" name="strict" class="bg-transparent" />
          <span class="mx-2">Proyecto interno</span>
          <el-tooltip
            content="Seleccione esta opción si el proyecto es una iniciativa de la empresa y no esta relacionado con un cliente en específico"
            placement="right">
            <!-- <i class="fa-solid fa-circle-info text-primary text-xs ml-2"></i> -->
            <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center">
              <i class="fa-solid fa-info text-primary text-[7px]"></i>
            </div>
          </el-tooltip>
        </label>
      </div>
      <div class="mt-5 col-span-full w-[calc(50%-16px)]">
        <div class="flex justify-between items-center mx-2">
          <div class="flex items-center space-x-2">
            <InputLabel value="Grupo" />
            <el-tooltip
              content="Organice su proyecto en grupos. Seleccione o cree un grupo para asociar este proyecto"
              placement="right">
              <!-- <i class="fa-solid fa-circle-info text-primary text-xs ml-2"></i> -->
              <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center">
                <i class="fa-solid fa-info text-primary text-[7px]"></i>
              </div>
            </el-tooltip>
          </div>
          <button type="button" class="text-primary text-xs">
            Agregar grupo nuevo
          </button>
        </div>
        <input v-model="form.tags" type="text" class="input mt-1" placeholder="Escriba un nombre de etiqueta">
      </div>
      <h2 class="font-bold text-sm my-2 col-span-full">Campos adicionales</h2>
      <div>
        <InputLabel value="Cliente *" class="ml-2" />
        <input v-model="form.name" type="text" class="input mt-1" placeholder="Seleccione">
      </div>
      <div>
        <InputLabel value="Sucursal *" class="ml-2" />
        <input v-model="form.name" type="text" class="input mt-1" placeholder="Seleccione">
      </div>
      <div>
        <InputLabel value="OP *" class="ml-2" />
        <input v-model="form.name" type="text" class="input mt-1" placeholder="Seleccione">
      </div>
    </form>

  </AppLayout>
</template>
  
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Checkbox from "@/Components/Checkbox.vue";
import RichText from "@/Components/MyComponents/RichText.vue";
import { Link, useForm } from "@inertiajs/vue3";
//   import Pagination from "@/Components/MyComponents/Pagination.vue";

export default {
  data() {
    const form = useForm({
      name: null,
      owner_id: null,
      start_date: null,
      limit_date: null,
      is_strict: false,
      description: null,
      tags: null,
    });

    return {
      form,
      search: '',
      inputSearch: '',
    }
  },
  components: {
    AppLayout,
    PrimaryButton,
    SecondaryButton,
    Link,
    InputLabel,
    Checkbox,
    RichText,
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
    updateDescription(content) {
      this.form.description = content;
    }
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
  