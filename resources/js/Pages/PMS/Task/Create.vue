<template>
    <AppLayout title="Crear tarea">
        <div class="flex justify-between items-center text-lg mx-8 mt-8">
            <b>Nueva tarea</b>
            <Link :href="route('pms.projects.show', { project: parent_id, defaultTab: 2 })">
            <p class="flex items-center text-sm text-primary">
                <i class="fa-solid fa-arrow-left-long mr-2"></i>
                <span>Regresar</span>
            </p>
            </Link>
        </div>
        <!-- Form -->
        <form @submit.prevent="store">
            <div class="mx-8 mt-3 grid grid-cols-2 gap-x-4 gap-y-2 mb-6">
                <div>
                    <InputLabel value="Nombre de la tarea *" class="ml-2" />
                    <input v-model="form.name" class="input mt-1" type="text" placeholder="Escriba un nombre para la tarea">
                    <InputError :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel value="Proyecto *" class="ml-2" />
                    <el-select v-model="form.project_id" clearable placeholder="Seleccione" class="w-full mt-1"
                        no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="(item, index) in projects" :key="item.id" :label="item.name" :value="item.id" />
                    </el-select>
                    <InputError :message="form.errors.project_id" />
                </div>
                <div class="mt-5 col-span-full">
                    <InputLabel value="Descripción" class="ml-2" />
                    <RichText @content="updateDescription($event)" />
                </div>
                <div class="ml-2 mt-2 col-span-full flex">
                    <FileUploader @files-selected="this.form.media = $event" />
                </div>
                <div>
                    <InputLabel value="Departamento *" class="ml-2" />
                    <el-select v-model="form.department" clearable placeholder="Seleccione" class="w-full mt-1"
                        no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="(item, index) in departments" :key="item.id" :label="item" :value="item" />
                    </el-select>
                    <InputError :message="form.errors.department" />
                </div>
                <div>
                    <InputLabel value="Participantes *" class="ml-2" />
                    <el-select v-model="form.participants" clearable multiple placeholder="Seleccione" class="w-full mt-1"
                        no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="(item, index) in projects.find(item => item.id === form.project_id).users"
                            :key="item.id" :label="item.name" :value="item.id">
                            <div v-if="$page.props.jetstream.managesProfilePhotos"
                                class="flex text-sm rounded-full items-center mt-[3px]">
                                <img class="h-7 w-7 rounded-full object-cover mr-4" :src="item.profile_photo_url"
                                    :alt="item.name" />
                                <p>{{ item.name }}</p>
                            </div>
                        </el-option>
                    </el-select>
                    <InputError :message="form.errors.participants" />
                </div>
                <div class="col-span-full">
                    <InputLabel class="ml-2">
                        Prioridad * <i class="fa-solid fa-circle text-xs ml-2" :class="{
                            'text-white': !form.priority,
                            'text-[#87CEEB]': form.priority == 'Baja',
                            'text-[#F2C940]': form.priority == 'Media',
                            'text-[#FB2A2A]': form.priority == 'Alta'
                        }"></i>
                    </InputLabel>
                    <el-select v-model="form.priority" clearable placeholder="Seleccione" class="w-1/2 mt-1"
                        no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="(item, index) in priorities" :key="item.id" :label="item" :value="item" />
                    </el-select>
                    <InputError :message="form.errors.priority" />
                </div>
                <div>
                    <InputLabel value="Duración *" class="ml-2" />
                    <el-date-picker @change="handleDateRange" v-model="range" type="daterange" range-separator="A"
                        start-placeholder="Fecha de inicio" end-placeholder="Fecha límite" value-format="YYYY-MM-DD"
                        :disabled-date="disabledStartOrLimitDate" />
                    <InputError :message="form.errors.start_date" />
                    <InputError :message="form.errors.limit_date" />
                </div>
                <div v-if="canSelectTime" class="col-span-full ml-2 text-sm mt-3 flex">
                    <label class="flex items-center cursor-pointer flex-shrink-0 flex-grow-0">
                        <Checkbox v-model:checked="enabledTime" name="time" class="bg-transparent" />
                        <span class="mx-2">Horario específico</span>
                        <el-tooltip content="Puedes agregar un horario específico para la tarea, si es necesario."
                            placement="right">
                            <!-- <i class="fa-solid fa-circle-info text-primary text-xs ml-2"></i> -->
                            <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center">
                                <i class="fa-solid fa-info text-primary text-[7px]"></i>
                            </div>
                        </el-tooltip>
                    </label>
                </div>
                <div v-if="enabledTime" class="flex space-x-3 items-center">
                    <span>De</span>
                    <el-time-select v-model="form.start_time" :max-time="form.limit_time" placeholder="Hora de inicio"
                        start="08:00" step="00:15" end="18:00" format="hh:mm A" />
                    <span>a</span>
                    <el-time-select v-model="form.limit_time" :min-time="form.start_time" placeholder="Hora límite"
                        start="08:00" step="00:15" end="18:00" format="hh:mm A" />
                </div>
                <div class="flex items-center justify-end mt-5 col-span-full space-x-2">
                    <CancelButton @click="$inertia.get(route('pms.projects.show', parent_id))" type="button"
                        :disabled="form.processing">Cancelar</CancelButton>
                    <PrimaryButton :disabled="form.processing">Agregar</PrimaryButton>
                </div>
            </div>
        </form>

        <!-- -------------- Remainder Modal -------------- -->
        <Modal :show="remainderModal" @close="remainderModal = false">
            <div class="mx-7 my-4 space-y-4 relative">
                <div @click="remainderModal = false"
                    class="cursor-pointer w-5 h-5 rounded-full flex items-center justify-center absolute top-0 right-0">
                    <i class="fa-solid fa-xmark"></i>
                </div>
                <div class="flex items-center">
                    <i class="fa-regular fa-clock text-sm text-primary ml-5 cursor-pointer"></i>
                    <p class="text-sm text-primary ml-2 cursor-pointer">Elige un horario y fecha</p>
                </div>

                <div class="text-center">
                    <el-date-picker v-model="form.reminder" type="datetime" placeholder="Selecciona la fecha y hora" />
                </div>

                <div class="flex justify-end space-x-3 pt-5">
                    <PrimaryButton>Agregar</PrimaryButton>
                    <CancelButton @close="remainderModal = false">Cancelar</CancelButton>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import CancelButton from "@/Components/CancelButton.vue";
