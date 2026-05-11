<x-app-layout>
    <x-slot name="header">
        Pengaturan Profil
    </x-slot>

    <div class="mb-4">
        <div class="p-4 bg-white shadow-sm border-0" style="border-radius: 15px;">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 bg-white shadow-sm border-0 mt-4" style="border-radius: 15px;">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-4 bg-white shadow-sm border-0 mt-4 mb-5" style="border-radius: 15px;">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
