<template>
  <AppLayout title="Presupuestos">
    <div @click="show_type_view = false" class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
      <div class="flex justify-between">
        <label class="text-lg">Presupuestos</label>
      </div>
      <div class="flex justify-between">
        <div v-if="type_view == 'Lista'" class="w-1/3 relative">
          <input @keyup.enter="handleSearch" v-model="inputSearch" class="input outline-none pr-8"
            placeholder="Buscar ticket" />
          <i class="fa-solid fa-magnifying-glass absolute top-2 right-4 text-xs text-[#9A9A9A]"></i>
        </div>
        <span v-if="type_view == 'Kanban'"></span>
        <div class="flex items-center space-x-2">
          <div @click.stop="show_type_view = !show_type_view"
            class="flex items-center text-primary mr-7 cursor-pointer relative">
            <p class="text-sm">{{ type_view }}</p>
            <i class="fa-solid fa-angle-down text-sm ml-2"></i>
            <div v-if="show_type_view" class="text-sm absolute -bottom-10 -left-4 border rounded-md py-1 px-1">
              <p v-if="type_view == 'Lista'" @click="type_view = 'Kanban'"
                class="cursor-pointer hover:bg-orange-100 rounded-full py-1 px-3">
                Kanban
              </p>
              <p v-if="type_view == 'Kanban'" @click="type_view = 'Lista'"
                class="cursor-pointer hover:bg-orange-100 rounded-full py-1 px-3">
                Lista
              </p>
            </div>
          </div>
          <Link v-if="$page.props.auth.user.permissions?.includes('Crear presupuestos')
          " :href="route('crm.opportunities.create')">
          <PrimaryButton class="rounded-lg">Nuevo presupuesto</PrimaryButton>
          </Link>
        </div>
      </div>
    </div>

    <!-- ------------ Kanban view starts ----------------- -->
    <div v-if="type_view === 'Kanban'" class="mt-16">
      <div>
        <p v-if="opportunities.data.length" class="text-gray-500 text-[11px] mx-8">
          Mostrando {{ opportunities.data.length }} de {{ total_opportunities }} elementos
        </p>
        <p v-if="loadingItems" class="text-xs text-center">
          Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
        </p>
        <button
          v-else-if="total_opportunities > 30 && opportunities.data.length < total_opportunities && opportunities.data.length"
          @click="fetchItemsByPage" class="w-full text-primary text-xs mx-auto underline">
          Cargar más elementos
        </button>
      </div>
      <div class="mx-4 contenedor text-center text-sm mb-16 mt-4 pb-9">
        <!-- ---- Trabajo terminado --- -->
        <section class="seccion">
          <h2 class="text-[#9A9A9A] bg-[#D9D9D9] border border-[#9A9A9A] py-1">Trabajo terminado</h2>
          <div class="border border-[#9A9A9A] p-2 min-h-full">
            <!-- <p class="text-[#9A9A9A] cursor-pointer mt-1">+ Agregar</p> -->
            <p class="text-primary text-xl my-2">
              ${{ newTotal?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? "0.00" }}
            </p>
            <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false" v-model="newOpportunitiesLocal"
              :animation="300" item-key="id" tag="ul" group="oportunities" id="new"
              :class="drag && !newOpportunitiesLocal?.length ? 'h-40' : ''">
              <template #item="{ element: opportunity }">
                <li>
                  <OpportunityCard class="my-3" :opportunity="opportunity" />
                </li>
              </template>
            </draggable>
            <div class="text-center" v-if="!newOpportunitiesLocal?.length">
              <p class="text-xs text-gray-500 mt-6">No hay presupuestos en este estatus</p>
            </div>
          </div>
        </section>
        <!-- ---- Presupuesto --- -->
        <section class="seccion">
          <h2 class="text-[#C88C3C] bg-[#F3FD85] border border-[#9A9A9A] py-1">
            Presupuesto
          </h2>
          <div class="border border-[#9A9A9A] p-2 min-h-full">
            <!-- <p class="text-[#9A9A9A] cursor-pointer mt-1">+ Agregar</p> -->
            <p class="text-primary text-xl my-2">
              ${{
                pendingTotal?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? "0.00"
              }}
            </p>
            <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false"
              v-model="pendingOpportunitiesLocal" :animation="300" item-key="id" tag="ul" group="oportunities"
              id="pending" :class="drag && !pendingOpportunitiesLocal?.length ? 'h-40' : ''">
              <template #item="{ element: opportunity }">
                <li>
                  <OpportunityCard class="my-3" :opportunity="opportunity" />
                </li>
              </template>
            </draggable>
            <div class="text-center" v-if="!pendingOpportunitiesLocal?.length">
              <p class="text-xs text-gray-500 mt-6">No hay presupuestos en este estatus</p>
            </div>
          </div>
        </section>
        <!-- ---- Facturado --- -->
        <section class="seccion">
          <h2 class="text-[#FD8827] bg-[#FEDBBD] border border-[#9A9A9A] py-1">
            Facturado
          </h2>
          <div class="border border-[#9A9A9A] p-2 min-h-full">
            <!-- <p class="text-[#9A9A9A] cursor-pointer mt-1">+ Agregar</p> -->
            <p class="text-primary text-xl my-2">
              ${{
                closedTotal?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? "0.00"
              }}
            </p>
            <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false"
              v-model="closedOpportunitiesLocal" :animation="300" item-key="id" tag="ul" group="oportunities"
              id="closed" :class="drag && !closedOpportunitiesLocal?.length ? 'h-40' : ''">
              <template #item="{ element: opportunity }">
                <li>
                  <OpportunityCard class="my-3" :opportunity="opportunity" />
                </li>
              </template>
            </draggable>
            <div class="text-center" v-if="!closedOpportunitiesLocal?.length">
              <p class="text-xs text-gray-500 mt-6">No hay presupuestos en este estatus</p>
            </div>
          </div>
        </section>
        <!-- ---- Pagado --- -->
        <section class="seccion">
          <h2 class="text-[#37951F] bg-[#AFFDB2] border border-[#9A9A9A] py-1">Pagado</h2>
          <div class="border border-[#9A9A9A] p-2 min-h-full">
            <!-- <p class="text-[#9A9A9A] cursor-pointer mt-1">+ Agregar</p> -->
            <p class="text-primary text-xl my-2">
              ${{ paidTotal?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? "0.00" }}
            </p>
            <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false"
              v-model="paidOpportunitiesLocal" :animation="300" item-key="id" tag="ul" group="oportunities" id="paid"
              :class="drag && !paidOpportunitiesLocal?.length ? 'h-40' : ''">
              <template #item="{ element: opportunity }">
                <li>
                  <OpportunityCard class="my-3" :opportunity="opportunity" />
                </li>
              </template>
            </draggable>
            <div class="text-center" v-if="!paidOpportunitiesLocal?.length">
              <p class="text-xs text-gray-500 mt-6">No hay presupuestos en este estatus</p>
            </div>
          </div>
        </section>
        <!-- ---- Perdidas --- -->
        <section class="seccion">
          <h2 class="text-[#9E0FA9] bg-[#F7B7FC] border border-[#9A9A9A] py-1">Perdidas</h2>
          <div class="border border-[#9A9A9A] p-2 min-h-full">
            <!-- <p class="text-[#9A9A9A] cursor-pointer mt-1">+ Agregar</p> -->
            <p class="text-primary text-xl my-2">
              ${{ lostTotal?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? "0.00" }}
            </p>
            <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false"
              v-model="lostOpportunitiesLocal" :animation="300" item-key="id" tag="ul" group="oportunities" id="lost"
              :class="drag && !lostOpportunitiesLocal?.length ? 'h-40' : ''">
              <template #item="{ element: opportunity }">
                <li>
                  <OpportunityCard class="my-3" :opportunity="opportunity" />
                </li>
              </template>
            </draggable>
            <div class="text-center" v-if="!lostOpportunitiesLocal?.length">
              <p class="text-xs text-gray-500 mt-6">No hay presupuestos en este estatus</p>
            </div>
          </div>
        </section>
      </div>
    </div>
    <!-- ------------ Kanban view ends ----------------- -->

    <!-- ------------ Lista view starts ----------------- -->
    <div v-if="type_view === 'Lista'" class="w-full mx-auto my-16 text-xs">
      <div v-if="opportunities.data.length" class="overflow-x-auto">
        <table class="lg:w-[95%] w-full mx-auto">
          <thead>
            <tr class="text-left">
              <th class="font-bold pb-5 min-w-[90px]">Nombre <i
                  class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
              </th>
              <th class="font-bold pb-5 min-w-[90px]">Estatus <i
                  class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
              </th>
              <th class="font-bold pb-5 min-w-[90px]">Cliente <i
                  class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
              </th>
              <th class="font-bold pb-5 min-w-[90px]">Sucursal <i
                  class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
              </th>
              <th class="font-bold pb-5 min-w-[130px]">Fecha inicio <i
                  class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
              </th>
              <th class="font-bold pb-5 min-w-[140px]">Estimación de cierre <i
                  class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
              </th>
              <th class="font-bold pb-5 min-w-[90px]">Cerrada el <i
                  class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
              </th>
              <th class="font-bold pb-5 min-w-[90px]">Pagado el <i
                  class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
              </th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="opportunity in filteredTableData" :key="opportunity.id"
              class="mb-4 cursor-pointer hover:bg-primarylight"
              @click="$inertia.get(route('crm.opportunities.show', opportunity.id))">
              <td :title="opportunity.name" class="text-left py-2 px-2 rounded-l-full max-w-[220px] truncate pr-2">
                {{ opportunity.name }}
              </td>
              <td class="text-left py-2">
                <span class="py-1 px-4 rounded-full border border-white" :class="getStatusStyles(opportunity)">{{
                  opportunity.status }}</span>
              </td>
              <td class="text-left py-2">
                <span class="py-1 rounded-full">{{
                  opportunity.customer.name
                }}</span>
              </td>
              <td class="text-left py-2">
                <span class="py-1 rounded-full">{{
                  opportunity.branch
                }}</span>
              </td>
              <td class="text-left py-2">
                <span class="py-1 rounded-full">{{
                  opportunity.created_at.isoFormat
                }}</span>
              </td>
              <td class="text-left py-2">
                {{ opportunity.close_date }}
              </td>
              <td class="text-left py-2">
                {{ opportunity.finished_at ?? "--" }}
              </td>
              <td class="text-left py-2">
                {{ opportunity.paid_at ?? "--" }}
              </td>
              <td v-if="$page.props.auth.user.permissions?.includes('Eliminar presupuestos')" @click.stop=""
                class="text-left py-2 rounded-r-full">
                <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#FD8827" title="¿Eliminar?"
                  @confirm="deleteOpportunity(opportunity)">
                  <template #reference>
                    <i @click.stop="" class="fa-regular fa-trash-can text-primary cursor-pointer p-2"></i>
                  </template>
                </el-popconfirm>
              </td>
            </tr>
          </tbody>
        </table>
        <p v-if="opportunities.data.length" class="text-gray-500 text-[11px] mt-3 mx-8">
          Mostrando {{ opportunities.data.length }} de {{ total_opportunities }} elementos
        </p>
        <p v-if="loadingItems" class="text-xs my-4 text-center">
          Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
        </p>
        <button
          v-else-if="total_opportunities > 30 && opportunities.data.length < total_opportunities && opportunities.data.length"
          @click="fetchItemsByPage" class="w-full text-primary my-4 text-xs mx-auto underline">
          Cargar más elementos
        </button>
      </div>
      <div v-else>
        <p class="text-sm text-center">No hay presupuestos</p>
      </div>
    </div>
    <!-- ------------ Lista view ends ----------------- -->

    <!-- ------- lost modal -------- -->
    <Modal :show="showLostOpportunityModal" @close="showLostOpportunityModal = false">
      <div class="mx-7 my-4 space-y-4 relative">
        <div>
          <label>Causa presupuesto perdido
            <el-tooltip content="Escribe la causa por la cual se PERDIÓ este presupuesto" placement="top">
              <i class="fa-regular fa-circle-question ml-2 text-primary text-xs"></i>
            </el-tooltip>
          </label>
          <textarea v-model="lost_oportunity_razon" required class="input h-24 mt-3"></textarea>
        </div>
        <div class="flex justify-end space-x-3 pt-5 pb-1">
          <PrimaryButton @click="updateOpportunityStatus('Perdida')">Actualizar estatus</PrimaryButton>
        </div>
      </div>
    </Modal>

    <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
      <template #title>
        Eliminar presupuesto
      </template>
      <template #content>
        Al eliminar el presupuesto se perderán permanentemente las actividades y los archivos relacionados, así como el
        ticket relacionado. ¿Deseas continuar?
      </template>
      <template #footer>
        <div class="flex space-x-1">
          <CancelButton @click="showConfirmModal = false">Cancelar</CancelButton>
          <PrimaryButton @click="deleteProject()">Eliminar</PrimaryButton>
        </div>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/CancelButton.vue";
