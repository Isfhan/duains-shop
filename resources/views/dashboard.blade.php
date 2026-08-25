<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-1">Duains Admin</h3>
                    <p class="mb-4">
                        {{ __("You're logged in! Manage your shop from the Aimeos backend.") }}
                    </p>
                    <a href="{{ airoute('admin') }}" class="du-admin-btn inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest">
                        {{ __('Open shop backend') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
