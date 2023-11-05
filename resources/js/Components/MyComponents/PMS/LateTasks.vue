<template>
    <div class="lg:min-h-[220px] min-h-[140px] border-gray3 border rounded-[10px] lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm relative">
        <div class="my-3 col-start-2 col-span-2">
            <h1 class="font-bold text-center">Tareas atrasadas <i class="fa-solid fa-clock-rotate-left ml-2"></i></h1>
        </div>
        <div v-if="loading" class="animate-pulse flex space-x-4">
            <div class="flex-1 space-y-3 py-1">
                <div class="h-4 bg-gray-200 rounded"></div>
                <div class="h-4 bg-gray-200 rounded"></div>
                <div class="h-4 bg-gray-200 rounded"></div>
                <div class="h-4 bg-gray-200 rounded"></div>
            </div>
        </div>
        <div v-else class="h-2/3 overflow-y-scroll">
            <div v-if="tasks.length" class="h-full">
                <table class="w-full table-fixed">
                    <thead>
                        <tr class="text-xs">
                            <th class="text-start">Usuario(s)</th>
                            <th class="text-start">Tarea</th>
                            <th class="text-start">Proyecto</th>
                            <th class="text-start">Atrasado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="task in tasks" :key="task.id" class="text-xs w-full">
                            <td class="w-1/6 text-start">
                                <div class="flex items-center">
                                    <el-tooltip :content="task.users[0].name" placement="top">
                                        <figure class="w-6 h-6 rounded-full">
                                            <img :src="task.users[0].profile_photo_url" class="w-full rounded-full">
                                        </figure>
                                    </el-tooltip>
                                    <el-tooltip placement="top">
                                        <template #content>
                                            <li v-for="(participant, index) in task.users.filter((item, index) => index !== 0)"
                                                :key="index" class="ml-2 text-xs">
                                                {{ participant.name }}
                                            </li>
                                        </template>
                                        <span v-if="task.users.length > 1" class="ml-1 text-primary text-xs">
                                            +{{ (task.users.length - 1) }}
                                        </span>
                                    </el-tooltip>
                                </div>
                            </td>
                            <td class="w-1/2 text-start truncate pr-4">
                                <el-tooltip :content="task.name" placement="top">
                                    {{ task.name }}
                                </el-tooltip>
                            </td>
                            <td class="w-1/6 text-start truncate pr-4">{{ task.project.name }}</td>
                            <td class="w-1/6 text-start">{{ task.late_days }} día(s)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p v-else class="text-gray-400 text-center mt-8">No hay tareas atrasadas</p>
            <!-- <div class="flex justify-end mx-6">
                <button class="text-primary text-xs">Ver detalles</button>
            </div> -->
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            loading: true,
            tasks: [],
        }
    },
    methods: {
        async fetchLateTasks() {
            this.loading = true;
            try {
                const response = await axios.get(route('pms.tasks.get-late-tasks'));

                if (response.status === 200) {
                    this.tasks = response.data.items;
                    this.loading = false;
                }
            } catch (error) {
                console.log(error);
            }
        }
    },
    mounted() {
        this.fetchLateTasks();
    }
}
</script>