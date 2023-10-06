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
        <el-select v-model="form.tags" clearable placeholder="Seleccione" multiple class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in tags" :key="item.id" :label="item.name" :value="item.id" />
        </el-select>
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
            <el-tooltip content="Organice su proyecto en grupos. Seleccione o cree un grupo para asociar este proyecto"
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
        <el-select v-model="form.project_group_id" clearable placeholder="Seleccione" class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in project_groups" :key="item.id" :label="item.name" :value="item.id" />
        </el-select>
      </div>
      <h2 class="font-bold text-sm my-2 col-span-full">Campos adicionales</h2>
      <div>
        <InputLabel value="Cliente *" class="ml-2" />
        <el-select v-model="form.customer_id" clearable placeholder="Seleccione" class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in customers" :key="item.id" :label="item.name" :value="item.id" />
        </el-select>
      </div>
      <div>
        <InputLabel value="Sucursal *" class="ml-2" />
        <el-select v-model="form.branch" clearable placeholder="Seleccione" class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in branches" :key="index" :label="item" :value="item" />
        </el-select>
      </div>
      <div>
        <InputLabel value="OP *" class="ml-2" />
        <el-select v-model="form.opportunity_id" clearable placeholder="Seleccione" class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in opportunities" :key="item.id" :label="item.name" :value="item.id" />
        </el-select>
      </div>
      <h2 class="font-bold text-sm my-2 col-span-full">Presupuesto</h2>
      <div>
        <InputLabel value="Moneda" class="ml-2" />
        <el-select v-model="form.currency" clearable placeholder="Seleccione" class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in currencies" :key="index" :label="item.label" :value="item.value" />
        </el-select>
      </div>
      <div>
        <InputLabel value="Monto" class="ml-2" />
        <input v-model="form.amount" type="number" class="input mt-1">
      </div>
      <div>
        <InputLabel value="Método de facturación" class="ml-2 mt-1" />
        <el-select v-model="form.invoice_type" clearable placeholder="Seleccione" class="w-full"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in invoiceTypes" :key="index" :label="item" :value="item" />
        </el-select>
      </div>
      <h2 class="font-bold text-sm my-2 col-span-full">Acceso al proyecto</h2>
      <div class="col-span-full text-sm">
        <div class="my-1">
          <input v-model="typeAccessProject" value="Public"
            class="checked:bg-primary focus:text-primary focus:ring-primary border-black mr-3" type="radio"
            name="typeAccessProject">
          <b>Público</b>
          <p class="text-[#9A9A9A] ml-7 text-xs">Los usuarios del portal solo pueden ver, seguir y comentar, mientras que
            los
            usuarios del proyecto tendrán acceso directo.</p>
        </div>
        <div class="my-1">
          <input v-model="typeAccessProject" value="Private"
            class="checked:bg-primary focus:text-primary focus:ring-primary border-black mr-3" type="radio"
            name="typeAccessProject">
          <b>Privado</b>
          <p class="text-[#9A9A9A] ml-7 text-xs">Solo los usuarios de proyecto pueden ver y acceder a este proyecto</p>
        </div>
      </div>
      <section class="rounded-[10px] py-12 mx-7 mt-5 max-h-[540px] col-span-full border border-gray3">
        <div class="flex px-16 mb-8">
          <div v-if="typeAccessProject === 'Private'" class="w-full">
            <h2 class="font-bold text-sm my-2 ml-2 col-span-full">Asignar participantes </h2>
            <el-select @change="addToSelectedUsers" filterable clearable placeholder="Seleccionar usuario" class="w-1/2"
              no-data-text="No hay más usuarios para añadir" no-match-text="No se encontraron coincidencias">
              <el-option v-for="(item, index) in availableUsersToPermissions" :key="item.id" :label="item.name"
                :value="item.id" />
            </el-select>
          </div>
          <ThirdButton v-if="typeAccessProject === 'Public'" type="button" class="ml-auto self-start"
            @click.stop="editAccesFlag = !editAccesFlag">
            {{ editAccesFlag ? 'Actualizar' : 'Editar' }}
          </ThirdButton>
        </div>
        <div class="flex justify-between px-16 mt-4">
          <div class="w-full">
            <div class="flex">
              <h2 class="font-bold border-b border-gray3 w-2/3 pl-3">Usuarios</h2>
              <h2 class="font-bold border-b border-gray3 w-1/3">Permisos</h2>
            </div>
            <div class="pl-3 overflow-y-auto min-h-[100px] max-h-[340px]">
              <div class="flex mt-2 border-b border-gray3" v-for="user in selectedUsersToPermissions" :key="user.id">
                <div class="w-2/3 flex space-x-2">
                  <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full w-12">
                    <img class="h-10 w-10 rounded-full object-cover" :src="user.profile_photo_url" :alt="user.name" />
                  </div>
                  <div class="text-sm w-full">
                    <p>{{ user.name }}</p>
                    <p v-if="user.employee_properties">{{ 'Depto. ' + user.employee_properties?.department }}</p>
                    <p v-else>Super admin</p>
                  </div>
                </div>

                <div class="w-1/3 flex items-center justify-between">
                  <div class="space-y-1 mb-2">
                    <label class="flex items-center">
                      <Checkbox :disabled="!editAccesFlag" v-model="form.is_strict_proyect" value="1"
                        :name="'p-' + user.id" />
                      <span :class="!editAccesFlag ? 'text-gray-500/80 cursor-not-allowed' : ''" class="ml-2 text-xs">
                        Crea tareas
                      </span>
                    </label>
                    <label class="flex items-center">
                      <Checkbox :disabled="!editAccesFlag" v-model="form.is_strict_proyect" value="2"
                        :name="'p-' + user.id" />
                      <span :class="!editAccesFlag ? 'text-gray-500/80 cursor-not-allowed' : ''"
                        class="ml-2 text-xs">Ver</span>
                    </label>
                    <label class="flex items-center">
                      <Checkbox :disabled="!editAccesFlag" v-model="form.is_strict_proyect" value="3"
                        :name="'p-' + user.id" />
                      <span :class="!editAccesFlag ? 'text-gray-500/80 cursor-not-allowed' : ''"
                        class="ml-2 text-xs">Editar</span>
                    </label>
                    <label class="flex items-center">
                      <Checkbox :disabled="!editAccesFlag" v-model="form.is_strict_proyect" value="4"
                        :name="'p-' + user.id" />
                      <span :class="!editAccesFlag ? 'text-gray-500/80 cursor-not-allowed' : ''"
                        class="ml-2 text-xs">Eliminar</span>
                    </label>
                    <label class="flex items-center">
                      <Checkbox :disabled="!editAccesFlag" v-model="form.is_strict_proyect" value="5"
                        :name="'p-' + user.id" />
                      <span :class="!editAccesFlag ? 'text-gray-500/80 cursor-not-allowed' : ''"
                        class="ml-2 text-xs">Comentar</span>
                    </label>
                  </div>
                  <el-popconfirm v-if="typeAccessProject === 'Private'" confirm-button-text="Si" cancel-button-text="No"
                    icon-color="#FD8827" title="Remover?" @confirm="removeUserFromPermissions(user.id)">
                    <template #reference>
                      <button :disabled="user.employee_properties == null"
                        class="text-primary mr-10 disabled:cursor-not-allowed disabled:opacity-50">
                        <i class="fa-regular fa-circle-xmark"></i>
                      </button>
                    </template>
                  </el-popconfirm>
                </div>

              </div>
            </div>
          </div>
        </div>
      </section>
      <div class="col-span-full flex mt-8 mb-5 justify-end space-x-2">
        <CancelButton>Cancelar</CancelButton>
        <PrimaryButton>Agregar</PrimaryButton>
      </div>
    </form>
  </AppLayout>
