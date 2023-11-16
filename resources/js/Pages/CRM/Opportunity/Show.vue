<template>
  <AppLayout title="Oportunidades">
    <SkeletonLoading v-if="loading" />
    <div v-else class="mb-5">
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
        <div class="lg:flex items-center justify-between mt-5 mx-2 lg:mx-8">
          <!-- <div class="w-2/3 mr-2 flex items-center"> -->
          <el-select v-model="selectedOpportunity" filterable placeholder="Buscar proyecto"
            class="w-full lg:w-1/2" no-data-text="No hay clientes registrados"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in opportunities" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
          <!-- </div> -->
          <div class="flex items-center justify-end w-full lg:w-1/2 mt-3 lg:mt-0">
            <el-tooltip v-if="$page.props.auth.user.permissions?.includes('Crear oportunidades') && currentTab == 1"
              content="Crear oportunidad" placement="top">
              <Link :href="route('crm.opportunities.create')">
              <PrimaryButton class="rounded-md w-[166px]">Nueva oportunidad</PrimaryButton>
              </Link>
            </el-tooltip>
            <Link v-if="currentTab == 1" :href="route('crm.opportunities.edit', selectedOpportunity)">
            <i class="fa-solid fa-pencil ml-3 text-primary rounded-full p-2 bg-primarylight cursor-pointer"></i>
            </Link>
            <i v-if="this.$page.props.auth.user.permissions.includes('Eliminar oportunidades') && currentTab == 1"
              @click="showConfirmModal = true"
              class="fa-regular fa-trash-can ml-3 text-primary rounded-full p-2 bg-primarylight cursor-pointer"></i>
            <el-tooltip v-if="currentTab == 2 && toBool(authUserPermissions[0])"
              content="Crear actividad en la oportunidad" placement="top">
              <Link :href="route('crm.opportunity-tasks.create', selectedOpportunity)">
              <PrimaryButton class="rounded-full w-40">Agregar actividad</PrimaryButton>
              </Link>
            </el-tooltip>
            <el-tooltip v-if="currentTab == 5 && (opportunity.data.finished_at || opportunity.data.paid_at)"
              content="Genera la url para la encuesta de satisfacción" placement="top">
              <PrimaryButton @click="generateSurveyUrl" class="rounded-md w-[120px]">Generar url</PrimaryButton>
            </el-tooltip>
            <el-dropdown v-if="currentTab == 3 && $page.props.auth.user.permissions?.includes('Registrar pagos en seguimiento integral')
              && $page.props.auth.user.permissions?.includes('Agendar citas en seguimiento integral')
              && $page.props.auth.user.permissions?.includes('Enviar correos en seguimiento integral')
              " split-button type="primary"
              @click="$inertia.get(route('crm.meeting-monitors.create', { opportunityId: selectedOpportunity }))">
              Agendar cita
              <template #dropdown>
                <el-dropdown-menu>
                  <el-dropdown-item
                    v-if="$page.props.auth.user.permissions?.includes('Registrar pagos en seguimiento integral')">
                    <Link :href="route('crm.payment-monitors.create', { opportunityId: selectedOpportunity })">
                    Registrar Pago
                    </Link>
                  </el-dropdown-item>
                  <el-dropdown-item
                    v-if="$page.props.auth.user.permissions?.includes('Enviar correos en seguimiento integral')">
                    <Link :href="route('crm.email-monitors.create', { opportunityId: selectedOpportunity })">
                    Enviar correo
                    </Link>
                  </el-dropdown-item>
                </el-dropdown-menu>
              </template>
            </el-dropdown>
          </div>
        </div>
      </div>
      <div class="flex items-center justify-center space-x-5 mt-5 mb-4">
        <p class="font-bold text-lg">
          {{ opportunity.data.folio }} ({{ opportunity.data.name }})
        </p>
        <p :class="getColorStatus()" class="px-2 py-1 font-bold rounded-sm">
          {{ opportunity.data.status }}
        </p>
      </div>

      <!-- ------------- tabs section starts ------------- -->
      <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2 w-full overflow-x-auto">
        <div class="flex">
          <Tab @click="currentTab = index + 1" :label="tab" :active="currentTab == index + 1" v-for="(tab, index) in tabs"
            :key="index" />
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Informacion general Starts 1 ------------- -->
      <div v-if="currentTab == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">
          <p class="text-secondary col-span-2 mb-2">Información de la oportunidad</p>

          <span class="text-gray-500">Folio</span>
          <span>{{ opportunity.data.folio }}</span>
          <span class="text-gray-500 my-2">Nombre de la oportunidad</span>
          <span>{{ opportunity.data.name }}</span>
          <span class="text-gray-500 my-2">Tipo de servicio</span>
          <span v-html="opportunity.data.service_type"></span>
          <span class="text-gray-500 my-2">Descripción del servicio</span>
          <span v-html="opportunity.data.description"></span>
          <span class="text-gray-500 my-2">Creado por</span>
          <span>{{ opportunity.data.user?.name }}</span>
          <span class="text-gray-500 my-2">Responsable</span>
          <span>{{ opportunity.data.seller?.name }}</span>
          <span class="text-gray-500 my-2">Estatus</span>
          <div class="flex items-center relative">
            <div :class="getColorStatus()" class="absolute -left-10 top-5 rounded-full w-3 h-3"></div>
            <el-select @change="
              status == 'Perdida' ? (showLostOpportunityModal = true)
                : status == 'Cerrada' ? showCreateProjectModal = true
                  : status == 'Pagado' ? showCreateProjectModal = true
                    : updateStatus()
              " class="lg:w-1/2 mt-2" v-model="status" filterable placeholder="Seleccionar estatus"
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
          <span>{{ opportunity.data.probability }}%</span>
          <span class="text-gray-500 my-2">Fecha de inicio</span>
          <span>{{ opportunity.data.start_date }}</span>
          <span class="text-gray-500 my-2">Fecha estimada de cierre</span>
          <span>{{ opportunity.data.close_date }}</span>
          <span class="text-gray-500 my-2">Monto</span>
          <span>${{ opportunity.data.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
          <span class="text-gray-500 my-2">Prioridad</span>
          <div class="relative">
            <div :class="getColorPriority(opportunity.data.priority.label)"
              class="absolute -left-10 top-1 rounded-full w-3 h-3"></div>
            <span>{{ opportunity.data.priority.label }}</span>
          </div>
          <span v-if="opportunity.data.finished_at" class="text-gray-500 my-2">Cerrada el</span>
          <span v-if="opportunity.data.finished_at" class="bg-green-300 py-1 px-2 rounded-full">{{
            opportunity.data.finished_at }}</span>
          <span v-if="opportunity.data.paid_at" class="text-gray-500 my-2">Pagado el</span>
          <span v-if="opportunity.data.paid_at" class="bg-green-300 py-1 px-2 rounded-full">{{
            opportunity.data.paid_at }}</span>
          <span v-if="opportunity.data.lost_oportunity_razon" class="text-gray-500 my-2">Causa de pérdida</span>
          <span class="bg-red-300 py-1 px-2 rounded-full" v-if="opportunity.data.lost_oportunity_razon">{{
            opportunity.data.lost_oportunity_razon }}</span>
        </div>

        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
          <p class="text-secondary col-span-2 mb-2">Usuarios con actividades</p>

          <div v-if="uniqueAsignedNames">
            <span v-for="asignedName in uniqueAsignedNames" :key="asignedName" class="text-gray-500">{{ asignedName
            }}</span>
            <span>{{ opportunity.data.company_branch }}</span>
          </div>
          <p class="text-sm text-gray-400" v-else>
            <i class="fa-solid fa-user-slash mr-3"></i>No hay actividades asignadas a usuarios
          </p>

          <p class="text-secondary col-span-2 mb-2 mt-5">Archivos adjuntos</p>
          <div v-if="opportunity.data.media?.length">
            <li v-for="file in opportunity.data.media" :key="file"
              class="flex items-center justify-between col-span-full">
              <a :href="file.original_url" target="_blank" class="flex items-center">
                <i :class="getFileTypeIcon(file.file_name)"></i>
                <span class="ml-2">{{ file.file_name }}</span>
              </a>
            </li>
          </div>
          <p class="text-sm text-gray-400" v-else>
            <i class="fa-regular fa-file-excel mr-3"></i>No hay archivos adjuntos en esta
            oportunidad
          </p>

          <p class="text-secondary col-span-full mt-7 mb-2">Etiquetas</p>
          <div class="col-span-full flex space-x-3">
            <Tag v-for="(item, index) in opportunity.data.tags" :key="index" :name="item.name" :color="item.color" />
          </div>

          <div class="flex items-center justify-end space-x-2 col-span-2 mr-4">
            <el-tooltip v-if="$page.props.auth.user.permissions?.includes('Agendar citas en seguimiento integral')"
              content="Agendar reunión" placement="top">
              <i @click="$inertia.get(route('crm.meeting-monitors.create', { opportunityId: selectedOpportunity }))"
                class="fa-regular fa-calendar text-primary cursor-pointer text-lg px-3 border-r border-[#9a9a9a]"></i>
            </el-tooltip>
            <el-tooltip v-if="$page.props.auth.user.permissions?.includes('Registrar pagos en seguimiento integral')"
              content="Registrar pago" placement="top">
              <i @click="$inertia.get(route('crm.payment-monitors.create', { opportunityId: selectedOpportunity }))"
                class="fa-solid fa-money-bill text-primary cursor-pointer text-lg px-3 border-r border-[#9a9a9a]"></i>
            </el-tooltip>
            <el-tooltip v-if="$page.props.auth.user.permissions?.includes('Enviar correos en seguimiento integral')"
              content="Enviar correo" placement="top">
              <i @click="$inertia.get(route('crm.email-monitors.create', { opportunityId: selectedOpportunity }))"
                class="fa-regular fa-envelope text-primary cursor-pointer text-lg px-3"></i>
            </el-tooltip>
          </div>
        </div>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->

      <!-- -------------tab 2 atividades starts ------------- -->

      <div v-if="currentTab == 2" class="contenedor text-left p-4 text-sm">
        <!-- -- TERMINAR HOY -- -->
        <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:pr-7 seccion mx-2">
          <h2 class="font-bold mb-10">
            TERMINAR HOY <span class="font-normal ml-7">{{ todayTasksList?.length }}</span>
          </h2>
          <OpportunityTaskCard @updated-opportunityTask="updateOpportunityTask" @delete-task="deleteTask"
            @task-done="markAsDone" class="mb-3" v-for="todayTask in todayTasksList" :key="todayTask"
            :opportunityTask="todayTask"/>
          <div class="text-center" v-if="!todayTasksList.length">
            <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
            <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
          </div>
        </div>

        <!-- -- TERMINAR ESTA SEMANA -- -->
        <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-4 seccion mx-2">
          <h2 class="font-bold mb-10 first-letter ml-2">
            TERMINAR ESTA SEMANA
            <span class="font-normal ml-7">{{ thisWeekTasksList?.length }}</span>
          </h2>
          <OpportunityTaskCard @updated-opportunityTask="updateOpportunityTask" @delete-task="deleteTask"
            @task-done="markAsDone" class="mb-3" v-for="thisWeekTask in thisWeekTasksList" :key="thisWeekTask"
            :opportunityTask="thisWeekTask"/>
          <div class="text-center" v-if="!thisWeekTasksList.length">
            <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
            <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
          </div>
        </div>

        <!-- -- ACTIVIDADES PROXIMAS -- -->
        <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-4 seccion mx-2">
          <h2 class="font-bold mb-10 first-letter ml-2">
            ACTIVIDADES PROXIMAS
            <span class="font-normal ml-7">{{ nextTasksList?.length }}</span>
          </h2>
          <OpportunityTaskCard @updated-opportunityTask="updateOpportunityTask" @delete-task="deleteTask"
            @task-done="markAsDone" class="mb-3" v-for="nextTask in nextTasksList" :key="nextTask"
            :opportunityTask="nextTask"/>
          <div class="text-center" v-if="!nextTasksList?.length">
            <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
            <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
          </div>
        </div>

        <!-- -- ATRASADAS -- -->
        <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-4 seccion mx-2">
          <h2 class="font-bold mb-10 first-letter ml-2">
            ATRASADAS <span class="font-normal ml-7">{{ lateTasksList.length }}</span>
          </h2>
          <OpportunityTaskCard @updated-opportunityTask="updateOpportunityTask" @delete-task="deleteTask"
            @task-done="markAsDone" class="mb-3" v-for="lateTask in lateTasksList" :key="lateTask"
            :opportunityTask="lateTask"/>
          <div class="text-center" v-if="!lateTasksList.length">
            <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
            <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
          </div>
        </div>

        <!-- -- TERMINADAS -- -->
        <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-4 seccion mx-2">
          <h2 class="font-bold mb-10 first-letter ml-2">
            TERMINADAS <span class="font-normal ml-7">{{ finishedTasksList?.length }}</span>
          </h2>
          <OpportunityTaskCard @updated-opportunityTask="updateOpportunityTask" @delete-task="deleteTask"
            @task-done="markAsDone" class="mb-3" v-for="finishedTask in finishedTasksList" :key="finishedTask"
            :opportunityTask="finishedTask"/>
          <div class="text-center" v-if="!finishedTasksList.length">
            <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
            <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
          </div>
        </div>

      </div>
      <!-- ------------- tab 2 atividades ends ------------ -->

      <!-- ------------ tab 3 seguimiento integral starts ------------- -->
      <div v-if="currentTab == 3" class="w-full mx-auto my-8">
        <div v-if="opportunity.data.clientMonitors?.length" class="overflow-x-auto">
          <table class="lg:w-[80%] w-full mx-auto text-sm">
            <thead>
              <tr class="text-left">
                <th class="font-bold pb-5">
                  Folio <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
                </th>
                <th class="font-bold pb-5">
                  Tipo <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
                </th>
                <th class="font-bold pb-5">
                  Fecha <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
                </th>
                <th class="font-bold pb-5">
                  Concepto <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
                </th>
                <th class="font-bold pb-5">
                  Vededor <i class="text-[9px] md:inline fa-solid fa-arrow-down-long md:ml-3"></i>
                </th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="monitor in opportunity.data.clientMonitors" :key="monitor" class="mb-4">
                <td @click="showMonitorType(monitor)"
                  class="text-left py-2 rounded-l-full text-primary hover:underline cursor-pointer min-w-[100px]">
                  {{ monitor.folio }}
                </td>
                <td class="text-left py-2 px-2 min-w-[120px]">
                  <span class="py-1 rounded-full">{{ monitor.type }}</span>
                </td>
                <td class="text-left py-2">
                  <span class="py-1">{{ monitor.date }}</span>
                </td>
                <td class="text-left py-2 max-w-[120px] truncate px-2">
                  {{ monitor.concept }}
                </td>
                <td @click="$inertia.get(route('users.show', monitor.seller?.id))"
                  class="text-left py-2 text-primary hover:underline cursor-pointer min-w-[100px]">
                  {{ monitor.seller?.name }}
                </td>
                <td v-if="$page.props.auth.user.permissions.includes('Eliminar seguimiento integral')"
                  class="text-left py-2 rounded-r-full">
                  <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#D90537" title="¿Eliminar?"
                    @confirm="deleteClientMonitor(monitor)">
                    <template #reference>
                      <i
                        class="fa-regular fa-trash-can text-primary hover:bg-[#FEDBBD] rounded-full cursor-pointer p-2"></i>
                    </template>
                  </el-popconfirm>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else>
          <p class="text-sm text-center text-gray-400">No hay seguimiento en esta oportunidad</p>
        </div>
      </div>
      <!-- ------------ tab 3 seguimiento integral ends ------------- -->

      <!-- ------------ tab 4 Historial starts ------------- -->
      <div class="lg:mx-16 mx-2 my-4 text-sm" v-if="currentTab == 4">
        <div v-if="opportunity.data.activities?.length">
          <ul>
            <li class="mb-1" v-for="(activity, index) in opportunity.data.activities" :key="index">
              <span class="mr-2">{{ index + 1 }}.</span>
              <span @click="$inertia.get(route('users.show', activity.user.id))"
                class="text-primary hover:underline cursor-pointer mr-2">{{ activity.user.name }}</span>
              <span>{{ activity.description }} el {{ activity.created_at }}</span>
            </li>
          </ul>
        </div>
        <div v-else>
          <p class="text-sm text-center text-gray-400">No hay historial en esta oportunidad</p>
        </div>
      </div>
      <!-- ------------ tab 4 Historial ends ------------- -->

      <!-- ------------ tab 5 Ecuesta post venta starts ------------- -->
      <div v-if="currentTab == 5" class="w-11/12 mx-auto my-8">
        <table v-if="opportunity.data.survey" class="lg:w-[80%] w-full mx-auto text-sm">
          <thead>
            <tr class="text-center">
              <th class="font-bold pb-5">
                ID <i class="fa-solid fa-arrow-down-long ml-3"></i>
              </th>
              <el-tooltip
                content="En una escala del 0 al 10, ¿qué tan satisfecho/a estás con la calidad de nuestros productos?"
                placement="top">
                <th class="font-bold pb-5">
                  P1 <i class="fa-solid fa-arrow-down-long ml-3"></i>
                </th>
              </el-tooltip>
              <el-tooltip content="¿Nuestros productos cumplieron con tus expectativas?" placement="top">
                <th class="font-bold pb-5">
                  P2 <i class="fa-solid fa-arrow-down-long ml-3"></i>
                </th>
              </el-tooltip>
              <el-tooltip content="¿Consideras que nuestro equipo de trabajo fue profesional y cortés?" placement="top">
                <th class="font-bold pb-5">
                  P3 <i class="fa-solid fa-arrow-down-long ml-3"></i>
                </th>
              </el-tooltip>
              <el-tooltip content="¿Recomendarías nuestros productos a otros?" placement="top">
                <th class="font-bold pb-5">
                  P4 <i class="fa-solid fa-arrow-down-long ml-3"></i>
                </th>
              </el-tooltip>
              <th class="font-bold pb-5">Comentario</th>
            </tr>
          </thead>
          <tbody>
            <tr class="mb-4 hover:bg-[#dfdbdba8]">
              <td class="text-center py-2 px-2 rounded-l-full">
                {{ opportunity.data.survey?.opportunity_id }}
              </td>
              <td class="text-center py-2 px-2">
                <span class="py-1 px-4 rounded-full">{{ opportunity.data.survey?.p1 }}</span>
              </td>
              <td class="text-center py-2 px-2">
                <span class="py-1 px-2 rounded-full">{{ opportunity.data.survey?.p2 }}</span>
              </td>
              <td class="text-center py-2 px-2">
                {{ opportunity.data.survey?.p3 }}
              </td>
              <td class="text-center py-2 px-2">
                {{ opportunity.data.survey?.p4 }}
              </td>
              <td class="text-center py-2 px-2 rounded-r-full">
                {{ opportunity.data.survey?.p5 }}
              </td>
            </tr>
          </tbody>
        </table>
        <div v-else>
          <p class="text-sm text-center text-gray-400">No se ha contestado la encuesta</p>
        </div>
      </div>
      <!-- ------------ tab 5 Ecuesta post venta ends ------------- -->

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

      <!-- ----------------- status modal ----------- -->
      <Modal :show="showLostOpportunityModal || showCreateProjectModal"
        @close="showLostOpportunityModal = false; showCreateProjectModal = false">
        <section v-if="showLostOpportunityModal" class="mx-7 my-4 space-y-4 relative">
          <div>
            <label>Causa oportunidad perdida
              <el-tooltip content="Escribe la causa por la cual se PERDIÓ esta oportunidad" placement="top">
                <i class="fa-regular fa-circle-question ml-2 text-primary text-xs"></i>
              </el-tooltip>
            </label>
            <textarea v-model="lost_oportunity_razon" class="input h-20 mt-3"></textarea>
          </div>
          <div class="flex justify-end space-x-3 pt-5 pb-1">
            <CancelButton @click="cancelUpdating">Cancelar</CancelButton>
            <PrimaryButton @click="updateStatus('Perdida')" :disabled="lost_oportunity_razon == null">Actualizar estatus
            </PrimaryButton>
          </div>
        </section>

        <section v-if="showCreateProjectModal" class="mx-7 my-4 space-y-4 relative">
          <div>
            <h2 class="font bold text-center font-bold mb-5">Paso clave - Crear proyecto</h2>
            <p class="px-5">Es necesario crear un proyecto al haber marcado como <span
                class="text-[#FD8827]">”cerrada”</span>
              o <span class="text-[#37951F]">”Pagada”</span> la oportunidad para llevar un correcto seguimiento y flujo de
              trabajo.
            </p>
          </div>
          <div class="flex justify-end space-x-3 pt-5 pb-1">
            <CancelButton @click="cancelUpdating">Cancelar</CancelButton>
            <PrimaryButton @click="CreateProject">Continuar</PrimaryButton>
          </div>
        </section>
      </Modal>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import CancelButton from "@/Components/CancelButton.vue";
import Tab from "@/Components/MyComponents/Tab.vue";
import Tag from "@/Components/MyComponents/Tag.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import OpportunityTaskCard from "@/Components/MyComponents/CRM/OpportunityTaskCard.vue";
import Modal from "@/Components/Modal.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import SkeletonLoading from "@/Components/MyComponents/SkeletonLoading/Show.vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";

export default {
  data() {
    return {
      selectedOpportunity: this.opportunity.data.id,
      currentTab: 1,
      showConfirmModal: false,
      showLostOpportunityModal: false,
      showCreateProjectModal: false,
      loading: false,
      status: null,
      lost_oportunity_razon: null,
      todayTasksList: [],
      thisWeekTasksList: [],
      nextTasksList: [],
      finishedTasksList: [],
      lateTasksList: [],
      tabs: [
        "Información general",
        "Actividades",
        "Seguimiento integral",
        "Historial",
        "Encuesta post venta",
      ],
      statuses: [
        {
          label: "Nueva",
          color: "text-[#f2f2f2]",
        },
        {
          label: "Pendiente",
          color: "text-[#F3FD85]",
        },
        {
          label: "Cerrada",
          color: "text-[#FEDBBD]",
        },
        {
          label: "Pagado",
          color: "text-[#AFFDB2]",
        },
        {
          label: "Perdida",
          color: "text-[#F7B7FC]",
        },
      ],
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    SecondaryButton,
    CancelButton,
    ConfirmationModal,
    OpportunityTaskCard,
    Modal,
    Dropdown,
    DropdownLink,
    Tab,
    Tag,
    Link,
    SkeletonLoading,
  },
  props: {
    opportunity: Object,
    opportunities: Object,
    defaultTab: Number,
  },
  methods: {
    cancelUpdating() {
      window.location.reload();
    },
    showMonitorType(monitor) {
      if (monitor.type == 'Correo') {
        this.$inertia.get(route('crm.email-monitors.show', monitor.emailMonitor?.id));
      } else if (monitor.type == 'Pago') {
        this.$inertia.get(route('crm.payment-monitors.show', monitor.paymentMonitor?.id));
      } else if (monitor.type == 'Reunión') {
        this.$inertia.get(route('crm.meeting-monitors.show', monitor.meetingMonitor?.id));
      }
    },
    async CreateProject() {
      try {
        const response = await axios.put(route('crm.opportunities.create-project', this.opportunity.data.id));
        if (response.status === 200) {
          if (response.data.message) {
            this.$notify({
              title: "Correcto",
              message: response.data.message,
              type: "success",
            });
            this.showCreateProjectModal = false;
            this.updateStatus();
          } else {
            this.updateStatus();
            this.$inertia.get(route('pms.projects.create'), { opportunityId: this.opportunity.data.id });
          }
        }
      } catch (error) {
        console.log(error);
      }
    },
    async deleteClientMonitor(monitor) {
      try {
        const response = await axios.delete(route('crm.client-monitors.destroy', monitor.id));

        if (response.status === 200) {
          this.$notify({
            title: "Correcto",
            message: "Se ha eliminado correctamente",
            type: "success",
          });
          const index = this.opportunity.data.clientMonitors.findIndex(item => item.id === monitor.id);

          if (index !== -1) {
            this.opportunity.data.clientMonitors.splice(index, 1);
          }
        }
      } catch (error) {
        console.log(error);
      }
    },
    toBool(value) {
      if (value == 1 || value == true) return true;
      return false;
    },
    generateSurveyUrl() {
      const textToCopy = window.location.origin + '/surveys/create/' + this.opportunity.data.id;
      alert(textToCopy);
    },
    deleteItem() {
      this.$inertia.delete(route("crm.opportunities.destroy", this.selectedOpportunity));
      // this.$inertia.get(route("crm.opportunities.index"));
    },
    async deleteTask(data) {
      try {
        const response = await axios.delete(route("crm.opportunity-tasks.destroy", data));

        if (response.status === 200) {
          this.$notify({
            title: "Éxito",
            message: "Se ha eliminado correctamente",
            type: "success",
          });

          const index = this.opportunity.data.opportunityTasks.findIndex(
            (item) => item.id === data
          );

          if (index !== -1) {
            this.opportunity.data.opportunityTasks.splice(index, 1);
          }
          this.opportunity.data.activities = response.data.item.activities;
        }
      } catch (error) {
        console.log(error);
      }
    },
    async markAsDone(data) {
      try {
        const response = await axios.put(
          route("crm.opportunity-tasks.mark-as-done", data)
        );

        if (response.status === 200) {
          this.$notify({
            title: "Correcto",
            message: "Se ha marcado como terminada",
            type: "success",
          });

          this.opportunity.data.opportunityTasks.find(
            (item) => item.id === data
          ).finished_at = new Date();

          this.opportunity.data.activities = response.data.item.opportunity.activities;
        }
      } catch (error) {
        console.log(error);
      }
    },
    updateOpportunityTask(task) {
      const index = this.opportunity.data.opportunityTasks.findIndex(
        (item) => item.id === task.id
      );

      if (index !== -1) {
        this.opportunity.data.opportunityTasks[index] = task;
        this.opportunity.data.activities = task.opportunity.activities;
      }
    },
    getColorStatus() {
      if (this.opportunity.data.status == "Nueva") {
        return "bg-[#F2F2F2] text-[#97989A]";
      } else if (this.opportunity.data.status == "Pendiente") {
        return "bg-[#F3FD85] text-[#C88C3C]";
      } else if (this.opportunity.data.status == "Cerrada") {
        return "bg-[#FEDBBD] text-[#FD8827]";
      } else if (this.opportunity.data.status == "Pagado") {
        return "bg-[#AFFDB2] text-[#37951F]";
      } else if (this.opportunity.data.status == "Perdida") {
        return "bg-[#F7B7FC] text-[#9E0FA9]";
      } else {
        return "bg-transparent";
      }
    },
    getColorPriority(priority) {

      if (priority == "Baja") {
        return "bg-[#87CEEB]";
      } else if (priority == "Media") {
        return "bg-[#F2C940]";
      } else if (priority == "Alta") {
        return "bg-[#FB2A2A]";
      } else {
        return "bg-transparent";
      }
    },
    getFileTypeIcon(fileName) {
      // Asocia extensiones de archivo a iconos
      const fileExtension = fileName.split(".").pop().toLowerCase();
      switch (fileExtension) {
        case "pdf":
          return "fa-regular fa-file-pdf text-red-700";
        case "jpg":
        case "jpeg":
        case "png":
        case "gif":
          return "fa-regular fa-image text-blue-300";
        default:
          return "fa-regular fa-file-lines";
      }
    },
    async updateStatus() {
      try {
        const response = await axios.put(
          route("crm.opportunities.update-status", this.opportunity.data.id),
          {
            status: this.status,
            lost_oportunity_razon: this.lost_oportunity_razon,
          }
        );
        if (response.status === 200) {
          this.$notify({
            title: "Correcto",
            message: "Se ha actulizado el estatus de la oportunidad",
            type: "success",
          });
          //Cambia dinamicamente las propiedades de la oportunidad al cmbair el estatus
          this.showLostOpportunityModal = false;
          this.opportunity.data.status = this.status;
          this.opportunity.data.finished_at = response.data.item.finished_at;
          this.opportunity.data.paid_at = response.data.item.paid_at;
          this.opportunity.data.lost_oportunity_razon = response.data.item.lost_oportunity_razon;
          this.opportunity.data.activities = response.data.item.activities;

        }
      } catch (error) {
        console.log(error);
      }
    },
  },
  watch: {
    selectedOpportunity(newVal) {
      this.loading = true;
      if (this.currentTab > 1) {
        this.$inertia.get(route('crm.opportunities.show', { opportunity: newVal, defaultTab: this.currentTab }));
      } else {
        this.$inertia.get(route('crm.opportunities.show', newVal));
      }
    },
  },
  mounted() {
    // this.opportunity.data = this.opportunity.data
    this.status = this.opportunity.data.status;

    // tab para mostrar al abrir
    if (this.defaultTab !== null) {
      this.currentTab = this.defaultTab;
    }
  },
  computed: {
    authUserPermissions() {
      const permissions = this.opportunity.data.users?.find(
        (item) => item.id == this.$page.props.auth.user.id
      )?.pivot.permissions;
      if (permissions) {
        return JSON.parse(permissions);
      } else {
        return [0, 0, 0, 0, 0];
      }
    },
    uniqueAsignedNames() {
      const asignedNamesSet = new Set(); // Usamos un Set para nombres únicos.

      if (this.opportunity.data.opportunityTasks?.length) {
        // Recorremos las tareas y agregamos los nombres de los asignados al conjunto.
        this.opportunity.data.opportunityTasks?.forEach((task) => {
          asignedNamesSet.add(task.asigned.name);
        });

        // Convertimos el conjunto a un array para mostrarlo en la plantilla.
        return Array.from(asignedNamesSet);
      }
    },
    todayTasksList() {
      return (this.todayTasksList = this.opportunity.data.opportunityTasks?.filter(
        (opportunity) =>
          opportunity.deadline_status === "Terminar hoy" && !opportunity.finished_at
      ));
    },
    thisWeekTasksList() {
      return (this.thisWeekTasksList = this.opportunity.data.opportunityTasks?.filter(
        (opportunity) =>
          opportunity.deadline_status === "Esta semana" && !opportunity.finished_at
      ));
    },
    nextTasksList() {
      return (this.nextTasksList = this.opportunity.data.opportunityTasks?.filter(
        (opportunity) =>
          opportunity.deadline_status === "Proximas" && !opportunity.finished_at
      ));
    },
    finishedTasksList() {
      return (this.finishedTasksList = this.opportunity.data.opportunityTasks?.filter(
        (opportunity) => opportunity.finished_at
      ));
    },
    lateTasksList() {
      return (this.lateTasksList = this.opportunity.data.opportunityTasks?.filter(
        (opportunity) =>
          opportunity.deadline_status === "Atrasadas" && !opportunity.finished_at
      ));
    },
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

.contenedor::-webkit-scrollbar {
  width: 4px;
  /* Ancho de la barra de desplazamiento */
}

.contenedor::-webkit-scrollbar-thumb {
  background-color: #ccc;
  /* Color de la barra de desplazamiento */
  border-radius: 5px;
  /* Radio de los bordes de la barra de desplazamiento */
}</style>
