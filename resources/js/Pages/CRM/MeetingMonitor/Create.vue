<template>
  <AppLayout title="Agendar cita">
    <div class="flex justify-between items-center text-lg mx-8 mt-8">
      <b>Agendar cita</b>
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
        <el-select @change="getCustomer" class="w-full" v-model="form.opportunity_id" clearable filterable
          placeholder="Seleccione" no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
          <el-option v-for="opportunity in opportunities.data" :key="opportunity"
            :label="opportunity.folio + ' - ' + opportunity.name" :value="opportunity.id" />
        </el-select>
        <InputError :message="form.errors.opportunity_id" />
      </div>
      <h2 class="text-primary col-span-2 my-3">Datos del cliente</h2>
      <div class="w-full">
        <InputLabel value="Cliente *" class="ml-2" />
        <el-select disabled @change="cleanCustomerInfo" class="w-full" v-model="form.customer_id" clearable filterable
          placeholder="Seleccione" no-data-text="No hay clientes para mostrar"
          no-match-text="No se encontraron coincidencias">
          <el-option v-for="customer in customers.data" :key="customer" :label="customer.name" :value="customer.id" />
        </el-select>
        <InputError :message="form.errors.customer_id" />
      </div>
      <div class="w-full">
        <InputLabel value="Contacto *" class="ml-2" />
        <el-select @change="getContactPhone" class="w-full" v-model="form.contact_id" clearable filterable
          placeholder="Seleccione" no-data-text="No hay contactos para mostrar"
          no-match-text="No se encontraron coincidencias">
          <el-option v-for="contact in customers.data.find(
            (item) => item.id == form.customer_id
          )?.contacts" :key="contact" :label="contact.name" :value="contact.id" />
        </el-select>
        <InputError :message="form.errors.contact_id" />
      </div>
      <div class="w-full">
        <InputLabel value="Sucursal *" class="ml-2" />
        <el-select class="w-full" v-model="form.branch" clearable filterable placeholder="Seleccione"
          no-data-text="No hay sucursales para mostrar" no-match-text="No se encontraron coincidencias">
          <el-option v-for="branch in customers.data.find(
            (item) => item.id == form.customer_id
          )?.contacts?.find((item) => item.id == form.contact_id)?.additional.branches" :key="branch"
            :label="branch" :value="branch" />
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
          placeholder="Seleccione una hora" />
        <InputError :message="form.errors.time" />
      </div>
      <div class="w-full">
        <InputLabel value="Vía de cita *" class="ml-2" />
        <el-select class="w-full" v-model="form.meeting_via" clearable filterable placeholder="Seleccione"
          no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
          <el-option v-for="meeting_via in meetingVias" :key="meeting_via" :label="meeting_via" :value="meeting_via" />
        </el-select>
        <InputError :message="form.errors.meeting_via" />
      </div>
      <div class="w-full">
        <InputLabel value="Ubicación" class="ml-2" />
        <input v-model="form.location" class="input" type="text">
        <InputError :message="form.errors.location" />
      </div>
      <div class="col-span-2">
        <InputLabel value="Participante(s) *" class="ml-2" />
        <el-select class="w-full mt-2" v-model="form.participants" clearable multiple
          placeholder="Seleccionar participantes" no-data-text="No hay usuarios registrados"
          no-match-text="No se encontraron coincidencias">
          <el-option v-for="user in users" :key="user.id" :label="user.name" :value="user.id">
            <div v-if="$page.props.jetstream.managesProfilePhotos"
              class="flex text-sm rounded-full items-center mt-[3px]">
              <img class="h-7 w-7 rounded-full object-cover mr-4" :src="user.profile_photo_url" :alt="user.name" />
              <p>{{ user.name }}</p>
            </div>
          </el-option>
        </el-select>
        <InputError :message="form.errors.participants" />
      </div>
      <div class="mt-5 col-span-full">
        <InputLabel value="Descripción *" class="ml-2" />
        <RichText @content="updateDescription($event)" />
      </div>
      <div class="flex justify-end items-center col-span-2 mt-5">
        <PrimaryButton :disabled="form.processing">
          Agendar
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
  data() {
    const form = useForm({
      opportunity_id: null,
      time: null,
      customer_id: null,
      meeting_date: null,
      branch: null,
      contact_id: null,
      contact_name: null,
      contact_phone: null,
      meeting_via: null,
      location: null,
      description: null,
      participants: null,
    });
    return {
      form,
      meetingVias: [
        'Presencial',
        'Videoconferencia',
        'Llamada',
        'Otro',
      ],
    }
  },
  components: {
    AppLayout,
    PrimaryButton,
    InputLabel,
    InputError,
    CancelButton,
    RichText,
    Link,
  },
  props: {
    opportunities: Object,
    customers: Object,
    users: Array,
  },
  methods: {
    store() {
      this.form.post(route("crm.meeting-monitors.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Correcto",
            message: "Se ha agendado una nueva cita",
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
      this.form.contact_name = this.customers.data.find((item) => item.id == this.form.customer_id)?.contacts?.find((item) => item.id == this.form.contact_id).name;
      this.form.contact_phone = this.customers.data.find((item) => item.id == this.form.customer_id)?.contacts?.find((item) => item.id == this.form.contact_id).phone;
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0); // Establece la hora a las 00:00:00.000
      return time < today;
    },
    updateDescription(content) {
      this.form.description = content;
    },
  },
};
</script>
