@csrf
<div class="row">
    <div class="col col-lg-6 col-md-12">
        <h4>Información general</h4>
        <div class="mt-4">
            <x-label for="name">Nombre</x-label>
            <x-input id="name" class="block mt-1 w-full"
                    type="text"
                    name="name"
                    required
                    autocomplete="current-password"
                    value="{{ old('name', $user->name) }}"/>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mt-4">
            <x-label for="rut">RUT</x-label>
            <x-input id="rut" class="block mt-1 w-full"
                type="text"
                name="rut"
                required
                value="{{ old('rut',$user->rut) }}"
            />
            @error('rut')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mt-4">
            <x-label for="username">Nombre de Usuario</x-label>
            <x-input id="username" class="block mt-1 w-full"
                    type="text"
                    name="username"
                    required
                    value="{{ old('username',$user->username) }}"/>
            @error('username')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mt-4">
            <x-label for="email">Correo</x-label>
            <x-input id="email" class="block mt-1 w-full"
                    type="email"
                    name="email"
                    required
                    value="{{ old('email',$user->email) }}"/>
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mt-4">
            <x-label for="password">Contraseña</x-label>
            <x-input id="password" class="block mt-1 w-full"
                     type="password"
                     name="password"
                     value="{{ old('password') }}"/>
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="mt-4">
        <div class="form-group">
            <x-label for="permissions">Permisos específicos</x-label>
            <select multiple
                    class="'rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50'"
                    style="height:300px;"
                    id="permissions"
                    name="permissions[]">
                @forelse ($permissions as $permission)
                    <option value="{{$permission->id }}" {{ ($user->hasPermissionTo($permission->id)) ? 'selected' : '' }}>{{ $permission->name }}</option>
                @empty
                @endforelse
            </select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        @can('user_update')
            <x-button type="submit" class="btn btn-success">{{ $button }}</x-button>
        @endcan
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('admin.user.index') }}">
                Volver
            </a>
    </div>
</div>
