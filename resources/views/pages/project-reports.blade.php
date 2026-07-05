<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - {{ $project->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Project Reports
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    {{ $project->name }}
                </p>
            </div>
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($files as $index => $file)
                <li class="px-4 py-4 sm:px-6 hover:bg-gray-50 transition duration-150 ease-in-out">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center truncate">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4 truncate">
                                <p class="text-sm font-medium text-indigo-600 truncate">
                                    Report Document {{ $index + 1 }}
                                </p>
                                <p class="text-sm text-gray-500 truncate">
                                    {{ basename($file) }}
                                </p>
                            </div>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <a href="{{ route('projects.report', ['project' => $project->id, 'file_index' => $index]) }}" target="_blank" class="font-medium text-indigo-600 hover:text-indigo-500">
                                View File
                            </a>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="mt-8 text-center">
             <a href="javascript:window.close()" class="text-sm text-gray-500 hover:text-gray-700">Close Window</a>
        </div>
    </div>
</body>
</html>
