<template>
  <AppLayout title="Nuevo cliente">
    <div class="flex justify-between items-center text-lg mx-8 mt-8">
      <b>Nuevo cliente</b>
      <Link :href="route('crm.customers.index')">
      <p class="flex items-center text-sm text-primary">
        <i class="fa-solid fa-arrow-left-long mr-2"></i>
        <span>Regresar</span>
      </p>
      </Link>
    </div>
    <form @submit.prevent="store" class="mx-8 mt-3 grid grid-cols-2 gap-x-4 gap-y-2">
      <div>
        <InputLabel value="Razón social *" class="ml-2" />
        <input v-model="form.name" type="text" class="input mt-1" placeholder="Asignar una razón social" required>
        <InputError :message="form.errors.name" />
      </div>
      <div>
        <InputLabel value="RFC *" class="ml-2" />
        <input v-model="form.rfc" type="text" class="input mt-1" placeholder="Escriba un RFC" required>
        <InputError :message="form.errors.rfc" />
      </div>
      <div class=" w-full">
        <div class="flex justify-between items-center mx-2">
          <InputLabel value="Etiquetas" />
          <button v-if="$page.props.auth.user.permissions?.includes('Crear etiquetas de clientes')"
            @click="showTagFormModal = true" type="button"
            class="rounded-full border border-primary w-4 h-4 flex items-center justify-center">
            <i class="fa-solid fa-plus text-primary text-[9px]"></i>
          </button>
        </div>
        <el-select v-model="form.tags"  placeholder="Seleccione" multiple class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="(item, index) in tags.data" :key="item.id" :label="item.name" :value="item.id">
            <Tag :name="item.name" :color="item.color" />
          </el-option>
        </el-select>
      </div>

      <!----- Datos del contacto ----------------------------------------------------------------->
      <h2 class="font-bold mt-7 col-span-2">Datos del contacto</h2>
      <div class="bg-[#f2f2f2] col-span-full p-5 grid grid-cols-2 gap-x-4 gap-y-2 rounded-[3px]">
        <div>
          <InputLabel value="Nombre *" class="ml-2" />
          <input v-model="contact.name" type="text" class="input mt-1" placeholder="Asignar un Nombre">
        </div>
        <div>
          <InputLabel value="Correo electrónico *" class="ml-2" />
          <input v-model="contact.email" type="text" class="input mt-1" placeholder="Escriba un correo electrónico">
        </div>
        <div>
          <InputLabel value="Teléfono *" class="ml-2" />
          <input v-model="contact.phone" type="text" class="input mt-1" placeholder="Escriba un teléfono">
          <InputError :message="phoneErrorMessage" />
        </div>
        <div>
          <InputLabel value="Puesto *" class="ml-2" />
          <input v-model="contact.position" type="text" class="input mt-1" placeholder="Escriba un puesto">
        </div>
        <div>
          <InputLabel value="Sucursal *" class="ml-2" />
          <input v-model="branch" type="text" class="input mt-1" placeholder="Asignar una sucursal">
          <p v-if="branchValidation" class="text-xs text-red-500">Favor de esciribir una sucursal</p>
        </div>
        <p @click="addBranch" class="text-primary text-sm mt-7 cursor-pointer"><i class="fa-solid fa-plus mr-1"></i>
          Agregar sucursal</p>
        <h2 v-if="contact.branches.length" class="col-span-full font-bold text-sm mt-3">Lista de sucursales </h2>
        <ul>
          <div v-for="(item, index) in contact.branches" :key="index">
            <div class="flex items-center justify-between text-xs">
              <p>
                <span class="font-bold mr-2">Sucursal {{ (index + 1) }}:</span>
                {{ item }}
              </p>
              <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#FD8827" title="¿Remover?"
                @confirm="deleteBranch(index)">
                <template #reference>
                  <i class="fa-regular fa-trash-can text-primary text-sm ml-5 cursor-pointer"></i>
                </template>
              </el-popconfirm>
            </div>
          </div>
        </ul>
        <InputError :message="form.errors.branches" />
        <PrimaryButton v-if="contactEditIndex !== null" type="button" @click="updateContact"
          class="justify-self-end self-end" :disabled="!isContactCompleted">
          Actualizar contacto
        </PrimaryButton>
        <PrimaryButton v-else type="button" @click="addContact" class="justify-self-end self-end"
          :disabled="!isContactCompleted">
          Agregar contacto
        </PrimaryButton>
      </div>

      <!-- lista de contactos -->
      <div v-for="(item, index) in form.contacts" :key="index" class="bg-[#f2f2f2] p-5 mt-2 rounded-[3px] col-span-full lg:col-span-1">
        <header class="flex justify-between items-center">
          <h2 class="font-bold text-sm mb-2">Contacto {{ (index + 1) }}</h2>
          <div class="flex space-x-1 items-center">
            <el-popconfirm v-if="contactEditIndex != index" confirm-button-text="Si" cancel-button-text="No"
              icon-color="#FD8827" title="¿Remover?" @confirm="deleteContact(index)">
              <template #reference>
                <SecondaryButton>
                  <i class="fa-regular fa-trash-can text-primary text-sm cursor-pointer"></i>
                </SecondaryButton>
              </template>
            </el-popconfirm>
            <SecondaryButton v-if="contactEditIndex != index" @click="editContact(index)">Editar</SecondaryButton>
            <label v-else class="text-[#0355B5] bg-[#B9D9FE] text-sm rounded-[15px] px-4 py-2">En edición <i
                class="fa-solid fa-arrow-up"></i></label>
          </div>
        </header>
        <div class="grid grid-cols-3 gap-x-3 gap-y-2 text-xs">
          <span>Nombre</span>
          <span class="col-span-2">{{ item.name }}</span>
          <span>Correo electrónico</span>
          <span class="col-span-2">{{ item.email }}</span>
          <span>Teléfono</span>
          <span class="col-span-2">{{ item.phone }}</span>
          <span>Puesto</span>
          <span class="col-span-2">{{ item.position }}</span>
          <h2 class="font-bold col-span-full">Lista de sucursales</h2>
          <ul v-for="(branch, index2) in item.branches" :key="index2"
            class="col-span-full grid grid-cols-3 gap-x-3 gap-y-2 text-xs">
            <span>Sucursal {{ (index2 + 1) }}</span>
            <span class="col-span-2">{{ branch }}</span>
          </ul>
        </div>
      </div>
      <InputError :message="form.errors.contacts" />
      <!----- Datos adicionales ----------------------------------------------------------------->
      <h2 class="font-bold mt-7 col-span-2">Datos adicionales</h2>
      <div>
        <InputLabel value="Condiciones de pago *" class="ml-2" />
        <el-select v-model="form.invoicing_method"  placeholder="Seleccione" class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="item in invoicingMetods" :key="item.id" :label="item" :value="item" />
        </el-select>
        <InputError :message="form.errors.invoicing_method" />
      </div>
      <div>
        <InputLabel value="Método de pago *" class="ml-2" />
        <el-select v-model="form.payment_method"  placeholder="Seleccione" class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="item in paymentMethods" :key="item.id" :label="item" :value="item" />
        </el-select>
        <InputError :message="form.errors.payment_method" />
      </div>
      <div>
        <InputLabel value="Uso de factura *" class="ml-2" />
        <el-select v-model="form.invoice_use"  placeholder="Seleccione" class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="item in invoiceUses" :key="item.id" :label="item" :value="item" />
        </el-select>
        <InputError :message="form.errors.invoice_use" />
      </div>

      <div>
        <InputLabel value="Moneda *" class="ml-2" />
        <el-select v-model="form.currency"  placeholder="Seleccione" class="w-full mt-1"
          no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="item in currencies" :key="item.id" :label="item" :value="item" />
        </el-select>
        <InputError :message="form.errors.currency" />
      </div>

      <div class="col-span-full flex mt-8 mb-5 justify-end space-x-2">
        <Link :href="route('crm.customers.index')">
        <CancelButton type="button">Cancelar</CancelButton>
        </Link>
        <PrimaryButton :disabled="form.processing">Agregar</PrimaryButton>
      </div>
    </form>

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
        <PrimaryButton @click="submitTagForm()" :disabled="tagForm.processing">Agregar</PrimaryButton>
      </template>
    </DialogModal>
  </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import DialogModal from "@/Components/DialogModal.vue";
