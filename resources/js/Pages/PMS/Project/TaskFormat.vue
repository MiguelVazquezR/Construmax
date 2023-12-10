<template>
  <Head title="Formato de tarea" />
  <div class="shrink-0 flex items-center py-5 pl-10 border-b border-[#9A9A9A]">
    <figure>
      <ApplicationMark class="w-full" />
    </figure>
  </div>

  <!-- <div class="text-center">
    <div
      class="inline-flex items-center justify-center rounded-md bg-[#FEDBBD] py-1 px-4 text-primary text-sm mt-10 text-center"
    >
      <i class="fa-solid fa-circle-info mr-4"></i>
      <p>Esta tarea no puede comenzar ni finalizar fuera de las fechas programadas</p>
    </div>
  </div> -->

  <section class="w-2/3 mx-auto mt-4">
    <h1 class="text-2xl font-bold">{{ task.data.name }}</h1>
    <h2 class="font-bold mt-4 text-lg">Información de la tarea</h2>
    <div class="">
      <div class="flex flex-wrap space-y-2">
        <p class="w-1/3">Creado por</p>
        <p class="w-2/3">{{ task.data.user?.name }}</p>
        <p class="w-1/3">Estado actual</p>
        <p class="w-2/3">{{ task.data.status }}</p>
        <p class="w-1/3">Proyecto</p>
        <p class="w-2/3">{{ task.data.project?.name }}</p>
        <p class="w-1/3">Departamento</p>
        <p class="w-2/3">{{ task.data.department }}</p>
        <p class="w-1/3">Prioridad</p>
        <p class="w-2/3">{{ task.data.priority?.label }}</p>
        <p class="w-1/3">Fecha de inicio</p>
        <p class="w-2/3">{{ task.data.start_date }}</p>
        <p class="w-1/3">Fecha límite</p>
        <p class="w-2/3">{{ task.data.limit_date }}</p>
        <p class="w-1/3">Horario</p>
        <p class="w-2/3">{{ task.data.start_time + "-" + task.data.limit_time }}</p>
        <p class="w-1/3">Participantes</p>
        <p class="w-2/3" v-if="task.data.project && task.data.project.users.length > 0">
          {{
            task.data.project.users
              .map((participant, index) => index + 1 + ". " + participant.name)
              .join(", ")
          }}
        </p>
        <p class="w-1/3">Descripción</p>
        <p class="w-2/3">{{ task.data.description }}</p>
        <p class="w-1/3">Documetntos</p>
        <p class="w-2/3">
          <a :href="file?.original_url" target="_blank" v-for="file in task.data.media" :key="file"
            class="flex justify-between items-center cursor-pointer">
            <div class="flex space-x-7 items-center">
              <i :class="getFileTypeIcon(file.file_name)"></i>
              <span class="ml-2">{{ file.file_name }}</span>
            </div>
            <i class="fa-solid fa-download text-right text-sm text-[#9a9a9a]"></i>
          </a>
          <p v-if="!task.data.media?.length">No hay documentos</p>
        </p>
        <!-- <p>Comentarios</p> -->
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
  methods: {
    getFileTypeIcon(fileName) {
      // Asocia extensiones de archivo a iconos
      const fileExtension = fileName.split(".").pop().toLowerCase();
      switch (fileExtension) {
        case "pdf":
          return "fa-regular fa-file-pdf text-red-700";
        case "jpg":
        case "jpeg":
        case "png":
        case "gif":
          return "fa-regular fa-image text-blue-300";
        default:
          return "fa-regular fa-file-lines";
      }
    },
  },
};
</script>
