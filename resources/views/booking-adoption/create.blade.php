<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="date"],
        input[type="text"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        select[multiple] {
            height: 150px;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
        button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        button:hover {
            background: #0056b3;
        }
        .cancel {
            background: #6c757d;
            text-decoration: none;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 4px;
        }
        .cancel:hover {
            background: #5a6268;
        }
        a {
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h1>Create New Booking</h1>
    
    @if ($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('booking:store') }}">
        @csrf

        <div class="form-group">
            <label for="userID">User *</label>
            <select name="userID" id="userID" required>
                <option value="">Select a user</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('userID') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} (ID: {{ $user->id }})
                    </option>
                @endforeach
            </select>
            @error('userID')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="appointment_date">Appointment Date *</label>
            <input type="date" name="appointment_date" id="appointment_date" 
                   value="{{ old('appointment_date') }}" required>
            @error('appointment_date')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="booking_time">Booking Time *</label>
            <input type="time" name="booking_time" id="booking_time" 
                   value="{{ old('booking_time') }}" required>
            @error('booking_time')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="status">Status *</label>
            <select name="status" id="status" required>
                <option value="">Select status</option>
                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            @error('status')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="animals">Animals * (Hold Ctrl/Cmd to select multiple)</label>
            <select name="animals[]" id="animals" multiple required>
                @foreach($animals as $animal)
                    <option value="{{ $animal->id }}" 
                            {{ in_array($animal->id, old('animals', [])) ? 'selected' : '' }}>
                        {{ $animal->name ?? 'Animal #' . $animal->id }} 
                        @if(isset($animal->species))
                            ({{ $animal->species }})
                        @endif
                    </option>
                @endforeach
            </select>
            @error('animals')
                <div class="error">{{ $message }}</div>
            @enderror
            @error('animals.*')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit">Create Booking</button>
            <a href="{{ route('booking:index') }}" class="cancel">Cancel</a>
        </div>
    </form>
</body>
</html>

