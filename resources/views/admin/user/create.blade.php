<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nuevo usuario
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" id="updateUserForm" action="{{ route('admin.user.store') }}">
                        @include('admin.user._form', ['button'=> 'Crear'])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
