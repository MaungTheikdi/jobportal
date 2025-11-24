<template>
    <Head title="Transfers" />

    <AuthenticatedLayout>
        <template #header>
            <div class="max-w-6xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Transfers</h1>
                    <Link href="/transfers/create" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Create Transfer
                    </Link>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-4 border-b flex justify-between items-center bg-gray-50">
                        <div class="relative w-64">
                            <input type="text" placeholder="Search transfers..." class="pl-10 pr-4 py-2 w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-2.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex space-x-2">
                            <button class="dt-button bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-50 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Copy
                            </button>
                            <button class="dt-button bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-50 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Excel
                            </button>
                            <button class="dt-button bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-50 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                PDF
                            </button>
                            <button class="dt-button bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-50 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Print
                            </button>
                        </div>
                    </div>
                    
                    <DataTable
                        :options="{
                            dom: 'Bfrtip',
                            buttons: ['copy', 'excel', 'pdf', 'print'],
                            language: {
                                search: '',
                                searchPlaceholder: 'Search...',
                                lengthMenu: 'Show _MENU_ entries',
                                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                                paginate: {
                                    previous: '←',
                                    next: '→'
                                }
                            },
                            responsive: true,
                            pageLength: 10,
                            fixedHeader: true
                        }"
                        :data="transfers.data" 
                        :columns="columns" 
                        class="display w-full text-sm text-left text-gray-700"
                    />
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

// DataTables imports
import jszip from 'jszip'
import pdfmake from 'pdfmake/build/pdfmake'
import DataTablesCore from 'datatables.net'
import DataTable from 'datatables.net-vue3'

import 'datatables.net-buttons-dt'
import 'datatables.net-buttons/js/buttons.colVis.mjs'
import 'datatables.net-buttons/js/buttons.html5.mjs'
import 'datatables.net-buttons/js/buttons.print.mjs'
import 'datatables.net-fixedcolumns-dt'
import 'datatables.net-fixedheader-dt'
import 'datatables.net-responsive-dt'
import 'datatables.net-searchbuilder-dt'
import 'datatables.net-searchpanes-dt'
import 'datatables.net-select-dt'

// Register plugins
DataTablesCore.Buttons.jszip(jszip)
DataTablesCore.Buttons.pdfMake(pdfmake)
DataTable.use(DataTablesCore)

// Props from controller
const props = defineProps({ transfers: Object })

// DataTable column definitions
const columns = ref([
  { 
    title: 'Invoice', 
    data: 'invoice_no',
    className: 'font-medium text-gray-900 whitespace-nowrap'
  },
  { 
    title: 'Sender', 
    data: 'sender_name',
    className: 'text-gray-700'
  },
  { 
    title: 'Receiver', 
    data: 'receiver_name',
    className: 'text-gray-700'
  },
  { 
    title: 'MMK', 
    data: 'mmk',
    className: 'text-right font-medium',
    render: function(data) {
      return data ? parseFloat(data).toLocaleString() + ' MMK' : '-'
    }
  },
  { 
    title: 'Ringgit', 
    data: 'ringgit',
    className: 'text-right font-medium',
    render: function(data) {
      return data ? parseFloat(data).toLocaleString() + ' RM' : '-'
    }
  },
  {
    title: 'Actions',
    data: null,
    className: 'text-right',
    render: function (data, type, row) {
      return `
        <div class="flex justify-end space-x-3">
          <a href="/transfers/${row.id}/edit" class="action-btn text-blue-600 hover:text-blue-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
            </svg>
          </a>
          <button onclick="deleteTransfer(${row.id})" class="action-btn text-red-600 hover:text-red-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      `
    }
  }
])

// Delete handler
window.deleteTransfer = function (id) {
  if (confirm('Are you sure you want to delete this transfer?')) {
    router.delete(`/transfers/delete/${id}`)
  }
}
</script>

<style>
@import 'datatables.net-dt';
@import 'datatables.net-buttons-dt';
@import 'datatables.net-responsive-dt';
@import 'datatables.net-fixedheader-dt';

.btn-primary {
  background-color: #3b82f6;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  transition: background-color 0.2s;
}
.btn-primary:hover {
  background-color: #2563eb;
}

/* DataTable styling */
.dataTables_wrapper {
  padding: 0;
}

.dataTables_wrapper .dataTables_filter {
  padding: 0.5rem 1rem;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_info {
  padding: 1rem;
}

.dataTables_wrapper .dataTables_paginate {
  padding: 0.75rem 1rem;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
  padding: 0.25rem 0.75rem;
  margin: 0 0.125rem;
  border-radius: 0.25rem;
  border: 1px solid #e5e7eb;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
  background: #3b82f6;
  color: white !important;
  border: 1px solid #3b82f6;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
  background: #e5e7eb;
  border: 1px solid #e5e7eb;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
  background: #2563eb;
  border: 1px solid #2563eb;
}

/* Table styling */
table.dataTable {
  width: 100% !important;
  border-collapse: collapse;
}

table.dataTable thead th {
  background-color: #f9fafb;
  padding: 0.75rem 1rem;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

table.dataTable tbody td {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #e5e7eb;
  vertical-align: middle;
}

table.dataTable tbody tr:hover td {
  background-color: #f8fafc;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.25rem;
  border-radius: 0.25rem;
  transition: all 0.2s;
}

.action-btn:hover {
  background-color: #f1f5f9;
}

.dt-button {
  transition: all 0.2s;
}
</style>