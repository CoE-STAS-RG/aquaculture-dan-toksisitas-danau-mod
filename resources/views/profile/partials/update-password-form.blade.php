<section>
    <div class="aq-profile-section-head">
        <div>
            <h3>Keamanan login</h3>
            <p>
                Ganti kata sandi untuk menjaga akses dashboard dan data sensor tetap aman.
            </p>
        </div>
        <span class="aq-profile-section-badge">Security</span>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="aq-profile-form-fields">
        @csrf
        @method('put')

        <div class="aq-profile-field">
            <label for="update_password_current_password">Password saat ini</label>
            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                autocomplete="current-password"
            >
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="text-sm text-red-500" />
        </div>

        <div class="aq-profile-field">
            <label for="update_password_password">Password baru</label>
            <input
                id="update_password_password"
                name="password"
                type="password"
                autocomplete="new-password"
            >
            <x-input-error :messages="$errors->updatePassword->get('password')" class="text-sm text-red-500" />
        </div>

        <div class="aq-profile-field">
            <label for="update_password_password_confirmation">Konfirmasi password baru</label>
            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
            >
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="text-sm text-red-500" />
        </div>

        <div class="aq-profile-actions">
            <button type="submit" class="aq-profile-primary-btn">Perbarui password</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2200)"
                    class="aq-profile-success"
                >
                    Password berhasil diperbarui.
                </p>
            @endif
        </div>
    </form>
</section>