import CancelButton from "@/Components/CancelButton.vue";
import Tag from "@/Components/MyComponents/Tag.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      name: null,
      rfc: null,
      contacts: [],
      tags: [],
      invoicing_method: null,
      payment_method: null,
      invoice_use: null,
      currency: 'Peso $MXN',
    });

    const tagForm = useForm({
      name: null,
      color: null,
    });
    return {
      form,
      tagForm,
      showTagFormModal: false,
      branchValidation: false,
      branch: null,
      contactEditIndex: null,
      invoicingMetods: [
        'Facturación a crédito',
        'Facturación al contado',
        'Otro',
      ],
      paymentMethods: [
        'Transferencia electrónica',
        'Pago en efectivo',
        'Otro',
      ],
      invoiceUses: [
        'Gastos en general',
        'Otro',
      ],
      currencies: [
        'Peso $MXN',
        'Dolar $USD',
      ],
      contact: {
        name: null,
        email: null,
        phone: null,
        position: null,
        branches: [],
      },
      // error messages
      phoneErrorMessage: null,
    }
  },
  components: {
    AppLayout,
    PrimaryButton,
    CancelButton,
    InputLabel,
    InputError,
    DialogModal,
    Link,
    Tag,
    SecondaryButton,
  },
  props: {
    tags: Object,
  },
  computed: {
    isContactCompleted() {
      return this.contact.name &&
        this.contact.email &&
        this.contact.phone &&
        this.contact.position &&
        this.contact.branches.length;
    },
  },
  methods: {
    store() {
      this.form.post(route('crm.customers.store'), {
        onSuccess: () => {
          this.$notify({
            title: 'Correcto',
            message: 'Cliente creado',
            type: 'success'
          });
        }
      })
    },
    submitTagForm() {
      this.$refs.tagForm.dispatchEvent(new Event('submit', { cancelable: true }));
    },
    async storeTag() {
      try {
        this.tagForm.processing = true;
        const response = await axios.post(route('pms.tags.store'), { name: this.tagForm.name, color: this.tagForm.color, type: 'crm', user_id: this.$page.props.auth.user.id });

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
    resetContact() {
      this.contact.name = null;
      this.contact.email = null;
      this.contact.phone = null;
      this.contact.position = null;
      this.contact.branches = [];
    },
    addContact() {
      if (this.contact.phone.length > 14) {
        this.phoneErrorMessage = "No debe exceder 14 caracteres";
      } else {
        this.phoneErrorMessage = null;
        this.form.contacts.push({ ...this.contact });
        this.resetContact();
      }
    },
    addBranch() {
      if (this.branch == null) {
        this.branchValidation = true;
        return
      } else {
        this.contact.branches.push(this.branch);
        this.branch = null;
        this.branchValidation = false;
      }
    },
    editContact(index) {
      this.contactEditIndex = index;
      this.contact = { ...this.form.contacts[index] };
    },
    updateContact() {
      this.form.contacts[this.contactEditIndex] = { ...this.contact };
      this.contactEditIndex = null;
      this.resetContact();
    },
    deleteBranch(index) {
      this.contact.branches.splice(index, 1);
    },
    deleteContact(index) {
      this.form.contacts.splice(index, 1);
    },
  },
};

</script>