import OpportunityCard from "@/Components/MyComponents/CRM/OpportunityCard.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Modal from "@/Components/Modal.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import draggable from "vuedraggable";
import { Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
      search: "",
      inputSearch: "",
      show_type_view: false,
      showLostOpportunityModal: false,
      showCreateProjectModal: false,
      lost_oportunity_razon: null,
      localStatus: null,
      type_view: "Kanban",
      newTotal: null,
      pendingTotal: null,
      closedTotal: null,
      paidTotal: null,
      lostTotal: null,
      newOpportunitiesLocal: [],
      pendingOpportunitiesLocal: [],
      closedOpportunitiesLocal: [],
      paidOpportunitiesLocal: [],
      lostOpportunitiesLocal: [],
      drag: false,
      draggingOpportunityId: null,
      opportunitiesLocal: null,
      showConfirmModal: false,
      projectToDelete: null,
      // paginacion
      loadingItems: false,
      currentPage: 1,
    };
  },
  components: {
    AppLayout,
    Dropdown,
    DropdownLink,
    PrimaryButton,
    CancelButton,
    SecondaryButton,
    OpportunityCard,
    draggable,
    Modal,
    ConfirmationModal,
    Link,
  },
  props: {
    opportunities: Object,
    total_opportunities: Number,
  },
  methods: {
    cancelUpdating() {
      window.location.reload();
    },
    handleSearch() {
      this.search = this.inputSearch;
    },
    handleStartDrag(evt) {
      this.draggingOpportunityId = evt.item.__draggable_context.element.id;
      this.drag = true;
    },
    handleAddDrag(evt) {
      let status = "Perdida";
      if (evt.to.id === "new") {
        status = "Trabajo terminado";
      } else if (evt.to.id === "pending") {
        status = "Presupuesto";
      } else if (evt.to.id === "closed") {
        status = "Facturado";
      } else if (evt.to.id === "paid") {
        status = "Pagado";
      } else if (evt.to.id === "lost") {
        status = "Perdido";
      }

      if (evt.to.id === "lost") {
        this.showLostOpportunityModal = true;
      }
      // else if (evt.to.id === "closed" || evt.to.id === "paid") {
      //   this.showCreateProjectModal = true;
      //   this.localStatus = status;
      // } 
      else {
        this.updateOpportunityStatus(status);
      }

      this.drag = false;
    },
    getStatusStyles(opportunity) {
      if (opportunity.status === 'Trabajo terminado') {
        return 'text-[#9A9A9A] bg-[#CCCCCCCC]';
      } else if (opportunity.status === 'Presupuesto') {
        return 'text-[#C88C3C] bg-[#F3FD85]';
      } else if (opportunity.status === 'Facturado') {
        return 'text-[#FD8827] bg-[#FEDBBD]';
      } else if (opportunity.status === 'Pagado') {
        return 'text-[#37951F] bg-[#ADFEB5]';
      } else if (opportunity.status === 'Perdido') {
        return 'text-[#9E0FA9] bg-[#F7B7FC]';
      }
    },
    updateLists() {
      this.newOpportunitiesLocal = this.opportunitiesLocal.filter(
        (opportunity) => opportunity.status === "Trabajo terminado"
      );
      this.pendingOpportunitiesLocal = this.opportunitiesLocal.filter(
        (opportunity) => opportunity.status === "Presupuesto"
      );
      this.closedOpportunitiesLocal = this.opportunitiesLocal.filter(
        (opportunity) => opportunity.status === "Facturado"
      );
      this.paidOpportunitiesLocal = this.opportunitiesLocal.filter(
        (opportunity) => opportunity.status === "Pagado"
      );
      this.lostOpportunitiesLocal = this.opportunitiesLocal.filter(
        (opportunity) => opportunity.status === "Perdido"
      );
    },
    calculateTotals() {
      this.newTotal = this.newOpportunitiesLocal.reduce(
        (total, opportunity) => total + opportunity.amount,
        0
      );
      this.pendingTotal = this.pendingOpportunitiesLocal.reduce(
        (total, opportunity) => total + opportunity.amount,
        0
      );
      this.closedTotal = this.closedOpportunitiesLocal.reduce(
        (total, opportunity) => total + opportunity.amount,
        0
      );
      this.paidTotal = this.paidOpportunitiesLocal.reduce(
        (total, opportunity) => total + opportunity.amount,
        0
      );
      this.lostTotal = this.lostOpportunitiesLocal.reduce(
        (total, opportunity) => total + opportunity.amount,
        0
      );
    },
    deleteOpportunity(opportunity) {
      this.$inertia.delete(route('crm.opportunities.destroy', opportunity));
      this.$notify({
        title: "Éxito",
        message: "Presupuesto eliminada",
        type: "success",
      });
      // window.location.reload();
    },
    async updateOpportunityStatus(status) {
      //cierra los modales antes de actualizar el estado
      this.showLostOpportunityModal = false;
      this.showCreateProjectModal = false;
      try {
        const response = await axios.put(route('crm.opportunities.update-status', this.draggingOpportunityId), { status: status, lost_oportunity_razon: this.lost_oportunity_razon });

        if (response.status === 200) {
          const OpportunityIndex = this.opportunitiesLocal.findIndex(item => item.id === this.draggingOpportunityId);
          this.opportunitiesLocal[OpportunityIndex].status = status;
          this.updateLists();
          this.calculateTotals();
        }
      } catch (error) {
        console.log(error);
      }
    },
    async CreateProject() {
      try {
        const response = await axios.put(route('crm.opportunities.create-project', this.draggingOpportunityId));
        if (response.status === 200) {
          if (response.data.message) {
            this.$notify({
              title: "Correcto",
              message: response.data.message,
              type: "success",
            });
            this.showCreateProjectModal = false;
            this.updateOpportunityStatus(this.localStatus);
          } else {
            this.updateOpportunityStatus(this.localStatus);
            this.$inertia.get(route('pms.projects.create'), { opportunityId: this.draggingOpportunityId });
          }
        }
      } catch (error) {
        console.log(error);
      }
    },
    async fetchItemsByPage() {
      try {
        this.loadingItems = true;
        const response = await axios.get(route('crm.opportunities.get-by-page', this.currentPage));

        if (response.status === 200) {
          this.opportunities.data = [...this.opportunities.data, ...response.data.items];
          this.opportunitiesLocal = [...this.opportunitiesLocal, ...response.data.items];
          this.currentPage++;
          this.updateLists();
        }
      } catch (error) {
        console.log(error)
      } finally {
        this.loadingItems = false;
      }
    },
  },
  computed: {
    filteredTableData() {
      if (!this.search) {
        return this.opportunities.data;
      } else {
        return this.opportunities.data.filter(
          (opportunity) =>
            opportunity.name.toLowerCase().includes(this.search.toLowerCase()) ||
            opportunity.status.toLowerCase().includes(this.search.toLowerCase())
        );
      }
    },
  },
  mounted() {
    this.opportunitiesLocal = this.opportunities.data;
    this.updateLists();
    // Calcula el dinero total de cada sección
    this.calculateTotals();
  },
};
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
  flex: 0 0 22%;
  /* Establece el ancho de cada sección al 25% */
}
</style>
