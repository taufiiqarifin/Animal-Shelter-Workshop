<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Stray Animal Shelter</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    @include('navbar')

    <!-- Dashboard Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @include('dashboard.welcome-section')
        
        @include('dashboard.stats-cards')

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @include('dashboard.recent-reports')

            <!-- Quick Actions & Notifications -->
            <div class="space-y-6">
                @include('dashboard.quick-actions')
                
                @include('dashboard.upcoming-events')
            </div>
        </div>
    </div>
</body>
</html>