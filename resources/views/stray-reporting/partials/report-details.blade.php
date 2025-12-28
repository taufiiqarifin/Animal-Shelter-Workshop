<!-- Report Information Card -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="border-b border-gray-200 px-6 py-4">
        <h2 class="text-lg font-semibold text-gray-900">Report Information</h2>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2 pb-4 border-b border-gray-200">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Reported By</label>
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $rescue->report->user->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500">{{ $rescue->report->user->email ?? 'No email available' }}</p>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Location Address</label>
                <p class="text-sm text-gray-900">{{ $rescue->report->address }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">City</label>
                <p class="text-sm text-gray-900">{{ $rescue->report->city }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">State</label>
                <p class="text-sm text-gray-900">{{ $rescue->report->state }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Coordinates</label>
                <p class="text-sm text-gray-900">{{ number_format($rescue->report->latitude, 6) }}, {{ number_format($rescue->report->longitude, 6) }}</p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Description</label>
                <p class="text-sm text-gray-900">
                    @if($rescue->report->description)
                        {{ $rescue->report->description }}
                    @else
                        <span class="text-gray-400 italic">No description provided</span>
                    @endif
                </p>
            </div>

            <div class="md:col-span-2 pt-4 border-t border-gray-200">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Report Submitted</label>
                <p class="text-sm text-gray-900">{{ $rescue->report->created_at->format('M j, Y \a\t g:i A') }}</p>
            </div>
        </div>
    </div>
</div>
