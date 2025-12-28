<!-- Admin Actions -->
<a href="{{ route('admin.audit.index') }}"
   class="flex items-center justify-center gap-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-semibold px-5 py-3.5 rounded-lg shadow-md hover:from-indigo-700 hover:to-indigo-800 hover:shadow-lg transition-all duration-200 group w-full">
    <svg class="w-5 h-5 flex-shrink-0 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
    </svg>
    <span class="whitespace-nowrap flex-1 text-center">View Audit Logs</span>
    <svg class="w-4 h-4 flex-shrink-0 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
    </svg>
</a>

<button onclick="openCaretakerModal()"
        class="flex items-center justify-center gap-3 bg-gradient-to-r from-teal-600 to-teal-700 text-white font-semibold px-5 py-3.5 rounded-lg shadow-md hover:from-teal-700 hover:to-teal-800 hover:shadow-lg transition-all duration-200 group w-full">
    <svg class="w-5 h-5 flex-shrink-0 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
    </svg>
    <span class="whitespace-nowrap flex-1 text-center">Add New Caretaker</span>
    <svg class="w-4 h-4 flex-shrink-0 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
    </svg>
</button>
