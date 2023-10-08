<template>
    <AppLayout title="Detalles de proyecto">
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
            <div class="md:w-1/3 mr-2">
                <el-select v-model="selectedProject" clearable filterable placeholder="Buscar proyecto" class="w-full"
                    no-data-text="No hay proyectos registrados" no-match-text="No se encontraron coincidencias">
                    <el-option v-for="item in projects.data" :key="item.id" :label="item.name" :value="item.id" />
                </el-select>
            </div>
            <div class="flex space-x-2">
                <PrimaryButton @click="$inertia.get(route('pms.projects.create'))">Nuevo proyecto</PrimaryButton>
            </div>
        </div>

        <!-- --------------project title--------------------------- -->
        <div class="text-center font-bold lg:text-lg mb-4 flex justify-center items-center mt-5 mx-2">
            <p>{{ currentProject?.name }}</p>
            <div class="flex items-center ml-5 lg:ml-24">
                <figure v-for="user in uniqueUsers.slice(0, maxUsersToShow)" :key="user.id">
                    <el-tooltip :content="user.name" placement="top">
                        <div v-if="$page.props.jetstream.managesProfilePhotos"
                            class="flex text-sm items-center rounded-full">
                            <img class="lg:h-12 h-10 w-10 lg:w-12 rounded-full object-cover" :src="user.profile_photo_url"
                                :alt="user.name" />
                        </div>
                    </el-tooltip>
                </figure>
                <el-tooltip v-if="remainingUsersCount > 0" placement="top">
                    <div
                        class="rounded-full lg:w-10 lg:h-10 w-8 h-8 bg-[#D9D9D9] flex items-center justify-center text-primary text-sm cursor-default">
                        +{{ remainingUsersCount }}
                    </div>
                    <template #content>
                        <div style="white-space: pre-line">{{ userNames.join("\n") }}</div>
                    </template>
                </el-tooltip>
            </div>
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

                <span class="text-gray-500">Creado por</span>
                <span>{{ currentProject?.user?.name }}</span>
                <span class="text-gray-500 my-2">Creado el</span>
                <span>{{ currentProject?.created_at }}</span>
                <span class="text-gray-500 my-2">Fecha de inicio</span>
                <span>{{ currentProject?.start_date }}</span>
                <span class="text-gray-500 my-2">Fecha final</span>
                <span>{{ currentProject?.limit_date }}</span>
                <div class="flex items-start my-2">
                    <span class="text-gray-500">Proyecto estricto</span>
                    <el-tooltip
                        content="Las tareas no pueden comenzar ni finalizar fuera de las fechas programadas de un proyecto  "
                        placement="top">
                        <i class="fa-regular fa-circle-question text-primary text-[10px] ml-1"></i>
                    </el-tooltip>
                </div>
                <span>
                    <i v-if="currentProject?.is_strict" class="fa-solid fa-check text-green-500"></i>
                    <i v-else class="fa-solid fa-minus"></i>
                </span>
                <span class="text-gray-500 my-2">Descripción</span>
                <span v-html="currentProject?.description"></span>
                <span class="text-gray-500 my-2">Proyecto interno</span>
                <span>
                    <i v-if="currentProject?.is_internal" class="fa-solid fa-check text-green-500"></i>
                    <i v-else class="fa-solid fa-minus"></i>
                </span>
                <span class="text-gray-500 my-2">Grupo</span>
                <span>{{ currentProject?.group.name }}</span>

                <p v-if="!currentProject?.is_internal" class="text-secondary col-span-2 mb-2 mt-8 font-bold">
                    Campos adicionales
                </p>

                <span v-if="!currentProject?.is_internal" class="text-gray-500">Cliente</span>
                <span v-if="!currentProject?.is_internal">{{
                    currentProject?.opportunity?.customer?.name
                }}</span>
                <span v-if="!currentProject?.is_internal" class="text-gray-500 my-2">Sucursal</span>
                <span v-if="!currentProject?.is_internal">{{
                    currentProject?.address
                }}</span>
                <span v-if="!currentProject?.is_internal" class="text-gray-500 my-2">OP</span>
                <span v-if="!currentProject?.is_internal">{{
                    'OP-' + currentProject?.opportunity_id
                }}</span>
            </div>

            <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">
                <p class="text-secondary col-span-2 mb-2 font-bold">Presupuestos</p>

                <span class="text-gray-500 mb-6">Moneda</span>
                <span class="mb-6">{{ currentProject?.currency }}</span>
                <span class="text-gray-500">Monto</span>
                <span>${{ currentProject?.budget?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                <span class="text-gray-500 my-2">Método de facturación</span>
                <span>{{ currentProject?.invoice_type }}</span>

                <p class="text-secondary col-span-2 mt-7 font-bold">Acceso al proyecto</p>

                <span class="text-gray-500 my-2">Acceso</span>
                <div class="flex items-start my-2">
                    <span>{{ currentProject?.type_access_project }}</span>
                    <el-tooltip
                        content="Los usuarios del portal pueden  ver el contenido y hacer comentarios, mientras que los usuarios del proyecto tendrán acceso directo."
                        placement="top">
                        <i class="fa-regular fa-circle-question text-primary text-[10px] ml-1"></i>
                    </el-tooltip>
                </div>

                <p class="text-secondary col-span-2 mb-2 font-bold">Usuarios</p>

                <ul>
                    <li v-for="user in uniqueUsers" :key="user.id" class="mt-1">{{ user.name }}</li>
                </ul>
                <ul>
                    <li v-for="user in uniqueUsers" :key="user.id" class="mt-1">
                        Depto. {{ user.employee_properties?.department ?? "Super admin" }}
                    </li>
                </ul>

                <p class="text-secondary col-span-2 mb-2 font-bold">Documentos adjuntos</p>
                <ul>
                    <li v-for="file in currentProject?.media" :key="file" class="mt-1">{{ file.file_name }}</li>
                </ul>
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
                            <ProjectTaskCard @updated-status="updateTask($event)" :taskComponent="task" :users="users"
                                :id="task.id" />
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
                <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false" v-model="inProgressTasksList"
                    :animation="300" item-key="id" tag="ul" group="tasks" id="process"
                    :class="(drag && !inProgressTasksList?.length) ? 'h-40' : ''">
                    <template #item="{ element: task }">
                        <li>
                            <ProjectTaskCard @updated-status="updateTask($event)" :taskComponent="task" :users="users" />
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
                            <ProjectTaskCard @updated-status="updateTask($event)" :taskComponent="task" :users="users" />
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
            <GanttDiagramMonth v-if="period === 'Mes'" :currentProject="currentProject" :currentDate="currentDate" />

            <GanttDiagramBimester v-if="period === 'Bimestre'" :currentProject="currentProject"
                :currentDate="currentDate" />

            <div class="text-right mr-9">
                <div class="border border-[#9A9A9A] rounded-md inline-flex justify-end mt-4">
                    <p :class="period == 'Mes' ? 'bg-primary text-white rounded-sm' : 'border-[#9A9A9A]'
                        " @click="period = 'Mes'" class="px-4 py-2 text-[#9A9A9A] cursor-pointer border-x">
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
    </AppLayout>
</template>
  
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ProjectTaskCard from "@/Components/MyComponents/ProjectTaskCard.vue";
import GanttDiagramMonth from "@/Components/MyComponents/GanttDiagramMonth.vue";
import GanttDiagramBimester from "@/Components/MyComponents/GanttDiagramBimester.vue";
import Tab from "@/Components/MyComponents/Tab.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import Modal from "@/Components/Modal.vue";
import Checkbox from "@/Components/Checkbox.vue";
import { Link } from "@inertiajs/vue3";
import draggable from 'vuedraggable';

export default {
    data() {
        return {
            selectedProyect: "",
            currentTab: 1,
            uniqueUsers: [],
            maxUsersToShow: 3,
            selectedProject: "",
            currentProject: null,
            period: "Mes", //period of time in cronograma table tab 3
            pendingTasksList: [],
            inProgressTasksList: [],
            finishedTasksList: [],
            drag: false,
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
    },
    props: {
        projects: Object,
        project: Object,
        users: Array,
    },
    methods: {
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
                const response = await axios.put(route('tasks.update-status', this.draggingTaskId), { status: status });

                if (response.status === 200) {
                    const taskIndex = this.currentProject.tasks.findIndex(item => item.id === this.draggingTaskId);
                    this.currentProject.tasks[taskIndex].status = status;
                }
            } catch (error) {
                console.log(error);
            }
        },
        updateTask(task) {
            const taskIndex = this.currentProject.tasks.findIndex(
                (item) => item.id === task.id
            );

            if (taskIndex !== -1) {
                this.currentProject.tasks[taskIndex] = task;
            }

            this.updateTasksLists();
        },
        pendingTasks() {
            this.pendingTasksList = this.currentProject?.tasks.filter((task) => task.status === "Por hacer");
        },
        inProgressTasks() {
            this.inProgressTasksList = this.currentProject?.tasks.filter((task) => task.status === "En curso");
        },
        finishedTasks() {
            this.finishedTasksList = this.currentProject?.tasks.filter((task) => task.status === "Terminada");
        },
        updateTasksLists() {
            this.pendingTasks();
            this.inProgressTasks();
            this.finishedTasks();
        },
    },
    computed: {
        // Calcular la lista de usuarios únicos
        uniqueUsers() {
            const uniqueUsers = [];
            const userIds = new Set();

            if (this.currentProject) {
                // Recorrer las tareas del proyecto
                for (const task of this.currentProject?.tasks) {
                    // Recorrer los usuarios de cada tarea
                    for (const user of task.participants) {
                        // Verificar si el usuario ya ha sido agregado
                        if (!userIds.has(user.id)) {
                            // Agregar el usuario a la lista de usuarios únicos
                            uniqueUsers.push(user);
                            this.uniqueUsers.push(user);
                            userIds.add(user.id);
                        }
                    }
                }
            }
            return uniqueUsers;
        },
        // Calcular la cantidad de usuarios restantes
        remainingUsersCount() {
            return Math.max(0, this.uniqueUsers?.length - this.maxUsersToShow);
        },

        // Calcular los nombres de los usuarios restantes
        userNames() {
            const remainingUsers = this.uniqueUsers.slice(this.maxUsersToShow);
            return remainingUsers.map((user) => user.name);
        },
    },
    watch: {
        selectedProject(newVal) {
            this.currentProject = this.projects.data.find((item) => item.id == newVal);
            this.uniqueUsers = [];
            this.updateTasksLists();

            // Verificar si hay tareas en el proyecto y si la primera tarea tiene una fecha de inicio
            if (this.currentProject && this.currentProject.tasks.length > 0) {
                const firstTask = this.currentProject.tasks[0];
                if (firstTask && firstTask.start_date) {
                    this.currentDate = new Date(firstTask.start_date);
                }
            }
        },
    },

    mounted() {
        this.selectedProject = this.project.data.id;
        this.currentProject = this.projects.data.find((item) => item.id == this.selectedProject);
    },
};
</script>
  