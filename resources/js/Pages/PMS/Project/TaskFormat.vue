<template>
  <Head title="Formato de tarea" />
  <div class="shrink-0 flex items-center py-5 pl-10 border-b border-[#9A9A9A]">
    <figure>
      <ApplicationMark class="w-full" />
    </figure>
  </div>

  <div class="text-center">
    <div
      class="inline-flex items-center justify-center rounded-md bg-[#FEDBBD] py-1 px-4 text-primary text-sm mt-10 text-center"
    >
      <i class="fa-solid fa-circle-info mr-4"></i>
      <p>Esta tarea no puede comenzar ni finalizar fuera de las fechas programadas</p>
    </div>
  </div>

  <section class="w-2/3 mx-auto mt-4">
    <h1 class="text-2xl font-bold">{{ task.data.name }}</h1>
    <h2 class="font-bold mt-4 text-lg">Información de la tarea</h2>
    <div class="grid grid-cols-3">
      <div class="space-y-3 mt-2">
        <p>Creado por</p>
        <p>Estado actual</p>
        <p>Proyecto</p>
        <p>Departamento</p>
        <p>Prioridad</p>
        <p>Fecha de inicio</p>
        <p>Fecha límite</p>
        <p>Horario</p>
        <p>Participantes</p>
        <p>Descripción</p>
        <p>Documentos</p>
        <p>Comentarios</p>
      </div>
      <div class="space-y-3 col-span-2">
        <p>{{ task.data.user?.name }}</p>
        <p>{{ task.data.status }}</p>
        <p>{{ task.data.project?.name }}</p>
        <p>{{ task.data.department }}</p>
        <p>{{ task.data.priority?.label }}</p>
        <p>{{ task.data.start_date }}</p>
        <p>{{ task.data.limit_date }}</p>
        <p>{{ task.data.start_time + "-" + task.data.limit_time }}</p>
        <p v-if="task.data.project && task.data.project.users.length > 0">
          {{
            task.data.project.users
              .map((participant, index) => index + 1 + ". " + participant.name)
              .join(", ")
          }}
        </p>
        <p>{{ task.data.description }}</p>
      </div>
    </div>
  </section>
</template>

<script>
import ApplicationMark from "@/Components/ApplicationMark.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { Link, Head } from "@inertiajs/vue3";

export default {
  data() {
    return {};
  },
  components: {
    ApplicationMark,
    PrimaryButton,
    InputLabel,
    Link,
    Head,
  },
  props: {
    task: Object,
  },
  methods: {},
};
</script>

<style></style>
