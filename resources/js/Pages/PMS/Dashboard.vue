<template>
    <AppLayout title="PMS Inicio">
        <div class="px-9 pt-3 pb-8 lg:px-14 lg:pt-8">
            <h1>Inicio</h1>

            <!-- Estadistics -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6 font-bold">Tickets en progreso</h2>
            <div class="lg:grid grid-cols-2 gap-5 mt-4 space-y-4 lg:space-y-0">
                <StackedColumn100Chart :options="projecsProgressChartOptions" title="Progreso de tickets"
                    icon="<i class='fa-regular fa-flag ml-2'></i>" />
                <!-- <PieChart :options="taskStatusChartOptions" title="Estado de las Tareas"
                    icon='<i class="fa-solid fa-clipboard-list ml-2"></i>' /> -->
                <DonutChart :options="projectGroupsChartOptions" title="Información de grupos de tickets"
                    icon='<i class="fa-solid fa-circle-nodes ml-2"></i>' />
                <PolarAreaChart :options="tasksPrioritiesChartOptions" title="Estado de Prioridades"
                    icon='<i class="fa-solid fa-stopwatch ml-2"></i>' />
            </div>

            <!-- performance -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6 font-bold">Desempeño</h2>
            <div class="lg:grid grid-cols-2 gap-5 mt-4 space-y-4 lg:space-y-0">
                <!-- <PendentTasks
                    :tasks="[{ title: 'Alta IMSS de colaboradores nuevos', status: 'En curso', start_date: '12/09/2023', priority: 'Alta' }]" /> -->
                <LateTasks v-if="this.$page.props.auth.user.permissions.includes('Crear tickets')"
                    :tasks="[{ title: 'Alta IMSS de colaboradores nuevos', project: { project_name: 'Dalton Honda' }, late_days: 7, participants: [{ profile_photo_url: 'https://ui-avatars.com/api/?name=S+a&color=7F9CF5&background=EBF4FF', name: 'Miguel VR' }, { profile_photo_url: 'https://ui-avatars.com/api/?name=A+v&color=7F12F5&background=EB44FF', name: 'Angel VR' }] }]" />
                <!-- <StackedBars100Chart :options="myProyectsProgressChartOptions" title="Mis tickets"
                    icon="<i class='fa-solid fa-chart-simple ml-2'></i>" /> -->
                <StackedBars100Chart :options="myTasksProgressChartOptions" title="Progreso de mis tareas"
                    icon="<i class='fa-solid fa-bars-progress ml-2'></i>" />
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PieChart from '@/Components/MyComponents/Charts/PieChart.vue';
import PolarAreaChart from '@/Components/MyComponents/Charts/PolarAreaChart.vue';
import DonutChart from '@/Components/MyComponents/Charts/DonutChart.vue';
import StackedColumn100Chart from '@/Components/MyComponents/Charts/StackedColumn100Chart.vue';
import StackedBars100Chart from '@/Components/MyComponents/Charts/StackedBars100Chart.vue';
import GroupedBarChar from '@/Components/MyComponents/Charts/GroupedBarChar.vue';
import PendentTasks from '@/Components/MyComponents/PMS/PendentTasks.vue';
import LateTasks from '@/Components/MyComponents/PMS/LateTasks.vue';

