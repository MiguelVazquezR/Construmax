<template>
    <div
        class="lg:min-h-[220px] min-h-[140px] border-gray3 border rounded-[10px] lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm relative">
        <Link :href="route('crm.meeting-monitors.create')">
        <button
            class="text-primary absolute top-3 right-5 border border-transparent text-lg w-7 h-7 hover:border-primary rounded-full "><i
                class="fa-solid fa-plus"></i></button>
        </Link>
        <div class="my-3 col-start-2 col-span-2">
            <h1 class="font-bold text-center">Citas próximas <i class="fa-regular fa-calendar-check ml-2"></i></h1>
        </div>
        <div v-if="loading" class="animate-pulse flex space-x-4">
            <div class="flex-1 space-y-3 py-1">
                <div class="h-4 bg-gray-200 rounded"></div>
                <div class="h-4 bg-gray-200 rounded"></div>
                <div class="h-4 bg-gray-200 rounded"></div>
            </div>
        </div>
        <div v-else class="h-2/3 overflow-y-scroll mb-6">
            <div v-if="meetings.length" class="h-full">
                <div>
                    <table class="w-full">
                        <thead>
                            <tr class="text-xs">
                                <th>Contacto</th>
                                <th class="text-start min-w-[90px]">Fecha</th>
                                <th class="text-start min-w-[90px]">Hora</th>
                                <th class="text-start min-w-[90px]">Cita</th>
                                <th class="text-start min-w-[90px]">Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr @click="$inertia.get(route('crm.meeting-monitors.show', meeting))" v-for="meeting in meetings" :key="meeting.id"
                                class="text-xs w-full cursor-pointer hover:bg-primarylight">
                                <td :title="meeting.contact" class="w-1/5 truncate py-1 pr-3 rounded-tl-lg rounded-bl-lg">
                                    <i class="fa-regular fa-user mr-2"></i>
                                    {{ meeting.contact.name }}
                                </td>
                                <td class="w-1/5">{{ formatDate(meeting.meeting_date) }}</td>
                                <td class="w-1/5">{{ formatTime(meeting.time) }}</td>
                                <td class="w-1/5">{{ meeting.meeting_via }}</td>
                                <td class="w-1/5 truncate rounded-tr-lg rounded-br-lg">{{ meeting.description }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <p v-else class="text-gray-400 text-center mt-8 text-xs">No hay citas proximas</p>
        </div>
        <div class="flex justify-end mx-6 absolute bottom-3 right-5">
            <Link :href="route('calendars.index')">
            <button class="text-primary text-xs">Ir a calendario</button>
            </Link>
        </div>
    </div>
</template>
<script>
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import moment from 'moment';

export default {
    data() {
        return {
            meetings: [],
            loading: true,
        };
    },
    components: {
        Link,
    },
    props: {

    },
    methods: {
        formatDate(date) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd \'de\' MMM', { locale: es }); // Formato personalizado
        },
        formatTime(time) {
            const parsedTime = moment(time, 'HH:mm:ss');
            return parsedTime.format('hh:mm a'); // Formato de hora en 12 horas con a.m./p.m.
        },
        async fetchMeetings() {
            this.loading = true;
            try {
                const response = await axios.get(route('users.get-meetings'));

                if (response.status === 200) {
                    this.meetings = response.data.items;
                    this.loading = false;
                }
            } catch (error) {
                console.log(error);
            }
        }
    },
    mounted() {
        this.fetchMeetings();
    }
}
</script>