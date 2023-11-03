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
    <form @submit.prevent="update" class="mx-8 mt-3 grid grid-cols-2 gap-x-4 gap-y-2">
      <div>
        <InputLabel value="Título del proyecto *" class="ml-2" />
        <input v-model="form.name" type="text" class="input mt-1" placeholder="Asignar un nombre al proyecto" required>
        <InputError :message="form.errors.name" />
      </div>
      <div>
        <InputLabel value="Tipo de servicio *" class="ml-2" />
        <el-select v-model="form.service_type" clearable placeholder="Seleccione" class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in serviceTypes" :key="item.id" :label="item" :value="item" />
        </el-select>
        <InputError :message="form.errors.service_type" />
      </div>
      <div>
        <InputLabel value="Fecha de inicio *" class="ml-2" />
        <el-date-picker v-model="form.start_date" type="date" placeholder="Inicio *" format="YYYY/MM/DD"
          value-format="YYYY-MM-DD" />
        <InputError :message="form.errors.start_date" />
      </div>
      <div>
        <InputLabel value="Fecha de límite *" class="ml-2" />
        <el-date-picker v-model="form.limit_date" type="date" placeholder="Límite *" format="YYYY/MM/DD"
          value-format="YYYY-MM-DD" />
        <InputError :message="form.errors.limit_date" />
      </div>
      <div>
        <InputLabel value="Responsable *" class="ml-2" />
        <el-select v-model="form.owner_id" clearable placeholder="Seleccione" class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in users" :key="item.id" :label="item.name" :value="item.id">
            <div v-if="$page.props.jetstream.managesProfilePhotos"
              class="flex text-sm rounded-full items-center mt-[3px]">
              <img class="h-7 w-7 rounded-full object-cover mr-4" :src="item.profile_photo_url" :alt="item.name" />
              <p>{{ item.name }}</p>
            </div>
          </el-option>
        </el-select>
        <InputError :message="form.errors.owner_id" />
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
        <RichText @content="updateDescription($event)" :defaultValue="form.description" />
      </div>
      <div class="ml-2 mt-2 col-span-full flex">
        <FileUploader @files-selected="this.form.media = $event" />
      </div>
      <div class="col-span-full">
        <li v-for="file in media" :key="file" class="flex items-center justify-between">
          <a :href="file.original_url" target="_blank" class="flex items-center">
            <i :class="getFileTypeIcon(file.file_name)"></i>
            <span class="ml-2">{{ file.file_name }}</span>
          </a>
        </li>
      </div>
      <div class="mt-5 col-span-full w-[calc(50%-16px)]">
        <div class="flex justify-between items-center mx-2">
          <InputLabel value="Etiquetas" />
          <button @click="showTagFormModal = true" type="button"
            class="rounded-full border border-primary w-4 h-4 flex items-center justify-center">
            <i class="fa-solid fa-plus text-primary text-[9px]"></i>
          </button>
        </div>
        <el-select v-model="form.tags" clearable placeholder="Seleccione" multiple class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in tags.data" :key="item.id" :label="item.name" :value="item.id">
            <Tag :name="item.name" :color="item.color" />
          </el-option>
        </el-select>
      </div>
      <div class="col-span-full ml-2 text-sm mt-3 flex">
        <label class="flex items-center cursor-pointer flex-shrink-0 flex-grow-0">
          <Checkbox v-model:checked="form.is_internal" name="strict" class="bg-transparent" />
          <span class="mx-2">Proyecto interno</span>
          <el-tooltip
            content="Seleccione esta opción si el proyecto es una iniciativa de la empresa y no esta relacionado con un cliente en específico"
            placement="right">
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
              <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center">
                <i class="fa-solid fa-info text-primary text-[7px]"></i>
              </div>
            </el-tooltip>
          </div>
          <button @click="showGroupFormModal = true" type="button" class="text-primary text-xs">
            Agregar grupo nuevo
          </button>
        </div>
        <el-select v-model="form.project_group_id" clearable placeholder="Seleccione" class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in project_groups.data" :key="item.id" :label="item.name" :value="item.id" />
        </el-select>
        <InputError :message="form.errors.project_group_id" />
      </div>
      <h2 v-if="!form.is_internal" class="font-bold text-sm my-2 col-span-full">Campos adicionales</h2>
      <div v-if="!form.is_internal">
        <InputLabel value="Cliente *" class="ml-2" />
        <el-select v-model="form.customer_id" @change="updateContacts()" clearable placeholder="Seleccione"
          class="w-full mt-1" no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in customers" :key="item.id" :label="item.name" :value="item.id" />
        </el-select>
        <InputError :message="form.errors.customer_id" />
      </div>
      <div v-if="!form.is_internal">
        <InputLabel value="Contacto *" class="ml-2" />
        <el-select v-model="form.contact_id" @change="updateBranches()" clearable placeholder="Seleccione"
          class="w-full mt-1" no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in contacts" :key="item.id" :label="item.name" :value="item.id" />
        </el-select>
        <InputError :message="form.errors.contact_id" />
      </div>
      <div v-if="!form.is_internal">
        <InputLabel value="Sucursal *" class="ml-2" />
        <el-select v-model="form.address" clearable placeholder="Seleccione" class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in branches" :key="index" :label="item" :value="item" />
        </el-select>
        <InputError :message="form.errors.address" />
      </div>
      <div v-if="!form.is_internal">
        <InputLabel value="OP *" class="ml-2" />
        <el-select v-model="form.opportunity_id" clearable placeholder="Seleccione" class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in opportunities" :key="item.id" :label="item.name" :value="item.id" />
        </el-select>
        <InputError :message="form.errors.opportunity_id" />
      </div>
      <h2 class="font-bold text-sm my-2 col-span-full">Presupuesto</h2>
      <div>
        <InputLabel value="Moneda" class="ml-2" />
        <el-select v-model="form.currency" clearable placeholder="Seleccione" class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in currencies" :key="index" :label="item.label" :value="item.value" />
        </el-select>
        <InputError :message="form.errors.currency" />
      </div>
      <div>
        <InputLabel value="Monto" class="ml-2" />
        <input v-model="form.budget" type="number" step="0.01" class="input mt-1">
        <InputError :message="form.errors.budget" />
      </div>
      <h2 class="font-bold text-sm my-2 col-span-full">Acceso al proyecto</h2>
      <div class="col-span-full text-sm">
        <div class="my-1">
          <input v-model="typeAccessProject" value="Public"
            class="checked:bg-primary focus:text-primary focus:ring-primary border-black mr-3" type="radio"
            name="typeAccessProject">
          <b>Público</b>
          <p class="text-[#9A9A9A] ml-7 text-xs">Los usuarios del portal solo pueden ver, seguir y comentar, mientras que
            los usuarios del proyecto tendrán acceso directo.</p>
        </div>
        <div class="my-1">
          <input v-model="typeAccessProject" value="Private"
            class="checked:bg-primary focus:text-primary focus:ring-primary border-black mr-3" type="radio"
            name="typeAccessProject">
          <b>Privado</b>
          <p class="text-[#9A9A9A] ml-7 text-xs">Solo los usuarios de proyecto pueden ver y acceder a este proyecto</p>
        </div>
      </div>
      <section class="rounded-[10px] py-12 mx-7 mt-5 max-h-[580px] col-span-full border border-gray3">
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
            <div class="pl-3 overflow-y-auto min-h-[100px] max-h-[380px]">
              <div class="flex mt-2 border-b border-gray3" v-for="user in form.selectedUsersToPermissions" :key="user.id">
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
                      <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                        v-model:checked="user.permissions[0]" :checked="user.permissions[0]" />
                      <span
                        :class="!editAccesFlag || user.employee_properties === null ? 'text-gray-500/80 cursor-not-allowed' : ''"
                        class="ml-2 text-xs">
                        Crea tareas
                      </span>
                    </label>
                    <label class="flex items-center">
                      <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                        v-model:checked="user.permissions[1]" :checked="user.permissions[1]" />
                      <span
                        :class="!editAccesFlag || user.employee_properties === null ? 'text-gray-500/80 cursor-not-allowed' : ''"
                        class="ml-2 text-xs">Ver</span>
                    </label>
                    <label class="flex items-center">
                      <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                        v-model:checked="user.permissions[2]" :checked="user.permissions[2]" />
                      <span
                        :class="!editAccesFlag || user.employee_properties === null ? 'text-gray-500/80 cursor-not-allowed' : ''"
                        class="ml-2 text-xs">Editar</span>
                    </label>
                    <label class="flex items-center">
                      <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                        v-model:checked="user.permissions[3]" :checked="user.permissions[3]" />
                      <span
                        :class="!editAccesFlag || user.employee_properties === null ? 'text-gray-500/80 cursor-not-allowed' : ''"
                        class="ml-2 text-xs">Eliminar</span>
                    </label>
                    <label class="flex items-center">
                      <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                        v-model:checked="user.permissions[4]" :checked="user.permissions[4]" />
                      <span
                        :class="!editAccesFlag || user.employee_properties === null ? 'text-gray-500/80 cursor-not-allowed' : ''"
                        class="ml-2 text-xs">Comentar</span>
                    </label>
                  </div>
                  <el-popconfirm v-if="typeAccessProject === 'Private'" confirm-button-text="Si" cancel-button-text="No"
                    icon-color="#FD8827" title="Remover?" @confirm="removeUserFromPermissions(user.id)">
                    <template #reference>
                      <button :disabled="user.employee_properties == null" type="button"
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
        <Link :href="route('pms.projects.index')">
        <CancelButton type="button">Cancelar</CancelButton>
        </Link>
        <PrimaryButton>Actualizar</PrimaryButton>
      </div>
    </form>

    <!-- group form -->
    <DialogModal :show="showGroupFormModal" @close="showGroupFormModal = false">
      <template #title>
        Agregar grupo
      </template>
      <template #content>
        <form @submit.prevent="storeGroup" ref="groupForm">
          <div>
            <InputLabel value="Nombre del grupo *" class="ml-2" />
            <input v-model="groupForm.name" type="text" class="input mt-1" placeholder="Escribe el nombre" required>
            <InputError :message="groupForm.errors.name" />
          </div>
        </form>
      </template>
      <template #footer>
        <CancelButton @click="showGroupFormModal = false" :disabled="groupForm.processing">Cancelar</CancelButton>
        <PrimaryButton @click="submitGroupForm()" :disabled="groupForm.processing">Crear</PrimaryButton>
      </template>
    </DialogModal>

    <!-- tag form -->
    <DialogModal :show="showTagFormModal" @close="showTagFormModal = false">
      <template #title>
        Agregar etiqueta
      </template>
      <template #content>
        <form @submit.prevent="storeTag" ref="tagForm">
          <div>
            <InputLabel value="Nombre de la etiqueta *" class="ml-2" />
            <input v-model="tagForm.name" type="text" class="input mt-1" placeholder="Escribe el nombre" required>
            <InputError :message="tagForm.errors.name" />
          </div>
          <div class="mt-3">
            <InputLabel value="Seleccione el color *" class="ml-2" />
            <el-color-picker v-model="tagForm.color" class="mt-1" />
            <InputError :message="tagForm.errors.color" />
          </div>
        </form>
      </template>
      <template #footer>
        <CancelButton @click="showTagFormModal = false" :disabled="tagForm.processing">Cancelar</CancelButton>
        <PrimaryButton @click="submitTagForm()" :disabled="tagForm.processing">Crear</PrimaryButton>
      </template>
    </DialogModal>
  </AppLayout>
