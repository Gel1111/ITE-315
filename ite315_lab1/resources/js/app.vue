<script setup>
import { ref, computed, onMounted } from 'vue';
import api from './api';
import AlertMessage from './components/AlertMessage.vue';
import StudentForm from './components/StudentForm.vue';
import StudentTable from './components/StudentTable.vue';
import LoginForm from './components/Login.vue'; 

const students = ref([]);
const loading = ref(false);
const saving = ref(false);
const editing = ref(null);
const formKey = ref(0);
const errors = ref({});
const alert = ref({ type: 'success', text: '' });

const token = ref(localStorage.getItem('token'));
const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const loginLoading = ref(false);
const loginError = ref('');
const isAdmin = computed(() => user.value?.role === 'admin');

function notify(type, text) {
    alert.value = { type, text };
    setTimeout(() => (alert.value.text = ''), 4000);
}

function clearSession() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    token.value = null;
    user.value = null;
    students.value = [];
    resetForm();
}

function handleError(e) {
    const r = e.response;
    if (r?.status === 401) {
        clearSession();
        notify('error', 'Session expired. Please log in again.');
    } else if (r?.status === 422) {
        errors.value = r.data.errors;
        notify('error', 'Check input and try again.');
    } else {
        notify('error', r?.data?.message || 'Network error. Is the server running?');
    }
}

async function handleLogin(credentials) {
    loginLoading.value = true;
    loginError.value = '';
    try {
        const { data } = await api.post('/login', credentials);
        token.value = data.data.token;
        user.value = data.data.user;
        localStorage.setItem('token', token.value);
        localStorage.setItem('user', JSON.stringify(user.value));
        notify('success', data.message);
        await fetchStudents();
    } catch (e) {
        loginError.value = e.response?.data?.message || 'Network error. Is the server running?';
    } finally {
        loginLoading.value = false;
    }
}

async function handleLogout() {
    try {
        await api.post('/logout');
    } catch (e) {
    } finally {
        clearSession();
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

onMounted(() => {
    if (token.value) fetchStudents();
});
</script>

<template>
    <div class="container">
        <header class="topbar">
            <h1>Student Management</h1>
            <div v-if="token && user">
                <span>{{ user.name }} ({{ user.role }})</span>
                <button class="btn" @click="handleLogout">Logout</button>
            </div>
        </header>

        <AlertMessage :type="alert.type" :text="alert.text" />

        <LoginForm
            v-if="!token"
            :error="loginError"
            :loading="loginLoading"
            @login="handleLogin"
        />

        <template v-else>
            <StudentForm
                v-if="isAdmin"
                :key="formKey"
                :student="editing"
                :errors="errors"
                :saving="saving"
                @submit="saveStudent"
                @cancel="resetForm"
            />

            <StudentTable
                :students="students"
                :is-admin="isAdmin"
                :loading="loading"
                @edit="startEdit"
                @delete="deleteStudent"
            />
        </template>
    </div>
</template>