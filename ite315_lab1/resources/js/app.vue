<script setup>
import { ref, onMounted } from 'vue';
import api from './api';
import AlertMessage from './components/AlertMessage.vue';
import LoginForm from './components/Login.vue';
import StudentForm from './components/StudentForm.vue';
import StudentTable from './components/StudentTable.vue';

const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const students = ref([]);
const loading = ref(false);
const saving = ref(false);
const editing = ref(null);
const formKey = ref(0);
const errors = ref({});
const loginError = ref('');
const alert = ref({ type: 'success', text: '' });

function notify(type, text) {
    alert.value = { type, text };
    setTimeout(() => (alert.value.text = ''), 4000);
}

function clearSession() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    user.value = null;
    students.value = [];
}

function handleError(e) {
    const r = e.response;
    if (r?.status === 422) {
        errors.value = r.data.errors;
        notify('error', 'invalid output');
    } else if (r?.status === 401) {
        clearSession();
        notify('error', 'Session expired. Please log in again.');
    } else {
        notify('error', r?.data?.message || 'Network error. Is the server running?');
    }
}

async function login(credentials) {
    loading.value = true;
    loginError.value = '';
    try {
        const { data } = await api.post('/login', credentials);
        localStorage.setItem('token', data.data.token);
        localStorage.setItem('user', JSON.stringify(data.data.user));
        user.value = data.data.user;
        notify('success', data.message);
        await fetchStudents();
    } catch (e) {
        loginError.value = e.response?.data?.message
            || e.response?.data?.errors?.email?.[0]
            || 'Unable to log in.';
    } finally {
        loading.value = false;
    }
}

async function logout() {
    try { await api.post('/logout'); } catch (e) {  }
    clearSession();
}


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

onMounted(() => {
    if (user.value) fetchStudents();
});
</script>

<template>
    <div class="container">
        <header class="topbar">
            <h1>Student Management</h1>
            <div v-if="user" class="userbox">
                <span>{{ user.name }}</span>
                <button class="btn" @click="logout">Logout</button>
            </div>
        </header>

        <AlertMessage :type="alert.type" :text="alert.text" />

        <LoginForm v-if="!user" :error="loginError" :loading="loading" @login="login" />

        <template v-else>
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
        </template>
    </div>
</template>