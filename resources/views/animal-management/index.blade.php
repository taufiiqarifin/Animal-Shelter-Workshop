<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Animals List</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        table, th, td { border: 1px solid black; padding: 8px; }
        th { background-color: #f2f2f2; }
        a { margin-right: 5px; }
    </style>
</head>
<body>
    <h1>Animals</h1>
    <a href="{{ route('animals.create') }}">Add New Animal</a>

    @if(session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    <table>
        <tr>
            <th>ID</th>
            <th>Species</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Actions</th>
        </tr>
        @foreach($animals as $animal)
        <tr>
            <td>{{ $animal->id }}</td>
            <td>{{ $animal->species }}</td>
            <td>{{ $animal->age }}</td>
            <td>{{ $animal->gender }}</td>
            <td>
                <a href="{{ route('animals.show', $animal->id) }}">View</a>
                <a href="{{ route('animals.edit', $animal->id) }}">Edit</a>
                <form action="{{ route('animals.destroy', $animal->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</body>
</html>
