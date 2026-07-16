<header
class="bg-white shadow h-16 flex items-center justify-between px-6">
    <button id="toggleSidebar">
        <i class="bi bi-list text-2xl"></i>
    </button>
    <div>
        {{ auth()->user()->name }}
    </div>
</header>
