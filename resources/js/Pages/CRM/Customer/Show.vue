<template>
    <AppLayout title="Detalles de proyecto">
        <SkeletonLoading v-if="loading" />
        <div v-else>
            <div class="flex justify-between text-lg mx-2 lg:mx-14 mt-11">
                <span>Clientes</span>
                <Link :href="route('crm.customers.index')">
                <p class="flex items-center text-sm text-primary">
                    <i class="fa-solid fa-arrow-left-long mr-2"></i>
                    <span>Regresar</span>
                </p>
                </Link>
            </div>

            <div class="lg:flex justify-between mt-5 mx-2 lg:mx-14">
                <div class="md:w-full mr-2 flex items-center">
                    <el-select v-model="selectedCustomer" clearable filterable placeholder="Buscar proyecto"
                        class="w-full lg:w-1/2" no-data-text="No hay clientes registrados"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in customers" :key="item.id" :label="item.name" :value="item.id" />
                    </el-select>
                </div>
                <div class="flex items-center justify-end w-full lg:w-1/2 mt-3 lg:mt-0">
                    <el-dropdown v-if="this.$page.props.auth.user.permissions.includes('Crear clientes')" split-button
                        type="primary" @click="$inertia.get(route('crm.customers.create'))">
                        Nuevo cliente
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item @click="$inertia.get(route('crm.email-monitors.create'))">Enviar
                                    correo</el-dropdown-item>
                                <el-dropdown-item @click="$inertia.get(route('crm.payment-monitors.create'))">Registar
                                    pago</el-dropdown-item>
                                <el-dropdown-item @click="$inertia.get(route('crm.meeting-monitors.create'))">Agendar
                                    cita</el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                    <Link v-if="this.$page.props.auth.user.permissions.includes('Editar clientes')"
                        :href="route('crm.customers.edit', selectedCustomer)">
                    <i class="fa-solid fa-pencil ml-3 text-primary rounded-full p-2 bg-[#FEDBBD] cursor-pointer"></i>
                    </Link>
                </div>
            </div>


            <!-- --------------project title--------------------------- -->
            <div class="text-center font-bold lg:text-lg mb-4 flex justify-center items-center mt-5 mx-2">
                <p>{{ customer.data.name }}</p>
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
                <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-gray-[#cccccc] items-center self-start">

                    <span class="text-gray-500">ID</span>
                    <span>{{ customer.data.id }}</span>

                    <p class="text-secondary col-span-2 mb-2 font-bold mt-4">Datos fiscales</p>

                    <span class="text-gray-500 my-2">Razon social</span>
                    <span>{{ customer.data.name }}</span>
                    <span class="text-gray-500 my-2">RFC</span>
                    <span>{{ customer.data.rfc }}</span>
                    <p class="text-secondary col-span-full mt-7 font-bold">Etiquetas</p>
                    <div class="col-span-full flex space-x-3">
                        <Tag v-for="(item, index) in customer.data.tags" :key="index" :name="item.name"
                            :color="item.color" />
                    </div>
                    <p class="text-secondary col-span-full font-bold mt-7">Datos adicionales</p>

                    <span class="text-gray-500">Método de facturación</span>
                    <span>{{ customer.data.invoicing_method }}</span>
                    <span class="text-gray-500">Método de pago</span>
                    <span>{{ customer.data.payment_method }}</span>
                    <span class="text-gray-500">Uso de factura</span>
                    <span>{{ customer.data.invoice_use }}</span>
                </div>

                <div class="text-left px-10 py-4 border-l-2 border-gray-[#cccccc]">
                    <p class="text-secondary col-span-full mb-2 font-bold">Contacto (s)</p>
                    <div v-for="(item, index) in customer.data.contacts" :key="index"
                        class="bg-[#f2f2f2] rounded-[3px] p-5 mb-5">
                        <h2 class="font-bold text-xs mb-2">Contacto {{ (index + 1) }}</h2>
                        <div class="grid grid-cols-3 gap-x-3 gap-y-2 text-xs">
                            <span>Nombre</span>
                            <span class="col-span-2">{{ item.name }}</span>
                            <span>Correo electrónico</span>
                            <span class="col-span-2">{{ item.email }}</span>
                            <span>Teléfono</span>
                            <span class="col-span-2">{{ item.phone }}</span>
                            <span>Puesto</span>
                            <span class="col-span-2">{{ item.position }}</span>
                            <h2 class="font-bold col-span-full">Lista de sucursales</h2>
                            <ul v-for="(branch, index2) in item.additional.branches" :key="index2"
                                class="col-span-full grid grid-cols-3 gap-x-3 gap-y-2 text-xs">
                                <span>Sucursal {{ (index2 + 1) }}</span>
                                <span class="col-span-2">{{ branch }}</span>
                            </ul>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-2 col-span-2 mr-4 mt-16">
                        <el-tooltip content="Agendar reunión" placement="top">
                            <i @click="$inertia.get(route('crm.meeting-monitors.create'))"
                                class="fa-regular fa-calendar text-primary cursor-pointer text-lg px-3 border-r border-[#9a9a9a]"></i>
                        </el-tooltip>
                        <el-tooltip content="Registrar pago" placement="top">
                            <i @click="$inertia.get(route('crm.payment-monitors.create'))"
                                class="fa-solid fa-money-bill text-primary cursor-pointer text-lg px-3 border-r border-[#9a9a9a]"></i>
                        </el-tooltip>
                        <el-tooltip content="Enviar correo" placement="top">
                            <i @click="$inertia.get(route('crm.email-monitors.create'))"
                                class="fa-regular fa-envelope text-primary cursor-pointer text-lg px-3"></i>
                        </el-tooltip>
                    </div>

                </div>
            </div>
            <!-- ------------- info project ends 1 ------------- -->

            <!-- -------------Oportunidades starts 2 ------------- -->
            <div v-if="currentTab == 2" class="p-7 w-full mx-auto my-4">
                <div v-if="customer.data.opportunities.length">
                    <CustomerOpportunityTable :opportunities="customer.data.opportunities" />
                </div>
                <div class="flex flex-col text-center justify-center" v-else>
                    <p class="text-sm text-center">No hay oportunidades para mostrar</p>
                    <i class="fa-regular fa-folder-open text-9xl mt-16 text-gray-400/30"></i>
                </div>
            </div>
            <!-- ------------- Oportunidades ends 2 ------------- -->

            <!-- -------------Seguimiento integral starts 3 ------------- -->
            <div v-if="currentTab == 3" class="p-7 w-full mx-auto my-4">
                <div v-if="customer.data.clientMonitors?.length">
                    <CustomerClientMonitorTable :client_monitors="customer.data.clientMonitors" />
                </div>
                <div class="flex flex-col text-center justify-center" v-else>
                    <p class="text-sm text-center">No hay Seguimiento para mostrar</p>
                    <i class="fa-regular fa-folder-open text-9xl mt-16 text-gray-400/30"></i>
                </div>
            </div>
            <!-- ------------- Seguimiento integral ends 3 ------------- -->
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import CustomerOpportunityTable from "@/Components/MyComponents/CRM/CustomerOpportunityTable.vue";
import CustomerClientMonitorTable from "@/Components/MyComponents/CRM/CustomerClientMonitorTable.vue";
import Tab from "@/Components/MyComponents/Tab.vue";
import Tag from "@/Components/MyComponents/Tag.vue";
import SkeletonLoading from "@/Components/MyComponents/SkeletonLoading/Show.vue";
import { Link } from "@inertiajs/vue3";

export default {
    data() {
        return {
            selectedCustomer: this.customer.data.id,
            currentTab: 1,
            loading: false,
            tabs: [
                'Información general',
                'Oportunidades',
                'Seguimiento integral',
                // 'Historial',
            ],
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        SecondaryButton,
        CustomerOpportunityTable,
        CustomerClientMonitorTable,
        Tab,
        Link,
        Tag,
        SkeletonLoading,
    },
    props: {
        customer: Object,
        customers: Array,
        defaultTab: Number,
    },
    watch: {
        selectedCustomer(newVal) {
            this.loading = true;
            if (this.currentTab > 1) {
                this.$inertia.get(route('crm.customers.show', { customer: newVal, defaultTab: this.currentTab }));
            } else {
                this.$inertia.get(route('crm.customers.show', newVal));
            }
        },
    },
    mounted() {
        // tab to open
        if (this.defaultTab !== null) {
            this.currentTab = this.defaultTab;
        }
    },

}

</script>