<template>
    <!-- sidebar -->
    <div class="h-screen hidden md:block shadow-lg relative">
        <div class="bg-[#313131] h-full overflow-auto">
            <nav class="px-2 pt-32 text-white">
                <template v-for="(menu, index) in menus" :key="index">
                    <div v-if="menu.show">
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
                        <button v-else @click="goToRoute(menu.route)" v-if="menu.show" :active="menu.active"
                            :title="menu.label"
                            class="w-full text-start px-2 mb-1 flex justify-between text-xs rounded-md py-1"
                            :class="menu.active ? 'bg-[#FD8827] text-white' : 'hover:text-[#FD8827]'">
                            <p class="w-full truncate"><span v-html="menu.icon"></span> {{ menu.label }}</p>
                        </button>
                    </div>
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
                            show: true,
                            active: route().current('crm.dashboard'),
                        },
                        {
                            label: 'Clientes',
                            route: route('crm.customers.index'),
                            show: this.$page.props.auth.user.permissions.includes('Ver clientes'),
                            active: route().current('crm.customers.*'),
                        },
                        {
                            label: 'Oportunidades',
                            route: route('crm.opportunities.index'),
                            show: this.$page.props.auth.user.permissions.includes('Ver oportunidades'),
                            active: route().current('crm.opportunities.*') || route().current('crm.opportunity-tasks.*'),
                        },
                        {
                            label: 'Seguimiento integral',
                            route: route('crm.client-monitors.index'),
                            show: this.$page.props.auth.user.permissions.includes('Ver seguimiento integral'),
                            active: route().current('crm.client-monitors.*') || route().current('crm.email-monitors.*') || route().current('crm.payment-monitors.*') || route().current('crm.meeting-monitors.*'),
                        },
                    ],
                    show: ['Ver clientes', 'Ver oportunidades', 'Ver seguimiento integral'].some(permission => this.$page.props.auth.user.permissions.includes(permission)),
                },
                {
                    label: 'Proyectos',
                    icon: '<i class="fa-solid fa-check mr-1"></i>',
                    route: route('pms.projects.index'),
                    active: route().current('pms.*'),
                    options: [
                        {
                            label: 'Inicio',
                            route: route('pms.dashboard'),
                            active: route().current('pms.dashboard'),
                            show: true,
                        },
                        {
                            label: 'Proyectos',
                            route: route('pms.projects.index'),
                            active: route().current('pms.projects.*') || route().current('pms.tasks.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver proyectos'),
                        },
                    ],
                    show: ['Ver proyectos'].some(permission => this.$page.props.auth.user.permissions.includes(permission)),
                },
                {
                    label: 'Usuarios',
                    icon: '<i class="fa-solid fa-user mr-1"></i>',
                    route: route('users.index'),
                    active: route().current('users.*'),
                    options: [],
                    show: this.$page.props.auth.user.permissions.includes('Ver usuarios')
                },
                {
                    label: 'Configuración',
                    icon: '<i class="fa-solid fa-gear mr-1"></i>',
                    route: route('settings.index'),
                    active: route().current('settings.*'),
                    options: [
                        {
                            label: 'Roles y permisos',
                            route: route('settings.role-permission.index'),
                            show: this.$page.props.auth.user.permissions.includes('Ver roles y permisos'),
                            active: route().current('settings.role-permission.index'),
                        },
                    ],
                    show: ['Ver roles y permisos'].some(permission => this.$page.props.auth.user.permissions.includes(permission)),
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