<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $filename ?? 'Document Viewer' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body, html { height: 100%; margin: 0; padding: 0; overflow: hidden; }
    </style>
</head>
<body class="h-full flex flex-col">
    <div class="bg-white border-b border-gray-200 px-4 py-3 flex justify-between items-center shadow-sm z-10">
        <div class="flex items-center">
            <h1 class="text-lg font-medium text-gray-900 truncate max-w-xl" title="{{ $filename }}">{{ $filename }}</h1>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ $url }}" download="{{ $filename }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download
            </a>
            <button onclick="window.close()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Close
            </button>
        </div>
    </div>
    <div class="flex-1 relative">
        <iframe src="{{ $url }}" class="absolute inset-0 w-full h-full border-0" title="PDF Viewer"></iframe>
    </div>
</body>
</html>
