<template>
    <AppLayout title="PMS Inicio">
        <div class="px-9 pt-3 pb-8 lg:px-14 lg:pt-8">
            <h1>Inicio</h1>

            <!-- Estadistics -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6 font-bold">Proyectos</h2>
            <div class="lg:grid grid-cols-2 gap-5 mt-4 space-y-4 lg:space-y-0">
                <StackedColumn100Chart :options="projecsProgressChartOptions" title="Progreso de proyectos"
                    icon="<i class='fa-regular fa-flag ml-2'></i>" />
                <PieChart :options="taskStatusChartOptions" title="Estado de las Tareas"
                    icon='<i class="fa-solid fa-clipboard-list ml-2"></i>' />
                <DonutChart :options="projectGroupsChartOptions" title="Información de grupos de proyectos"
                    icon='<i class="fa-solid fa-circle-nodes ml-2"></i>' />
                <PolarAreaChart :options="tasksPrioritiesChartOptions" title="Estado de Prioridades"
                    icon='<i class="fa-solid fa-stopwatch ml-2"></i>' />
            </div>

            <!-- performance -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6 font-bold">Desempeño</h2>
            <div class="lg:grid grid-cols-2 gap-5 mt-4 space-y-4 lg:space-y-0">
                <PendentTasks
                    :tasks="[{ title: 'Alta IMSS de colaboradores nuevos', status: 'En curso', start_date: '12/09/2023', priority: 'Alta' }]" />
                <LateTasks
                    :tasks="[{ title: 'Alta IMSS de colaboradores nuevos', project: { project_name: 'Dalton Honda' }, late_days: 7, participants: [{ profile_photo_url: 'https://ui-avatars.com/api/?name=S+a&color=7F9CF5&background=EBF4FF', name: 'Miguel VR' }, { profile_photo_url: 'https://ui-avatars.com/api/?name=A+v&color=7F12F5&background=EB44FF', name: 'Angel VR' }] }]" />
                <StackedBars100Chart :options="myTasksProgressChartOptions" title="Progreso de mis tareas"
                    icon="<i class='fa-solid fa-bars-progress ml-2'></i>" />
                <StackedBars100Chart :options="myProyectsProgressChartOptions" title="Mis proyectos"
                    icon="<i class='fa-solid fa-chart-simple ml-2'></i>" />
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
                series: [44, 55, 13],
            },
            projecsProgressChartOptions: {
                colors: ['#BEBFC1', '#FD8827', '#45E142',],
                categories: ['Dalton Honda', 'Grupo Plascencia', 'Grupo Plascencia', 'BMW placas', 'BMW placas'],
                series: [{
                    name: 'Por hacer',
                    data: [5, 10, 3, 7, 2]
                }, {
                    name: 'En curso',
                    data: [3, 3, 2, 8, 1]
                }, {
                    name: 'Terminado',
                    data: [1, 7, 5, 5, 2]
                }],
            },
            projectGroupsChartOptions: {
                colors: ['#F2C940', '#FD8827', '#97989A', '#313131', '#FB2A2A'],
                labels: ['Construcción', 'Mantenimiento', 'Ventas', 'Costos', 'Proyectos internos'],
                series: [44, 19, 22, 41, 12],
            },
            tasksPrioritiesChartOptions: {
                colors: ['#87CEEB', '#F2C940', '#FB2A2A'],
                labels: ['Baja', 'Media', 'Alta'],
                series: [44, 19, 22],
            },
            myTasksProgressChartOptions: {
                colors: ['#BEBFC1', '#FD8827', '#45E142',],
                categories: ['Dalton Honda', 'Grupo Plascencia', 'Grupo Plascencia',],
                series: [{
                    name: 'Por hacer',
                    data: [2, 1, 3]
                }, {
                    name: 'En curso',
                    data: [1, 1, 1]
                }, {
                    name: 'Terminado',
                    data: [1, 0, 0]
                }],
            },
            myProyectsProgressChartOptions: {
                colors: ['#BEBFC1', '#FD8827', '#45E142',],
                categories: ['Dalton Honda', 'Grupo Plascencia', 'Grupo Plascencia',],
                series: [{
                    name: 'Por hacer',
                    data: [6, 11, 3]
                }, {
                    name: 'En curso',
                    data: [3, 3, 2]
                }, {
                    name: 'Terminado',
                    data: [0, 7, 5]
                }],
            },
        }
    },
    props: {
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

    },
    mounted() {

    }
}
</script>