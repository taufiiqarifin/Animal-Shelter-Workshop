<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bookings</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f4f4f4; }
        a { margin-right: 5px; }
    </style>
</head>
<body>
    <h1>Bookings</h1>
    <a href="{{ route('booking:create') }}">Create Booking</a>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Animals</th>
                <th>Appointment Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
            <tr>
                <td>{{ $booking->id }}</td>
                <td>{{ $booking->user ? $booking->user->name : 'N/A' }}</td>
                <td>
                    @forelse($booking->animals as $animal)
                        {{ $animal->name }}@if(!$loop->last), @endif
                    @empty
                        No animals
                    @endforelse
                </td>
                <td>{{ $booking->appointment_date ?? 'N/A' }}</td>
                <td>{{ $booking->status ?? 'N/A' }}</td>
                <td>
                    <a href="{{ route('booking:show', $booking) }}">View</a>
                    <a href="{{ route('booking:edit', $booking) }}">Edit</a>
                    <form method="POST" action="{{ route('booking:destroy', $booking) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this booking?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">No bookings found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
