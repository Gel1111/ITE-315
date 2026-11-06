<script setup>
import { ref } from 'vue';

defineProps({ error: String, loading: Boolean });
const emit = defineEmits(['login']);

const email = ref('');
const password = ref('');

function submit() {
    emit('login', { email: email.value, password: password.value });
}
</script>

<template>
    <form class="card login" @submit.prevent="submit">
        <h2>Login</h2>
        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" v-model="email" required />
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" v-model="password" required />
        </div>
        <p v-if="error" class="error">{{ error }}</p>
        <button class="btn primary" :disabled="loading">
            {{ loading ? 'Logging in...' : 'Login' }}
        </button>
    </form>
</template>