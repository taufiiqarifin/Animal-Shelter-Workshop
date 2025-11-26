<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Animal Details</title>
</head>
<body>
    <h1>Animal Details</h1>
    <a href="{{ route('animals.index') }}">Back to list</a>
    <a href="{{ route('animals.edit', $animal->id) }}">Edit</a>

    <p><strong>ID:</strong> {{ $animal->id }}</p>
    <p><strong>Species:</strong> {{ $animal->species }}</p>
    <p><strong>Age:</strong> {{ $animal->age }}</p>
    <p><strong>Gender:</strong> {{ $animal->gender }}</p>
    <p><strong>Health Details:</strong> {{ $animal->health_details }}</p>
    <p><strong>Adoption Status:</strong> {{ $animal->adoption_status }}</p>
    <p><strong>Arrival Date:</strong> {{ $animal->arrival_date }}</p>
    <p><strong>Medical Status:</strong> {{ $animal->medical_status }}</p>
    <p><strong>Rescue ID:</strong> {{ $animal->rescueID }}</p>
    <p><strong>Slot ID:</strong> {{ $animal->slotID }}</p>
    <p><strong>Vaccination ID:</strong> {{ $animal->vaccinationID }}</p>
</body>
</html>
