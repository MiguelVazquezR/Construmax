<template>
   <AppLayout title="Agendar en calendario">
        <div class="flex justify-between items-center text-lg mx-8 mt-8">
        <b>Agendar en calendario</b>
        <Link :href="route('dashboard')">
        <p class="flex items-center text-sm text-primary">
            <i class="fa-solid fa-arrow-left-long mr-2"></i>
            <span>Regresar</span>
        </p>
        </Link>
        </div>
        <form @submit.prevent="store" class="mx-8 mt-3 grid grid-cols-2 gap-x-4 gap-y-2">
            <div class="flex justify-center items-center space-x-12 col-span-2">
                <div class="flex items-center mr-5">
                    <input v-model="form.type" value="Evento"
                    class="checked:bg-primary focus:text-primary focus:ring-primary border-black bg-transparent" type="radio"
                    name="task_type" />
                    <p class="ml-3">Evento</p>
                </div>
                <div class="flex items-center">
                    <input v-model="form.type" value="Tarea"
                    class="checked:bg-primary focus:text-primary focus:ring-primary border-black bg-transparent" type="radio"
                    name="task_type" />
                    <p class="ml-3">Tarea</p>
                </div>
                <InputError :message="form.errors.type" />
            </div>
            <!-- --------------- Evento -------------- -->
        <section class="space-y-3 col-span-2" v-if="form.type == 'Evento'">
          <div>
            <InputLabel value="Título del evento *" class="ml-2" />
            <input v-model="form.title" class="input" type="text" placeholder="Agregar título" />
            <InputError :message="form.errors.title" />
          </div>
          <div>
            <InputLabel value="Participante(s) *" class="ml-2" />
            <el-select class="w-full mt-2" v-model="form.participants" clearable filterable multiple
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
          <div class="flex items-center">
            <div class="mt-2 lg:mt-0">
              <InputLabel value="Fecha *" class="ml-2" />
              <el-date-picker v-model="form.start_date" type="date" placeholder="Fecha *" :disabled-date="disabledDate" />
              <InputError :message="form.errors.start_date" />
            </div>
            <label class="flex items-center mt-5 ml-4">
              <Checkbox v-model:checked="form.is_full_day" class="bg-transparent disabled:border-gray-400" />
              <span class="ml-2 text-xs">Todo el día</span>
            </label>
          </div>
          <div v-if="!form.is_full_day">
            <InputLabel value="Horario *" class="ml-2" />
            <el-time-select class="mr-5 mb-3 lg:mb-0" v-model="form.start_time" start="08:00" step="00:30" end="20:30"
              placeholder="Hora de inicio" :max-time="form.end_time" format="hh:mm A" />
            <el-time-select v-model="form.end_time" start="08:00" step="00:30" end="20:30"
              placeholder="Hora de término" :min-time="form.start_time" format="hh:mm A" />
            <!-- <InputError :message="form.errors.time" /> -->
          </div>
          <!-- <div>
            <label class="block">Repetir</label>
            <el-select
              class="w-full mt-2"
              v-model="form.repeater"
              clearable
              placeholder="Seleccionar"
              no-data-text="No hay opciones registradas"
              no-match-text="No se encontraron coincidencias"
            >
              <el-option
                v-for="repeater in repeaters"
                :key="repeater"
                :label="repeater"
                :value="repeater"
              />
            </el-select>
            <InputError :message="form.errors.repeater" />
          </div> -->
          <div>
            <InputLabel value="Ubicación *" class="ml-2" />
            <input v-model="form.location" class="input" type="text" placeholder="Agregar ubicación" />
            <InputError :message="form.errors.location" />
          </div>
          <div>
            <InputLabel value="Descripción *" class="ml-2" />
            <textarea v-model="form.description" class="input h-24"> </textarea>
            <InputError :message="form.errors.description" />
          </div>
          <!-- <div>
            <label class="block">Recordatorio</label>
            <div class="flex items-center">
              <el-select
                class="w-1/2 mt-2"
                v-model="form.reminder"
                clearable
                placeholder="Seleccionar"
                no-data-text="No hay opciones registradas"
                no-match-text="No se encontraron coincidencias"
              >
                <el-option
                  v-for="reminder in reminders"
                  :key="reminder"
                  :label="reminder"
                  :value="reminder"
                />
              </el-select>
              <p class="text-sm text-primary ml-7 cursor-pointer w-1/2">
                + Agregar recordatorio
              </p>
            </div>
            <InputError :message="form.errors.reminder" />
          </div> -->
        </section>

         <!-- ------------- Tarea .............. -->
        <section class="space-y-3 col-span-2" v-else>
          <div>
            <InputLabel value="Título del evento *" class="ml-2" />
            <input v-model="form.title" class="input" type="text" placeholder="Agregar título" />
            <InputError :message="form.errors.title" />
          </div>
          <div class="flex items-center mt-2">
            <div class="mt-2 lg:mt-0">
              <InputLabel value="Fecha *" class="ml-2" />
              <el-date-picker v-model="form.start_date" type="date" placeholder="Fecha *" :disabled-date="disabledDate" />
              <InputError :message="form.errors.start_date" />
            </div>
            <label class="flex items-center mt-5 ml-5">
              <Checkbox v-model:checked="form.is_full_day" class="bg-transparent disabled:border-gray-400" />
              <span class="ml-2 text-xs">Todo el día</span>
            </label>
          </div>
          <div v-if="!form.is_full_day">
            <InputLabel value="Horario" class="ml-2" />
            <el-time-select class="mr-5 mb-3 lg:mb-0" v-model="form.start_time" start="08:00" step="00:30" end="20:30"
              placeholder="Hora de inicio" :max-time="form.end_time" format="hh:mm A" />
            <el-time-select v-model="form.end_time" start="08:00" step="00:30" end="20:30"
              placeholder="Hora de término" :min-time="form.start_time" format="hh:mm A" />
            <!-- <div class="demo-range">
              <el-time-picker v-model="form.time" is-range range-separator="-" start-placeholder="Hora inicio"
                end-placeholder="Hora final" />
            </div>
            <InputError :message="form.errors.time" /> -->
          </div>
          <!-- <div>
            <label class="block">Repetir</label>
            <el-select class="w-full mt-2" v-model="form.repeater" clearable placeholder="Seleccionar"
              no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
              <el-option v-for="repeater in repeaters" :key="repeater" :label="repeater" :value="repeater" />
            </el-select>
            <InputError :message="form.errors.repeater" />
          </div> -->
          <div>
            <InputLabel value="descripción" class="ml-2" />
            <textarea v-model="form.description" class="input h-24"> </textarea>
            <InputError :message="form.errors.description" />
          </div>
          <!-- <div>
            <label class="block">Recordatorio</label>
            <div class="flex items-center">
              <el-select class="w-1/2 mt-2" v-model="form.reminder" clearable placeholder="Seleccionar"
                no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                <el-option v-for="reminder in reminders" :key="reminder" :label="reminder" :value="reminder" />
              </el-select>
              <p class="text-sm text-primary ml-7 cursor-pointer w-1/2">
                + Agregar recordatorio
              </p>
            </div>
            <InputError :message="form.errors.reminder" />
          </div> -->
        </section>

        <div class="flex md:text-left items-center mt-4">
          <PrimaryButton :disabled="form.processing"> Guardar </PrimaryButton>
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
import DialogModal from "@/Components/DialogModal.vue";
import CancelButton from "@/Components/CancelButton.vue";
import Tag from "@/Components/MyComponents/Tag.vue";
import Checkbox from "@/Components/Checkbox.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
data(){
    const form = useForm({
      type: "Evento",
      title: null,
      participants: [],
      repeater: "No se repite",
      location: null,
      description: null,
      reminder: null,
      is_full_day: false,
      // time: null,
      start_time: null,
      end_time: null,
      start_date: null,
    });
    return {
        form,
        repeaters: [
            "No se repite",
            "Todos los días",
            "Cada semana, el lunes",
            "Personalizado",
        ],
        reminders: [
            "5 minutos antes",
            "10 minutos antes",
            "1 hora antes",
            "1 día antes",
            "Personalizado",
        ],
    }
},
components:{
    AppLayout,
    PrimaryButton,
    SecondaryButton,
    InputLabel,
    InputError,
    DialogModal,
    CancelButton,
    Checkbox,
    Link,
    Tag
},
props:{
    users: Array,
},
methods:{
    store() {
      this.form.post(route("calendars.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Correcto",
            message: "Se ha agendado a tu calendario",
            type: "success",
          });
        },
      });
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() < today.getTime();
    },
}
}
</script>
