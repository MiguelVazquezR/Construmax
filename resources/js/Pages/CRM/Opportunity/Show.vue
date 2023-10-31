<template>
    <AppLayout title="Oportunidades">
        <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
            <div class="flex justify-between text-lg mx-2 lg:mx-14 mt-11">
                <span>Oportunidades</span>
                <Link :href="route('crm.opportunities.index')">
                <p class="flex items-center text-sm text-primary">
                    <i class="fa-solid fa-arrow-left-long mr-2"></i>
                    <span>Regresar</span>
                </p>
                </Link>
            </div>
            <div class="flex justify-between mt-5 mx-2 lg:mx-14">
                <div class="md:w-full mr-2 flex items-center">
                    <el-select v-model="selectedOpportunity" clearable filterable placeholder="Buscar proyecto" class="w-1/2 mr-4"
                        no-data-text="No hay clientes registrados" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in opportunities.data" :key="item.id" :label="item.name" :value="item.id" />
                    </el-select>
                </div>
                <div class="flex justify-end mr-3 w-1/2">
                    <!-- <el-dropdown split-button type="primary" @click="$inertia.get(route('crm.opportunities.create'))">
                        Nuevo cliente
                        <template #dropdown>
                        <el-dropdown-menu>
                            <el-dropdown-item @click="update">Enviar correo</el-dropdown-item>
                            <el-dropdown-item @click="showConfirmModal= true">Registar pago</el-dropdown-item>
                            <el-dropdown-item @click="showConfirmModal= true">Agendar cita</el-dropdown-item>
                        </el-dropdown-menu>
                        </template>
                    </el-dropdown> -->
                    <Link
                        v-if="
                        $page.props.auth.user.permissions?.includes('Crear oportunidades') || true
                        "
                        :href="route('crm.opportunities.create')"
                    >
                        <PrimaryButton class="rounded-full">Nueva oportunidad</PrimaryButton>
                    </Link>
                    <Link v-if="this.$page.props.auth.user.permissions.includes('Editar oportunidades')" :href="route('crm.opportunities.edit', selectedOpportunity)">
                        <i class="fa-solid fa-pencil ml-3 text-primary rounded-full p-2 bg-[#FEDBBD] cursor-pointer"></i>
                    </Link>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-center space-x-5 mb-4">
            <p class="font-bold text-lg">
            {{ currentOpportunity?.folio }} - {{ currentOpportunity?.name }}
            </p>
            <p :class="getColorStatus()" class="px-2 py-1 font-bold rounded-sm">{{ currentOpportunity?.status }}</p>
        </div>

        
        <!-- ------------- tabs section starts ------------- -->
        <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
            <div class="flex">
                <Tab @click="currentTab = (index + 1)" :label="tab" :active="currentTab == (index + 1)"
                    v-for="(tab, index) in tabs" :key="index" />
            </div>
        </div>
        <!-- ------------- tabs section ends ------------- -->

        <!-- ------------- Informacion general Starts 1 ------------- -->
    <div v-if="currentTab == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
      <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">
        <p class="text-secondary col-span-2 mb-2">Información de la oportunidad</p>

        <span class="text-gray-500">Folio</span>
        <span>{{ currentOpportunity?.folio }}</span>
        <span class="text-gray-500 my-2">Nombre de la oportunidad</span>
        <span>{{ currentOpportunity?.name }}</span>
        <span class="text-gray-500 my-2">Tipo de servicio</span>
        <span v-html="currentOpportunity?.service_type"></span>
        <span class="text-gray-500 my-2">Descripción del servicio</span>
        <span v-html="currentOpportunity?.description"></span>
        <span class="text-gray-500 my-2">Creado por</span>
        <span>{{ currentOpportunity?.user?.name }}</span>
        <span class="text-gray-500 my-2">Responsable</span>
        <span>{{ currentOpportunity?.seller?.name }}</span>
        <!-- <span class="text-gray-500 my-2">Estatus</span>
          <p :class="getStatusStyles()" class="rounded-full px-2 py-1 w-1/2 text-center">{{ currentOpportunity?.status }}</p> -->
        <span class="text-gray-500 my-2">Estatus</span>
        <div class="flex items-center space-x-4 relative">
          <!-- <i :class="getColorStatus()" class="fa-solid fa-circle absolute -left-3 top-4"></i> -->
          <el-select @change="status == 'Perdida' ? showLostOportunityModal = true : updateStatus()" class="lg:w-1/2 mt-2"
            v-model="status" clearable filterable placeholder="Seleccionar estatus"
            no-data-text="No hay estatus registrados" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in statuses" :key="item" :label="item.label" :value="item.label">
              <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
              <span style="float: center; margin-left: 5px; font-size: 13px">{{
                item.label
              }}</span>
            </el-option>
          </el-select>
        </div>
        <span class="text-gray-500 my-2">Probabilidad %</span>
        <span>{{ currentOpportunity?.probability }}%</span>
        <span class="text-gray-500 my-2">Fecha de inicio</span>
        <span>{{ currentOpportunity?.start_date }}</span>
        <span class="text-gray-500 my-2">Fecha estimada de cierre</span>
        <span>{{ currentOpportunity?.close_date }}</span>
        <span class="text-gray-500 my-2">Monto estimado</span>
        <span>${{ currentOpportunity?.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
        <span class="text-gray-500 my-2">Prioridad</span>
        <div class="relative">
            <span>{{ currentOpportunity?.priority.label }}</span>
            <i class="fa-solid fa-circle text-xs absolute -left-12 top-[2px]"></i>
        </div>
        <span v-if="currentOpportunity?.lost_oportunity_razon" class="text-gray-500 my-2">Causa de pérdida</span>
        <span class="bg-red-300 py-1 px-2 rounded-full" v-if="currentOpportunity?.lost_oportunity_razon">{{
          currentOpportunity?.lost_oportunity_razon }}</span>
      </div>

      <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
        <p class="text-secondary col-span-2 mb-2">Usuarios</p>

        <div v-if="uniqueAsignedNames">
          <span v-for="asignedName in uniqueAsignedNames" :key="asignedName" class="text-gray-500">{{ asignedName
          }}</span>
          <span>{{ currentOpportunity?.company_branch }}</span>
        </div>
        <p class="text-sm text-gray-400" v-else><i class="fa-solid fa-user-slash mr-3"></i>No hay tareas asignadas a
          usuarios</p>


        <p class="text-secondary col-span-2 mb-2 mt-5">Archivos adjuntos</p>
        <div v-if="currentOpportunity?.media?.length">
          <li v-for="file in currentOpportunity?.media" :key="file"
            class="flex items-center justify-between col-span-full">
            <a :href="file.original_url" target="_blank" class="flex items-center">
              <i :class="getFileTypeIcon(file.file_name)"></i>
              <span class="ml-2">{{ file.file_name }}</span>
            </a>
          </li>
        </div>
        <p class="text-sm text-gray-400" v-else><i class="fa-regular fa-file-excel mr-3"></i>No hay archivos adjuntos en
          esta oportunidad</p>



        <p class="text-secondary col-span-full mt-7 mb-2">Etiquetas</p>
        <div class="col-span-full flex space-x-3">
          <Tag v-for="(item, index) in currentOpportunity?.tags" :key="index" :name="item.name" :color="item.color" />
        </div>

        <div class="flex items-center justify-end space-x-2 col-span-2 mr-4">
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
    <!-- ------------- Informacion general ends 1 ------------- -->



    
    <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
      <template #title> Eliminar oportunidad </template>
      <template #content> ¿Continuar con la eliminación? </template>
      <template #footer>
        <div>
          <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
          <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
        </div>
      </template>
    </ConfirmationModal>

    <Modal :show="showLostOportunityModal" @close="showLostOportunityModal = false">
      <div class="mx-7 my-4 space-y-4 relative">
        <div>
          <label>Causa oportunidad perdida
            <el-tooltip content="Escribe la causa por la cual se PERDIÓ esta oportunidad" placement="top">
              <i class="fa-regular fa-circle-question ml-2 text-primary text-xs"></i>
            </el-tooltip>
          </label>
          <textarea v-model="lost_oportunity_razon" required class="textarea mt-3"></textarea>

        </div>
        <div class="flex justify-end space-x-3 pt-5 pb-1">
          <PrimaryButton @click="updateStatus">Actualizar estatus</PrimaryButton>
        </div>
      </div>
    </Modal>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Tab from "@/Components/MyComponents/Tab.vue";
import Tag from "@/Components/MyComponents/Tag.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import Modal from "@/Components/Modal.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
    data(){
        return {
            selectedOpportunity: "",
            currentTab: 1,
            currenOpportunity: null,
            showConfirmModal: false,
            showLostOportunityModal: false,
            status: null,
            lost_oportunity_razon: null,
            todayTasksList: [],
            thisWeekTasksList: [],
            nextTasksList: [],
            finishedTasksList: [],
            lateTasksList: [],
            tabs: [
                'Información general',
                'Actividades',
                'Seguimiento integral',
                'Historial',
                'Encuesta post venta',
                ],
            statuses: [
                {
                label: "Nueva",
                color: "text-[#9A9A9A]",
                },
                {
                label: "Pendiente",
                color: "text-[#F3FD85]",
                },
                {
                label: "En proceso",
                color: "text-[#FEDBBD]",
                },
                {
                label: "Cerrada",
                color: "text-[#AFFDB2]",
                },
                {
                label: "Perdida",
                color: "text-[#F7B7FC]",
                },
            ],
        }
    },
    components:{
        AppLayout,
        PrimaryButton,
        SecondaryButton,
        ConfirmationModal,
        Modal,
        Tab,
        Tag,
        Link,
    },
    props:{
        opportunity: Object,
        opportunities: Object,
    },
    methods:{
        deleteItem() {
      this.$inertia.delete(route('oportunities.destroy', this.oportunitySelected));
      this.$inertia.get(route('oportunities.index'));
    },
        getColorStatus() {
      if (this.currentOpportunity?.status == "Nueva") {
        return "bg-[#F2F2F2] text-[#97989A]";
      } else if (this.currentOpportunity?.status == "Pendiente") {
        return "bg-[#F3FD85] text-[#C88C3C]";
      } else if (this.currentOpportunity?.status == "En proceso") {
        return "bg-[#FEDBBD] text-[#FD8827]";
      } else if (this.currentOpportunity?.status == "Cerrada") {
        return "bg-[#AFFDB2] text-[#37951F]";
      } else if (this.currentOpportunity?.status == "Perdida") {
        return "bg-[#F7B7FC] text-[#9E0FA9]";
      } else {
        return "bg-transparent";
      }
    },
    getFileTypeIcon(fileName) {
      // Asocia extensiones de archivo a iconos
      const fileExtension = fileName.split('.').pop().toLowerCase();
      switch (fileExtension) {
        case 'pdf':
          return 'fa-regular fa-file-pdf text-red-700';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
          return 'fa-regular fa-image text-blue-300';
        default:
          return 'fa-regular fa-file-lines';
      }
    },
    async updateStatus() {
      try {
        const response = await axios.put(route('crm.opportunities.update-status', this.currentOpportunity.id), {
          status: this.status,
          lost_oportunity_razon: this.lost_oportunity_razon
        });
        if (response.status === 200) {
          this.$notify({
            title: "Correcto",
            message: "Se ha actulizado el estatus de la oportunidad",
            type: "success",
          });
          this.showLostOportunityModal = false;
          this.currenOpportunity.status = this.status;
          if (this.lost_oportunity_razon) {
            this.currentOportunity.lost_oportunity_razon = this.lost_oportunity_razon;
            this.lost_oportunity_razon = null;
          } else {
            this.currentOportunity.lost_oportunity_razon = null;
          }
        }
      } catch (error) {
        console.log(error);
      }
    },
    },
    watch: {
    selectedOpportunity(newVal) {
        this.currentOpportunity = this.opportunities.data.find((item) => item.id == newVal);
    },
    },
mounted() {
    this.selectedOpportunity = this.opportunity.id;
    this.currentOpportunity = this.opportunities.data.find((item) => item.id == this.selectedOpportunity);
    this.status = this.currentOportunity?.status;
    if (this.defaultTab != null) {
      this.tabs = parseInt(this.defaultTab);
    }   
},
}
</script>

<style>
.contenedor {
  display: flex;
  overflow-x: scroll;
  /* Permite el desplazamiento horizontal */
  white-space: nowrap;
  /* Evita el salto de línea de las secciones */
}

.seccion {
  flex: 0 0 25%;
  /* Establece el ancho de cada sección al 25% */
}

.contenedor::-webkit-scrollbar {
  width: 4px;
  /* Ancho de la barra de desplazamiento */
}

.contenedor::-webkit-scrollbar-thumb {
  background-color: #ccc;
  /* Color de la barra de desplazamiento */
  border-radius: 5px;
  /* Radio de los bordes de la barra de desplazamiento */
}
</style>