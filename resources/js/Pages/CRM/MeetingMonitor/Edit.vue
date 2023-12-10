<template>
  <AppLayout title="Editar cita">
    <div class="flex justify-between items-center text-lg mx-8 mt-8">
      <b>Editar cita</b>
      <Link :href="route('crm.meeting-monitors.show', meeting_monitor.data.id)">
      <p class="flex items-center text-sm text-primary">
        <i class="fa-solid fa-arrow-left-long mr-2"></i>
        <span>Regresar</span>
      </p>
      </Link>
    </div>
    <form @submit.prevent="update" class="mx-8 mt-3 grid grid-cols-2 gap-x-4 gap-y-2">
        <div>
            <InputLabel value="Folio de oportunidad *" class="ml-2" />
            <el-select @change="getCustomer" class="w-full" v-model="form.opportunity_id"  filterable placeholder="Seleccione"
                no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
                <el-option v-for="opportunity in opportunities.data" :key="opportunity" :label="opportunity.folio + ' - ' + opportunity.name" :value="opportunity.id" />
            </el-select>
            <InputError :message="form.errors.opportunity_id" />
        </div>
        <h2 class="text-primary col-span-2 my-3">Datos del cliente</h2>
        <div class="w-full">
            <InputLabel value="Cliente *" class="ml-2" />
            <el-select disabled @change="cleanCustomerInfo" class="w-full" v-model="form.customer_id"  filterable
                placeholder="Seleccione" no-data-text="No hay clientes registrados"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="customer in customers.data" :key="customer" :label="customer.name" :value="customer.id" />
            </el-select>
            <InputError :message="form.errors.customer_id" />
        </div>
        <div class="w-full">
            <InputLabel value="Contacto *" class="ml-2" />
            <el-select @change="getContactPhone" class="w-full" v-model="form.contact_id"  filterable placeholder="Seleccione"
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
            <el-select class="w-full" v-model="form.branch"  filterable
            placeholder="Seleccione" no-data-text="No hay sucursales registradas"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="branch in customers.data.find(
                (item) => item.id == form.customer_id
            )?.contacts?.find( (item) => item.id == form.contact_id)?.additional.branches" :key="branch" :label="branch" :value="branch" />
            </el-select>
            <InputError :message="form.errors.branch" />
        </div>
        <h2 class="text-primary col-span-2 my-3">Detalles de la cita</h2>
        <div class="w-full">
            <InputLabel value="Fecha *" class="ml-2" />
            <el-date-picker class="w-full" v-model="form.meeting_date" type="date" placeholder="Fecha*" format="YYYY/MM/DD"
                value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
            <InputError :message="form.errors.meeting_date" />
        </div>
        <div class="w-full">
            <InputLabel value="Hora *" class="ml-2" />
            <el-time-select class="w-full" v-model="form.time" start="07:00" step="00:15" end="23:30"
                placeholder="Seleccione una hora" format="hh:mm A" />
            <InputError :message="form.errors.time" />
        </div>
        <div class="w-full">
            <InputLabel value="Vía de cita *" class="ml-2" />
            <el-select class="w-full" v-model="form.meeting_via"  filterable placeholder="Seleccione"
              no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
              <el-option v-for="meeting_via in meetingVias" :key="meeting_via" :label="meeting_via"
                :value="meeting_via" />
            </el-select>
            <InputError :message="form.errors.meeting_via" />
        </div>
        <div class="w-full">
            <InputLabel value="Ubicación *" class="ml-2" />
            <input v-model="form.location" class="input" type="text">
            <InputError :message="form.errors.location" />
        </div>
        <div class="col-span-2">
            <InputLabel value="Participante(s) *" class="ml-2" />
            <el-select class="w-full mt-2" v-model="form.participants"  filterable multiple
                placeholder="Seleccionar participantes" no-data-text="No hay usuarios registrados"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="user in users" :key="user.id" :label="user.name" :value="user.id" />
            </el-select>
            <InputError :message="form.errors.participants" />
        </div>
        <div class="mt-5 col-span-full">
          <InputLabel value="Descripción" class="ml-2" />
          <textarea class="input h-24" v-model="form.description" rows="3"></textarea>
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
import RichText from "@/Components/MyComponents/RichText.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
data(){
    const form = useForm({
        opportunity_id: this.meeting_monitor.data.opportunity?.id,
        time: this.meeting_monitor.data.time,
        customer_id: this.meeting_monitor.data.customer?.id,
        meeting_date: this.meeting_monitor.data.meeting_date_raw,
        branch: this.meeting_monitor.data.branch,
        contact_id: this.meeting_monitor.data.contact?.id,
        contact_name: this.meeting_monitor.data.contact_name,
        contact_phone: this.meeting_monitor.data.contact_phone,
        meeting_via: this.meeting_monitor.data.meeting_via,
        location: this.meeting_monitor.data.location,
        description: this.meeting_monitor.data.description,
        participants: this.meeting_monitor.data.participants,
    });
    return {
        form,
        // has_contact: false,
        // company_branch_obj: null,
        meetingVias: [
            'Presencial',
            'Videoconferencia',
            'Llamada',
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
    RichText,
    Link,
},
props:{
    meeting_monitor: Object,
    opportunities: Object,
    customers: Object,
    users: Array,
},
methods:{
    update() {
      this.form.put(route("crm.meeting-monitors.update", this.meeting_monitor.data.id), {
        onSuccess: () => {
          this.$notify({
            title: "Correcto",
            message: "Se ha actualizado tu cita",
            type: "success",
          });
        },
      });
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
      disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0); // Establece la hora a las 00:00:00.000
      return time < today;
    },
},
};
</script>
