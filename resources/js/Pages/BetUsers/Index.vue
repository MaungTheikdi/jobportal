<template>

	<Head title="Users" />
	<AuthenticatedLayout>
		<template #header>
			<div class="py-6">
				<div class="flex justify-between items-center mb-6">
					<h1 class="text-3xl font-bold text-gray-800">Users</h1>
					<button @click="showCreate = true"
						class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20"
							fill="currentColor">
							<path fill-rule="evenodd"
								d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
								clip-rule="evenodd" />
						</svg>
						Add User
					</button>
				</div>

				<div class="bg-white shadow-md rounded-lg overflow-hidden">
					<table class="min-w-full divide-y divide-gray-200">
						<thead class="bg-gray-50">
							<tr>
								<th scope="col"
									class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									User ID
								</th>
								<th scope="col"
									class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									User Name
								</th>
								<th scope="col"
									class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									Password
								</th>
								<th scope="col"
									class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									Balance
								</th>
								<th scope="col"
									class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									Created At
								</th>
								<th scope="col"
									class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									Action
								</th>
							</tr>
						</thead>
						<tbody class="bg-white divide-y divide-gray-200">
							<tr v-for="(user, index) in users" :key="user.user_id"
								:class="{ 'bg-gray-50': index % 2 === 0 }">
								<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
									{{ user.user_id }}
								</td>
								<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
									<span v-if="editId !== user.user_id">
										{{ user.username }}
									</span>
									<input v-else v-model="edit_username" type="text"
										class="border border-gray-300 rounded-md px-2 py-1 text-sm w-full max-w-xs" />
								</td>
								<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
									<span v-if="editId !== user.user_id">
										{{ user.password_hash }}
									</span>
									<input v-else v-model="edit_password_hash" type="text"
										class="border border-gray-300 rounded-md px-2 py-1 text-sm w-full max-w-xs" />
								</td>
								<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
									<span v-if="editId !== user.balance">
										{{ user.balance }}
									</span>
									<input v-else v-model="edit_balance" type="text"
										class="border border-gray-300 rounded-md px-2 py-1 text-sm w-full max-w-xs" />
								</td>
								<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
									{{ user.created_at }}
								</td>

								<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
									<button v-if="editId !== user.user_id" @click="editUser(user)"
										class="text-indigo-600 hover:text-indigo-900 mr-3">
										<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
											fill="currentColor">
											<path
												d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
										</svg>
									</button>
									<button v-else @click="updateUser(user.user_id)"
										class="text-green-600 hover:text-green-900 mr-3">
										<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
											fill="currentColor">
											<path fill-rule="evenodd"
												d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
												clip-rule="evenodd" />
										</svg>
									</button>
									<button @click="deleteUser(user.user_id)"
										class="text-red-600 hover:text-red-900">
										<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
											fill="currentColor">
											<path fill-rule="evenodd"
												d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
												clip-rule="evenodd" />
										</svg>
									</button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- Empty State -->
				<div v-if="users.length === 0" class="text-center py-12">
					<svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none"
						viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
					</svg>
					<h3 class="mt-2 text-sm font-medium text-gray-900">No Users</h3>
					<p class="mt-1 text-sm text-gray-500">Get started by creating a new user.</p>
					<div class="mt-6">
						<button @click="showCreate = true"
							class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
							<svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20"
								fill="currentColor">
								<path fill-rule="evenodd"
									d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
									clip-rule="evenodd" />
							</svg>
							New User
						</button>
					</div>
				</div>


				<!-- Create User Modal -->
				<div v-if="showCreate" class="fixed z-10 inset-0 overflow-y-auto">
					<div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
						<div class="fixed inset-0 transition-opacity" aria-hidden="true">
							<div class="absolute inset-0 bg-gray-500 opacity-75"></div>
						</div>
						<span class="hidden sm:inline-block sm:align-middle sm:h-screen"
							aria-hidden="true">&#8203;</span>
						<div
							class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
							<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
								<div class="sm:flex sm:items-start">
									<div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
										<h3 class="text-lg leading-6 font-medium text-gray-900">Create New User</h3>
										<div class="mt-4">
											<!-- <input 
												v-model="newMatch" 
												placeholder="Enter match name"
												class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
												@keyup.enter="createMatch" 
											/> -->
											<input 
												v-model="username" 
												type="text"
												class="mt-2 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
												placeholder="User Name" />
											<input 
												v-model="password_hash" 
												type="text"
												class="mt-2 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
												placeholder="Password" />
											<input 
												v-model="balance" 
												type="number"
												class="mt-2 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
												placeholder="Balance" />
										</div>
									</div>
								</div>
							</div>
							<div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
								<button @click="createUser" type="button"
									class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
									Create
								</button>
								<button @click="showCreate = false" type="button"
									class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
									Cancel
								</button>
							</div>
						</div>
					</div>
				</div>


			</div>
		</template>
	</AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import axios from 'axios'

export default {
	components: {
		Head,
		Link,
		AuthenticatedLayout
	},

	data() {
		return {
			
			users: [],
			showCreate: false,
			
			username: '',
			password_hash: '',
			balance: null,
			created_at: new Date().toISOString(),

			editId: null,
			edit_username: '',
			edit_password_hash: '',
			edit_balance: null,
		}
	},
	methods: {
		fetchUsers() {
			axios.get('api/get/bet_users').then(res => {
				this.users = res.data;
			})
		},
		createUser() {
			axios.post('create/bet_users', {
				username: this.username,
				password: this.password_hash,
				balance: this.balance
			}).then(() => {
					this.showCreate = false
					this.fetchUsers()
			})
		},

		

		editUser(user){
			this.editId = user.user_id;
			this.username = user.username;
			this.password_hash = user.password_hash;
			this.balance = user.balance;
		},

		updateUser(user_id) {
			axios.put(`/api/update/bet_users/${user_id}`,
				{
					username: this.edit_username,
					password: this.edit_password_hash
				}
			).then(() => {
				this.editId = null
				this.edit_username = ''
				this.edit_password_hash = ''
				this.fetchUsers()
			})
		},
		deleteUser(id){
			if (confirm('Are you sure you want to delete this user?')) {
				axios.delete(`/api/delete/bet_users/${id}`).then(() => {
					this.fetchUsers()
				})
			}
		},
	},
	mounted() {
		this.fetchUsers();
	}
}
</script>