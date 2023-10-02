<template>
    <!-- sidebar -->
    <div class="h-screen hidden md:block shadow-lg relative">
        <div class="bg-[#313131] h-full overflow-auto">
            <nav class="px-2 pt-32 text-white">
                <template v-for="(menu, index) in menus" :key="index">
                    <button @click="handleClickInMenu(index)" v-if="menu.show" :href="menu.route" :active="menu.active"
                        :dropdown="menu.dropdown"
                        class="w-full text-start px-2 mb-1 flex justify-between text-xs rounded-md py-1"
                        :class="menu.active ? 'font-bold text-[#FD8827]' : ''">
                        <p class="w-full truncate"><span v-html="menu.icon"></span> {{ menu.label }}</p>
                        <span v-if="menu.dropdown"><i class="fa-solid fa-angle-down"></i></span>
                    </button>
                    <transition name="collapse-transition">
                        <ul v-if="collapsedMenu == index">
                            <template v-for="(option, index2) in menu.options" :key="index2">
                                <button @click="goToRoute(option.route)" v-if="option.show" :href="option.route"
                                    :active="option.active" :dropdown="option.dropdown"
                                    class="w-full px-2 mb-1 flex justify-between text-xs rounded-md py-1"
                                    :class="option.active ? 'bg-[#FD8827] text-white' : ''">
                                    <p class="w-full truncate"> {{ option.label }}</p>
                                </button>
                            </template>
                        </ul>
                    </transition>
                </template>
            </nav>
        </div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            collapsedMenu: null,
            menus: [
                {
                    label: 'CRM',
                    icon: '<i class="fa-solid fa-chart-line mr-1"></i>',
                    active: true,//route().current('crm.*'),
                    options: [
                        {
                            label: 'Inicio',
                            route: route('crm.dashboard'),
                            // show: this.$page.props.auth.user.permissions.includes('Inicio crm'),
                            active: true,//route().current('crm.*'),
                            show: true,
                        },
                        {
                            label: 'Oportunidades',
                            route: route('crm.opportunities.index'),
                            // show: this.$page.props.auth.user.permissions.includes('Ver oportunidades'),
                            active: false,//route().current('crm.*'),
                            show: true,
                        },
                        {
                            label: 'Clientes',
                            route: route('crm.customers.index'),
                            // show: this.$page.props.auth.user.permissions.includes('Ver clientes'),
                            active: false,//route().current('crm.*'),
                            show: true,
                        },
                    ],
                    dropdown: true,
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
                            active: true,//route().current('crm.*'),
                            show: true,
                        },
                        {
                            label: 'Proyectos',
                            route: route('pms.projects.index'),
                            // show: this.$page.props.auth.user.permissions.includes('Ver proyectos'),
                            active: true,//route().current('crm.*'),
                            show: true,
                        },
                    ],
                    dropdown: true,
                    // show: this.$page.props.auth.user.permissions.includes('Ver proyectos')
                    show: true,
                },
                {
                    label: 'Usuarios',
                    icon: '<i class="fa-solid fa-user mr-1"></i>',
                    route: route('users.index'),
                    active: route().current('users.*'),
                    dropdown: false,
                    // show: this.$page.props.auth.users.permissions.includes('Ver usuarios')
                    show: true,
                },
                {
                    label: 'Configuración',
                    icon: '<i class="fa-solid fa-gear mr-1"></i>',
                    route: route('settings.index'),
                    active: route().current('settings.*'),
                    dropdown: false,
                    // show: this.$page.props.auth.user.permissions.includes('Ver configuraciones')
                    show: true,
                },
            ],
        }
    },
    components: {
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