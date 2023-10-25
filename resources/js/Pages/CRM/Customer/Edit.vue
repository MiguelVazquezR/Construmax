<template>
    <AppLayout title="Editar cliente">
        <div class="flex justify-between items-center text-lg mx-8 mt-8">
            <b>Editar cliente</b>
            <Link :href="route('crm.customers.show', customer.id)">
            <p class="flex items-center text-sm text-primary">
                <i class="fa-solid fa-arrow-left-long mr-2"></i>
                <span>Regresar</span>
            </p>
            </Link>
        </div>

        <form @submit.prevent="update" class="mx-8 mt-3 grid grid-cols-2 gap-x-4 gap-y-2">
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
            <div>
                <div>
                    <InputLabel value="Sucursal *" class="ml-2" />
                    <input v-model="branch" type="text" class="input mt-1" placeholder="Asignar una sucursal">
                    <p v-if="branchValidation" class="text-xs text-red-500">Favor de esciribir una sucursal</p>
                </div>
                <p @click="addBranch" class="text-primary text-sm mt-4 cursor-pointer"><i class="fa-solid fa-plus mr-1"></i> Agregar sucursal</p>
            </div>
            <div class=" w-full">
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
            <ul class="col-span-2">
                <div class="mt-3" v-for="(item, index) in form.branches" :key="index">
                    <InputLabel :value="'Sucursal ' + (index + 1)" class="ml-2 w-1/2" />
                    <div class="flex items-center">
                        <input :value="item" type="text" class="input w-1/2 mt-1" disabled>
                        <i @click="deleteBranch(index)" class="fa-regular fa-trash-can text-red-500 text-sm ml-4 cursor-pointer"></i>
                    </div>
                </div>
            </ul>
            <InputError :message="form.errors.branches" />


<!----- Datos del contacto ----------------------------------------------------------------->
            <h2 class="font-bold mt-7 col-span-2">Datos del contacto</h2>
            <div>
                <InputLabel value="Nombre *" class="ml-2" />
                <input v-model="form.contact_name" type="text" class="input mt-1" placeholder="Asignar una Nombre" required>
                <InputError :message="form.errors.contact_name" />
            </div>
            <div>
                <InputLabel value="Correo electrónico *" class="ml-2" />
                <input v-model="form.contact_email" type="text" class="input mt-1" placeholder="Escriba un correo electrónico" required>
                <InputError :message="form.errors.contact_email" />
            </div>
            <div>
                <InputLabel value="Teléfono *" class="ml-2" />
                <input v-model="form.contact_phone" type="text" class="input mt-1" placeholder="Escriba un teléfono" required>
                <InputError :message="form.errors.contact_phone" />
            </div>


<!----- Datos adicionales ----------------------------------------------------------------->
            <h2 class="font-bold mt-7 col-span-2">Datos adicionales</h2>
            <div>
                <InputLabel value="Método de facturación *" class="ml-2" />
                <el-select v-model="form.invoicing_method" clearable placeholder="Seleccione" class="w-full mt-1"
                no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in invoicingMetods" :key="item.id" :label="item" :value="item" />
                </el-select>
                <InputError :message="form.errors.invoicing_method" />
            </div>
            <div>
                <InputLabel value="Método de pago *" class="ml-2" />
                <el-select v-model="form.payment_method" clearable placeholder="Seleccione" class="w-full mt-1"
                no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in paymentMethods" :key="item.id" :label="item" :value="item" />
                </el-select>
                <InputError :message="form.errors.payment_method" />
            </div>
            <div>
                <InputLabel value="Use de factura *" class="ml-2" />
                <el-select v-model="form.invoice_use" clearable placeholder="Seleccione" class="w-full mt-1"
                no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in invoiceUses" :key="item.id" :label="item" :value="item" />
                </el-select>
                <InputError :message="form.errors.invoice_use" />
            </div>

            <div>
                <InputLabel value="Moneda *" class="ml-2" />
                <el-select v-model="form.currency" clearable placeholder="Seleccione" class="w-full mt-1"
                no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in currencies" :key="item.id" :label="item" :value="item" />
                </el-select>
                <InputError :message="form.errors.currency" />
            </div>

            <div class="col-span-full flex mt-8 mb-5 justify-end space-x-2">
                <Link :href="route('crm.customers.index')">
                <CancelButton type="button">Cancelar</CancelButton>
                </Link>
                <PrimaryButton>Guardar cambios</PrimaryButton>
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
import InputLabel from "@/Components/InputLabel.vue";   
import InputError from "@/Components/InputError.vue";
import DialogModal from "@/Components/DialogModal.vue";
import CancelButton from "@/Components/CancelButton.vue";
import Tag from "@/Components/MyComponents/Tag.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
        name: this.customer.name,
        rfc: this.customer.rfc,
        branches: this.customer.branches,
        contact_name: this.customer.contact_name,
        contact_email: this.customer.contact_email,
        contact_phone: this.customer.contact_phone,
        invoicing_method: this.customer.invoicing_method,
        payment_method: this.customer.payment_method,
        invoice_use: this.customer.invoice_use,
        currency: this.customer.currency,
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
    }
},
components:{
    AppLayout,
    PrimaryButton,
    CancelButton,
    InputLabel,
    InputError,
    DialogModal,
    Link,
    Tag,
},
props:{
    tags: Object,
    customer: Object
},
methods:{
    update() {
      this.form.put(route('crm.customers.update', this.customer.id), {
        onSuccess: () => {
          this.$notify({
            title: 'Correcto',
            message: 'Cliente Editado',
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
    addBranch() {
        if (this.branch == null ) {
            this.branchValidation = true;
            return
        } else {
            this.form.branches.push(this.branch);
            this.branch = null;
            this.branchValidation = false;
        }
    },
    deleteBranch(index) {
        this.form.branches.splice(index, 1);
    },
},
};

</script>