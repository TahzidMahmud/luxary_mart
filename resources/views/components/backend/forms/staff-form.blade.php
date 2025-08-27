<form action="{{ $staff ? route('admin.staffs.update', $staff->id) : route('admin.staffs.store') }}" method="POST">
    @csrf

    @if ($staff)
        @method('PUT')
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type staff name" value="{{ $staff?->name }}" />

        <x-backend.inputs.text label="Email" name="email" placeholder="Type staff email" value="{{ $staff?->email }}" />

        <x-backend.inputs.text label="Phone No." name="phone_no" placeholder="Type staff phone no"
            value="{{ $staff?->phone }}" />

        <x-backend.inputs.select label="Role" name="role_id" class="">
            @foreach ($roles as $role)
                <x-backend.inputs.select-option name="{{ $role->name }}" value="{{ $role->id }}"
                    selected="{{ $staff?->role_id == $role->id ? $role->id : 0 }}" />
            @endforeach
        </x-backend.inputs.select>

        <x-backend.inputs.password label="Password" name="password" placeholder="" value="" :isRequired="false" />

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save staff" type="submit" />
        </div>
    </div>
</form>
