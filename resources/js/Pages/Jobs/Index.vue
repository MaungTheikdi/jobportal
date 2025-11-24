<template>
    <AuthenticatedLayout>
        <div class="bg-gray-50 min-h-screen">
            <div class="py-12 bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Find Your Next Opportunity</h1>
                    <p class="mt-2 text-lg text-gray-600">Browse through the latest job openings available now.</p>
                    
                    <div class="mt-6">
                        <input 
                            v-model="searchTerm"
                            type="text"
                            placeholder="Search by job title or company..."
                            class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        />
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div v-if="loading" class="text-center text-gray-500">
                    <p>Loading jobs...</p>
                </div>
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="job in filteredJobs" :key="job.id" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 flex flex-col">
                        <div class="flex-grow">
                            <h2 class="text-xl font-semibold text-gray-800">{{ job.title }}</h2>
                            <p class="text-md text-gray-600 mt-1">{{ job.employer.company_name }}</p>
                            <div class="mt-4 space-y-2 text-sm text-gray-500">
                                <p class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                                    {{ job.location }}
                                </p>
                                <p class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path d="M8.433 7.418c.158-.103.346-.196.567-.267v1.698a2.5 2.5 0 004 0V7.15c.22.071.409.164.567.267C13.863 7.518 14 7.738 14 8c0 .262-.137.482-.367.582a1 1 0 01-.567.267v1.698a2.5 2.5 0 004 0V8.849c.22.071.409.164.567.267C18.863 9.218 19 9.438 19 9.7c0 .262-.137.482-.367.582a1 1 0 01-.567.267v1.698a2.5 2.5 0 004 0V10.55c.22.071.409.164.567.267C23.863 10.918 24 11.138 24 11.4c0 .262-.137.482-.367.582a1 1 0 01-.567.267v1.517a4.5 4.5 0 01-9 0v-1.517a1 1 0 01-.567-.267C13.137 11.882 13 11.662 13 11.4c0-.262.137-.482.367-.582a1 1 0 01.567-.267V9.7a2.5 2.5 0 00-4 0v1.149c-.22-.071-.409-.164-.567-.267C8.137 10.182 8 9.962 8 9.7c0-.262.137-.482.367-.582A1 1 0 019 8.849V7.151a2.5 2.5 0 00-4 0v1.698c.22.071.409.164.567.267C5.863 9.218 6 9.438 6 9.7c0 .262-.137.482-.367.582A1 1 0 015 10.55V8.849a2.5 2.5 0 00-4 0v1.698c.22.071.409.164.567.267C1.863 10.918 2 11.138 2 11.4c0 .262-.137.482-.367.582A1 1 0 011 12.249V10.7a4.5 4.5 0 019 0v1.549a1 1 0 01-.567.267C8.137 12.618 8 12.838 8 13.1c0 .262.137.482.367.582a1 1 0 01.567.267v1.517a4.5 4.5 0 01-9 0v-1.517a1 1 0 01-.567-.267C-.863 13.582-1 13.362-1 13.1c0-.262.137-.482.367-.582a1 1 0 01.567-.267V11.4a2.5 2.5 0 00-4 0V9.7c-.22-.071-.409-.164-.567-.267C-3.863 9.218-4 8.998-4 8.7c0-.262.137-.482.367-.582A1 1 0 01-3 7.849V6.151a2.5 2.5 0 00-4 0v1.698c.22.071.409.164.567.267C-5.863 8.218-6 8.438-6 8.7c0 .262-.137.482-.367.582A1 1 0 01-7 9.55V7.849a4.5 4.5 0 019 0v1.7z" /></svg>
                                    {{ formatCurrency(job.salary_min) }} - {{ formatCurrency(job.salary_max) }} {{ job.currency }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button @click="viewJobDetails(job)" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition-colors">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="!loading && filteredJobs.length === 0" class="text-center text-gray-500 mt-10">
                    <p>No jobs found matching your search criteria.</p>
                </div>
            </div>
        </div>

        <div v-if="showDetailModal" class="fixed inset-0 z-10 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center">
            <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-3xl m-4" @click.stop>
                <div class="flex justify-between items-start">
                    <div>
						<p>{{ selectedJob.id }}</p>
                        <h2 class="text-2xl font-bold text-gray-900">{{ selectedJob.title }}</h2>
                        <p class="text-lg text-gray-600">{{ selectedJob.employer.company_name }} - {{ selectedJob.location }}</p>
                    </div>
                    <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <div class="mt-4 pt-4 border-t prose max-w-none" v-html="selectedJob.description"></div>
                <div class="mt-6 flex justify-end space-x-4">
                    <button @click="closeDetailModal" class="bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded">Close</button>
                    <button @click="openApplyModal" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">Apply Now</button>
                </div>
            </div>
        </div>
        
        <div v-if="showApplyModal" class="fixed inset-0 z-20 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center">
            <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-lg m-4" @click.stop>
                <h2 class="text-xl font-bold mb-4">Apply for: {{ selectedJob.title }}</h2>
                <form @submit.prevent="submitApplication">
                    <div class="mb-4">
                        <label for="cover_letter" class="block text-gray-700 text-sm font-bold mb-2">Cover Letter (Optional)</label>
                        <textarea v-model="applicationForm.cover_letter" id="cover_letter" rows="8" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>
                    <div class="flex items-center justify-end">
                        <button @click="closeApplyModal" type="button" class="bg-gray-500 text-white font-bold py-2 px-4 rounded mr-2" :disabled="isApplying">Cancel</button>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded flex items-center" :disabled="isApplying">
                            <span v-if="isApplying">Submitting...</span>
                            <span v-else>Submit Application</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

export default {
    name: 'JobIndex',
    components: {
        AuthenticatedLayout,
    },
    data() {
        return {
            jobs: [],
            loading: true,
            searchTerm: '',
            showDetailModal: false,
            showApplyModal: false,
            selectedJob: null,
            applicationForm: {
                cover_letter: '',
            },
            isApplying: false,
        };
    },
    computed: {
        candidateId() {
            // Access candidate ID from Inertia's shared props
            // The structure might vary based on your backend setup
            return this.$page.props.auth.user?.candidate?.id || 1;
        },
        filteredJobs() {
            if (!this.searchTerm) {
                return this.jobs;
            }
            const lowerSearchTerm = this.searchTerm.toLowerCase();
            return this.jobs.filter(job => 
                job.title.toLowerCase().includes(lowerSearchTerm) ||
                job.employer.company_name.toLowerCase().includes(lowerSearchTerm)
            );
        },
    },
    methods: {
        async fetchJobs() {
            this.loading = true;
            try {
                // Fetch only 'open' jobs
                const response = await axios.get('/api/jobs', { params: { status: 'open' } });
                this.jobs = response.data.data;
                console.log('Jobs fetched:', JSON.stringify(this.jobs));
            } catch (error) {
                console.error('Error fetching jobs:', error);
                alert('Failed to load job listings.');
            } finally {
                this.loading = false;
            }
        },
        formatCurrency(value) {
            if (value === null || isNaN(value)) return 'N/A';
            return new Intl.NumberFormat('ja-JP').format(value);
        },
        viewJobDetails(job) {
            this.selectedJob = job;
            this.showDetailModal = true;
        },
        closeDetailModal() {
            this.showDetailModal = false;
            this.selectedJob = null;
        },
        openApplyModal() {
            if (!this.candidateId) {
                alert('You must have a candidate profile to apply for jobs.');
                return;
            }
            this.showDetailModal = false; // Close detail modal
            this.showApplyModal = true;
        },
        closeApplyModal() {
            this.showApplyModal = false;
            this.applicationForm.cover_letter = ''; // Reset form
            this.selectedJob = null; // Clear selected job
        },
        async submitApplication() {
            this.isApplying = true;
            const payload = {
                job_id: this.selectedJob.id,
                candidate_id: this.candidateId,
                cover_letter: this.applicationForm.cover_letter,
            };

            try {
                await axios.post('/api/applications', payload);
                alert('Application submitted successfully!');
                this.closeApplyModal();
            } catch (error) {
                console.error('Error submitting application:', error.response?.data);
                const errorMessage = error.response?.data?.message || 'Failed to submit application. You may have already applied for this job.';
                alert(`Error: ${errorMessage}`);
            } finally {
                this.isApplying = false;
            }
        },
    },
    created() {
        this.fetchJobs();
    },
};
</script>