<!-- VISIT LIST MODAL -->
<div id="visitModal"
     class="fixed inset-0 hidden bg-black/40 backdrop-blur-md z-50 flex items-center justify-center p-4 transition-opacity">

    <div id="visitModalContent"
         class="bg-white max-w-3xl w-full rounded-2xl shadow-xl overflow-y-auto max-h-[90vh]
                opacity-0 scale-95 transform transition-all duration-300 p-6">

        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Your Visit List</h1>
            <button onclick="closeVisitModal()" class="text-gray-600 text-2xl hover:text-black">&times;</button>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 p-4 rounded-xl mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($animals->isEmpty())
            <div class="bg-gray-100 p-6 rounded-xl text-center">
                <p class="text-gray-600">Your visit list is empty.</p>
            </div>

            <div class="mt-4 text-right">
                <button onclick="closeVisitModal()"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg">
                    Close
                </button>
            </div>
            @return
        @endif

        <form method="POST" action="{{ route('adoption.book') }}" class="space-y-6">
            @csrf

            <!-- Selected animals -->
            <div class="bg-gray-50 shadow-md rounded-xl p-6">
                <h2 class="text-xl font-bold mb-4">Animals You Want to Visit</h2>

                <div class="space-y-4">
                    @foreach($animals as $animal)
                        <div class="border p-4 rounded-lg flex justify-between items-center">
                            <div>
                                <strong>{{ $animal->name }}</strong>
                                <br>
                                <span class="text-gray-600 text-sm">{{ $animal->species }}</span>
                            </div>

                            <form action="{{ route('visit.list.remove', $animal->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="text-red-600 font-semibold hover:text-red-800">
                                    Remove
                                </button>
                            </form>
                        </div>

                        <input type="hidden" name="animal_ids[]" value="{{ $animal->id }}">
                    @endforeach
                </div>
            </div>

            <!-- Remarks -->
            <div class="bg-gray-50 shadow-md rounded-xl p-6">
                <h2 class="text-xl font-bold mb-4">Remarks (Optional)</h2>

                @foreach($animals as $animal)
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium">
                            Why are you interested in {{ $animal->name }}?
                        </label>
                        <textarea name="remarks[{{ $animal->id }}]"
                                  class="w-full border rounded-lg p-3"
                                  rows="2"></textarea>
                    </div>
                @endforeach
            </div>

            <!-- Appointment -->
            <div class="bg-gray-50 shadow-md rounded-xl p-6">
                <label class="block font-semibold mb-2">Appointment Date & Time</label>
                <input type="datetime-local" name="appointment_date" required
                       min="{{ date('Y-m-d\TH:i') }}"
                       class="w-full border rounded-lg p-3">
            </div>

            <!-- Terms -->
            <div class="flex items-start">
                <input type="checkbox" name="terms" required class="mt-1">
                <span class="ml-3 text-sm">
                    I understand this is an appointment request, pending approval.
                </span>
            </div>

            <button type="submit"
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-xl">
                Confirm Appointment
            </button>
        </form>

        <div class="mt-4 text-right">
            <button onclick="closeVisitModal()"
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    function openVisitModal() {
        const modal = document.getElementById('visitModal');
        const content = document.getElementById('visitModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('opacity-0', 'scale-95');
            content.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function closeVisitModal() {
        const modal = document.getElementById('visitModal');
        const content = document.getElementById('visitModalContent');
        content.classList.add('opacity-0', 'scale-95');
        content.classList.remove('opacity-100', 'scale-100');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    document.addEventListener('click', function (e) {
        const modal = document.getElementById('visitModal');
        if (!modal.classList.contains('hidden') && e.target === modal) {
            closeVisitModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === "Escape") closeVisitModal();
    });

    @if (session('open_visit_modal'))
    document.addEventListener("DOMContentLoaded", function () {
        openVisitModal();
    });
    @endif
</script>
