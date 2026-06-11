<section>
    <div class="aq-profile-section-head">
        <div>
            <h3>{{ __('ui.account_info') }}</h3>
            <p>
                {{ __('ui.account_info_desc') }}
            </p>
        </div>
        <span class="aq-profile-section-badge">Editable</span>
    </div>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="aq-profile-form-grid">
            <div class="aq-profile-upload-card">
                <div class="aq-profile-preview" id="profilePhotoPreview">
                    @if ($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}">
                    @else
                        <span>{{ $profileInitials }}</span>
                    @endif
                </div>

                <div class="aq-profile-upload-actions">
                    <label for="photo" class="aq-profile-upload-btn">{{ __('ui.choose_photo') }}</label>
                    <input id="photo" name="photo" type="file" class="hidden" accept="image/*">
                    <span class="aq-profile-inline-note">{{ __('ui.photo_hint') }}</span>
                    <x-input-error :messages="$errors->get('photo')" class="text-sm text-red-500" />
                </div>
            </div>

            <div class="aq-profile-form-fields">
                <div class="aq-profile-field">
                    <label for="name">{{ __('ui.full_name') }}</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" autocomplete="name">
                    <x-input-error :messages="$errors->get('name')" class="text-sm text-red-500" />
                </div>

                <div class="aq-profile-field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="username">
                    <x-input-error :messages="$errors->get('email')" class="text-sm text-red-500" />
                </div>

                <div class="aq-profile-field">
                    <label for="phone">{{ __('ui.phone_number') }}</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" autocomplete="tel">
                    <x-input-error :messages="$errors->get('phone')" class="text-sm text-red-500" />
                </div>

                <div class="aq-profile-inline-meta">
                    <span class="aq-profile-inline-note">{{ __('ui.role') }}: {{ $profileRoleLabel }}</span>
                    <span class="aq-profile-inline-note">{{ __('ui.language') }}: {{ $profileLocaleLabel }}</span>
                </div>

                <div class="aq-profile-actions">
                    <button type="submit" class="aq-profile-primary-btn">{{ __('ui.save_changes') }}</button>

                    @if (session('status') === 'profile-updated')
                        <p
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2200)"
                            class="aq-profile-success"
                        >
                            {{ __('ui.profile_updated') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const photoInput = document.getElementById('photo');
            const preview = document.getElementById('profilePhotoPreview');

            if (!photoInput || !preview) {
                return;
            }

            photoInput.addEventListener('change', function (event) {
                const file = event.target.files && event.target.files[0];

                if (!file) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (loadEvent) {
                    preview.innerHTML = '<img src="' + loadEvent.target.result + '" alt="Profile preview">';
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
</section>
