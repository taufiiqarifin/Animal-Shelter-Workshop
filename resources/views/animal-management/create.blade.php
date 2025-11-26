<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Create Animal</title>
</head>
<body>
    <h1>Add New Animal</h1>
    <a href="{{ route('animals.index') }}">Back to list</a>

    @if($errors->any())
        <ul style="color:red">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('animals.store') }}" method="POST">
        @csrf
        <label>Species:</label><br>
        <input type="text" name="species" required><br>

        <label>Age:</label><br>
        <input type="number" name="age"><br>

        <label>Gender:</label><br>
        <input type="text" name="gender"><br>

        <label>Health Details:</label><br>
        <textarea name="health_details"></textarea><br>

        <label>Adoption Status:</label><br>
        <input type="text" name="adoption_status"><br>

        <label>Arrival Date:</label><br>
        <input type="date" name="arrival_date"><br>

        <label>Medical Status:</label><br>
        <input type="text" name="medical_status"><br>

        <label>Rescue ID:</label><br>
        <input type="number" name="rescueID"><br>

        <label>Slot ID:</label><br>
        <input type="number" name="slotID"><br>

        <label>Vaccination ID:</label><br>
        <input type="number" name="vaccinationID"><br><br>

        <button type="submit">Create</button>
    </form>
</body>
</html>
