<script setup>
import { ref, onMounted } from 'vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

const date = ref();

const props = defineProps({
    priorities: Object,
    statuses: Object,
})

onMounted(() => {
    const startDate = new Date();
    const endDate = new Date(new Date().setDate(startDate.getDate() + 7));
    date.value = [startDate, endDate];
})

</script>
<template>
    <form class="flex justify-center">
        <div class="mb-8 mt-4 flex flex-wrap gap-2">
            <div class="flex flex-nowrap items-center">
                <input type="text" placeholder="search" class="w-50" />
            </div>

            <div class="flex flex-nowrap items-center">
                <select class="input-filter-l w-28">
                    <option :value="null">Priority</option>
                    <option v-for="(priority, label) in priorities" :value="priority">{{ label }}
                    </option>
                </select>
                <select class="input-filter-r w-28">
                    <option :value="null">Status</option>
                    <option v-for="(status, label) in statuses" :value="status">{{ label }}</option>
                </select>
            </div>

            <div class="flex flex-nowrap items-center">
                <VueDatePicker v-model="date" range class="w-50"></VueDatePicker>
            </div>

            <button type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Filter</button>
            <button type="reset"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Clear</button>
        </div>
    </form>
</template>
