<x-filament-panels::page.simple>
    <div class="w-full space-y-6">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-500 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Admin Control Panel</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Sistem Manajemen E-Surat Akademik</p>
        </div>

        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-amber-800 dark:text-amber-300">Area Terbatas</p>
                    <p class="text-xs text-amber-700 dark:text-amber-400 mt-1">Hanya administrator yang memiliki akses. Semua aktivitas dicatat.</p>
                </div>
            </div>
        </div>

        <x-filament-panels::form wire:submit="authenticate">
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </x-filament-panels::form>

        <div class="text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Lupa password? Hubungi Super Admin untuk reset akun
            </p>
        </div>
    </div>
</x-filament-panels::page.simple>