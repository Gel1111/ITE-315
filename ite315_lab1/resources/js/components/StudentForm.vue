<script setup>
import { reactive, watch } from 'vue';

const props = defineProps({
    student: { type: Object, default: null },
    errors: { type: Object, default: () => ({}) },
    saving: Boolean,
});
const emit = defineEmits(['submit', 'cancel']);

const fields = [
    { key: 'firstname', label: 'First name' },
    { key: 'middlename', label: 'Middle name (optional)' },
    { key: 'lastname', label: 'Last name' },
    { key: 'email', label: 'Email' },
    { key: 'course', label: 'Course' },
    { key: 'year', label: 'Year' },
    { key: 'section', label: 'Section' },
];
const years = ['1st Year', '2nd Year', '3rd Year', '4th Year'];

const blank = () => ({
    firstname: '', middlename: '', lastname: '',
    email: '', course: '', year: '', section: '',
});
const form = reactive(blank());

watch(
    () => props.student,
    (s) => Object.assign(form, blank(), s ?? {}),
    { immediate: true }
);
</script>

<template>
    <form class="card" @submit.prevent="emit('submit', { ...form })" novalidate>
        <h2>{{ student ? 'Edit Student' : 'Add Student' }}</h2>

        <div class="grid">
            <div v-for="f in fields" :key="f.key" class="field">
                <label :for="f.key">{{ f.label }}</label>

                <select v-if="f.key === 'year'" :id="f.key" v-model="form[f.key]">
                    <option value="">-- Select year --</option>
                    <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                </select>
                <input v-else :id="f.key" v-model="form[f.key]" />

                <small v-if="errors[f.key]" class="error">{{ errors[f.key][0] }}</small>
            </div>
        </div>

        <div class="actions">
            <button class="btn primary" :disabled="saving">
                {{ saving ? 'Saving...' : student ? 'Update Student' : 'Add Student' }}
            </button>
            <button v-if="student" type="button" class="btn" @click="emit('cancel')">Cancel</button>
        </div>
    </form>
</template>