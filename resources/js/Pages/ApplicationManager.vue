<template>
	<AuthenticatedLayout>
    <div class="p-8">
        <h1 class="text-2xl font-bold mb-6">Application Management</h1>

        <div class="flex space-x-4 mb-6 bg-white p-4 rounded-lg shadow">
            <div class="w-1/2">
                <label for="job_filter" class="block text-sm font-medium text-gray-700">Filter by Job</label>
                <select v-model="filters.job_id" id="job_filter" @change="fetchApplications" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">All Jobs</option>
                    <option v-for="job in jobs" :key="job.id" :value="job.id">{{ job.title }}</option>
                </select>
            </div>
            <div class="w-1/2">
                <label for="status_filter" class="block text-sm font-medium text-gray-700">Filter by Status</label>
                <select v-model="filters.status" id="status_filter" @change="fetchApplications" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="reviewed">Reviewed</option>
                    <option value="accepted">Accepted</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Candidate</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Job Title</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Applied On</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading">
                        <td colspan="5" class="text-center py-4">Loading applications...</td>
                    </tr>
                    <tr v-for="app in applications" :key="app.id" class="hover:bg-gray-50">
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ app.candidate.user.name }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ app.job.title }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ formatDate(app.applied_at) }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span :class="getStatusClass(app.status)" class="px-2 py-1 font-semibold leading-tight rounded-full text-xs">
                                {{ app.status }}
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
                            <button @click="openModal(app)" class="text-indigo-600 hover:text-indigo-900">View / Update</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-10 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center">
            <div v-if="selectedApplication" class="bg-white p-8 rounded-lg shadow-xl w-full max-w-2xl m-4">
                <h2 class="text-xl font-bold mb-2">Application Details</h2>
                <p class="text-sm text-gray-600 mb-4">
                    <strong>{{ selectedApplication.candidate.user.name }}</strong> for <strong>{{ selectedApplication.job.title }}</strong>
                </p>

                <div class="border-t pt-4">
                    <h3 class="font-semibold text-gray-800">Cover Letter</h3>
                    <p class="mt-2 p-3 bg-gray-50 rounded-md text-gray-700 whitespace-pre-wrap">{{ selectedApplication.cover_letter || 'No cover letter submitted.' }}</p>
                </div>

                <div class="mt-6">
                    <label for="status_update" class="block text-sm font-medium text-gray-700">Update Status</label>
                    <select v-model="selectedApplication.status" id="status_update" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="pending">Pending</option>
                        <option value="reviewed">Reviewed</option>
                        <option value="accepted">Accepted</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div class="mt-6 flex justify-end space-x-4">
                    <button @click="closeModal" class="bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded">Cancel</button>
                    <button @click="updateApplicationStatus" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" :disabled="isUpdating">
                        {{ isUpdating ? 'Saving...' : 'Save Changes' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
	</AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

export default {
    name: 'ApplicationManage',
	components: {
		AuthenticatedLayout,
	},
    data() {
        return {
            applications: [],
            jobs: [], // For the filter dropdown
            loading: true,
            isUpdating: false,
            showModal: false,
            selectedApplication: null,
            filters: {
                job_id: '',
                status: '',
            },
        };
    },
    methods: {
        async fetchApplications() {
            this.loading = true;
            try {
                // Pass filters as URL parameters
                const response = await axios.get('/api/applications', { params: this.filters });
                this.applications = response.data.data;
				console.log("fetchApplications", this.applications);
            } catch (error) {
                console.error('Error fetching applications:', error);
                alert('Could not fetch applications.');
            } finally {
                this.loading = false;
            }
        },
        async fetchJobs() {
            try {
                // Fetch all jobs to populate the filter dropdown
                const response = await axios.get('/api/jobs');
                this.jobs = response.data.data;
            } catch (error) {
                console.error('Error fetching jobs:', error);
            }
        },
        openModal(application) {
            // Create a copy to avoid modifying the original list directly
            this.selectedApplication = { ...application };
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.selectedApplication = null;
        },
        async updateApplicationStatus() {
            if (!this.selectedApplication) return;
            this.isUpdating = true;
            try {
                const payload = { status: this.selectedApplication.status };
                await axios.put(`/api/applications/${this.selectedApplication.id}`, payload);
                alert('Status updated successfully!');
                this.closeModal();
                this.fetchApplications(); // Refresh the list
            } catch (error) {
                console.error('Error updating status:', error);
                alert('Could not update application status.');
            } finally {
                this.isUpdating = false;
            }
        },
        formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        },
        getStatusClass(status) {
            const classes = {
                pending: 'bg-yellow-100 text-yellow-800',
                reviewed: 'bg-blue-100 text-blue-800',
                accepted: 'bg-green-100 text-green-800',
                rejected: 'bg-red-100 text-red-800',
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
    },
    created() {
        this.fetchApplications();
        this.fetchJobs();
    },
};
</script>