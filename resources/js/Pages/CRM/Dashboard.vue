<template>
    <AppLayout title="CRM inicio">
        <div class="px-9 pt-3 pb-8 lg:px-14 lg:pt-8">
            <h1>Inicio</h1>

            <!-- customers -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6 font-bold">Clientes</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4 space-y-4 lg:space-y-0">
                <CustomerDates />
            </div>

            <!-- Estadistics -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6 font-bold">Estadísticas</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4 space-y-4 lg:space-y-0">
                <!-- <PieChart :options="monthSalesChartOptions" :title="'Ventas de ' + currentMonth + ' según tipo de servicio'"
                    icon='<i class="fa-solid fa-hand-holding-dollar ml-2"></i>' /> -->
                <BarChart :options="monthSalesChartOptions" :title="'Ventas de ' + currentMonth + ' según tipo de servicio'"
                    icon='<i class="fa-solid fa-hand-holding-dollar ml-2"></i>' />
                <BarChart :options="yearComparisonChartOptions" title="Ventas año en curso vs anterior"
                    icon='<i class="fa-solid fa-scale-unbalanced-flip ml-2"></i>' />
            </div>

            <!-- sales -->
            <!-- <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6 font-bold">Seguimiento de ventas</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4 space-y-4 lg:space-y-0">
                <FunnelChart :options="funnelSalesChartOptions" title="Embudo de ventas"
                    icon='<i class="fa-solid fa-filter-circle-dollar ml-2"></i>' />
                <RecentSales
                    :sales="[{ close_date: '24 ago 2023', customer_name: 'BOSH', total_sold: '$19,458.5', seller: 'Evelin Montero' }]" />
            </div> -->

            <!-- performance -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6 font-bold">Desempeño</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4 space-y-4 lg:space-y-0">
                <GroupedBarChar :options="totalSalesBySeller" title="Ventas totales por vendedor"
                    icon='<i class="fa-solid fa-bullseye ml-2"></i>' />
            </div>
        </div>
    </AppLayout>
</template>

<script>
import CustomerDates from '@/Components/MyComponents/CRM/CustomerDates.vue';
import CancelButton from '@/Components/CancelButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PieChart from '@/Components/MyComponents/Charts/PieChart.vue';
import BarChart from '@/Components/MyComponents/Charts/BarChart.vue';
import GroupedBarChar from '@/Components/MyComponents/Charts/GroupedBarChar.vue';

import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        return {
            currentMonth: null,
            monthSalesChartOptions: {
                colors: ['#FEDBBD'],
                categories: this.getMonthSales().map(item => item.type),
                series: [{
                    name: 'venta',
                    data: this.getMonthSales().map(item => item.amount.toFixed(2)) //to fixed para 2 decimales
                }]
            },
            yearComparisonChartOptions: {
                colors: ['#BEBFC1', '#FD8827'],
                categories: this.getMonthlySum(this.last_year_opportunities).map(item => item.month),
                series: [{
                    name: 'Año pasado',
                    data: this.getMonthlySum(this.last_year_opportunities).map(item => item.amount.toFixed(2))
                },
                {
                    name: 'Año en curso',
                    data: this.getMonthlySum(this.current_year_opportunities).map(item => item.amount.toFixed(2))
                }],
            },
            totalSalesBySeller: {
                colors: ['#45E142', '#FD8827'],
                categories: this.sellers_total_amount.map(item => item.seller),
                series: [{
                    name: 'Ventas',
                    data: this.sellers_total_amount.map(item => (item.amount / 1000).toFixed(2))
                }],
                labelPrefix: '$ ',
                labelSufix: 'K',
            },
        }
    },
    props: {
        month_opportunities: Array,
        current_year_opportunities: Array,
        last_year_opportunities: Array,
        sellers_total_amount: Array,
    },
    components: {
        AppLayout,
        CancelButton,
        CustomerDates,
        PieChart,
        BarChart,
        GroupedBarChar,
    },
    methods: {
        getMonthSales() {
            // Utiliza un objeto para almacenar la información de cada tipo de servicio
            const serviceTypeMap = {};

            // Itera sobre las oportunidades y agrega cada service_type y amount al objeto
            this.month_opportunities.forEach(opportunity => {
                const { service_type, amount } = opportunity;

                // Si el service_type no está en el objeto, crea una entrada con el monto actual
                if (!serviceTypeMap[service_type]) {
                    serviceTypeMap[service_type] = { type: service_type, amount: 0 };
                }

                // Suma el amount al monto existente
                serviceTypeMap[service_type].amount += amount / 1000;
            });

            // Convierte el objeto a un array de objetos
            return Object.values(serviceTypeMap);
        },
        getMonthlySum(opportunities) {
            // Inicializa un objeto para almacenar la suma del monto por mes
            const monthlySumMap = [];

            // Crea un array con los nombres cortos de los meses
            const monthsOrder = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

            // Inicializa el objeto con todas las entradas para cada mes
            monthsOrder.forEach(month => {
                monthlySumMap[month] = { month, amount: 0 };
            });

            // Itera sobre las oportunidades y agrega el monto al mes correspondiente
            opportunities.forEach(opportunity => {
                const created_at = new Date(opportunity.created_at);
                const month = created_at.toLocaleString('default', { month: 'short' }).replace(/\b\w/g, match => match.toUpperCase());

                // Verifica si la propiedad 'amount' existe en el objeto antes de sumar
                if (monthlySumMap[month] && opportunity.amount !== null && typeof opportunity.amount == 'number') {
                    // Suma el amount al monto existente
                    monthlySumMap[month].amount += opportunity.amount / 1000;
                }
            });
            
            // console.log('array:',monthlySumMap);
            // Convierte el objeto a un array de objetos
            const monthlySumArray = Object.values(monthlySumMap);

            // Ordena el array por el nombre del mes
            monthlySumArray.sort((a, b) => monthsOrder.indexOf(a.month) - monthsOrder.indexOf(b.month));

            return monthlySumArray;
        }
    },
    mounted() {
        // Obtiene la fecha actual
        const currentDate = new Date();
        // Formatea el mes actual en español
        this.currentMonth = format(currentDate, 'MMMM', { locale: es });
    },
}
</script>
