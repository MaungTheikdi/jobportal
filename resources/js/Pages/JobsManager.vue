<template>
    <AuthenticatedLayout>
    <div class="p-8">
        <h1 class="text-2xl font-bold mb-6">Job Listings</h1>

        <div class="text-right mb-4">
            <button
                @click="openModal()"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
            >
                Add New Job
            </button>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                        >
                            Title
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                        >
                            Company
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                        >
                            Location
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                        >
                            Status
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"
                        ></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading">
                        <td colspan="5" class="text-center py-4">Loading...</td>
                    </tr>
                    <tr
                        v-for="job in jobs"
                        :key="job.id"
                        class="hover:bg-gray-50"
                    >
                        <td
                            class="px-5 py-5 border-b border-gray-200 bg-white text-sm"
                        >
                            {{ job.title }}
                        </td>
                        <td
                            class="px-5 py-5 border-b border-gray-200 bg-white text-sm"
                        >
                            {{ job.employer.company_name }}
                        </td>
                        <td
                            class="px-5 py-5 border-b border-gray-200 bg-white text-sm"
                        >
                            {{ job.location }}
                        </td>
                        <td
                            class="px-5 py-5 border-b border-gray-200 bg-white text-sm"
                        >
                            <span
                                :class="getStatusClass(job.status)"
                                class="px-2 py-1 font-semibold leading-tight rounded-full text-xs"
                            >
                                {{ job.status }}
                            </span>
                        </td>
                        <td
                            class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right"
                        >
                            <button
                                @click="openModal(job)"
                                class="text-indigo-600 hover:text-indigo-900 mr-4"
                            >
                                Edit
                            </button>
                            <button
                                @click="deleteJob(job.id)"
                                class="text-red-600 hover:text-red-900"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="showModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center"
        >
            <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-2xl">
                <h2 class="text-xl font-bold mb-4">
                    {{ isEditing ? "Edit Job" : "Add New Job" }}
                </h2>
                <form @submit.prevent="saveJob">
                    <div class="mb-4">
                        <label
                            class="block text-gray-700 text-sm font-bold mb-2"
                            for="title"
                            >Job Title</label
                        >
                        <input
                            v-model="form.title"
                            type="text"
                            id="title"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                            required
                        />
                    </div>
                    <div class="flex items-center justify-end">
                        <button
                            @click="showModal = false"
                            type="button"
                            class="bg-gray-500 text-white font-bold py-2 px-4 rounded mr-2"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                        >
                            Save
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
import axios from "axios";

export default {
    name: 'JobListings',
    components: {
        AuthenticatedLayout,
    },
    data() {
        return {
            jobs: [],
            loading: true,
            showModal: false,
            isEditing: false,
            form: {
                id: null,
				title: "",
				description: "Desc",
				location: "Loc",
				employer_id: 1,
				job_category_id: 1,
				salary_min: 50000,
				currency: "JPY",
				employment_type: "full-time",
				status: "open",
            },
            API_URL: "/api/jobs"
        }
    },
    mounted() {
        this.fetchJobs();
    },
    methods: {
        // Fetch jobs from the API
        async fetchJobs() {
            this.loading = true;
            try {
                const response = await axios.get(this.API_URL);
                this.jobs = response.data.data; // Assuming pagination
            } catch (error) {
                console.error("Error fetching jobs:", error);
            } finally {
                this.loading = false;
            }
        },

        // Open the modal for creating or editing a job
        openModal(job = null) {
            if (job) {
                this.isEditing = true;
                // Copy job data to form
                this.form = { ...job };
            } else {
                this.isEditing = false;
                // Reset form for new entry
                this.form = {
                    id: null,
                    title: "",
                    description: "Desc",
                    location: "Loc",
                    employer_id: 1,
                    job_category_id: 1,
                    salary_min: 50000,
                    currency: "JPY",
                    employment_type: "full-time",
                    status: "open",
                };
            }
            this.showModal = true;
        },

        // Save (create or update) a job
        async saveJob() {
            try {
                if (this.isEditing) {
                    // Update existing job
                    await axios.put(`${this.API_URL}/${this.form.id}`, this.form);
                } else {
                    // Create new job
                    await axios.post(this.API_URL, this.form);
                }
                this.showModal = false;
                this.fetchJobs(); // Refresh the list
            } catch (error) {
                console.error("Error saving job:", error);
                // You could show an error message to the user here
            }
        },

        // Delete a job
        async deleteJob(id) {
            if (confirm("Are you sure you want to delete this job?")) {
                try {
                    await axios.delete(`${this.API_URL}/${id}`);
                    this.fetchJobs(); // Refresh the list
                } catch (error) {
                    console.error("Error deleting job:", error);
                }
            }
        },

        // Helper function for status styling
        getStatusClass(status) {
            switch (status) {
                case "open":
                    return "bg-green-100 text-green-800";
                case "closed":
                    return "bg-red-100 text-red-800";
                case "draft":
                    return "bg-yellow-100 text-yellow-800";
                default:
                    return "bg-gray-100 text-gray-800";
            }
        }
    }
}
</script>