export default {
    data() {
        return {
            taskStatusChartOptions: {
                colors: ['#BEBFC1', '#FD8827', '#45E142',],
                labels: ['Sin iniciar', 'En curso', 'Terminado'],
                series: this.totalTasksByStatus(),
            },
            projecsProgressChartOptions: {
                colors: ['#BEBFC1', '#FD8827', '#45E142',],
                categories: this.projects_progress.map(item => item.name),
                series: [{
                    name: 'Por hacer',
                    data: this.projects_progress.map(project => {
                        return project.tasks.filter(task => task.status === 'Por hacer').length;
                    }),
                }, {
                    name: 'En curso',
                    data: this.projects_progress.map(project => {
                        return project.tasks.filter(task => task.status === 'En curso').length;
                    }),
                }, {
                    name: 'Terminado',
                    data: this.projects_progress.map(project => {
                        return project.tasks.filter(task => task.status === 'Terminada').length;
                    }),
                }],
            },
            projectGroupsChartOptions: {
                colors: ['#F2C940', '#FD8827', '#97989A', '#313131', '#FB2A2A'],
                labels: this.project_groups.map(item => item.name),
                series: this.projectGroupCounts(),
            },
            tasksPrioritiesChartOptions: {
                colors: ['#87CEEB', '#F2C940', '#FB2A2A'],
                labels: ['Baja', 'Media', 'Alta'],
                series: this.totalTasksByPriority(),
            },
            myTasksProgressChartOptions: {
                colors: ['#BEBFC1', '#FD8827', '#45E142',],
                categories: this.projectsWithAssignedTasks().map(item => item.name),
                series: [{
                    name: 'Por hacer',
                    data: this.projectsWithAssignedTasks().map(project => {
                        return project.tasks.filter(task => task.status === 'Por hacer').length;
                    })
                }, {
                    name: 'En curso',
                    data: this.projectsWithAssignedTasks().map(project => {
                        return project.tasks.filter(task => task.status === 'En curso').length;
                    })
                }, {
                    name: 'Terminado',
                    data: this.projectsWithAssignedTasks().map(project => {
                        return project.tasks.filter(task => task.status === 'Terminada').length;
                    })
                }],
            },
            myProyectsProgressChartOptions: {
                colors: ['#BEBFC1', '#FD8827', '#45E142',],
                categories: this.my_projects.map(item => item.name),
                series: [{
                    name: 'Por hacer',
                    data: this.my_projects.map(project => {
                        return project.tasks.filter(task => task.status === 'Por hacer').length;
                    }),
                }, {
                    name: 'En curso',
                    data: this.my_projects.map(project => {
                        return project.tasks.filter(task => task.status === 'En curso').length;
                    }),
                }, {
                    name: 'Terminado',
                    data: this.my_projects.map(project => {
                        return project.tasks.filter(task => task.status === 'Terminada').length;
                    }),
                }],
            },
        }
    },
    props: {
        projects_progress: Array,
        project_groups: Array,
        my_projects: Array,
    },
    components: {
        AppLayout,
        PieChart,
        PolarAreaChart,
        DonutChart,
        StackedColumn100Chart,
        GroupedBarChar,
        PendentTasks,
        LateTasks,
        StackedBars100Chart,
    },
    methods: {
        projectsWithAssignedTasks() {
            return this.projects_progress.filter(project => {
                // Verifica si al menos una tarea en el ticket tiene a tu usuario por su ID
                return project.tasks.some(task => task.users.some(user => user.id === this.$page.props.auth.user.id));
            });
        },
        projectGroupCounts() {
            const counts = {};

            // Inicializa el objeto counts con cero para cada grupo
            this.project_groups.forEach(group => {
                counts[group.name] = 0;
            });

            // Realiza el conteo de tickets en cada grupo
            this.projects_progress.forEach(project => {
                const groupName = project.project_group.name;
                if (counts[groupName] !== undefined) {
                    counts[groupName]++;
                }
            });

            // Convierte el objeto counts en un array de objetos
            return Object.values(counts);
        },
        totalTasksByStatus() {
            const totalPorHacer = this.projects_progress.reduce((count, project) => {
                return count + project.tasks.filter(task => task.status === 'Por hacer').length;
            }, 0);

            const totalEnCurso = this.projects_progress.reduce((count, project) => {
                return count + project.tasks.filter(task => task.status === 'En curso').length;
            }, 0);

            const totalTerminada = this.projects_progress.reduce((count, project) => {
                return count + project.tasks.filter(task => task.status === 'Terminada').length;
            }, 0);

            return [totalPorHacer, totalEnCurso, totalTerminada];
        },
        totalTasksByPriority() {
            const totalLow = this.projects_progress.reduce((count, project) => {
                return count + project.tasks.filter(task => task.priority === 'Baja').length;
            }, 0);

            const totalMedium = this.projects_progress.reduce((count, project) => {
                return count + project.tasks.filter(task => task.priority === 'Media').length;
            }, 0);

            const totalHigh = this.projects_progress.reduce((count, project) => {
                return count + project.tasks.filter(task => task.priority === 'Alta').length;
            }, 0);

            return [totalLow, totalMedium, totalHigh];
        },
    },
    mounted() {

    }
}
</script>