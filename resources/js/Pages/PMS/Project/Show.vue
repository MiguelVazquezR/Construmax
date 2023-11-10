<template>
    <AppLayout title="Detalles de proyecto">
        <SkeletonLoading v-if="loading" />
        <div v-else>
            <div class="flex justify-between text-lg mx-2 lg:mx-14 mt-11">
                <span>Proyectos</span>
                <Link :href="route('pms.projects.index')">
                <p class="flex items-center text-sm text-primary">
                    <i class="fa-solid fa-arrow-left-long mr-2"></i>
                    <span>Regresar</span>
                </p>
                </Link>
            </div>
            <div class="flex justify-between mt-5 mx-2 lg:mx-14">
                <div class="md:w-full mr-2 flex items-center">
                    <el-select v-model="selectedProject" clearable filterable placeholder="Buscar proyecto"
                        class="w-1/2 mr-4" no-data-text="No hay proyectos registrados"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in projects" :key="item.id" :label="item.name" :value="item.id" />
                    </el-select>
                    <div class="flex items-center">
                        <div v-for="(user, index) in project.data.users" :key="index">
                            <el-tooltip :content="user.name" placement="top">
                                <div v-if="index < 3" class="flex text-sm rounded-full w-8">
                                    <img class="h-7 w-7 rounded-full object-cover" :src="user.profile_photo_url"
                                        :alt="user.name" />
                                </div>
                            </el-tooltip>
                        </div>
                        <el-tooltip placement="top">
                            <template #content>
                                <li v-for="(user, index) in project.data.users.filter((item, index) => index >= 3)"
                                    :key="index" class="ml-2 text-xs">
                                    {{ user.name }}
                                </li>
                            </template>
                            <span v-if="project.data.users.length > 3" class="ml-1 text-primary text-sm">
                                +{{ (project.data.users.length - 3) }}
                            </span>
                        </el-tooltip>
                    </div>
                </div>
                <div v-if="currentTab == 1" class="flex space-x-2 w-full justify-end">
                    <PrimaryButton v-if="this.$page.props.auth.user.permissions.includes('Crear proyectos')"
                        @click="$inertia.get(route('pms.projects.create'))">Nuevo proyecto</PrimaryButton>
                    <SecondaryButton v-if="this.$page.props.auth.user.permissions.includes('Editar proyectos')"
                        @click="$inertia.get(route('pms.projects.edit', project.data.id ?? 1))"><i
                            class="fa-solid fa-pen"></i></SecondaryButton>
                </div>
                <div v-if="currentTab == 2 || currentTab == 3" class="flex space-x-2 w-full justify-end">
                    <PrimaryButton @click="$inertia.get(route('pms.tasks.create', { projectId: project.data.id ?? 1 }))">
                        Nueva
                        tarea</PrimaryButton>
                </div>
            </div>
            <!-- --------------project title--------------------------- -->
            <div class="text-center font-bold lg:text-lg mb-4 flex justify-center items-center mt-5 mx-2">
                <p>{{ project.data.name }}</p>
            </div>
            <!-- ------------- tabs section starts ------------- -->
            <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
                <div class="flex">
                    <Tab @click="currentTab = (index + 1)" :label="tab" :active="currentTab == (index + 1)"
                        v-for="(tab, index) in tabs" :key="index" />
                </div>
            </div>
            <!-- ------------- tabs section ends ------------- -->
            <!-- ------------- info project Starts 1 ------------- -->
            <div v-if="currentTab == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
                <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">
                    <p class="text-secondary col-span-2 mb-2 font-bold">Información del proyecto</p>

                    <span class="text-gray-500">Folio</span>
                    <span>{{ project.data.folio }}</span>
                    <span class="text-gray-500">Creado por</span>
                    <span>{{ project.data.user?.name }}</span>
                    <span class="text-gray-500 my-2">Creado el</span>
                    <span>{{ project.data.created_at }}</span>
                    <span class="text-gray-500 my-2">Responsable</span>
                    <span>{{ project.data.owner.name }}</span>
                    <span class="text-gray-500 my-2">Tipo de servicio</span>
                    <span>{{ project.data.service_type }}</span>
                    <span class="text-gray-500 my-2">Fecha de inicio</span>
                    <span>{{ project.data.start_date }}</span>
                    <span class="text-gray-500 my-2">Fecha final</span>
                    <span>{{ project.data.limit_date }}</span>
                    <div class="flex items-start my-2">
                        <span class="text-gray-500">Proyecto estricto</span>
                        <el-tooltip
                            content="Las tareas no pueden comenzar ni finalizar fuera de las fechas programadas de un proyecto  "
                            placement="top">
                            <i class="fa-regular fa-circle-question text-primary text-[10px] ml-1"></i>
                        </el-tooltip>
                    </div>
                    <span>
                        <i v-if="project.data.is_strict" class="fa-solid fa-check text-green-500"></i>
                        <i v-else class="fa-solid fa-minus"></i>
                    </span>
                    <span class="text-gray-500 my-2">Descripción</span>
                    <span v-html="project.data.description"></span>
                    <span class="text-gray-500 my-2">Proyecto interno</span>
                    <span>
                        <i v-if="project.data.is_internal" class="fa-solid fa-check text-green-500"></i>
                        <i v-else class="fa-solid fa-minus"></i>
                    </span>
                    <span class="text-gray-500 my-2">Grupo</span>
                    <span>{{ project.data.group.name }}</span>

                    <p v-if="!project.data.is_internal" class="text-secondary col-span-2 mb-2 mt-8 font-bold">
                        Campos adicionales
                    </p>

                    <span v-if="!project.data.is_internal" class="text-gray-500">Cliente</span>
                    <span v-if="!project.data.is_internal">{{
                        project.data.opportunity?.customer?.name
                    }}</span>
                    <span v-if="!project.data.is_internal" class="text-gray-500 my-2">Contacto</span>
                    <span v-if="!project.data.is_internal">{{
                        project.data.contact?.name
                    }}</span>
                    <span v-if="!project.data.is_internal" class="text-gray-500 my-2">Sucursal</span>
                    <span v-if="!project.data.is_internal">{{
                        project.data.address
                    }}</span>
                    <span v-if="!project.data.is_internal" class="text-gray-500 my-2">OP</span>
                    <Link :href="route('crm.opportunities.show', project.data.opportunity.id ?? 1)"
                        v-if="!project.data.is_internal" class="text-primary underline">
                    <span>{{ 'OP-' + project.data.opportunity.id }}</span>
                    </Link>
                </div>

                <div
                    class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center self-start">
                    <p class="text-secondary col-span-full mb-2 font-bold">Presupuestos</p>

                    <span class="text-gray-500">Moneda</span>
                    <span>{{ project.data.currency }}</span>
                    <span class="text-gray-500">Monto</span>
                    <span>${{ project.data.budget?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>

                    <p class="text-secondary col-span-full mt-7 font-bold">Etiquetas</p>
                    <div class="col-span-full flex space-x-3">
                        <Tag v-for="(item, index) in project.data.tags" :key="index" :name="item.name"
                            :color="item.color" />
                    </div>
                    <p v-if="project.data.media.length" class="text-secondary col-span-full font-bold mt-7">Documentos
                        adjuntos</p>
                    <li v-for="file in project.data.media" :key="file"
                        class="flex items-center justify-between col-span-full">
                        <a :href="file.original_url" target="_blank" class="flex items-center">
                            <i :class="getFileTypeIcon(file.file_name)"></i>
                            <span class="ml-2">{{ file.file_name }}</span>
                        </a>
                    </li>
                </div>
            </div>
            <!-- ------------- info project ends 1 ------------- -->
            <!-- ------------- Tasks Starts 2 ------------- -->
            <div v-if="currentTab == 2" class="md:grid grid-cols-3 text-left p-4 text-sm">
                <!-- -- Por hacer -- -->
                <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:pr-7">
                    <h2 class="font-bold mb-10">
                        POR HACER <span class="font-normal ml-7">{{ pendingTasksList?.length }}</span>
                    </h2>
                    <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false" v-model="pendingTasksList"
                        :animation="300" item-key="id" tag="ul" group="tasks" id="pendent"
                        :class="(drag && !pendingTasksList?.length) ? 'h-40' : ''">
                        <template #item="{ element: task }">
                            <li>
                                <Link :href="route('pms.tasks.show', task.id)">
                                <ProjectTaskCard @updated-status="updateTask($event)" :taskComponent="task" :users="users"
                                    :id="task.id" />
                                </Link>
                            </li>
                        </template>
                    </draggable>
                    <div class="text-center" v-if="!pendingTasksList?.length">
                        <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
                        <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
                    </div>
                </div>

                <!-- -- En curso -- -->
                <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-7">
                    <h2 class="font-bold mb-10">
                        EN CURSO <span class="font-normal ml-7">{{ inProgressTasksList?.length }}</span>
                    </h2>
                    <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false"
                        v-model="inProgressTasksList" :animation="300" item-key="id" tag="ul" group="tasks" id="process"
                        :class="(drag && !inProgressTasksList?.length) ? 'h-40' : ''">
                        <template #item="{ element: task }">
                            <li>
                                <Link :href="route('pms.tasks.show', task.id)">
                                <ProjectTaskCard @updated-status="updateTask($event)" :taskComponent="task"
                                    :users="users" />
                                </Link>
                            </li>
                        </template>
                    </draggable>
                    <div class="text-center" v-if="!inProgressTasksList?.length">
                        <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
                        <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
                    </div>
                </div>

                <!-- -- Terminado -- -->
                <div class="h-auto lg:px-7">
                    <h2 class="font-bold mb-10">
                        TERMINADA <span class="font-normal ml-7">{{ finishedTasksList?.length }}</span>
                    </h2>
                    <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false" v-model="finishedTasksList"
                        :animation="300" item-key="id" tag="ul" group="tasks" id="finished"
                        :class="(drag && !finishedTasksList?.length) ? 'h-40' : ''">
                        <template #item="{ element: task }">
                            <li>
                                <Link :href="route('pms.tasks.show', task.id)">
                                <ProjectTaskCard @updated-status="updateTask($event)" :taskComponent="task"
                                    :users="users" />
                                </Link>
                            </li>
                        </template>
                    </draggable>
                    <div class="text-center" v-if="!finishedTasksList?.length">
                        <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
                        <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
                    </div>
                </div>
            </div>
            <!-- ------------- Tasks ends 2 ------------- -->
            <!-- ------------- Cronograma Starts 3 ------------- -->
            <div v-if="currentTab == 3" class="text-left text-sm items-center">
                <GanttDiagramMonth v-if="period === 'Mes'" :currentProject="project.data" :currentDate="currentDate" />

                <GanttDiagramBimester v-if="period === 'Bimestre'" :currentProject="project.data"
                    :currentDate="currentDate" />

                <div class="text-right mr-9">
                    <div class="border border-[#9A9A9A] rounded-md inline-flex justify-end mt-4">
                        <p :class="period == 'Mes' ? 'bg-primary text-white rounded-sm' : 'border-[#9A9A9A]'
                            " @click="period = 'Mes'" class="px-4 py-2 text-[#9A9A9A] cursor-pointer">
                            Mes
                        </p>
                        <p :class="period == 'Bimestre'
                            ? 'bg-primary text-white rounded-sm'
                            : 'border-[#9A9A9A]'
                            " @click="period = 'Bimestre'"
                            class="px-4 py-2 text-[#9A9A9A] cursor-pointer border-x border-transparent">
                            Bimestre
                        </p>
                    </div>
                </div>
            </div>
            <!-- ------------- Cronograma ends 3 ------------- -->
        </div>
    </AppLayout>
</template>
  
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import ProjectTaskCard from "@/Components/MyComponents/ProjectTaskCard.vue";
import GanttDiagramMonth from "@/Components/MyComponents/PMS/GanttDiagramMonth.vue";
import GanttDiagramBimester from "@/Components/MyComponents/PMS/GanttDiagramBimester.vue";
import Tab from "@/Components/MyComponents/Tab.vue";
import Tag from "@/Components/MyComponents/Tag.vue";
import SkeletonLoading from "@/Components/MyComponents/SkeletonLoading/Show.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import Modal from "@/Components/Modal.vue";
import Checkbox from "@/Components/Checkbox.vue";
import { Link } from "@inertiajs/vue3";
import draggable from 'vuedraggable';
import axios from "axios";

export default {
    data() {
        return {
            selectedProject: this.project.data.id,
            currentTab: 1,
            maxUsersToShow: 3,
            period: "Mes", //period of time in cronograma table tab 3
            pendingTasksList: [],
            inProgressTasksList: [],
            finishedTasksList: [],
            drag: false,
            loading: false,
            draggingTaskId: null,
            tabs: [
                'Información del proyecto',
                'Tareas',
                'Cronograma',
            ]
        };
    },
    components: {
        AppLayout,
        PrimaryButton,
        SecondaryButton,
        ProjectTaskCard,
        Link,
        Dropdown,
        DropdownLink,
        Modal,
        Checkbox,
        draggable,
        GanttDiagramMonth,
        GanttDiagramBimester,
        Tab,
        Tag,
        SkeletonLoading,
    },
    props: {
        projects: Array,
        project: Object,
        users: Array,
        defaultTab: Number,
    },
    methods: {
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
        handleStartDrag(evt) {
            this.draggingTaskId = evt.item.__draggable_context.element.id;
            this.drag = true;
        },
        handleAddDrag(evt) {
            let status = 'Terminada';
            if (evt.to.id === 'pendent') {
                status = 'Por hacer';
            } else if (evt.to.id === 'process') {
                status = 'En curso';
            }

            this.updateTaskStatus(status);
            this.drag = false;
        },
        async updateTaskStatus(status) {
            try {
                const response = await axios.put(route('pms.tasks.update-status', this.draggingTaskId), { status: status });

                if (response.status === 200) {
                    const taskIndex = this.project.data.tasks.findIndex(item => item.id === this.draggingTaskId);
                    this.project.data.tasks[taskIndex].status = status;
                }
            } catch (error) {
                console.log(error);
            }
        },
        updateTask(task) {
            const taskIndex = this.project.data.tasks.findIndex(
                (item) => item.id === task.id
            );

            if (taskIndex !== -1) {
                this.project.data.tasks[taskIndex] = task;
            }

            this.updateTasksLists();
        },
        pendingTasks() {
            this.pendingTasksList = this.project.data.tasks.filter((task) => task.status === "Por hacer");
        },
        inProgressTasks() {
            this.inProgressTasksList = this.project.data.tasks.filter((task) => task.status === "En curso");
        },
        finishedTasks() {
            this.finishedTasksList = this.project.data.tasks.filter((task) => task.status === "Terminada");
        },
        updateTasksLists() {
            this.pendingTasks();
            this.inProgressTasks();
            this.finishedTasks();

            // Verificar si hay tareas en el proyecto y si la primera tarea tiene una fecha de inicio
            if (this.project.data && this.project.data.tasks.length > 0) {
                const firstTask = this.project.data.tasks[0];
                if (firstTask && firstTask.start_date) {
                    this.currentDate = new Date(firstTask.start_date);
                }
            }
        },
    },
    watch: {
        selectedProject(newVal) {
            this.loading = true;
            if (this.currentTab > 1) {
                this.$inertia.get(route('pms.projects.show', { project: newVal, defaultTab: this.currentTab }));
            } else {
                this.$inertia.get(route('pms.projects.show', newVal));
            }
        },
    },
    mounted() {
        // tab to open
        if (this.defaultTab !== null) {
            this.currentTab = this.defaultTab;
        }
        this.updateTasksLists();
    },
};
</script>
  