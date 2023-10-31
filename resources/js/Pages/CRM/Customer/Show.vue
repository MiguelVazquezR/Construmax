<template>
    <AppLayout title="Detalles de proyecto">
        <div class="flex justify-between text-lg mx-2 lg:mx-14 mt-11">
            <span>Clientes</span>
            <Link :href="route('crm.customers.index')">
            <p class="flex items-center text-sm text-primary">
                <i class="fa-solid fa-arrow-left-long mr-2"></i>
                <span>Regresar</span>
            </p>
            </Link>
        </div>

        <div class="flex justify-between mt-5 mx-2 lg:mx-14">
            <div class="md:w-full mr-2 flex items-center">
                <el-select v-model="selectedCustomer" clearable filterable placeholder="Buscar proyecto" class="w-1/2 mr-4"
                    no-data-text="No hay clientes registrados" no-match-text="No se encontraron coincidencias">
                    <el-option v-for="item in customers.data" :key="item.id" :label="item.name" :value="item.id" />
                </el-select>
            </div>
            <div class="flex justify-end mr-3 w-1/2">
                <el-dropdown v-if="this.$page.props.auth.user.permissions.includes('Crear clientes')" split-button type="primary" @click="$inertia.get(route('crm.customers.create'))">
                    Nuevo cliente
                    <template #dropdown>
                    <el-dropdown-menu>
                        <el-dropdown-item @click="update">Enviar correo</el-dropdown-item>
                        <el-dropdown-item @click="showConfirmModal= true">Registar pago</el-dropdown-item>
                        <el-dropdown-item @click="showConfirmModal= true">Agendar cita</el-dropdown-item>
                    </el-dropdown-menu>
                    </template>
                 </el-dropdown>
                <Link v-if="this.$page.props.auth.user.permissions.includes('Editar clientes')" :href="route('crm.customers.edit', selectedCustomer)">
                    <i class="fa-solid fa-pencil ml-3 text-primary rounded-full p-2 bg-[#FEDBBD] cursor-pointer"></i>
                </Link>
            </div>
        </div>
        

        <!-- --------------project title--------------------------- -->
        <div class="text-center font-bold lg:text-lg mb-4 flex justify-center items-center mt-5 mx-2">
            <p>{{ currentCustomer?.name }}</p>
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
                
                <span class="text-gray-500">ID</span>
                <span>{{ currentCustomer?.id }}</span>

                <p class="text-secondary col-span-2 mb-2 font-bold mt-4">Datos fiscales</p>

                <span class="text-gray-500 my-2">Razon social</span>
                <span>{{ currentCustomer?.name }}</span>
                <span class="text-gray-500 my-2">RFC</span>
                <span>{{ currentCustomer?.rfc }}</span>
                <span class="text-gray-500 my-2">Sucursales</span>
                <span class="col-start-2" v-for="item in currentCustomer?.branches" :key="item">{{ item }}</span >
                <p class="text-secondary col-span-full mt-7 font-bold">Etiquetas</p>
                <div class="col-span-full flex space-x-3">
                    <Tag v-for="(item, index) in currentCustomer?.tags" :key="index" :name="item.name" :color="item.color" />
                </div>
            </div>

            <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center self-start">
                <p class="text-secondary col-span-full mb-2 font-bold">Datos del contacto</p>

                <span class="text-gray-500">Nombre</span>
                <span>{{ currentCustomer?.contact_name }}</span>
                <span class="text-gray-500">Correo electrónico</span>
                <span>{{ currentCustomer?.contact_email ?? '-' }}</span>
                <span class="text-gray-500 my-2">Teléfono</span>
                <span>{{ currentCustomer?.contact_phone }}</span>

                <p class="text-secondary col-span-full font-bold mt-7">Datos adicionales</p>

                <span class="text-gray-500">Método de facturación</span>
                <span>{{ currentCustomer?.invoicing_method }}</span>
                <span class="text-gray-500">Método de pago</span>
                <span>{{ currentCustomer?.payment_method }}</span>
                <span class="text-gray-500">Uso de factura</span>
                <span>{{ currentCustomer?.invoice_use }}</span>

                <div class="flex items-center justify-end space-x-2 col-span-2 mr-4 mt-16">
                    <el-tooltip content="Agendar reunión" placement="top">
                        <i @click="$inertia.get(route('meeting-monitors.create'))"
                        class="fa-regular fa-calendar text-primary cursor-pointer text-lg px-3 border-r border-[#9a9a9a]"></i>
                    </el-tooltip>
                    <el-tooltip content="Registrar pago" placement="top">
                        <i @click="$inertia.get(route('payment-monitors.create'))"
                        class="fa-solid fa-money-bill text-primary cursor-pointer text-lg px-3 border-r border-[#9a9a9a]"></i>
                    </el-tooltip>
                    <el-tooltip content="Enviar correo" placement="top">
                        <i class="fa-regular fa-envelope text-primary cursor-pointer text-lg px-3"></i>
                    </el-tooltip>
                </div>

            </div>
        </div>
        <!-- ------------- info project ends 1 ------------- -->
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Tab from "@/Components/MyComponents/Tab.vue";
import Tag from "@/Components/MyComponents/Tag.vue";
import { Link } from "@inertiajs/vue3";

export default {
data() {

    return {
        selectedCustomer: "",
        currentTab: 1,
        currenCustomer: null,
        tabs: [
            'Información general',
            'Oportunidades',
            'Seguimiento integral',
            'Historial',
            ],
    }
},
components: {
    AppLayout,
    PrimaryButton,
    SecondaryButton,
    Tab,
    Link,
    Tag,
},
props: {
    customer: Object,
    customers: Object,
},
methods: {

},
watch: {
    selectedCustomer(newVal) {
        this.currentCustomer = this.customers.data.find((item) => item.id == newVal);
    },
    },
mounted() {
    this.selectedCustomer = this.customer.id;
    this.currentCustomer = this.customers.data.find((item) => item.id == this.selectedCustomer);
},

}

</script>