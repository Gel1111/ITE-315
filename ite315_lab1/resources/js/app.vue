<script setup>
import { ref, onMounted } from 'vue';
import api from './api';
import AlertMessage from './components/AlertMessage.vue';
import StudentForm from './components/StudentForm.vue';
import StudentTable from './components/StudentTable.vue';

const students = ref([]);
const loading = ref(false);
const saving = ref(false);
const editing = ref(null);
const formKey = ref(0);
const errors = ref({});
const alert = ref({ type: 'success', text: '' });

function notify(type, text) {
    alert.value = { type, text };
    setTimeout(() => (alert.value.text = ''), 4000);
}

function handleError(e) {
    const r = e.response;
    if (r?.status === 422) {
        errors.value = r.data.errors;
        notify('error', 'Please fix the validation errors below.');
    } else {
        notify('error', r?.data?.message || 'Network error. Is the server running?');
    }
}

// READ
async function fetchStudents() {
    loading.value = true;
    try {
        const { data } = await api.get('/students');
        students.value = data.data;
    } catch (e) {
        handleError(e);
    } finally {
        loading.value = false;
    }
}

// CREATE + UPDATE
async function saveStudent(form) {
    saving.value = true;
    errors.value = {};
    try {
        const { data } = editing.value
            ? await api.put(`/students/${editing.value.id}`, form)
            : await api.post('/students', form);
        notify('success', data.message);
        resetForm();
        await fetchStudents();
    } catch (e) {
        handleError(e);
    } finally {
        saving.value = false;
    }
}

// DELETE
async function deleteStudent(student) {
    if (!confirm(`Delete ${student.firstname} ${student.lastname}?`)) return;
    try {
        const { data } = await api.delete(`/students/${student.id}`);
        notify('success', data.message);
        await fetchStudents();
    } catch (e) {
        handleError(e);
    }
}

function startEdit(student) {
    editing.value = student;
    errors.value = {};
    formKey.value++;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
    editing.value = null;
    errors.value = {};
    formKey.value++;
}

onMounted(fetchStudents);
</script>

<template>
    <div class="container">
        <header class="topbar">
            <h1>Student Management</h1>
        </header>

        <AlertMessage :type="alert.type" :text="alert.text" />

        <StudentForm
            :key="formKey"
            :student="editing"
            :errors="errors"
            :saving="saving"
            @submit="saveStudent"
            @cancel="resetForm"
        />

        <StudentTable
            :students="students"
            :is-admin="true"
            :loading="loading"
            @edit="startEdit"
            @delete="deleteStudent"
        />
    </div>
</template>