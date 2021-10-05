<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Usuarios') }}
            <a href="{{ route('admin.user.create' )}}" class="text-indigo-600 hover:text-indigo-900 inline-flex">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </a>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(Session::has('message'))
                        @if(Session::get('alert-type') == 'success')
                            <x-alerts.success>{{ Session::get('message') }}</x-alerts.success>
                        @elseif(Session::get('alert-type') == 'error')
                            <x-alerts.error>{{ Session::get('message') }}</x-alerts.error>
                        @endif
                    @endif

                    <div class="flex flex-col">

                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="shadow overflow-hidden sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <tbody class="divide-y divide-gray-200">
                                        @foreach($users as $user)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ $user->name }}
                                                            </div>
                                                            <div class="text-sm text-gray-500">
                                                                {{ $user->username }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">@rut($user->rut)</div>
                                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @can('user_update')
                                                        <form action="{{ route('admin.user.permission', $user) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="permission" value="user_login">
                                                            <button class="btn btn-danger btn-circle" type="submit">
                                                                @if($user->hasPermissionTo('user_login'))
                                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                  {{ __('Activo') }}
                                                                </span>
                                                                @else
                                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                                  {{ __('Desactivado') }}
                                                                </span>
                                                                @endif
                                                            </button>
                                                        </form>
                                                    @else
                                                        @if($user->hasPermissionTo('user_login') == 1)
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                              {{ __('Activo') }}
                                                            </span>
                                                        @else
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                              {{ __('Desactivado') }}
                                                            </span>
                                                        @endif
                                                    @endcan
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    @can('user_update')
                                                    <a href="{{ route('admin.user.edit', $user )}}" class="text-indigo-600 hover:text-indigo-900 inline-flex">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </a>
                                                    @endcan

                                                    @can('user_destroy')
                                                    <form action="{{ route('admin.user.destroy', $user ) }}" class="inline-flex" method="post" onclick="return confirm('¿Seguro de querer borrar a {{$user->name}}?');">
                                                        @csrf
                                                        @method('delete')
                                                        <button class="text-indigo-600 hover:text-indigo-900" type="submit">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