import Checkbox from "@/Components/Checkbox.vue";
import RichText from "@/Components/MyComponents/RichText.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import { isSameDay, parseISO } from "date-fns";

export default {
    data() {
        const form = useForm({
            project_id: parseInt(this.parent_id),
            name: null,
            description: null,
            department: null,
            participants: null,
            priority: null,
            reminder: null,
            start_date: '',
            limit_date: '',
            start_time: '',
            limit_time: '',
            media: [],
        });

        return {
            form,
            range: null,
            canSelectTime: false,
            remainderModal: false,
            selectedProject: null,
            enabledTime: false,
            mediaNames: [], // Agrega esta propiedad para almacenar los nombres de los archivos
            priorities: [
                'Baja',
                'Media',
                'Alta',
            ],
            departments: [
                'Contrucción',
                'Mantenimiento',
            ],
            reminders: [
                'Cada día',
                'Un dia antes de la fecha de vencimiento',
                'En la fecha de vencimiento',
            ],
        };
    },
    components: {
        AppLayout,
        PrimaryButton,
        Link,
        InputError,
        InputLabel,
        Modal,
        CancelButton,
        Checkbox,
        RichText,
        FileUploader,
    },
    props: {
        projects: Array,
        parent_id: Number,
        users: Array,
    },
    methods: {
        handleDateRange(range) {
            this.form.start_date = range[0];
            this.form.limit_date = range[1];

            const date1 = parseISO(range[0]);
            const date2 = parseISO(range[1]);

            // Compara si son del mismo día
            if (isSameDay(date1, date2)) {
                this.canSelectTime = true;
            } else {
                this.canSelectTime = false;
                this.enabledTime = false;
            }

        },
        updateDescription(content) {
            this.form.description = content;
        },
        store() {
            this.form.post(route("pms.tasks.store"), {
                // _method: 'post',
                onSuccess: () => {
                    this.$notify({
                        title: "Éxito",
                        message: "Se ha creado una nueva tarea",
                        type: "success",
                    });
                },
            });
        },
        // activateFileInput() {
        //     // Simula un clic en el campo de entrada de archivos al hacer clic en el párrafo
        //     document.getElementById('fileInput').click();
        // },
        handleFileUpload(event) {
            // Este método se llama cuando se selecciona un archivo en el input file
            const selectedFiles = event.target.files;
            const fileNames = [];

            // Obtén los nombres de los archivos seleccionados y guárdalos en form.mediaNames
            for (let i = 0; i < selectedFiles.length; i++) {
                fileNames.push(selectedFiles[i].name);
            }

            // Actualiza la propiedad form.media con los archivos seleccionados
            this.form.media = selectedFiles;
            // Actualiza la propiedad form.mediaNames con los nombres de los archivos
            this.form.mediaNames = fileNames;
        },
        getProject() {
            this.selectedProject = this.projects.find(item => item.id == this.form.project_id);
        },
        // Función para deshabilitar fechas fuera del rango [start_date, limit_date]
        disabledStartOrLimitDate(time) {
            if (this.selectedProject && this.selectedProject.is_strict) {
                const startTime = new Date(this.selectedProject.start_date).getTime();
                const limitTime = new Date(this.selectedProject.limit_date).getTime();
                return time.getTime() < startTime || time.getTime() > limitTime;
            }
            return false;
        },

    },
};
</script>