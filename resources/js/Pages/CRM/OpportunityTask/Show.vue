<template>
  <AppLayout title="Vista de actividad">
    <div class="flex justify-between items-center text-lg mx-8 mt-2">
      <b>{{ opportunityTask.name }}</b>
      <Link :href="route('crm.opportunities.show', { opportunity: opportunityTask.opportunity?.id, defaultTab: 2 })
        ">
      <p class="flex items-center text-sm text-primary">
        <i class="fa-solid fa-arrow-left-long mr-2"></i>
        <span>Regresar</span>
      </p>
      </Link>
    </div>
    <form @submit.prevent="update" class="mx-7 my-4 space-y-4 relative">
      <div class="flex justify-between items-center">
        <h2 class="font-bold text-sm">Información de la actividad</h2>
        <ThirdButton type="button" v-if="toBool(authUserPermissions[2])" @click="canEdit = !canEdit">
          {{ canEdit ? 'Cancelar edición' : 'Editar' }}
        </ThirdButton>
      </div>

      <div class="grid grid-cols-2 gap-x-4 gap-y-2">
        <div>
          <InputLabel value="Oportunidad" class="ml-2" />
          <input :value="opportunityTask.opportunity.name" disabled class="input mt-1 cursor-not-allowed" type="text">
        </div>
        <div>
          <InputLabel value="Tipo de servicio" class="ml-2" />
          <input :value="opportunityTask.opportunity.service_type" disabled class="input mt-1 cursor-not-allowed"
            type="text">
        </div>
        <div>
          <InputLabel value="Creado por" class="ml-2" />
          <input :value="opportunityTask.user.name" disabled class="input mt-1 cursor-not-allowed" type="text">
        </div>
        <div class="mt-1">
          <InputLabel value="Asignado a *" class="ml-2" />
          <el-select v-model="form.asigned_id" filterable placeholder="Seleccionar usuario" :disabled="!canEdit"
            no-data-text="No hay usuarios registrados" no-match-text="No se encontraron coincidencias" class="w-full">
            <el-option v-for="user in opportunityTask.opportunity.users" :key="user" :label="user.name" :value="user.id">
              <div v-if="$page.props.jetstream.managesProfilePhotos"
                class="flex text-sm rounded-full items-center mt-[3px]">
                <img class="h-7 w-7 rounded-full object-cover mr-4" :src="user.profile_photo_url" :alt="user.name" />
                <p>{{ user.name }}</p>
              </div>
            </el-option>
          </el-select>
          <InputError :message="form.errors.asigned_id" />
        </div>
        <div class="mt-1">
          <InputLabel value="Fecha limite para completar tarea *" class="ml-2" />
          <el-date-picker v-model="form.limit_date" type="date" placeholder="Fecha limite *" :disabled-date="disabledDate"
            :disabled="!canEdit" format="YYYY-MM-DD" />
          <InputError :message="form.errors.limit_date" />
        </div>
        <div>
          <InputLabel value="Hora *" class="ml-2" />
          <el-time-select class="mr-5 mb-3 lg:mb-0" v-model="form.time" start="08:00" step="00:15" end="18:00"
            placeholder="Selecciona una hora" format="hh:mm A" :disabled="!canEdit" />
          <InputError :message="form.errors.time" />
        </div>

        <div class=" relative">
          <InputLabel value="Prioridad *" class="ml-2" />
          <el-select class="w-full" v-model="form.priority" filterable placeholder="Seleccionar prioridad"
            :disabled="!canEdit" no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in priorities" :key="item" :label="item.label" :value="item.label">
              <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
              <span style="float: center; margin-left: 5px; font-size: 13px">{{
                item.label
              }}</span>
            </el-option>
          </el-select>
          <i :class="getColorPriority(form.priority)"
            class="fa-solid fa-circle text-[10px] top-1 left-16 lg:left-20 absolute z-30"></i>
          <InputError :message="form.errors.priority" />
        </div>
        <div class="col-span-2">
          <InputLabel value="Descrpción" class="ml-2" />
          <RichText v-if="canEdit" @content="updateDescription($event)" :defaultValue="form.description" />
          <div v-else
            class="rounded-[10px] bg-transparent border border-[#BEBFC1] px-3 py-2 min-h-[100px] text-sm w-full">
            {{ form.description }}</div>
          <InputError :message="form.errors.description" />
        </div>

        <div v-if="opportunityTask?.media?.length" class="text-sm">
          <h2 class="font-bold py-5">Archivos adjuntos</h2>
          <a :href="file?.original_url" target="_blank" v-for="file in opportunityTask?.media" :key="file"
            class="flex justify-between items-center cursor-pointer">
            <div class="flex space-x-7 items-center">
              <i :class="getFileTypeIcon(file.file_name)"></i>
              <span class="ml-2">{{ file.file_name }}</span>
            </div>
            <i class="fa-solid fa-download text-right text-sm text-[#9a9a9a]"></i>
          </a>
        </div>


        <h2 class="font-bold py-4 col-span-2">Comentarios</h2>

        <!-- ------- Comments ------- -->
        <div class="mt-1 col-span-2">
          <section v-if="opportunityTask?.comments?.length > 0">
            <figure class="flex space-x-2 mt-4" v-for="comment in opportunityTask?.comments" :key="comment">
              <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full w-10">
                <img class="h-8 w-8 rounded-full object-cover" :src="comment.user?.profile_photo_url"
                  :alt="comment.user?.name" />
              </div>
              <div class="text-sm w-full">
                <p class="font-bold">{{ comment.user?.name }}</p>
                <p v-html="comment.content"></p>
              </div>
            </figure>
          </section>
          <p class="text-sm text-center text-gray-600" v-else>No hay comentarios</p>
          <div v-if="toBool(authUserPermissions[4]) && !opportunityTask?.finished_at && !canEdit"
            class="flex space-x-1 mt-5">
            <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full w-10">
              <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url"
                :alt="$page.props.auth.user.name" />
            </div>
            <RichText @submitComment="storeComment(taskComponentLocal)" @content="updateComment($event)"
              ref="commentEditor" class="flex-1" withFooter :userList="opportunityTask.opportunity.users" :disabled="sendingComments" />
          </div>
        </div>

        <div class="flex justify-start space-x-3 pt-5 pb-1">
          <template v-if="taskComponentLocal?.finished_at === null && !canEdit && authUserIsParticipant">
            <el-dropdown v-if="toBool(authUserPermissions[3])" split-button type="primary" @click="handleClick"
              class="custom-dropdown" :disabled="!toBool(authUserPermissions[2]) && !authUserIsParticipant">
              Marcar actividad como hecha
              <template #dropdown>
                <el-dropdown-menu>
                  <el-dropdown-item @click="showConfirmModal = true">Eliminar</el-dropdown-item>
                </el-dropdown-menu>
              </template>
            </el-dropdown>
            <PrimaryButton v-else type="button" @click="handleClick">Marcar actividad como hecha</PrimaryButton>
          </template>
          <template v-else-if="!authUserIsParticipant && toBool(authUserPermissions[2]) && !canEdit">
            <PrimaryButton type="button" @click="showConfirmModal = true">Eliminar</PrimaryButton>
          </template>
          <PrimaryButton v-if="canEdit" type="button" @click="update()">Guardar cambios</PrimaryButton>
          <CancelButton v-if="canEdit" @click="canEdit = false">
            Cancelar edición
          </CancelButton>
        </div>
      </div>
    </form>

    <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
      <template #title> Eliminar actividad </template>
      <template #content> ¿Continuar con la eliminación? </template>
      <template #footer>
        <div>
          <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
          <PrimaryButton @click="deleteOpportunityTask">Eliminar</PrimaryButton>
        </div>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>
  
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/CancelButton.vue";
import ThirdButton from "@/Components/ThirdButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { Link, useForm } from "@inertiajs/vue3";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import RichText from "@/Components/MyComponents/RichText.vue";
import axios from "axios";

