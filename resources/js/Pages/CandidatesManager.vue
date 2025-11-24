<template>
	<AuthenticatedLayout>
    <div class="p-8">
        <h1 class="text-2xl font-bold mb-6">Candidate Management</h1>

        <div class="text-right mb-4">
            <button @click="openModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Candidate
            </button>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Japanese Level</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading">
                        <td colspan="4" class="text-center py-4">Loading candidates...</td>
                    </tr>
                    <tr v-for="candidate in candidates" :key="candidate.id" class="hover:bg-gray-50">
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ candidate.user ? candidate.user.name : 'N/A' }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ candidate.current_location || 'Not specified' }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                <span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                <span class="relative">{{ candidate.japanese_level }}</span>
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
                            <button @click="openModal(candidate)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                            <button @click="deleteCandidate(candidate.id)" class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
            <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-2xl">
                <h2 class="text-xl font-bold mb-4">{{ isEditing ? 'Edit Candidate Profile' : 'Create Candidate Profile' }}</h2>
                <form @submit.prevent="saveCandidate">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="user_id">User ID</label>
                            <input v-model="form.user_id" type="number" id="user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" :disabled="isEditing" required>
                            <p v-if="!isEditing" class="text-xs text-gray-500 mt-1">Assign a user ID. Cannot be changed later.</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="japanese_level">Japanese Level</label>
                            <select v-model="form.japanese_level" id="japanese_level" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                                <option>None</option>
                                <option>N5</option>
                                <option>N4</option>
                                <option>N3</option>
                                <option>N2</option>
                                <option>N1</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="date_of_birth">Date of Birth</label>
                            <input v-model="form.date_of_birth" type="date" id="date_of_birth" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        </div>

                        <div class="mb-4">
                             <label class="block text-gray-700 text-sm font-bold mb-2" for="gender">Gender</label>
                             <select v-model="form.gender" id="gender" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                                <option :value="null">-- Select --</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                         <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="nationality">Nationality</label>
                            <input v-model="form.nationality" type="text" id="nationality" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        </div>

                         <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="current_location">Current Location</label>
                            <input v-model="form.current_location" type="text" id="current_location" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <button @click="closeModal" type="button" class="bg-gray-500 text-white font-bold py-2 px-4 rounded mr-2">
                            Cancel
                        </button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Save Candidate
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

const API_URL = '/api/candidates';

export default {
    name: 'CandidatesManager',
	components: {
		AuthenticatedLayout,
	},
    data() {
        return {
            candidates: [],
            loading: true,
            showModal: false,
            isEditing: false,
            form: {
                id: null,
                user_id: this.$page.props.auth.user.id,
                date_of_birth: null,
                nationality: '',
                gender: null,
                resume_path: null,
                visa_status: '',
                japanese_level: 'None',
                current_location: '',
            },
        };
    },
    methods: {
        async fetchCandidates() {
            this.loading = true;
            try {
                const response = await axios.get(API_URL);
                this.candidates = response.data.data;
            } catch (error) {
                console.error('Error fetching candidates:', error);
                alert('Could not fetch candidates.');
            } finally {
                this.loading = false;
            }
        },
        resetForm() {
            this.form = {
                id: null,
                user_id: this.$page.props.auth.user.id,
                date_of_birth: null,
                nationality: '',
                gender: null,
                resume_path: null,
                visa_status: '',
                japanese_level: 'None',
                current_location: '',
            };
        },
        openModal(candidate = null) {
            if (candidate) {
                this.isEditing = true;
                this.form = { ...candidate };
            } else {
                this.isEditing = false;
                this.resetForm();
            }
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        },
        async saveCandidate() {
            try {
                if (this.isEditing) {
                    await axios.put(`${API_URL}/${this.form.id}`, this.form);
                } else {
                    await axios.post(API_URL, this.form);
                }
                this.closeModal();
                this.fetchCandidates();
            } catch (error) {
                console.error('Error saving candidate:', error.response.data);
                const errorMessages = Object.values(error.response.data.errors).flat().join('\n');
                alert('Error: \n' + errorMessages);
            }
        },
        async deleteCandidate(id) {
            if (confirm('Are you sure you want to delete this candidate?')) {
                try {
                    await axios.delete(`${API_URL}/${id}`);
                    this.fetchCandidates();
                } catch (error) {
                    console.error('Error deleting candidate:', error);
                    alert('Could not delete the candidate.');
                }
            }
        },
    },
    created() {
        this.fetchCandidates();
    },
};
</script>