</template>
  
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import CancelButton from "@/Components/CancelButton.vue";
import Checkbox from "@/Components/Checkbox.vue";
import RichText from "@/Components/MyComponents/RichText.vue";
import ThirdButton from "@/Components/ThirdButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import Tag from "@/Components/MyComponents/Tag.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import { Link, useForm } from "@inertiajs/vue3";
import axios from 'axios';
//   import Pagination from "@/Components/MyComponents/Pagination.vue";

export default {
  data() {
    const form = useForm({
      name: this.project.name,
      owner_id: this.project.owner_id,
      start_date: this.project.start_date,
      limit_date: this.project.limit_date,
      is_strict: Boolean(this.project.is_strict),
      is_internal: Boolean(this.project.is_internal),
      description: this.project.description,
      tags: [],
      project_group_id: this.project.project_group_id,
      service_type: this.project.service_type,
      address: this.project.address,
      opportunity_id: this.project.opportunity_id,
      customer_id: parseInt(this.project.opportunity?.customer_id),
      contact_id: parseInt(this.project.contact_id),
      currency: this.project.currency,
      budget: this.project.budget,
      selectedUsersToPermissions: [],
      media: [],
      user_id: this.project.user_id,
    });

    const groupForm = useForm({
      name: null,
    });

    const tagForm = useForm({
      name: null,
      color: null,
    });

    return {
      form,
      groupForm,
      tagForm,
      showGroupFormModal: false,
      showTagFormModal: false,
      editAccesFlag: true,
      typeAccessProject: 'Private',
      search: '',
      inputSearch: '',
      serviceTypes: [
        'Iluminacón',
        'Herrería',
        'Acabados',
        'Eléctrico',
        'A. acondicionado',
        'Sanitario',
        'Anuncios',
        'Pintura',
        'Carpintería',
        'Vidrio',
        'Aluminio',
        'Protección civil STPS',
        'Monta cargas',
        'Control de plagas',
        'Impermeabilización',
        'Servicios varios',
      ],
      currencies: [
        { label: 'MXN - Peso Mexicano', value: '$MXN' },
        { label: 'USD - Dolar ', value: '$USD' },
      ],
      opportunities: [],
      contacts: [],
      branches: [],
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
    DialogModal,
    InputError,
    Tag,
    FileUploader,
    //   Pagination
  },
  props: {
    customers: Array,
    project_groups: Object,
    project: Array,
    tags: Object,
    users: Array,
    media: Array,
  },
  computed: {

  },
  methods: {
    getFileTypeIcon(fileName) {
      // Asocia extensiones de archivo a iconos
      const fileExtension = fileName.split('.').pop().toLowerCase();
      switch (fileExtension) {
        case 'pdf':
          return 'fa-regular fa-file-pdf text-red-700';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
          return 'fa-regular fa-image text-blue-300';
        default:
          return 'fa-regular fa-file-lines';
      }
    },
    updateContacts() {
      const selectedCustomer = this.customers.find(
        (item) => item.id === this.form.customer_id
      );

      this.contacts = selectedCustomer ? selectedCustomer.contacts : [];
      this.opportunities = selectedCustomer ? selectedCustomer.opportunities : [];
    },
    updateBranches() {
      const selectedContact = this.contacts.find(
        (item) => item.id === this.form.contact_id
      );

      this.branches = selectedContact ? selectedContact?.additional.branches : [];
    },
    update() {
      if (this.form.media.length) {
        this.form.post(route("pms.projects.update-with-media", this.project.id), {
          method: '_put',
          onSuccess: () => {
            this.$notify({
              title: "Correcto",
              message: "Se ha actualizado el proyecto",
              type: "success",
            });

          },
        });
      } else {
        this.form.put(route("pms.projects.update", this.project.id), {
          onSuccess: () => {
            this.$notify({
              title: "Correcto",
              message: "Se ha actualizado el proyecto",
              type: "success",
            });

          },
        });
      }
    },
    submitGroupForm() {
      this.$refs.groupForm.dispatchEvent(new Event('submit', { cancelable: true }));
    },
    async storeGroup() {
      try {
        this.groupForm.processing = true;
        const response = await axios.post(route('pms.project-groups.store'), { name: this.groupForm.name, user_id: this.$page.props.auth.user.id });

        if (response.status === 200) {
          this.$notify({
            title: 'Correcto',
            message: response.data.message,
            type: 'success'
          });

          this.showGroupFormModal = false;
          this.project_groups.data.push(response.data.item);
          this.groupForm.reset();
          this.groupForm.errors = {};
          this.form.project_group_id = response.data.item.id;
        }
      } catch (error) {
        if (error.response.status === 422) {
          // guardando errores de validacion a formulario para mostrarlos
          this.groupForm.errors.name = error.response.data.errors.name[0];
        }
        console.log(error)
      } finally {
        this.groupForm.processing = false;
      }
    },
    submitTagForm() {
      this.$refs.tagForm.dispatchEvent(new Event('submit', { cancelable: true }));
    },
    async storeTag() {
      try {
        this.tagForm.processing = true;
        const response = await axios.post(route('pms.tags.store'), { name: this.tagForm.name, color: this.tagForm.color, type: 'projects', user_id: this.$page.props.auth.user.id });

        if (response.status === 200) {
          this.$notify({
            title: 'Correcto',
            message: response.data.message,
            type: 'success'
          });

          this.showTagFormModal = false;
          this.tags.data.push(response.data.item);
          this.tagForm.reset();
          this.tagForm.errors = {};
          this.form.tags.push(response.data.item.id);
        }
      } catch (error) {
        if (error.response.status === 422) {
          // guardando errores de validacion a formulario para mostrarlos
          this.tagForm.errors.name = error.response.data.errors.name[0];
          this.tagForm.errors.color = error.response.data.errors.color[0];
        }
        console.log(error)
      } finally {
        this.tagForm.processing = false;
      }
    },
    removeUserFromPermissions(userId) {
      const index = this.form.selectedUsersToPermissions.findIndex(item => item.id === userId);

      this.form.selectedUsersToPermissions.splice(index, 1);
    },
    addToSelectedUsers(userId) {
      let user = this.users.find(item => item.id === userId);
      const defaultPermissions = [false, true, false, false, true];
      user.permissions = defaultPermissions;
      this.form.selectedUsersToPermissions.push(user);
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
        const defaultPermissions = [true, true, true, true, true];
        admin.permissions = defaultPermissions;
      });
      this.form.selectedUsersToPermissions = admins;
    }
  },
  computed: {
    availableUsersToPermissions() {
      if (this.form.selectedUsersToPermissions.length == 0) return this.users;

      const availableUsers = this.users.filter((item) => {
        // Verifica si el item no se encuentra en item2
        return !this.form.selectedUsersToPermissions.find((item2) => item.id === item2.id);
      });

      return availableUsers;
    }
  },
  watch: {
    typeAccessProject(newVal) {
      if (newVal === 'Public') {
        this.form.selectedUsersToPermissions = this.users;
        this.form.selectedUsersToPermissions.forEach(user => {
          let defaultPermissions = [true, true, true, true, true];
          if (user.employee_properties) {
            defaultPermissions = [false, true, false, false, true];
          }

          user.permissions = defaultPermissions;
        });
        this.editAccesFlag = false;
      } else {
        this.selectAdmins();
        this.editAccesFlag = true;
      }
    }
  },
  mounted() {
    // this.selectAdmins();
    this.form.tags = this.project.tags.map(tag => tag.id);

    // inicializar permisos
    this.project.users.forEach(user => {
      const participant = {
        id: user.id,
        name: user.name,
        profile_photo_url: user.profile_photo_url,
        permissions: JSON.parse(user.pivot.permissions),
      };
      this.form.selectedUsersToPermissions.push(participant);
    });
    this.updateContacts();
    this.updateBranches();
  }
}
</script>
  