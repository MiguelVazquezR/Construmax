<template>
  <AppLayout title="Vista de tarea">
    <div class="flex justify-end mx-48 mt-8">
      <InfoMessage v-if="showStrictProjectMessage" @close="showStrictProjectMessage = false"
        message="Esta tarea no puede comenzar ni finalizar fuera de las fechas programadas" />
    </div>
    <div class="flex justify-between items-center text-lg mx-8 mt-2">
      <b>{{ task.data.name }}</b>
      <Link :href="route('pms.projects.show', { project: task.data.project?.id, defaultTab: 2 })
        ">
      <p class="flex items-center text-sm text-primary">
        <i class="fa-solid fa-arrow-left-long mr-2"></i>
        <span>Regresar</span>
      </p>
      </Link>
    </div>
    <div class="mx-8 mt-3 grid grid-cols-2 gap-x-4 gap-y-2">
      <div class="relative">
        <InputLabel class="ml-2">
          Estado actual
          <i :class="getColorStatus(form.status)" class="fa-solid fa-circle text-xs ml-1"></i>
        </InputLabel>
        <div class="flex items-center space-x-4">
          <el-select :disabled="task.data.is_paused || !authUserIsParticipant" class="lg:w-full" v-model="form.status"
            @change="updateStatus()" clearable filterable placeholder="Seleccionar estatus"
            no-data-text="No hay estatus registrados" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in statuses" :key="item" :label="item.label" :value="item.label">
              <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
              <span style="float: center; margin-left: 5px; font-size: 13px">{{
                item.label
              }}</span>
            </el-option>
          </el-select>
          <el-tooltip v-if="task.data.status == 'En curso' && (authUserIsParticipant || toBool(authUserPermissions[2]))"
            :content="task.data.is_paused ? 'Reanudar tarea' : 'Pausar tarea'" placement="top">
            <button type="button">
              <i @click.stop="handlePause" :class="task.data.is_paused ? 'fa-circle-play' : 'fa-circle-pause'"
                class="fa-regular text-primary text-xl cursor-pointer"></i>
            </button>
          </el-tooltip>
        </div>
        <InputError :message="form.errors.status" />
      </div>
      <el-dropdown v-if="toBool(authUserPermissions[2]) && !canEdit" split-button type="primary" @click="canEdit = true"
        class="custom-dropdown mt-5 ml-auto">
        <span>Editar</span>
        <template #dropdown>
          <el-dropdown-menu>
            <el-dropdown-item @click="copyToClipboard">Copiar link</el-dropdown-item>
          </el-dropdown-menu>
          <el-dropdown-menu v-if="toBool(authUserPermissions[3])">
            <el-dropdown-item @click="showConfirmModal = true">Eliminar tarea</el-dropdown-item>
          </el-dropdown-menu>
        </template>
      </el-dropdown>
      <el-dropdown v-if="toBool(authUserPermissions[2]) && canEdit" split-button type="primary" @click="updateTask"
        class="custom-dropdown mt-5 ml-auto">
        <span>Guardar cambios</span>
        <template #dropdown>
          <el-dropdown-menu>
            <el-dropdown-item @click="canEdit = false">Cancelar edición</el-dropdown-item>
          </el-dropdown-menu>
          <el-dropdown-menu>
            <el-dropdown-item @click="copyToClipboard">Copiar link</el-dropdown-item>
          </el-dropdown-menu>
          <el-dropdown-menu v-if="toBool(authUserPermissions[3])">
            <el-dropdown-item @click="showConfirmModal = true">Eliminar tarea</el-dropdown-item>
          </el-dropdown-menu>
        </template>
      </el-dropdown>
    </div>
    <!-- Form -->
    <div class="mx-8 mt-3 grid grid-cols-2 gap-x-4 gap-y-2">
      <h2 class="font-bold col-span-full mt-10">Información de la tarea</h2>
      <div>
        <InputLabel value="Proyecto" class="ml-2" />
        <input v-model="form.project_name" disabled class="input" type="text" />
        <InputError :message="form.errors.project_name" />
      </div>
      <div>
        <InputLabel value="Creado por" class="ml-2" />
        <input v-model="form.user" disabled class="input" type="text" />
        <InputError :message="form.errors.user" />
      </div>
      <div>
        <InputLabel value="Departamento" class="ml-2" />
        <el-select class="w-full" v-model="form.department" clearable filterable :disabled="!canEdit"
          placeholder="Seleccionar departamento" no-data-text="No hay departamentos registrados"
          no-match-text="No se encontraron coincidencias">
          <el-option v-for="item in departments" :key="item" :label="item" :value="item" />
        </el-select>
        <InputError :message="form.errors.department" />
      </div>
      <div>
        <InputLabel value="Participantes" class="ml-2" />
        <el-select class="w-full" v-model="form.participants" clearable filterable multiple :disabled="!canEdit"
          placeholder="Seleccionar participantes" no-data-text="No hay usuarios registrados"
          no-match-text="No se encontraron coincidencias">
          <el-option v-for="user in task.data.project.users" :key="user?.id" :label="user.name" :value="user?.id">
            <div v-if="$page.props.jetstream.managesProfilePhotos"
              class="flex text-sm rounded-full items-center mt-[3px]">
              <img class="h-7 w-7 rounded-full object-cover mr-4" :src="user.profile_photo_url" :alt="user.name" />
              <p>{{ user.name }}</p>
            </div>
          </el-option>
        </el-select>
        <InputError :message="form.errors.participants" />
      </div>
      <div class="col-span-full">
        <InputLabel value="Descripción" class="ml-2" />
        <RichText v-if="canEdit" @content="updateDescription($event)" :defaultValue="form.description" />
        <div v-else v-html="form.description" class="input cursor-not-allowed min-h-[70px] px-3 py-2"></div>
        <InputError :message="form.errors.description" />
      </div>
      <div class="col-span-full">
        <InputLabel class="ml-2">
          Prioridad
          <i :class="getColorPriority(form.priority)" class="fa-solid fa-circle text-xs ml-1"></i>
        </InputLabel>
        <el-select class="w-[calc(50%-8px)]" v-model="form.priority" clearable filterable
          placeholder="Seleccionar prioridad" :disabled="!canEdit" no-data-text="No hay registros"
          no-match-text="No se encontraron coincidencias">
          <el-option v-for="item in priorities" :key="item" :label="item.label" :value="item.label">
            <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
            <span style="float: center; margin-left: 5px; font-size: 13px">{{
              item.label
            }}</span>
          </el-option>
        </el-select>
        <InputError :message="form.errors.priority" />
      </div>
      <div>
        <InputLabel value="Duración *" class="ml-2" />
        <el-date-picker @change="handleDateRange" v-model="range" type="daterange" range-separator="A"
          start-placeholder="Fecha de inicio" end-placeholder="Fecha límite" value-format="YYYY-MM-DD"
          :disabled-date="disabledStartOrLimitDate" :disabled="!canEdit" />
        <InputError :message="form.errors.start_date" />
        <InputError :message="form.errors.limit_date" />
      </div>
      <!-- --------------------- currentTab -------------------- -->
      <section class="col-span-full mt-9">
        <div class="flex items-center">
          <p @click="currentTab = 1" :class="currentTab == 1 ? 'border-b-2 border-primary text-primary' : ''"
            class="h-8 p-1 cursor-pointer transition duration-300 ease-in-out text-xs md:text-base">
            Comentarios ({{ task.data.comments?.length }})
          </p>
          <div class="border-r-2 border-[#cccccc] h-7 ml-3"></div>
          <p @click="currentTab = 2" :class="currentTab == 2 ? 'border-b-2 border-primary text-primary' : ''"
            class="ml-3 h-8 p-1 cursor-pointer transition duration-300 ease-in-out text-xs md:text-base">
            Documentos ({{ task.data.media?.length }})
          </p>
        </div>
        <!-- -------------- Tab 1 comentarios starts ----------------->
        <div v-if="currentTab == 1" class="my-7 min-h-[170px]">
          <div>
            <figure class="flex space-x-2 mt-4" v-for="comment in task.data.comments" :key="comment">
              <div class="flex text-sm rounded-full w-10">
                <img class="h-8 w-8 rounded-full object-cover" :src="comment.user?.profile_photo_url"
                  :alt="comment.user?.name" />
              </div>
              <div class="text-sm w-full">
                <p class="font-bold">{{ comment.user?.name }}</p>
                <p v-html="comment.content"></p>
              </div>
            </figure>
            <div v-if="toBool(authUserPermissions[4])" class="flex space-x-1 mt-5">
              <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full w-10">
                <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url"
                  :alt="$page.props.auth.user.name" />
              </div>
              <RichText @submitComment="storeComment(task)" @content="updateComment($event)" ref="commentEditor"
                class="flex-1" withFooter :userList="task.data.project.users" :disabled="sendingComments" />
            </div>
          </div>
        </div>
        <!-- ---------------- tab 1 comentarios ends  -------------->

        <!-- -------------- Tab 2 documentos starts ----------------->
        <div v-if="currentTab == 2" class="my-7 min-h-[170px]">
          <a :href="file?.original_url" target="_blank" v-for="file in task.data.media" :key="file"
            class="flex justify-between items-center cursor-pointer">
            <div class="flex space-x-7 items-center">
              <i :class="getFileTypeIcon(file.file_name)"></i>
              <span class="ml-2">{{ file.file_name }}</span>
            </div>
            <i class="fa-solid fa-download text-right text-sm text-[#9a9a9a]"></i>
          </a>
        </div>
        <!-- ---------------- tab 2 documentos ends  -------------->

        <!-- -------------- Tab 3 historial starts ----------------->
        <div v-if="currentTab == 3" class="mt-7 min-h-[170px]"></div>
        <!-- ---------------- tab 3 historial ends  -------------->
      </section>
      <!-- {{ form }} -->
    </div>

    <DialogModal :show="showPauseModal" @close="showPauseModal = false">
      <template #title>
        Pausar tarea
      </template>
      <template #content>
        <div>
          <InputLabel value="Motivo de pausa" class="ml-2" />
          <RichText @content="updateReazon($event)" />
        </div>
      </template>
      <template #footer>
        <PrimaryButton @click="playPauseTask()" :disabled="!pausaReazon">
          Pausar
        </PrimaryButton>
      </template>
    </DialogModal>

    <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
      <template #title> Eliminar tarea </template>
      <template #content> ¿Continuar con la eliminación? </template>
      <template #footer>
        <div class="flex space-x-1">
          <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
          <PrimaryButton @click="deleteProjectTask">Eliminar</PrimaryButton>
        </div>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script>
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import CancelButton from "@/Components/CancelButton.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import RichText from "@/Components/MyComponents/RichText.vue";
import DialogModal from "@/Components/DialogModal.vue";

