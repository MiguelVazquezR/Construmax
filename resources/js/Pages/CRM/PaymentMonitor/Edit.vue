<template>
<AppLayout title="Editar registro de pago">
    <div class="flex justify-between items-center text-lg mx-8 mt-8">
        <b>Editar pago o transacción</b>
        <Link :href="route('crm.client-monitors.index')">
        <p class="flex items-center text-sm text-primary">
        <i class="fa-solid fa-arrow-left-long mr-2"></i>
        <span>Regresar</span>
        </p>
        </Link>
    </div>
    <form @submit.prevent="update" class="mx-8 mt-3 grid grid-cols-2 gap-x-4 gap-y-2">
        <div>
            <InputLabel value="Folio de oportunidad *" class="ml-2" />
            <el-select @change="getCustomer" class="w-full" v-model="form.opportunity_id" clearable filterable placeholder="Seleccione"
                no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
                <el-option v-for="opportunity in opportunities.data" :key="opportunity" :label="opportunity.folio + ' - ' + opportunity.name" :value="opportunity.id" />
            </el-select>
            <InputError :message="form.errors.opportunity_id" />
        </div>
        <h2 class="text-primary col-span-2 my-3">Datos del cliente</h2>
        <div class="w-full">
            <InputLabel value="Cliente *" class="ml-2" />
            <el-select disabled @change="cleanCustomerInfo" class="w-full" v-model="form.customer_id" clearable filterable
                placeholder="Seleccione" no-data-text="No hay clientes registrados"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="customer in customers.data" :key="customer" :label="customer.name" :value="customer.id" />
            </el-select>
            <InputError :message="form.errors.customer_id" />
        </div>
        <div class="w-full">
            <InputLabel value="Contacto *" class="ml-2" />
            <el-select @change="getContactPhone" class="w-full" v-model="form.contact_id" clearable filterable placeholder="Seleccione"
            no-data-text="No hay contactos registrados" no-match-text="No se encontraron coincidencias">
            <el-option v-for="contact in customers.data.find(
                (item) => item.id == form.customer_id
            )?.contacts" :key="contact" :label="contact.name"
                :value="contact.id" />
            </el-select>
            <InputError :message="form.errors.contact_id" />
        </div>
        <div class="w-full">
            <InputLabel value="Sucursal *" class="ml-2" />
            <el-select class="w-full" v-model="form.branch" clearable filterable
            placeholder="Seleccione" no-data-text="No hay sucursales registradas"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="branch in customers.data.find(
                (item) => item.id == form.customer_id
            )?.contacts?.find( (item) => item.id == form.contact_id)?.additional.branches" :key="branch" :label="branch" :value="branch" />
            </el-select>
            <InputError :message="form.errors.branch" />
        </div>

        <h2 class="text-primary col-span-2 my-3">Datos del pago</h2>

        <div class="w-full">
            <InputLabel value="Monto pagado *" class="ml-2" />
            <input v-model="form.amount" class="input" type="number" min="0">
            <InputError :message="form.errors.amount" />
        </div>
        <div class="w-full">
            <InputLabel value="Método de pago *" class="ml-2" />
            <el-select class="w-full" v-model="form.payment_method" clearable filterable placeholder="Seleccione"
                no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
                <el-option v-for="payment_method in payment_methods" :key="payment_method" :label="payment_method" :value="payment_method" />
            </el-select>
            <InputError :message="form.errors.payment_method" />
        </div>
        <div class="w-full">
            <InputLabel value="Concepto *" class="ml-2" />
            <input v-model="form.concept" class="input" type="text">
            <InputError :message="form.errors.concept" />
        </div>
        <div class="w-full">
            <InputLabel value="Fecha del pago *" class="ml-2" />
            <el-date-picker class="w-full" v-model="form.paid_at" type="date" placeholder="Fecha*" format="YYYY/MM/DD"
                 :disabled-date="disabledDate" />
            <InputError :message="form.errors.paid_at" />
        </div>
        <div class="mt-5 col-span-full">
          <InputLabel value="Notas " class="ml-2" />
          <textarea class="input h-24" v-model="form.notes" rows="3"></textarea>
        </div>
        <div class="ml-2 mt-2 col-span-full">
              <FileUploader @files-selected="this.form.media = $event" />
        </div>
        <div class="flex justify-end items-center col-span-2 mt-5">
          <PrimaryButton :disabled="form.processing">
            Guardar cambios
          </PrimaryButton>
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
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
data(){
    const form = useForm({
        opportunity_id: this.payment_monitor.data.opportunity?.id,
        customer_id: this.payment_monitor.data.customer?.id,
        branch: this.payment_monitor.data.branch,
        contact_id: this.payment_monitor.data.contact?.id,
        contact_name: this.payment_monitor.data.contact_name,
        contact_phone: this.payment_monitor.data.contact_phone,
        paid_at: this.payment_monitor.data.paid_at_raw,
        amount: this.payment_monitor.data.amount,
        payment_method: this.payment_monitor.data.payment_method,
        concept: this.payment_monitor.data.concept,
        notes: this.payment_monitor.data.notes,
        media: [],
    });
    return{
        form,
        payment_methods: [
        'Transferencia electrónica',
        'Depósito',
        'Pago en efectivo',
        'Otro',
      ],
    }
},
components:{
    AppLayout,
    PrimaryButton,
    InputLabel,
    InputError,
    CancelButton,
    FileUploader,
    Link
},
props:{
    payment_monitor: Object,
    opportunities: Object,
    customers: Object,
},
methods:{
    update() {
      if (this.form.media.length > 0) {
        this.form.post(route("crm.payment-monitors.update-with-media", this.payment_monitor.data.id), {
          method: '_put',
          onSuccess: () => {
            this.$notify({
              title: "Correcto",
              message: "Registro de pago editado",
              type: "success",
            });
          },
        });
      } else {
        this.form.put(route("crm.payment-monitors.update", this.payment_monitor.data.id), {
          onSuccess: () => {
            this.$notify({
              title: "Correcto",
              message: "Registro de pago editado",
              type: "success",
            });
          },
        });
      }
    },
    getCustomer() {
        const opportunity = this.opportunities.data.find(opportunity => opportunity.id === this.form.opportunity_id);
        this.form.branch = null;
        this.form.contact_id = null;
        this.form.customer_id = opportunity.customer.id;
      },
      cleanCustomerInfo() {
        this.form.contact_id = null;
        this.form.branch = null;
      },
      getContactPhone() {
        this.form.branch = null; 
        this.form.contact_name = this.customers.data.find((item) => item.id == this.form.customer_id)?.contacts?.find( (item) => item.id == this.form.contact_id).name; 
        this.form.contact_phone = this.customers.data.find((item) => item.id == this.form.customer_id)?.contacts?.find( (item) => item.id == this.form.contact_id).phone;
      },
}
}
</script>
