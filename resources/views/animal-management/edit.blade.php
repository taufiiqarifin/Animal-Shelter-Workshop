<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Animal</title>
</head>
<body>
    <h1>Edit Animal</h1>
    <a href="{{ route('animals.index') }}">Back to list</a>

    @if($errors->any())
        <ul style="color:red">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('animals.update', $animal->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label>Species:</label><br>
        <input type="text" name="species" value="{{ $animal->species }}" required><br>

        <label>Age:</label><br>
        <input type="number" name="age" value="{{ $animal->age }}"><br>

        <label>Gender:</label><br>
        <input type="text" name="gender" value="{{ $animal->gender }}"><br>

        <label>Health Details:</label><br>
        <textarea name="health_details">{{ $animal->health_details }}</textarea><br>

        <label>Adoption Status:</label><br>
        <input type="text" name="adoption_status" value="{{ $animal->adoption_status }}"><br>

        <label>Arrival Date:</label><br>
        <input type="date" name="arrival_date" value="{{ $animal->arrival_date }}"><br>

        <label>Medical Status:</label><br>
        <input type="text" name="medical_status" value="{{ $animal->medical_status }}"><br>

        <label>Rescue ID:</label><br>
        <input type="number" name="rescueID" value="{{ $animal->rescueID }}"><br>

        <label>Slot ID:</label><br>
        <input type="number" name="slotID" value="{{ $animal->slotID }}"><br>

        <label>Vaccination ID:</label><br>
        <input type="number" name="vaccinationID" value="{{ $animal->vaccinationID }}"><br><br>

        <button type="submit">Update</button>
    </form>
</body>
</html>
