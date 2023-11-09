<template>
  <AppLayout title="Crear usuario">
    <div class="flex justify-between items-center text-lg mx-8 mt-8">
      <b>Nuevo usuario</b>
      <Link :href="route('users.index')">
      <p class="flex items-center text-sm text-primary">
        <i class="fa-solid fa-arrow-left-long mr-2"></i>
        <span>Regresar</span>
      </p>
      </Link>
    </div>
    <div class="flex justify-center">
      <InfoMessage
        message="Dar al empleado su correo y la contraseña: Construmax123 para que pueda ingrsar al sistema. Esta contraseña la pueden cambiar en cualquier momento" />
    </div>
    <form @submit.prevent="store" class="mx-8 mt-10 grid grid-cols-4 gap-x-4 gap-y-2">
      <div>
        <div @click="openFileInput" v-if="!selectedImage"
          class="rounded-full w-52 h-52 bg-gray2 mx-auto flex items-center justify-center cursor-pointer">
          <label for="fileInput">
            <i class="fa-solid fa-camera text-white text-3xl"></i>
          </label>
          <input @change="previewImage" type="file" id="fileInput" name="fileInput" style="display: none"
            accept="image/*" />
        </div>
        <Dropdown v-else align="right" width="48">
          <template #trigger>
            <div class="rounded-full w-52 h-52 bg-gray2 mx-auto flex items-center justify-center cursor-pointer">
              <img :src="selectedImage" alt="User Profile" class="object-cover rounded-full w-52 h-52 mx-auto" />
              <input @change="previewImage" type="file" id="fileInput" name="fileInput" style="display: none"
                accept="image/*" />
            </div>
          </template>
          <template #content>
            <DropdownLink @click="editProfilePhoto()" as="no-submit-button">
              Editar
            </DropdownLink>
            <DropdownLink @click="deleteProfilePhoto()" as="no-submit-button">
              Eliminar
            </DropdownLink>
          </template>
        </Dropdown>
        <InputError :message="form.errors.photo" />
      </div>
      <div class="col-span-3 grid grid-cols-2 gap-x-4 gap-y-2">
        <div>
          <InputLabel value="Nombre de usuario *" class="ml-2" />
          <input v-model="form.name" type="text" class="input mt-1" placeholder="Escriba el nombre" required />
          <InputError :message="form.errors.name" />
        </div>
        <div>
          <InputLabel value="Departamento *" class="ml-2" />
          <el-select v-model="form.employee_properties.department" clearable placeholder="Seleccione" class="w-full mt-1"
            no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
            <el-option v-for="(item, index) in departments" :key="index" :label="item" :value="item" />
          </el-select>
          <InputError :message="form.errors.employee_properties?.department" />
        </div>
        <div>
          <InputLabel value="Puesto *" class="ml-2" />
          <input v-model="form.employee_properties.position" type="text" class="input mt-1"
            placeholder="Escriba el puesto" required />
          <InputError :message="form.errors.employee_properties?.position" />
        </div>
        <div>
          <InputLabel value="Correo electrónico *" class="ml-2" />
          <input v-model="form.email" type="email" class="input mt-1" placeholder="Escriba el correo" required />
          <InputError :message="form.errors.email" />
        </div>
        <div>
          <InputLabel value="Teléfono *" class="ml-2" />
          <input v-model="form.employee_properties.phone" type="text" class="input mt-1" placeholder="Escriba el teléfono"
            required />
          <InputError :message="form.errors.employee_properties?.phone" />
        </div>
      </div>

      <!-- roles -->
      <br><el-divider content-position="left" class="col-span-full">Roles</el-divider>
      <br>
      <div class="col-span-full grid grid-cols-3 gap-2">
        <InputLabel v-for="role in roles" :key="role.id" class="flex items-center">
          <input type="checkbox" v-model="form.roles" :value="role.id"
            class="rounded text-primary shadow-sm focus:ring-primary bg-transparent" />
          <span class="ml-2 text-sm">{{ role.name }}</span>
        </InputLabel>
      </div>
      <InputError :message="form.errors.roles" />
      <div class="col-span-full flex mt-8 mb-5 justify-end space-x-2">
        <PrimaryButton :disabled="form.processing">Crear usuario</PrimaryButton>
      </div>
    </form>
  </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import CancelButton from "@/Components/CancelButton.vue";
import InfoMessage from "@/Components/MyComponents/InfoMessage.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      name: null,
      employee_properties: {
        department: null,
        position: null,
        phone: null,
      },
      email: null,
      photo: null,
      roles: [],
    });
    
    return {
      selectedImage: null,
      form,
      departments: [
        "Construcción",
        "Costos y presupuestos",
        "Mantenimiento",
        "Ventas",
      ],
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    CancelButton,
    InputLabel,
    Checkbox,
    InputError,
    Link,
    InfoMessage,
    Dropdown,
    DropdownLink,
    //   Pagination
  },
  props: {
    roles: Array,
  },
  methods: {
    openFileInput() {
      // Al hacer clic en el div, activar el input de tipo "file" invisible
      document.getElementById('fileInput').click();
    },
    previewImage(event) {
      const file = event.target.files[0];
      if (file) {
        // Almacena el archivo seleccionado en una propiedad 'selectedImage'
        this.form.photo = file;

        // Crea una URL temporal para mostrar la vista previa
        this.selectedImage = URL.createObjectURL(file);
      }
    },
    editProfilePhoto() {
      this.openFileInput();
    },
    deleteProfilePhoto() {
      this.form.photo = null;
      this.selectedImage = null;
    },
    store() {
      this.form.post(route("users.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Correcto",
            message: "Usuario creado",
            type: "success",
          });
        },
      });
    },
  }
};
</script>
