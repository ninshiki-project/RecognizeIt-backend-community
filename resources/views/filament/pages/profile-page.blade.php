<x-filament-panels::page>
    {{--    Update Profile Details --}}
    <x-filament-panels::form wire:submit="updateProfile">
        {{$this->editProfileForm}}
        <div class="flex flex-row-reverse">
            <x-filament::button type="submit">
                <span wire:loading.remove wire:target="updateProfile">Update</span>
                <span wire:loading wire:target="updateProfile">Updating...</span>
            </x-filament::button>
        </div>
    </x-filament-panels::form>
    {{--    Update passowrd --}}
    <x-filament-panels::form wire:submit="updatePassword">
        {{$this->editPasswordForm}}
        <div class="flex flex-row-reverse">
            <x-filament::button type="submit">
                <span wire:loading.remove wire:target="updatePassword">Update</span>
                <span wire:loading wire:target="updatePassword">Updating...</span>
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