export default {
  data() {
    const form = useForm({
      status: this.task.data.status,
      name: null,
      project_name: this.task.data.project.name,
      user: this.task.data.user.name,
      department: this.task.data.department,
      participants: null,
      description: this.task.data.description,
      priority: this.task.data.priority.label,
      start_date: this.task.data.start_date_raw,
      limit_date: this.task.data.limit_date_raw,
      reminder: this.task.data.reminder,
      comment: null,
    });
    return {
      form,
      showConfirmModal: false,
      sendingComments: false,
      currentTab: 1,
      canEdit: false,
      showPauseModal: false,
      itemToShow: null,
      range: null,
      pausaReazonValidation: null,
      pausaReazon: null,
      statuses: [
        {
          label: "Por hacer",
          color: "text-gray3",
          color_border: "border-gray3",
        },
        {
          label: "En curso",
          color: "text-primary",
          color_border: "border-primary",
        },
        {
          label: "Terminada",
          color: "text-[#45E142]",
          color_border: "border-[#45E142]",
        },
      ],
      priorities: [
        {
          label: "Baja",
          color: "text-[#87CEEB]",
        },
        {
          label: "Media",
          color: "text-[#F2C940]",
        },
        {
          label: "Alta",
          color: "text-[#FB2A2A]",
        },
      ],
      departments: ["Construcción", "Mantenimiento"],
    };
  },
  components: {
    Modal,
    AppLayout,
    PrimaryButton,
    CancelButton,
    Link,
    InputError,
    RichText,
    InputLabel,
    ConfirmationModal,
    DialogModal,
  },
  props: {
    task: Object,
    users: Array,
  },
  computed: {
    authUser() {
      return this.task.data.project.users.find(
        (item) => item?.id == this.$page.props.auth.user?.id
      );
    },
    authUserIsParticipant() {
      return this.task.data.project.users?.some((user) => user?.id === this.authUser?.id);
    },
    authUserPermissions() {
      const permissions = this.authUser?.pivot.permissions;
      if (permissions) {
        return JSON.parse(permissions);
      } else {
        return [0, 0, 0, 0, 0];
      }
    },
  },
  methods: {
    handleDateRange(range) {
      this.form.start_date = range[0];
      this.form.limit_date = range[1];
    },
    handlePause() {
      if (!this.task.data.is_paused) {
        this.showPauseModal = true;
      } else {
        this.playPauseTask();
      }
    },
    copyToClipboard() {
      const textToCopy = window.location.origin + '/tasks-format/' + this.task.data?.id;

      // Create a temporary input element
      const input = document.createElement("input");
      input.value = textToCopy;
      document.body.appendChild(input);

      // Select the content of the input element
      input.select();

      // Try to copy the text to the clipboard
      document.execCommand("copy");

      // Remove the temporary input element
      document.body.removeChild(input);

      this.$notify({
        title: "Éxito",
        message: "Se ha copiado el link en el portapapeles",
        type: "success",
      });
    },
    getColorStatus(taskStatus) {
      if (taskStatus === "Por hacer") {
        return "text-gray3";
      } else if (taskStatus === "En curso") {
        return "text-primary";
      } else {
        return "text-[#45E142]";
      }
    },
    async updateStatus() {
      try {
        const response = await axios.put(
          route("pms.tasks.update-status", this.task.data?.id),
          {
            status: this.form.status,
          }
        );

        if (response.status === 200) {
          this.task.data.status = this.form.status;
          this.$notify({
            title: "Éxito",
            message: "Se ha cambiado el status",
            type: "success",
          });
        }
      } catch (error) {
        console.log(error);
      }
    },
    toBool(value) {
      if (value == 1 || value == true) return true;
      return false;
    },
    updateDescription(content) {
      this.form.description = content;
    },
    updateComment(content) {
      this.form.comment = content;
    },
    updateReazon(content) {
      this.pausaReazon = content;
    },
    async playPauseTask() {
      try {
        const response = await axios.put(route("pms.tasks.pause-play", this.task.data?.id), { reazon: this.pausaReazon });

        if (response.status === 200) {
          this.task.data = response.data.item;

          if (this.task.data.is_paused) {
            this.$notify({
              title: "Éxito",
              message: "Se ha pausado la tarea",
              type: "success",
            });
          } else {
            this.$notify({
              title: "Éxito",
              message: "Se ha reanudado la tarea",
              type: "success",
            });
          }
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.showPauseModal = false;
        this.pausaReazon = null;
      }
    },
    updateTask() {
      this.form.put(route("pms.tasks.update", this.task.data), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha actualizado la tarea",
            type: "success",
          });
        },
      });
    },
    async storeComment() {
      const editor = this.$refs.commentEditor;
      if (this.form.comment) {
        this.sendingComments = true;
        try {
          const response = await axios.post(
            route("pms.tasks.comment", this.task.data?.id),
            {
              comment: this.form.comment,
              mentions: editor.mentions,
            }
          );
          if (response.status === 200) {
            this.task.data.comments.push(response.data.item);
            this.form.comment = null;
            editor.clearContent();
          }
        } catch (error) {
          console.log(error);
        } finally {
          this.sendingComments = false;
        }
      }
    },
    getColorPriority(taskPriority) {
      if (taskPriority === "Baja") {
        return "text-[#87CEEB]";
      } else if (taskPriority === "Media") {
        return "text-[#F2C940]";
      } else {
        return "text-[#FB2A2A]";
      }
    },
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
    deleteProjectTask() {
      this.form.delete(route("pms.tasks.destroy", this.task.data), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha eliminado la tarea",
            type: "success",
          });
        },
      });
    },
    // Función para deshabilitar fechas fuera del rango [start_date, limit_date]
    disabledStartOrLimitDate(time) {
      const project = this.task.data.project;
      if (project.is_strict) {
        const startTime = new Date(project.start_date).getTime();
        const limitTime = new Date(project.limit_date).getTime();
        return time.getTime() < startTime || time.getTime() > limitTime;
      }
      return false;
    },
  },

  mounted() {
    this.showStrictProjectMessage = this.task.data.project.is_strict;
    this.form.participants = this.task.data.users.map((user) => user?.id);

    // inicializar fechas en range
    this.range = [this.task.data.start_date_raw, this.task.data.limit_date_raw];
  },
};
</script>
