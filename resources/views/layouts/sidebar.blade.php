<!-- resources/views/layouts/sidebar.blade.php -->
<aside class="w-64 bg-white shadow-md min-h-screen">
    <div class="p-4 font-bold text-lg border-b">Menu</div>
    <nav class="mt-4">
        <a href="{{ route('user.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
        </a>
        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
            <i class="fas fa-fish me-2"></i> Manajemen Ikan
        </a>
    </nav>
</aside>
