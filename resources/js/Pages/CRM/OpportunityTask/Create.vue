<template>
<AppLayout title="Crear actividad">
    <div class="flex justify-between items-center text-lg mx-8 mt-8">
        <b>Crear actividad</b>
        <Link :href="route('crm.opportunities.show', opportunity_id)">
        <p class="flex items-center text-sm text-primary">
        <i class="fa-solid fa-arrow-left-long mr-2"></i>
        <span>Regresar</span>
        </p>
        </Link>
    </div>
    <form @submit.prevent="store" class="mx-8 mt-3 grid grid-cols-2 gap-x-4 gap-y-2">
        <div>
            <InputLabel value="Nombre de la actividad *" class="ml-2" />
            <input v-model="form.name" type="text" class="input mt-1" placeholder="Escribe el nombre de la actividad" required>
            <InputError :message="form.errors.name" />
        </div>
        <div>
            <label>Asignado a *</label>
            <el-select class="w-full" v-model="form.asigned_id" clearable filterable placeholder="Seleccionar usuario"
                no-data-text="No hay usuarios registrados" no-match-text="No se encontraron coincidencias">
                <el-option v-for="user in users" :key="user" :label="user.name" :value="user.id">
                <div v-if="$page.props.jetstream.managesProfilePhotos"
                    class="flex text-sm rounded-full items-center mt-[3px]">
                    <img class="h-7 w-7 rounded-full object-cover mr-4" :src="user.profile_photo_url" :alt="user.name" />
                    <p>{{ user.name }}</p>
                </div>
                </el-option>
            </el-select>
            <InputError :message="form.errors.asigned_id" />
        </div>
        <div>
            <InputLabel value="Fecha límite *" class="ml-2" />
            <el-date-picker v-model="form.limit_date" type="date" placeholder="Fecha límite *" format="YYYY/MM/DD"
            value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
            <InputError :message="form.errors.name" />
        </div>
        <div>
            <InputLabel value="Hora *" class="ml-2" />
            <input v-model="form.time" class="input" type="time">
            <InputError :message="form.errors.limit_date" />
        </div>
        <div class="relative">
            <i :class="getColorPriority(form.priority)"
              class="fa-solid fa-circle text-xs top-1 left-20 absolute z-30"></i>
            <InputLabel value="Prioridad *" class="ml-2" />
            <div class="flex items-center space-x-4">
              <el-select v-model="form.priority" clearable filterable placeholder="Seleccione"
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
        <div class="mt-5 col-span-full">
          <label>Descripción</label>
          <RichText @content="updateDescription($event)" />
        </div>
        <div class="ml-2 mt-2 col-span-full flex">
          <FileUploader @files-selected="this.form.media = $event" />
        </div>
        <div class="flex justify-end items-center col-span-2 space-x-2">
          <!-- <CancelButton type="button" @click.stop="$inertia.get(route('crm.opportunities.show', opportunity_id))">
            Cancelar
          </CancelButton> -->
          <PrimaryButton :disabled="form.processing">
            Agregar
          </PrimaryButton>
        </div>
    </form>
</AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import RichText from "@/Components/MyComponents/RichText.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import CancelButton from "@/Components/CancelButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
export default {
data(){
    const form = useForm({
      name: null,
      asigned_id: null,
      limit_date: null,
      time: null,
      priority: null,
      description: null,
      media: [],
    });
    return{
        form,
        priorities: [
        {
          label: "Baja",
          color: "text-[#87CEEB]",
        },
        {
          label: "Media",
          color: "text-orange-500",
        },
        {
          label: "Alta",
          color: "text-red-600",
        },
      ],
    }
},
components:{
    AppLayout,
    PrimaryButton,
    SecondaryButton,
    CancelButton,
    InputLabel,
    InputError,
    RichText,
    FileUploader,
    Link,
},
props:{
    users: Array,
    opportunity_id: Number,
},
methods:{
    store() {
      this.form.post(route("crm.opportunity-tasks.store", this.opportunity_id), {
        onSuccess: () => {
          this.$notify({
            title: "Correcto",
            message: "Se ha creado una nueva actividad",
            type: "success",
          });
        },
      });
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0); // Establece la hora a las 00:00:00.000
      return time < today;
    },
    updateDescription(content) {
      this.form.description = content;
    },
    getColorPriority(opportunityPriority) {
      if (opportunityPriority === "Baja") {
        return "text-[#87CEEB]";
      } else if (opportunityPriority === "Media") {
        return "text-[#D97705]";
      } else if (opportunityPriority === "Alta") {
        return "text-[#D90537]";
      } else {
        return "text-transparent";
      }
    },
}
}
</script>