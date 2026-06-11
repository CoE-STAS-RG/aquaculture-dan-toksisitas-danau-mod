<section>
    <div class="aq-profile-section-head">
        <div>
            <h3>{{ __('ui.login_security') }}</h3>
            <p>
                {{ __('ui.login_security_desc') }}
            </p>
        </div>
        <span class="aq-profile-section-badge">Security</span>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="aq-profile-form-fields">
        @csrf
        @method('put')

        <div class="aq-profile-field">
            <label for="update_password_current_password">{{ __('ui.current_password') }}</label>
            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                autocomplete="current-password"
            >
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="text-sm text-red-500" />
        </div>

        <div class="aq-profile-field">
            <label for="update_password_password">{{ __('ui.new_password') }}</label>
            <input
                id="update_password_password"
                name="password"
                type="password"
                autocomplete="new-password"
            >
            <x-input-error :messages="$errors->updatePassword->get('password')" class="text-sm text-red-500" />
        </div>

        <div class="aq-profile-field">
            <label for="update_password_password_confirmation">{{ __('ui.confirm_new_password') }}</label>
            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
            >
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="text-sm text-red-500" />
        </div>

        <div class="aq-profile-actions">
            <button type="submit" class="aq-profile-primary-btn">{{ __('ui.update_password') }}</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2200)"
                    class="aq-profile-success"
                >
                    {{ __('ui.password_updated') }}
                </p>
            @endif
        </div>
    </form>
</section>
