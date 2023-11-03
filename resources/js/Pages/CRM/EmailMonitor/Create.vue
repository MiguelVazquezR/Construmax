<template>
  <AppLayout title="Correo electrónico">
    <div class="flex justify-between items-center text-lg mx-8 mt-8">
      <b>Correo electrónico</b>
      <Link :href="route('crm.client-monitors.index')">
      <p class="flex items-center text-sm text-primary">
        <i class="fa-solid fa-arrow-left-long mr-2"></i>
        <span>Regresar</span>
      </p>
      </Link>
    </div>
    <form @submit.prevent="store" class="mx-8 mt-3 grid grid-cols-2 gap-x-4 gap-y-2">
        <div>
            <InputLabel value="Folio de oportunidad *" class="ml-2" />
            <el-select @change="getCustomer" class="w-full" v-model="form.opportunity_id" clearable filterable placeholder="Seleccione"
                no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
                <el-option v-for="opportunity in opportunities.data" :key="opportunity" :label="opportunity.folio + ' - ' + opportunity.name" :value="opportunity.id" />
            </el-select>
            <InputError :message="form.errors.opportunity_id" />
        </div>
        <h2 class="text-primary col-span-2 mt-2">Datos del cliente</h2>
        <div class="w-full">
            <InputLabel value="Cliente *" class="ml-2" />
            <el-select disabled class="w-full" v-model="form.customer_id" clearable filterable
                placeholder="Seleccione" no-data-text="No hay clientes registrados"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="customer in customers.data" :key="customer" :label="customer.name" :value="customer.id" />
            </el-select>
            <InputError :message="form.errors.customer_id" />
        </div>
        <div class="w-full">
            <InputLabel value="Contacto *" class="ml-2" />
            <el-select @change="getContactEmail" class="w-full" v-model="form.contact_id" clearable filterable placeholder="Seleccione"
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
        <div class="w-full">
            <InputLabel value="Correo electrónico *" class="ml-2" />
            <input v-model="form.contact_email" class="input" type="text">
            <InputError :message="form.errors.contact_email" />
        </div>
        <div>
            <InputLabel value="Asunto *" class="ml-2" />
            <input v-model="form.subject" class="input" type="text" >
            <InputError :message="form.errors.subject" />
        </div>
        <div class="col-span-2">
            <InputLabel value="Contenido *" class="ml-2" />
            <textarea v-model="form.content" class="input h-24" rows="3">
            </textarea>
            <InputError :message="form.errors.content" />
        </div>
    <!-- <div class="ml-2 mt-2 col-span-full flex">
            <FileUploader @files-selected="this.form.media = $event" />
        </div> -->
        <div class="flex justify-end items-center col-span-2 mt-5">
        <PrimaryButton :disabled="form.processing">
            Enviar
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
import { Link, useForm } from "@inertiajs/vue3";

export default {
data(){
    const form = useForm({
    opportunity_id: null,
    customer_id: null,
    branch: null,
    contact_id: null,
    contact_name: null,
    contact_email: null,
    subject: null,
    content: null,
    // media: [],
        });
    return {
        form,
    }
},
components:{
    AppLayout,
    PrimaryButton,
    InputLabel,
    InputError,
    CancelButton,
    Link,
},
props:{
    opportunities: Object,
    customers: Array,
    users: Array,
},
methods:{
    store(){
      this.form.post(route('crm.email-monitors.store'), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se registró interacción de correo electrónico",
            type: "success",
          });
        },
      });
    },
    getCustomer() {
        const opportunity = this.opportunities.data.find(opportunity => opportunity.id === this.form.opportunity_id);
        this.form.customer_id = null;
        this.form.branch = null;
        this.form.contact_id = null;
        this.form.contact_email = null;
        this.form.customer_id = opportunity.customer.id;
      },
    getContactEmail() {
        this.form.branch = null; 
        this.form.contact_name = this.customers.data.find((item) => item.id == this.form.customer_id)?.contacts?.find( (item) => item.id == this.form.contact_id).name; 
        this.form.contact_email = this.customers.data.find((item) => item.id == this.form.customer_id)?.contacts?.find( (item) => item.id == this.form.contact_id).email;
      },
}
}
</script>

<style>

</style>