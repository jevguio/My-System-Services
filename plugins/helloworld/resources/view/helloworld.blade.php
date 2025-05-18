<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ 'Test Plugin' }}
        </h2>

    </x-slot>

    {{-- Flash messages --}}
    @if (session('success'))
    
    <div id="success-alert" class="absolute top-0 right-0 bg-green-600 shadow mt-4 p-4 dark:bg-green-600 hover:bg-green-600">
        <div class="shadow text-green-800 dark:text-gray-200">
            {{ session('success') }}
        </div>
    </div>
@endif

@if (session('error'))
    <div id="error-alert" class="absolute top-0 right-0 bg-red-600 shadow mt-4 p-4 dark:bg-red-600 hover:bg-red-600">
        <div class="shadow text-red-800 dark:text-gray-200">
            {{ session('error') }}
        </div>
    </div>
@endif

<script>
    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            const successAlert = document.getElementById('success-alert');
            if(successAlert) successAlert.style.display = 'none';

            const errorAlert = document.getElementById('error-alert');
            if(errorAlert) errorAlert.style.display = 'none';
        }, 3000); // 3000ms = 3 seconds
    });
</script>

    <div class="py-12">
    <h1>Hello World</h1>
    </div>
</x-app-layout>
