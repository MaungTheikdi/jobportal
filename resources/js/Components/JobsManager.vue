<template>
  <div class="p-8">
    <h1 class="text-2xl font-bold mb-6">Job Listings</h1>
    
    <div class="text-right mb-4">
      <button @click="openModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Add New Job
      </button>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full leading-normal">
        <thead>
          <tr>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Title</th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Company</th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="5" class="text-center py-4">Loading...</td>
          </tr>
          <tr v-for="job in jobs" :key="job.id" class="hover:bg-gray-50">
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ job.title }}</td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ job.employer.company_name }}</td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ job.location }}</td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <span :class="getStatusClass(job.status)" class="px-2 py-1 font-semibold leading-tight rounded-full text-xs">
                {{ job.status }}
              </span>
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
              <button @click="openModal(job)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
              <button @click="deleteJob(job.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
      <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-2xl">
        <h2 class="text-xl font-bold mb-4">{{ isEditing ? 'Edit Job' : 'Add New Job' }}</h2>
        <form @submit.prevent="saveJob">
           <div class="mb-4">
             <label class="block text-gray-700 text-sm font-bold mb-2" for="title">Job Title</label>
             <input v-model="form.title" type="text" id="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
           </div>
           <div class="flex items-center justify-end">
             <button @click="showModal = false" type="button" class="bg-gray-500 text-white font-bold py-2 px-4 rounded mr-2">
               Cancel
             </button>
             <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
               Save
             </button>
           </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const jobs = ref([]);
const loading = ref(true);
const showModal = ref(false);
const isEditing = ref(false);

const form = ref({
  id: null,
  title: '',
  description: '',
  location: '',
  // Add all other relevant fields from the Job model
  // Dummy IDs for example purposes. In a real app, these would come from dropdowns.
  employer_id: 1, 
  job_category_id: 1,
  salary_min: 50000,
  currency: 'JPY',
  employment_type: 'full-time',
  status: 'open',
});

const API_URL = '/api/jobs';

// Fetch jobs from the API
const fetchJobs = async () => {
  loading.value = true;
  try {
    const response = await axios.get(API_URL);
    jobs.value = response.data.data; // Assuming pagination
  } catch (error) {
    console.error('Error fetching jobs:', error);
  } finally {
    loading.value = false;
  }
};

// Open the modal for creating or editing a job
const openModal = (job = null) => {
  if (job) {
    isEditing.value = true;
    // Copy job data to form
    form.value = { ...job };
  } else {
    isEditing.value = false;
    // Reset form for new entry
    form.value = { id: null, title: '', description: '', location: '', employer_id: 1, job_category_id: 1, salary_min: 50000, currency: 'JPY', employment_type: 'full-time', status: 'open' };
  }
  showModal.value = true;
};

// Save (create or update) a job
const saveJob = async () => {
  try {
    if (isEditing.value) {
      // Update existing job
      await axios.put(`${API_URL}/${form.value.id}`, form.value);
    } else {
      // Create new job
      await axios.post(API_URL, form.value);
    }
    showModal.value = false;
    fetchJobs(); // Refresh the list
  } catch (error) {
    console.error('Error saving job:', error);
    // You could show an error message to the user here
  }
};

// Delete a job
const deleteJob = async (id) => {
  if (confirm('Are you sure you want to delete this job?')) {
    try {
      await axios.delete(`${API_PURL}/${id}`);
      fetchJobs(); // Refresh the list
    } catch (error) {
      console.error('Error deleting job:', error);
    }
  }
};

// Helper function for status styling
const getStatusClass = (status) => {
  switch (status) {
    case 'open': return 'bg-green-100 text-green-800';
    case 'closed': return 'bg-red-100 text-red-800';
    case 'draft': return 'bg-yellow-100 text-yellow-800';
    default: return 'bg-gray-100 text-gray-800';
  }
};

// Load jobs when the component is mounted
onMounted(fetchJobs);
</script>