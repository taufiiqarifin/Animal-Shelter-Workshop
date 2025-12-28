<!-- Status Update Card -->
@php
    $isFinal = in_array($rescue->status, ['Success', 'Failed']);
@endphp

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="border-b border-gray-200 px-6 py-4">
        <h2 class="text-lg font-semibold text-gray-900">Update Status</h2>
    </div>
    <div class="p-6">
        @if($isFinal)
            <div class="flex items-start gap-3 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <svg class="w-5 h-5 text-gray-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <p class="text-sm text-gray-600">This rescue has been finalized and cannot be updated.</p>
            </div>
        @else
            <form id="statusForm" action="{{ route('rescues.update-status', $rescue->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="updateStatus('Scheduled')" id="statusScheduledBtn"
                            class="bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-white px-3 py-2 rounded-lg text-sm font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-amber-200"></span>
                        <span id="statusScheduledText">Scheduled</span>
                    </button>

                    <button type="button" onclick="updateStatus('In Progress')" id="statusProgressBtn"
                            class="bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white px-3 py-2 rounded-lg text-sm font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-sky-200"></span>
                        <span id="statusProgressText">In Progress</span>
                    </button>

                    <button type="button" onclick="updateStatus('Success')" id="statusSuccessBtn"
                            class="bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white px-3 py-2 rounded-lg text-sm font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-200"></span>
                        <span id="statusSuccessText">Success</span>
                    </button>

                    <button type="button" onclick="updateStatus('Failed')" id="statusFailedBtn"
                            class="bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white px-3 py-2 rounded-lg text-sm font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-rose-200"></span>
                        <span id="statusFailedText">Failed</span>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
