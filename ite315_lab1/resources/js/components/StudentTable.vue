<script setup>
defineProps({
    students: { type: Array, default: () => [] },
    isAdmin: Boolean,
    loading: Boolean,
});
const emit = defineEmits(['edit', 'delete']);
</script>

<template>
    <div class="card">
        <h2>Student Records ({{ students.length }})</h2>

        <p v-if="loading">Loading students...</p>
        <p v-else-if="students.length === 0">No students yet.</p>

        <table v-else>
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Email</th>
                    <th>Course</th><th>Year</th><th>Section</th>
                    <th v-if="isAdmin">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="s in students" :key="s.id">
                    <td>{{ s.id }}</td>
                    <td>{{ s.firstname }} {{ s.middlename }} {{ s.lastname }}</td>
                    <td>{{ s.email }}</td>
                    <td>{{ s.course }}</td>
                    <td>{{ s.year }}</td>
                    <td>{{ s.section }}</td>
                    <td v-if="isAdmin" class="actions">
                        <button class="btn" @click="emit('edit', s)">Edit</button>
                        <button class="btn danger" @click="emit('delete', s)">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>