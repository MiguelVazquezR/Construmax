<template>
    <!-- sidebar -->
    <div class="h-screen hidden md:block shadow-lg relative">
        <div class="bg-[#313131] h-full overflow-auto">
            <nav class="px-2 pt-32 text-white">
                <template v-for="(menu, index) in menus" :key="index">
                    <Accordion v-if="menu.options.length" :icon="menu.icon" :active="menu.active" :title="menu.label"
                        :id="index">
                        <div v-for="(option, index2) in menu.options" :key="index2">
                            <button @click="goToRoute(option.route)" v-if="option.show" :active="option.active"
                                :title="option.label"
                                class="w-full text-start pl-6 pr-2 mb-1 flex justify-between text-xs rounded-md py-1"
                                :class="option.active ? 'bg-[#FD8827] text-white' : 'hover:text-[#FD8827]'">
                                <p class="w-full truncate"> {{ option.label }}</p>
                            </button>
                        </div>
                    </Accordion>
                    <button v-else @click="goToRoute(menu.route)" v-if="menu.show" :active="menu.active"  :title="menu.label"
                        class="w-full text-start px-2 mb-1 flex justify-between text-xs rounded-md py-1"
                        :class="menu.active ? 'bg-[#FD8827] text-white' : 'hover:text-[#FD8827]'">
                        <p class="w-full truncate"><span v-html="menu.icon"></span> {{ menu.label }}</p>
                    </button>
                </template>
            </nav>
        </div>
    </div>
</template>

<script>
import Accordion from './Accordion.vue';

export default {
    data() {
        return {
            collapsedMenu: null,
            menus: [
                {
                    label: 'CRM',
                    icon: '<i class="fa-solid fa-chart-line mr-1"></i>',
                    active: route().current('crm.*'),
                    options: [
                        {
                            label: 'Inicio',
                            route: route('crm.dashboard'),
                            // show: this.$page.props.auth.user.permissions.includes('Inicio crm'),
                            active: route().current('crm.dashboard'),
                            show: true,
                        },
                        {
                            label: 'Oportunidades',
                            route: route('crm.opportunities.index'),
                            // show: this.$page.props.auth.user.permissions.includes('Ver oportunidades'),
                            active: route().current('crm.opportunities.index'),
                            show: true,
                        },
                        {
                            label: 'Clientes',
                            route: route('crm.customers.index'),
                            // show: this.$page.props.auth.user.permissions.includes('Ver clientes'),
                            active: route().current('crm.customers.index'),
                            show: true,
                        },
                    ],
                    // show: this.$page.props.auth.user.permissions.includes('Ver cotizaciones') ||
                    //     this.$page.props.auth.user.permissions.includes('Ver clientes')
                    show: true,
                },
                {
                    label: 'PMS',
                    icon: '<i class="fa-solid fa-check mr-1"></i>',
                    route: route('pms.projects.index'),
                    active: route().current('pms.*'),
                    options: [
                        {
                            label: 'Inicio',
                            route: route('pms.dashboard'),
                            // show: this.$page.props.auth.user.permissions.includes('Inicio pms'),
                            active: route().current('pms.dashboard'),
                            show: true,
                        },
                        {
                            label: 'Proyectos',
                            route: route('pms.projects.index'),
                            // show: this.$page.props.auth.user.permissions.includes('Ver proyectos'),
                            active: route().current('pms.projects.index'),
                            show: true,
                        },
                    ],
                    // show: this.$page.props.auth.user.permissions.includes('Ver proyectos')
                    show: true,
                },
                {
                    label: 'Usuarios',
                    icon: '<i class="fa-solid fa-user mr-1"></i>',
                    route: route('users.index'),
                    active: route().current('users.*'),
                    options: [],
                    // show: this.$page.props.auth.users.permissions.includes('Ver usuarios')
                    show: true,
                },
                {
                    label: 'Configuración',
                    icon: '<i class="fa-solid fa-gear mr-1"></i>',
                    route: route('settings.index'),
                    active: route().current('settings.*'),
                    options: [],
                    // show: this.$page.props.auth.user.permissions.includes('Ver configuraciones')
                    show: true,
                },
            ],
        }
    },
    components: {
        Accordion,
    },
    methods: {
        handleClickInMenu(index) {
            if (this.menus[index].options.length) {
                if (this.collapsedMenu === index) {
                    this.collapsedMenu = null;
                } else {
                    this.collapsedMenu = index;
                }
            } else {
                this.goToRoute(this.menus[index].route)
            }
        },
        goToRoute(route) {
            this.$inertia.get(route);
        }
    },
    mounted() {
    }
}
</script>