</template>
  
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import CancelButton from "@/Components/CancelButton.vue";
import Checkbox from "@/Components/Checkbox.vue";
import RichText from "@/Components/MyComponents/RichText.vue";
import ThirdButton from "@/Components/ThirdButton.vue";
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
      project_group_id: null,
      opportunity_id: null,
      currency: '$MXN',
      amount: null,
      invoice_type: null,
    });

    return {
      form,
      editAccesFlag: true,
      typeAccessProject: 'Private',
      search: '',
      inputSearch: '',
      invoiceTypes: [
        'Facturación al contado',
        'Facturación a crédito',
        'Facturación por adelantado',
      ],
      currencies: [
        { label: 'MXN - Peso Mexicano', value: '$MXN' },
        { label: 'USD - Dolar ', value: '$USD' },
      ],
      opportunities: [],
      selectedUsersToPermissions: [],

    }
  },
  components: {
    AppLayout,
    PrimaryButton,
    CancelButton,
    Link,
    InputLabel,
    Checkbox,
    RichText,
    ThirdButton,
    //   Pagination
  },
  props: {
    customers: Array,
    project_groups: Array,
    tags: Array,
    users: Array,
  },
  computed: {
    branches() {
      const selectedCustomer = this.customers.find(item => item.id === this.form.customer_id);

      return selectedCustomer ? selectedCustomer.branches : [];
    },
  },
  methods: {
    removeUserFromPermissions(userId) {
      const index = this.selectedUsersToPermissions.findIndex(item => item.id === userId);

      this.selectedUsersToPermissions.splice(index, 1);
    },
    addToSelectedUsers(userId) {
      let user = this.users.find(item => item.id === userId);
      const defaultPermissions = {
        create: 0,
        show: 1,
        edit: 0,
        delete: 0,
        comment: 1,
      };
      user.permissions = defaultPermissions;
      this.selectedUsersToPermissions.push(user);
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
    },
    selectAdmins() {
      // obtener los usuarios admin para que siempre aparezcan en los proyectos y dar todos los permisos
      let admins = this.users.filter(item => item.employee_properties == null);
      admins.forEach(admin => {
        const defaultPermissions = {
          create: 1,
          show: 1,
          edit: 1,
          delete: 1,
          comment: 1,
        };
        admin.permissions = defaultPermissions;
      });
      this.selectedUsersToPermissions = admins;
    }
  },
  computed: {
    availableUsersToPermissions() {
      if (this.selectedUsersToPermissions.length == 0) return this.users;

      const availableUsers = this.users.filter((item) => {
        // Verifica si el item no se encuentra en item2
        return !this.selectedUsersToPermissions.find((item2) => item.id === item2.id);
      });

      return availableUsers;
    }
  },
  watch: {
    typeAccessProject(newVal) {
      if (newVal === 'Public') {
        this.selectedUsersToPermissions = this.users;
        this.editAccesFlag = false;
      } else {
        this.selectAdmins();
        this.editAccesFlag = true;
      }
    }
  },
  mounted() {
    this.selectAdmins();
  }
}
</script>
  