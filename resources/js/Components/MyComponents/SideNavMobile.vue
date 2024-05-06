<template>
    <i @click="options = null" v-if="options" class="fa-solid fa-chevron-left text-xs text-white ml-4 mb-3"></i>
    <template v-for="(menu, index) in options ?? menus" :key="index">
        <ResponsiveNavLink v-if="!menu?.options?.length && menu.show" :href="menu.route" :active="menu.active">
            <div class="flex justify-between items-center">
                <p class="space-x-2">
                    <span v-html="menu.icon"></span>
                    <span>{{ menu.label }}</span>
                </p>
                <i v-if="menu?.options?.length" class="fa-solid fa-chevron-right text-xs"></i>
            </div>
        </ResponsiveNavLink>
        <ResponsiveNavLink v-else-if="menu.show" as="button" @click="showOptions(index)" :active="menu.active">
            <div class="flex justify-between items-center">
                <p class="space-x-2">
                    <span v-html="menu.icon"></span>
                    <span>{{ menu.label }}</span>
                </p>
                <i v-if="menu?.options?.length" class="fa-solid fa-chevron-right text-xs"></i>
            </div>
        </ResponsiveNavLink>
    </template>
</template>

<script>

import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

export default {
    data() {
        return {
            options: null,
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
                            label: 'Presupuestos',
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
                            label: 'Tickets',
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
                    active: route().current('settings.*') || route().current('profile.*'),
                    options: [
                        {
                            label: 'Roles y permisos',
                            route: route('settings.role-permission.index'),
                            show: this.$page.props.auth.user.permissions.includes('Ver roles y permisos'),
                            active: route().current('settings.role-permission.index'),
                        },
                        {
                            label: 'Perfil',
                            route: route('profile.show'),
                            show: true,
                            active: route().current('profile.show'),
                        },
                    ],
                    show: true,
                },
            ],
        }
    },
    components: {
        ResponsiveNavLink,
    },
    methods: {
        showOptions(index) {
            this.options = this.menus[index].options;
        },
    }
}
</script>