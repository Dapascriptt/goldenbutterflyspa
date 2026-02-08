<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Golden Spa')</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f7f2eb] text-slate-800">
        <div class="min-h-screen" x-data="{ sidebarOpen: window.innerWidth >= 768 }">
            <div
                class="fixed inset-0 z-30 bg-black/40 md:hidden"
                x-show="sidebarOpen"
                x-transition.opacity
                @click="sidebarOpen = false"
            ></div>

            <aside
                class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-[#eadfce] transform transition-transform duration-200"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <div class="px-6 py-6 flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-sm uppercase tracking-[0.3em] text-[#9c7a4c]">Golden Spa</div>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-[#eadfce] text-[#9c7a4c] hover:bg-[#f7f2eb]"
                                @click="sidebarOpen = !sidebarOpen"
                                aria-label="Toggle sidebar"
                            >
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                                           </div>
                </div>
                <nav class="px-4 pb-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium hover:bg-[#f7f2eb]">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#f7f2eb] text-[#9c7a4c]">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5v8.5a1 1 0 0 1-1 1h-5.5v-6h-5v6H4a1 1 0 0 1-1-1z" />
                            </svg>
                        </span>
                        Beranda
                    </a>
                    <a href="{{ route('manage.omset.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium hover:bg-[#f7f2eb]">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#f7f2eb] text-[#9c7a4c]">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V9m5 10V5m5 14v-7m5 7v-4" />
                            </svg>
                        </span>
                        Omset
                    </a>
                    <details class="group rounded-lg" @if(request()->routeIs('manage.therapist.*')) open @endif>
                        <summary class="list-none flex items-center justify-between px-4 py-3 rounded-lg text-sm font-medium hover:bg-[#f7f2eb] cursor-pointer transition-colors">
                            <span class="flex items-center gap-3">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#f7f2eb] text-[#9c7a4c]">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4zm7 8a7 7 0 0 0-14 0" />
                                    </svg>
                                </span>
                                Therapist
                            </span>
                            <span class="text-[#9c7a4c] text-lg group-open:rotate-180 transition-transform">▾</span>
                        </summary>
                        <div class="pl-12 pr-4 pb-3 space-y-2 origin-top transition-all duration-200 group-open:animate-[fadeInDown_.2s_ease-out]">
                            <a href="{{ route('manage.therapist.index') }}" class="block text-sm hover:text-[#4b2f1a] {{ request()->routeIs('manage.therapist.index') ? 'text-[#4b2f1a] font-semibold' : 'text-slate-600' }}">
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-2.5 w-2.5 rounded-full bg-[#9c7a4c]"></span>
                                    Detail Therapist
                                </span>
                            </a>
                            <a href="{{ route('manage.therapist.summary') }}" class="block text-sm hover:text-[#4b2f1a] {{ request()->routeIs('manage.therapist.summary') ? 'text-[#4b2f1a] font-semibold' : 'text-slate-500' }}">
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-2.5 w-2.5 rounded-full bg-[#c7b49a]"></span>
                                    Summary Therapist
                                </span>
                            </a>
                        </div>
                    </details>
                    <a href="{{ route('manage.inventory.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium hover:bg-[#f7f2eb]">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#f7f2eb] text-[#9c7a4c]">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16v10H4zM8 7V4h8v3" />
                            </svg>
                        </span>
                        Inventory
                    </a>
                    <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium hover:bg-[#f7f2eb]">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#f7f2eb] text-[#9c7a4c]">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h10l6 6v10H4zM14 4v6h6" />
                            </svg>
                        </span>
                        Reports
                    </a>
                </nav>
            </aside>

            <div class="flex-1 transition-all duration-200" :class="sidebarOpen ? 'md:pl-64' : 'md:pl-0'">
                <button
                    type="button"
                    class="fixed z-30 left-4 top-4 inline-flex items-center justify-center w-10 h-10 rounded-lg border border-[#eadfce] bg-white text-[#9c7a4c] shadow-sm hover:bg-[#f7f2eb]"
                    @click="sidebarOpen = true"
                    x-show="!sidebarOpen"
                    x-transition.opacity
                    aria-label="Open sidebar"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <header class="bg-white border-b border-[#eadfce]">
                    <div class="flex flex-col gap-3 px-6 py-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h1 class="text-xl font-semibold text-[#4b2f1a]">@yield('page_title', 'Dashboard')</h1>
                            <p class="text-sm text-slate-500">Sistem manajemen Golden Spa</p>
                        </div>
                        <div class="flex items-center gap-3">
                            @auth
                                <span class="px-3 py-1 text-xs font-semibold uppercase rounded-full bg-[#f7f2eb] text-[#9c7a4c]">
                                    {{ auth()->user()->role }}
                                </span>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-2 text-sm font-medium text-white rounded-lg bg-[#9c7a4c] hover:bg-[#7b5f3d]">
                                        Logout
                                    </button>
                                </form>
                            @endauth
                        </div>
                    </div>
                </header>

                <main class="px-6 py-8">
                    @if (session('status'))
                        <div class="mb-6 rounded-xl border border-[#eadfce] bg-white px-4 py-3 text-sm text-[#4b2f1a]">
                            {{ session('status') }}
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
        @stack('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('form[data-confirm]').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Hapus item ini?',
                        text: 'Data yang dihapus tidak bisa dikembalikan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Hapus',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#9c7a4c',
                        cancelButtonColor: '#b9a58a'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
        </script>
    </body>
</html>
