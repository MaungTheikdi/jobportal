<template>
    <AuthenticatedLayout>
        <div class="p-8">
            <h1 class="text-2xl font-bold mb-6">Employer Management</h1>

            <div class="text-right mb-4">
                <button @click="openModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add New Employer
                </button>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Company Name</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contact Person</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Website</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="4" class="text-center py-4">Loading employers...</td>
                        </tr>
                        <tr v-for="employer in employers" :key="employer.id" class="hover:bg-gray-50">
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ employer.company_name }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ employer.user ? employer.user.name : 'N/A' }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <a :href="employer.website" target="_blank" class="text-blue-600 hover:underline">{{ employer.website }}</a>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
                                <button @click="openModal(employer)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                                <button @click="deleteEmployer(employer.id)" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
                <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-2xl">
                    <h2 class="text-xl font-bold mb-4">{{ isEditing ? 'Edit Employer Profile' : 'Create Employer Profile' }}</h2>
                    <form @submit.prevent="saveEmployer">

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="company_name">Company Name</label>
                            <input v-model="form.company_name" type="text" id="company_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="user_id">Associated User ID</label>
                            <input v-model="form.user_id" type="number" id="user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-200 cursor-not-allowed" readonly required>
                            <p v-if="!isEditing" class="text-xs text-gray-500 mt-1">This profile will be linked to your user account.</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="website">Website</label>
                            <input v-model="form.website" type="url" id="website" placeholder="https://example.com" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Company Description</label>
                            <textarea v-model="form.company_description" id="description" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"></textarea>
                        </div>

                        <div class="flex items-center justify-end">
                            <button @click="closeModal" type="button" class="bg-gray-500 text-white font-bold py-2 px-4 rounded mr-2">
                                Cancel
                            </button>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Save Employer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const API_URL = '/api/employers';

export default {
    name: 'EmployersManager',
    components: {
        AuthenticatedLayout, // <-- FIX: Register the layout component
    },
    data() {
        return {
            employers: [],
            loading: true,
            showModal: false,
            isEditing: false,
            // Initialize form as an empty object. It will be populated in the created() hook.
            form: {},
        };
    },
    methods: {
        async fetchEmployers() {
            this.loading = true;
            try {
                const response = await axios.get(API_URL);
                this.employers = response.data.data;
            } catch (error) {
                console.error('Error fetching employers:', error);
            } finally {
                this.loading = false;
            }
        },
        // FIX: This method now correctly resets the form for a NEW employer
        // using the authenticated user's details from Inertia's page props.
        resetForm() {
            this.form = {
                //id: null,
                user_id: this.$page.props.auth.user.id,
                company_name: this.$page.props.auth.user.name, // Pre-fill company name with user's name
                company_description: '',
                website: '',
                address: '',
            };
        },
        openModal(employer = null) {
            if (employer) {
                // Editing an existing employer
                this.isEditing = true;
                this.form = { ...employer }; // Copy employer data to form
            } else {
                // Adding a new employer
                this.isEditing = false;
                this.resetForm(); // This will now correctly use the auth user's data
            }
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        },
        async saveEmployer() {
            try {
                if (this.isEditing) {
                    await axios.post(`/api/employer/${this.form.id}`, this.form);
                } else {
                    await axios.post(API_URL, this.form);
                }
                this.closeModal();
                this.fetchEmployers();
            } catch (error) {
                console.error('Error saving employer:', error.response.data);
                alert('Error: ' + (error.response.data.message || 'Could not save employer.'));
            }
        },
        async deleteEmployer(id) {
            if (confirm('Are you sure you want to delete this employer? This action cannot be undone.')) {
                try {
                    await axios.delete(`/api/delete/employer/${id}`, {id: id});
                    this.fetchEmployers();
                } catch (error) {
                    console.error('Error deleting employer:', error);
                    alert('Could not delete employer.');
                }
            }
        },
    },
    created() {
        // FIX: Initialize the form state when the component is created
        this.resetForm();
        // Load existing employers
        this.fetchEmployers();
    },
};
</script>