export default {
  data() {
    const form = useForm({
      asigned_id: parseInt(this.opportunityTask.asigned.id),
      limit_date: this.opportunityTask.limit_date,
      time: this.opportunityTask.time,
      priority: this.opportunityTask.priority,
      reminder: this.opportunityTask.reminder,
      description: this.opportunityTask.description,
      comment: null,
    });

    return {
      form,
      taskComponentLocal: null,
      oportunity: this.opportunityTask.opportunity?.name,
      creator: this.opportunityTask.user?.name,
      taskInformationModal: false,
      showConfirmModal: false,
      sendingComments: false,
      canEdit: false,
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
    }
  },
  components: {
    PrimaryButton,
    CancelButton,
    ThirdButton,
    InputError,
    ConfirmationModal,
    Link,
    RichText,
    InputLabel,
    AppLayout,
  },
  props: {
    opportunityTask: Object,
  },
  methods: {
    updateComment(content) {
      this.form.comment = content;
    },
    async storeComment() {
      const editor = this.$refs.commentEditor;
      if (this.form.comment) {
        this.sendingComments = true;
        try {
          const response = await axios.post(route("crm.opportunity-tasks.comment", this.taskComponentLocal.id), {
            comment: this.form.comment,
            mentions: editor.mentions,
          });
          if (response.status === 200) {
            this.taskComponentLocal?.comments.push(response.data.item);
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
    toBool(value) {
      if (value == 1 || value == true) return true;
      return false;
    },
    updateDescription(content) {
      this.form.description = content;
    },
    async update() {
      this.form.put(route("crm.opportunity-tasks.update", this.opportunityTask), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha actualizado la actividad",
            type: "success",
          });
        },
      });
    },
    getPriorityStyles() {
      if (this.opportunityTask?.priority === 'Baja') {
        return 'text-[#87CEEB]';
      } else if (this.opportunityTask?.priority === 'Media') {
        return 'text-[#F2C940]';
      } else if (this.opportunityTask?.priority === 'Alta') {
        return 'text-[#FB2A2A]';
      }
    },
    disabledDate(time) {
      const today = new Date(); // Obtener la fecha de hoy
      return time.getTime() < today.getTime();
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
    async comment(opportunityTask) {
      try {
        const response = await axios.post(route("crm.opportunity-tasks.comment", opportunityTask.id), {
          comment: this.form.comment,
        });
        if (response.status === 200) {
          this.opportunityTask?.comments.push(response.data.item);
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.form.comment = null;
      }
    },
    handleClick() {
      this.$emit('task-done', this.opportunityTask.id);
      this.taskInformationModal = false;
    },
    deleteOpportunityTask() {
      this.$emit('delete-task', this.opportunityTask.id);
      this.taskInformationModal = false;
    },
    getFileTypeIcon(fileName) {
      // Asocia extensiones de archivo a iconos
      const fileExtension = fileName.split('.').pop().toLowerCase();
      switch (fileExtension) {
        case 'pdf':
          return 'fa-regular fa-file-pdf text-red-700';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
          return 'fa-regular fa-image text-blue-300';
        default:
          return 'fa-regular fa-file-lines';
      }
    },
  },
  computed: {
    authUser() {
      return this.opportunityTask.opportunity.users.find(
        (item) => item?.id == this.$page.props.auth.user?.id
      );
    },
    authUserIsParticipant() {
      return this.taskComponentLocal?.asigned.id === this.authUser?.id;
    },
    authUserPermissions() {
      const permissions = this.authUser?.pivot?.permissions;
      if (permissions) {
        return JSON.parse(permissions);
      } else {
        return [0, 0, 0, 0, 0];
      }
    }
  },
  mounted() {
    this.taskComponentLocal = this.opportunityTask;
  },

}
</script>
  