<template>
  <AppLayout title="Editar Presupuesto">
    <div class="flex justify-between items-center text-lg mx-8 mt-8">
      <b>Editar presupuesto</b>
      <Link :href="route('crm.opportunities.show', opportunity)">
      <p class="flex items-center text-sm text-primary">
        <i class="fa-solid fa-arrow-left-long mr-2"></i>
        <span>Regresar</span>
      </p>
      </Link>
    </div>

    <form @submit.prevent="update" class="mx-8 mt-3 grid grid-cols-2 gap-x-4 gap-y-2">
      <div class="col-span-full lg:col-span-1">
        <InputLabel value="Nombre del presupuesto *" class="ml-2" />
        <input v-model="form.name" type="text" class="input mt-1" placeholder="Asignar un nombre a el presupuesto"
          required />
        <InputError :message="form.errors.name" />
      </div>
      <div>
        <InputLabel value="Tipo de servicio *" class="ml-2" />
        <el-select v-model="form.service_type" placeholder="Seleccione" class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in serviceTypes" :key="item.id" :label="item" :value="item" />
        </el-select>
        <InputError :message="form.errors.service_type" />
      </div>
      <div class="relative">
        <i :class="getColorStatus(form.status)" class="fa-solid fa-circle text-xs top-[2px] left-20 absolute z-30"></i>
        <InputLabel value="Estatus *" class="ml-2" />
        <div class="flex items-center space-x-4">
          <el-select class="w-full" v-model="form.status" filterable placeholder="Seleccionar estatus"
            no-data-text="No hay estatus registrados" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in statuses" :key="item" :label="item.label" :value="item.label">
              <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
              <span style="float: center; margin-left: 5px; font-size: 13px">{{
                item.label
                }}</span>
            </el-option>
          </el-select>
        </div>
        <InputError :message="form.errors.status" />
      </div>
      <div>
        <InputLabel value="Responsable *" class="ml-2" />
        <el-select @change="handleChangeSeller" class="w-full" v-model="form.seller_id" filterable
          placeholder="Seleccione" no-data-text="No hay vendedores registrados"
          no-match-text="No se encontraron coincidencias">
          <el-option v-for="seller in users.filter(
            (user) => user.employee_properties?.department == 'Administración y Ventas'
          )" :key="seller" :label="seller.name" :value="seller.id">
            <div v-if="$page.props.jetstream.managesProfilePhotos"
              class="flex text-sm rounded-full items-center mt-[3px]">
              <img class="h-7 w-7 rounded-full object-cover mr-4" :src="seller.profile_photo_url" :alt="seller.name" />
              <p>{{ seller.name }}</p>
            </div>
          </el-option>
        </el-select>
        <InputError :message="form.errors.seller_id" />
      </div>
      <!-- <label class="inline-flex items-center col-span-full my-3">
        <Checkbox v-model:checked="form.is_new_company" @change="handleChecked"
          class="bg-transparent disabled:border-gray-400" />
        <span class="ml-2 text-xs">Nuevo cliente</span>
      </label> -->
      <div v-if="form.is_new_company">
        <InputLabel value="Cliente *" class="ml-2" />
        <input v-model="form.customer_name" class="input" type="text" required />
        <InputError :message="form.errors.contact_name" />
      </div>
      <div v-if="form.is_new_company">
        <InputLabel value="Contacto *" class="ml-2" />
        <input v-model="form.contact_name" class="input" type="text" required />
        <InputError :message="form.errors.contact_name" />
      </div>
      <div v-if="form.is_new_company">
        <InputLabel value="Teléfono *" class="ml-2" />
        <input v-model="form.contact_phone" class="input" type="text" required />
        <InputError :message="form.errors.contact_phone" />
      </div>
      <div v-if="!form.is_new_company">
        <InputLabel value="Cliente *" class="ml-2" />
        <el-select class="w-full" v-model="form.customer_id" filterable placeholder="Seleccione"
          no-data-text="No hay clientes registrados" no-match-text="No se encontraron coincidencias">
          <el-option v-for="customer in customers.data" :key="customer.id" :label="customer.name"
            :value="customer.id" />
        </el-select>
        <InputError :message="form.errors.customer" />
      </div>
      <div v-if="!form.is_new_company">
        <InputLabel value="Contacto *" class="ml-2" />
        <el-select class="w-full" v-model="form.contact_id" filterable placeholder="Seleccione"
          no-data-text="No hay contactos registrados" no-match-text="No se encontraron coincidencias">
          <el-option v-for="contact in customers.data.find(
            (item) => item.id == form.customer_id
          )?.contacts" :key="contact" :label="contact.name" :value="contact.id" />
        </el-select>
        <InputError :message="form.errors.contact_id" />
      </div>
      <div v-if="!form.is_new_company">
        <InputLabel value="Sucursal *" class="ml-2" />
        <el-select class="w-full" v-model="form.branch" filterable placeholder="Seleccione"
          no-data-text="No hay sucursales registradas" no-match-text="No se encontraron coincidencias">
          <el-option v-for="branch in customers.data.find(
            (item) => item.id == form.customer_id
          )?.contacts.find((item) => item.id == form.contact_id).additional.branches" :key="branch" :label="branch"
            :value="branch" />
        </el-select>
        <InputError :message="$page.props.errors.branch" />
      </div> <br>
      <div class="col-span-full lg:col-span-1">
        <InputLabel value="Duración *" class="ml-2" />
        <el-date-picker @change="handleDateRange" v-model="range" type="daterange" range-separator="A"
          start-placeholder="Fecha de inicio" end-placeholder="Fecha de cierre" value-format="YYYY-MM-DD" />
        <InputError :message="form.errors.start_date" />
        <InputError :message="form.errors.close_date" />
      </div>
      <div class="mt-5 col-span-full">
        <InputLabel value="Descripción" class="ml-2" />
        <textarea v-model="form.description" rows="3" class="textarea"></textarea>
        <!-- <RichText @content="updateDescription($event)" :defaultValue="form.description" /> -->
      </div>
      <div class="ml-4 mt-2 col-span-full flex">
        <FileUploader @files-selected="this.form.media = $event" />
      </div>
      <div class="mt-5 w-full">
        <div class="flex justify-between items-center mx-2">
          <InputLabel value="Etiquetas" />
          <button @click="showTagFormModal = true" type="button"
            class="rounded-full border border-primary w-4 h-4 flex items-center justify-center">
            <i class="fa-solid fa-plus text-primary text-[9px]"></i>
          </button>
        </div>
        <el-select v-model="form.tags" placeholder="Seleccione" multiple class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in tags.data" :key="item.id" :label="item.name" :value="item.id">
            <Tag :name="item.name" :color="item.color" />
          </el-option>
        </el-select>
      </div>
      <div class="mt-5">
        <InputLabel value="Probabilidad %" />
        <input v-model="form.probability" class="input mt-1" placeholder="Probabilidad de cierre" type="number" min="0"
          max="100" />
      </div>
      <div class="w-full">
        <div class="relative">
          <i :class="getColorPriority(form.priority)"
            class="fa-solid fa-circle text-xs top-1 left-20 absolute z-30"></i>
          <InputLabel value="Prioridad *" />
          <div class="flex items-center space-x-4">
            <el-select class="w-full" v-model="form.priority" filterable placeholder="Seleccione"
              no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in priorities" :key="item" :label="item.label" :value="item.label">
                <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
                <span style="float: center; margin-left: 5px; font-size: 13px">{{
                  item.label
                  }}</span>
              </el-option>
            </el-select>
            <InputError :message="form.errors.priority" />
          </div>
        </div>
      </div>
      <div v-if="form.status == 'Perdida'" class="w-full">
        <label class="text-sm">Causa presupuesto perdido *
          <el-tooltip content="Escribe la causa por la cual se PERDIÓ este presupuesto" placement="right">
            <i class="fa-regular fa-circle-question ml-2 text-primary text-xs"></i>
          </el-tooltip>
        </label>
        <input v-model="form.lost_oportunity_razon" class="input" type="text" />
        <InputError :message="form.errors.lost_oportunity_razon" />
      </div>
      <!-- <div class="w-full col-span-full lg:col-span-1">
        <label class="text-sm">Valor de presupuesto *
          <el-tooltip content="Monto esperado si se cierra la venta" placement="right">
            <i class="fa-regular fa-circle-question ml-2 text-primary text-xs"></i>
          </el-tooltip>
        </label>
        <input v-model="form.amount" class="input" type="number" min="0" step="0.01" placeholder="Ingresa el monto" />
        <InputError :message="form.errors.amount" />
      </div> -->
      <h2 class="font-bold text-sm mt-3 col-span-full">Presupuesto</h2>
      <section class="col-span-full">
        <InputError :message="budgetMessage" id="budgetMessage" />
        <div v-for="(item, index) in form.budgets" :key="index" class="col-span-full flex items-center space-x-2 mt-2">
          <div class="w-[75%]">
            <InputLabel value="Concepto" class="ml-2" />
            <el-input v-model="form.budgets[index].concept" class="mt-1" placeholder="Ej. Costo de materiales"
              required />
          </div>
          <div class="w-[20%]">
            <InputLabel value="Monto" class="ml-2" />
            <el-input v-model="form.budgets[index].amount" placeholder="Ej. 2,800" class="mt-1"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/[^\d.]/g, '')" required>
              <template #prefix>
                <i class="fa-solid fa-dollar-sign"></i>
              </template>
            </el-input>
          </div>
          <el-popconfirm v-if="form.budgets.length > 1" confirm-button-text="Si" cancel-button-text="No"
            icon-color="#FD8827" title="¿Remover?" @confirm="deleteBudget(index)" class="w-[5%]">
            <template #reference>
              <button type="button" class="text-primary text-sm w-6 h-6 self-end mb-1 hover:bg-gray-100 rounded-full">
                <i class="fa-regular fa-trash-can"></i>
              </button>
            </template>
          </el-popconfirm>
        </div>
        <div class="flex justify-center mt-4">
          <button @click="addBudget()" type="button" class="text-xs text-primary underline">
            + Agregar otro concepto
          </button>
        </div>
      </section>
      <h2 class="font-bold text-sm my-2 col-span-full">Acceso a el presupuesto</h2>
      <div class="col-span-full text-sm">
        <div class="my-1">
          <input v-model="typeAccessProject" value="Public"
            class="checked:bg-primary focus:text-primary focus:ring-primary border-black mr-3" type="radio"
            name="typeAccessProject">
          <b>Público</b>
          <p class="text-[#9A9A9A] ml-7 text-xs">Los usuarios del portal solo pueden ver, seguir y comentar, mientras
            que los usuarios del ticket tendrán acceso directo.</p>
        </div>
        <div class="my-1">
          <input v-model="typeAccessProject" value="Private"
            class="checked:bg-primary focus:text-primary focus:ring-primary border-black mr-3" type="radio"
            name="typeAccessProject">
          <b>Privado</b>
          <p class="text-[#9A9A9A] ml-7 text-xs">Solo los usuarios de ticket pueden ver y acceder a este ticket
          </p>
        </div>
      </div>

      <section class="rounded-[10px] py-12 mx-7 mt-5 max-h-[580px] col-span-full border border-gray3">
        <div class="flex px-5 lg:px-16 mb-8">
          <div v-if="typeAccessProject === 'Private'" class="w-full">
            <h2 class="font-bold text-sm my-2 ml-2 col-span-full">
              Asignar participantes
            </h2>
            <el-select @change="addToSelectedUsers" filterable placeholder="Seleccionar usuario" class="w-full lg:w-1/2"
              no-data-text="No hay más usuarios para añadir" no-match-text="No se encontraron coincidencias">
              <el-option v-for="(item, index) in availableUsersToPermissions" :key="item.id" :label="item.name"
                :value="item.id">
                <div v-if="$page.props.jetstream.managesProfilePhotos"
                  class="flex text-sm rounded-full items-center mt-[3px]">
                  <img class="h-7 w-7 rounded-full object-cover mr-4" :src="item.profile_photo_url" :alt="item.name" />
                  <p>{{ item.name }}</p>
                </div>
              </el-option>
            </el-select>
          </div>
          <ThirdButton v-if="typeAccessProject === 'Public'" type="button" class="ml-auto self-start"
            @click.stop="editAccesFlag = !editAccesFlag">
            {{ editAccesFlag ? "Actualizar" : "Editar" }}
          </ThirdButton>
        </div>
        <div class="flex justify-between px-5 lg:px-16 mt-4">
          <div class="w-full">
            <div class="flex">
              <h2 class="font-bold border-b border-gray3 w-2/3 pl-3">Usuarios</h2>
              <h2 class="font-bold border-b border-gray3 w-1/3">Permisos</h2>
            </div>
            <div class="pl-3 overflow-y-auto min-h-[100px] max-h-[380px]">
              <div class="flex mt-2 border-b border-gray3" v-for="user in form.selectedUsersToPermissions"
                :key="user.id">
                <div class="w-2/3 flex space-x-2">
                  <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full w-10 lg:w-12">
                    <img class="h-8 lg:h-10 w-8 lg:w-10 rounded-full object-cover" :src="user.profile_photo_url"
                      :alt="user.name" />
                  </div>
                  <div class="text-xs lg:text-sm w-full">
                    <p>{{ user.name }}</p>
                    <p v-if="user.employee_properties">
                      {{ "Depto. " + user.employee_properties?.department }}
                    </p>
                    <p v-else>Super admin</p>
                  </div>
                </div>

                <div class="w-1/3 flex items-center justify-between">
                  <div class="space-y-1 mb-2">
                    <label class="flex items-center">
                      <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                        v-model:checked="user.permissions[0]" :checked="user.permissions[0]" />
                      <span :class="!editAccesFlag || user.employee_properties === null
                        ? 'text-gray-500/80 cursor-not-allowed'
                        : ''
                        " class="ml-2 text-xs">
                        Crea tareas
                      </span>
                    </label>
                    <label class="flex items-center">
                      <Checkbox disabled v-model:checked="user.permissions[1]" :checked="user.permissions[1]" />
                      <span :class="!editAccesFlag || user.employee_properties === null
                        ? 'text-gray-500/80 cursor-not-allowed'
                        : ''
                        " class="ml-2 text-xs">Ver</span>
                    </label>
                    <label class="flex items-center">
                      <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                        v-model:checked="user.permissions[2]" :checked="user.permissions[2]" />
                      <span :class="!editAccesFlag || user.employee_properties === null
                        ? 'text-gray-500/80 cursor-not-allowed'
                        : ''
                        " class="ml-2 text-xs">Editar</span>
                    </label>
                    <label class="flex items-center">
                      <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                        v-model:checked="user.permissions[3]" :checked="user.permissions[3]" />
                      <span :class="!editAccesFlag || user.employee_properties === null
                        ? 'text-gray-500/80 cursor-not-allowed'
                        : ''
                        " class="ml-2 text-xs">Eliminar</span>
                    </label>
                    <label class="flex items-center">
                      <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                        v-model:checked="user.permissions[4]" :checked="user.permissions[4]" />
                      <span :class="!editAccesFlag || user.employee_properties === null
                        ? 'text-gray-500/80 cursor-not-allowed'
                        : ''
                        " class="ml-2 text-xs">Comentar</span>
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
        <Link :href="route('crm.opportunities.index')">
        <CancelButton type="button">Cancelar</CancelButton>
        </Link>
        <PrimaryButton :disabled="form.processing || (editAccesFlag && typeAccessProject == 'Public')">Actualizar
          presupuesto</PrimaryButton>
      </div>
    </form>

    <!-- tag form -->
    <DialogModal :show="showTagFormModal" @close="showTagFormModal = false">
      <template #title> Agregar etiqueta </template>
      <template #content>
        <form @submit.prevent="storeTag" ref="tagForm">
          <div>
            <InputLabel value="Nombre de la etiqueta *" class="ml-2" />
            <input v-model="tagForm.name" type="text" class="input mt-1" placeholder="Escribe el nombre" required />
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
import ThirdButton from "@/Components/ThirdButton.vue";
import CancelButton from "@/Components/CancelButton.vue";
import RichText from "@/Components/MyComponents/RichText.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Tag from "@/Components/MyComponents/Tag.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      name: this.opportunity.name,
      service_type: this.opportunity.service_type,
      status: this.opportunity.status,
      seller_id: this.opportunity.seller_id,
      start_date: this.opportunity.start_date,
      close_date: this.opportunity.close_date,
      description: this.opportunity.description,
      tags: null,
      probability: this.opportunity.probability,
      lost_oportunity_razon: this.opportunity.lost_oportunity_razon,
      priority: this.opportunity.priority,
      // amount: this.opportunity.amount,
      budgets: this.opportunity.budgets,
      selectedUsersToPermissions: [],
      media: [],
      is_new_company: false,
      contact_id: this.opportunity.contact_id,
      customer_id: this.opportunity.customer_id,
      customer_name: this.opportunity.customer_name,
      branch: this.opportunity.branch,
      contact_name: this.opportunity.contact_name,
      contact_phone: this.opportunity.contact_phone,

    });

    const tagForm = useForm({
      name: null,
      color: null,
    });
    return {
      form,
      tagForm,
      showTagFormModal: false,
      company_branch: null,
      budgetMessage: null,
      showTagFormModal: false,
      company_branch_obj: null,
      range: null,
      typeAccessProject: 'Private',
      // owner: this.$page.props.auth.user.name,
      mediaNames: [], // Agrega esta propiedad para almacenar los nombres de los archivos
      editAccesFlag: true,
      statuses: [
        {
          label: "Trabajo terminado",
          color: "text-[#f2f2f2]",
        },
        {
          label: "Presupuesto",
          color: "text-[#F3FD85]",
        },
        {
          label: "Facturado",
          color: "text-[#FEDBBD]",
        },
        {
          label: "Pagado",
          color: "text-[#AFFDB2]",
        },
        {
          label: "Perdido",
          color: "text-[#F7B7FC]",
        },
      ],
      priorities: [
        {
          label: "Baja",
          color: "text-[#87CEEB]",
        },
        {
          label: "Media",
          color: "text-[#F2C940]",
        },
        {
          label: "Alta",
          color: "text-[#D90537]",
        },
      ],
      serviceTypes: [
        "Iluminacón",
        "Herrería",
        "Acabados",
        "Eléctrico",
        "A. acondicionado",
        "Sanitario",
        "Anuncios",
        "Pintura",
        "Carpintería",
        "Vidrio",
        "Aluminio",
        "Protección civil STPS",
        "Monta cargas",
        "Control de plagas",
        "Impermeabilización",
        "Servicios varios",
      ],
      contacts: [],
      branches: [],
    }
  },
  components: {
    AppLayout,
    PrimaryButton,
    InputLabel,
    InputError,
    ThirdButton,
    RichText,
    CancelButton,
    SecondaryButton,
    DialogModal,
    FileUploader,
    Checkbox,
    Link,
    Tag,
  },
  props: {
    users: Array,
    tags: Object,
    customers: Object,
    opportunity: Object,
  },
  methods: {
    scrollToElement(elementId) {
      const el = document.getElementById(elementId);
      el.scrollIntoView({ behavior: 'smooth' });
    },
    deleteBudget(index) {
      this.form.budgets.splice(index, 1);
    },
    addBudget() {
      const newBudget = { concept: null, amount: null };
      this.form.budgets.push(newBudget);
    },
    handleChangeSeller() {
      if (!this.form.selectedUsersToPermissions.some(item => item.id == this.form.seller_id)) {
        this.addToSelectedUsers(this.form.seller_id, true);
      }
    },
    update() {
      if (this.form.media.length) {
        this.form.post(route("crm.opportunities.update-with-media", this.opportunity.id), {
          method: '_put',
          onSuccess: () => {
            this.$notify({
              title: "Correcto",
              message: "Se ha actualizado el presupuesto",
              type: "success",
            });

          },
        });
      } else {
        this.form.put(route("crm.opportunities.update", this.opportunity.id), {
          onSuccess: () => {
            this.$notify({
              title: "Correcto",
              message: "Se ha actualizado el presupuesto",
              type: "success",
            });

          },
        });
      }
    },
    getColorStatus(oportunityStatus) {
      if (oportunityStatus === "Trabajo terminado") {
        return "text-[#f2f2f2]";
      } else if (oportunityStatus === "Presupuesto") {
        return "text-[#F3FD85]";
      } else if (oportunityStatus === "Facturado") {
        return "text-[#FEDBBD]";
      } else if (oportunityStatus === "Pagado") {
        return "text-[#AFFDB2]";
      } else if (oportunityStatus === "Perdido") {
        return "text-[#F7B7FC]";
      } else {
        return "text-transparent";
      }
    },
    getColorPriority(opportunityPriority) {
      if (opportunityPriority === "Baja") {
        return "text-[#87CEEB]";
      } else if (opportunityPriority === "Media") {
        return "text-[#F2C940]";
      } else if (opportunityPriority === "Alta") {
        return "text-[#D90537]";
      } else {
        return "text-transparent";
      }
    },
    submitTagForm() {
      this.$refs.tagForm.dispatchEvent(new Event("submit", { cancelable: true }));
    },
    async storeTag() {
      try {
        this.tagForm.processing = true;
        const response = await axios.post(route("pms.tags.store"), {
          name: this.tagForm.name,
          color: this.tagForm.color,
          type: "opportunities",
          user_id: this.$page.props.auth.user.id,
        });

        if (response.status === 200) {
          this.$notify({
            title: "Correcto",
            message: response.data.message,
            type: "success",
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
        console.log(error);
      } finally {
        this.tagForm.processing = false;
      }
    },
    updateDescription(content) {
      this.form.description = content;
    },
    handleChecked() {
      //resetea la busqueda de contacto en formulario
      this.form.customer_id = null;
      this.form.branch = null;
      this.form.contact_id = null;
    },
    updateContacts() {
      const selectedCustomer = this.customers.data.find(
        (item) => item.id === this.form.customer_id
      );

      this.contacts = selectedCustomer ? selectedCustomer.contacts : [];
    },
    updateBranches() {
      const selectedContact = this.contacts.find(
        (item) => item.id === this.form.contact_id
      );

      this.branches = selectedContact ? selectedContact?.additional.branches : [];
    },
    selectAdmins() {
      // obtener los usuarios admin para que siempre aparezcan en los tickets y dar todos los permisos
      let admins = this.users.filter((item) => item.employee_properties == null);
      admins.forEach((admin) => {
        const defaultPermissions = [true, true, true, true, true];
        admin.permissions = defaultPermissions;
      });
      this.form.selectedUsersToPermissions = admins;
    },
    selectAuthUser() {
      if (this.$page.props.auth.user.employee_properties !== null) {
        // obtener usuario que esta creando el ticket para dar todos los permisos
        const user = this.users.find((item) => item.id === this.$page.props.auth.user.id);
        const defaultPermissions = [true, true, true, true, true];
        let authUser = {
          id: user.id,
          name: user.name,
          profile_photo_url: user.profile_photo_url,
          permissions: [...defaultPermissions],
        };
        this.form.selectedUsersToPermissions.push(authUser);
      }
    },
    removeUserFromPermissions(userId) {
      const index = this.form.selectedUsersToPermissions.findIndex(
        (item) => item.id === userId
      );

      this.form.selectedUsersToPermissions.splice(index, 1);
    },
    addToSelectedUsers(userId, allPermissions = false) {
      const user = this.users.find((item) => item.id === userId);
      let defaultPermissions = [false, true, false, false, true];
      if (allPermissions) {
        defaultPermissions = [true, true, true, true, true];
      }
      let foundUser = {
        id: user.id,
        name: user.name,
        employee_properties: user.employee_properties,
        profile_photo_url: user.profile_photo_url,
        permissions: [...defaultPermissions],
      };
      this.form.selectedUsersToPermissions.push(foundUser);
    },
  },
  computed: {
    availableUsersToPermissions() {
      if (this.form.selectedUsersToPermissions.length == 0) return this.users;

      const availableUsers = this.users.filter((item) => {
        // Verifica si el item no se encuentra en item2
        return !this.form.selectedUsersToPermissions.find(
          (item2) => item.id === item2.id
        );
      });

      return availableUsers;
    },
  },
  watch: {
    typeAccessProject(newVal) {
      this.selectAdmins();
      if (newVal === "Public") {
        let defaultPermissions = [false, true, false, false, true];
        let usersWithSelectedProperties = this.users
          .filter((element) => element.employee_properties !== null)
          .map((user) => ({
            id: user.id,
            name: user.name,
            employee_properties: user.employee_properties,
            profile_photo_url: user.profile_photo_url,
            permissions: [...defaultPermissions],
          }));
        this.form.selectedUsersToPermissions = [
          ...this.form.selectedUsersToPermissions,
          ...usersWithSelectedProperties,
        ];
        this.editAccesFlag = false;
      } else {
        this.selectAuthUser();
        this.editAccesFlag = true;
        this.handleChangeSeller();
      }
    },
  },
  mounted() {
    // this.selectAdmins();
    this.form.tags = this.opportunity.tags.map(tag => tag.id);

    // inicializar permisos
    this.opportunity.users.forEach(user => {
      const participant = {
        id: user.id,
        name: user.name,
        profile_photo_url: user.profile_photo_url,
        employee_properties: user.employee_properties,
        permissions: JSON.parse(user.pivot.permissions),
      };
      this.form.selectedUsersToPermissions.push(participant);
    });
    this.updateContacts();
    this.updateBranches();

    // inicializar fechas en range
    this.range = [this.opportunity.start_date, this.opportunity.close_date];
  },
}
</script>