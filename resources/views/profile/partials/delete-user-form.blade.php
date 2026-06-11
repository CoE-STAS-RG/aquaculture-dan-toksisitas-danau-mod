<section class="space-y-6">
    <div class="aq-profile-section-head">
        <div>
            <h3>{{ __('ui.danger_zone') }}</h3>
            <p class="aq-profile-danger-copy">
                {{ __('ui.danger_zone_desc') }}
            </p>
        </div>
        <span class="aq-profile-section-badge">Permanent</span>
    </div>

    <div class="aq-profile-inline-meta">
        <span class="aq-profile-inline-note">{{ __('ui.danger_note1') }}</span>
        <span class="aq-profile-inline-note">{{ __('ui.danger_note2') }}</span>
    </div>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="!bg-transparent !p-0 !shadow-none !text-inherit"
    >
        <span class="aq-profile-danger-btn">{{ __('ui.delete_account') }}</span>
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="aq-profile-modal-card">
            @csrf
            @method('delete')

            <h3>{{ __('ui.delete_account_confirm_title') }}</h3>
            <p>
                {{ __('ui.delete_account_confirm_desc') }}
            </p>

            <div class="aq-profile-field mt-6">
                <label for="delete_profile_password">{{ __('ui.password') }}</label>
                <input
                    id="delete_profile_password"
                    name="password"
                    type="password"
                    placeholder="{{ __('ui.enter_your_password') }}"
                >
                <x-input-error :messages="$errors->userDeletion->get('password')" class="text-sm text-red-500" />
            </div>

            <div class="aq-profile-modal-actions">
                <x-secondary-button x-on:click="$dispatch('close')" class="!rounded-xl !px-5 !py-3">
                    {{ __('ui.cancel') }}
                </x-secondary-button>

                <button type="submit" class="aq-profile-danger-btn">{{ __('ui.yes_delete_account') }}</button>
            </div>
        </form>
    </x-modal>
</section>
