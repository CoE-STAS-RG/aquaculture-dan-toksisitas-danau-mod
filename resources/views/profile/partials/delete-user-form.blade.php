<section class="space-y-6">
    <div class="aq-profile-section-head">
        <div>
            <h3>Zona bahaya</h3>
            <p class="aq-profile-danger-copy">
                Jika akun dihapus, akses ke data profil dan resource yang terkait akan berhenti permanen.
            </p>
        </div>
        <span class="aq-profile-section-badge">Permanent</span>
    </div>

    <div class="aq-profile-inline-meta">
        <span class="aq-profile-inline-note">Pastikan password Anda siap untuk konfirmasi akhir.</span>
        <span class="aq-profile-inline-note">Tindakan ini tidak bisa dibatalkan.</span>
    </div>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="!bg-transparent !p-0 !shadow-none !text-inherit"
    >
        <span class="aq-profile-danger-btn">Hapus akun</span>
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="aq-profile-modal-card">
            @csrf
            @method('delete')

            <h3>Yakin ingin menghapus akun?</h3>
            <p>
                Setelah akun dihapus, semua data yang terkait akan dihapus permanen. Masukkan password
                Anda untuk mengonfirmasi tindakan ini.
            </p>

            <div class="aq-profile-field mt-6">
                <label for="delete_profile_password">Password</label>
                <input
                    id="delete_profile_password"
                    name="password"
                    type="password"
                    placeholder="Masukkan password Anda"
                >
                <x-input-error :messages="$errors->userDeletion->get('password')" class="text-sm text-red-500" />
            </div>

            <div class="aq-profile-modal-actions">
                <x-secondary-button x-on:click="$dispatch('close')" class="!rounded-xl !px-5 !py-3">
                    Batal
                </x-secondary-button>

                <button type="submit" class="aq-profile-danger-btn">Ya, hapus akun</button>
            </div>
        </form>
    </x-modal>
</section>
