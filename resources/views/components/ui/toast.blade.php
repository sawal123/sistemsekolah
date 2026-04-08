<div x-data="{
    notifications: [],
    add(e) {
        // Handle different Livewire event detail structures
        let data = e.detail;
        if (Array.isArray(data)) {
            data = data[0];
        } else if (data && typeof data === 'object' && Object.keys(data).length === 1 && Array.isArray(Object.values(data)[0])) {
            // Some Livewire versions/configs wrap parameters in a named object
            data = Object.values(data)[0][0];
        }

        let id = Date.now();
        this.notifications.push({
            id: id,
            type: data?.type || 'info',
            message: data?.message || '',
            show: false
        });

        // Trigger animation after push
        setTimeout(() => {
            let index = this.notifications.findIndex(n => n.id === id);
            if(index !== -1) this.notifications[index].show = true;
        }, 50);

        // Auto remove
        setTimeout(() => {
            this.remove(id);
        }, 5000);
    },
    remove(id) {
        let index = this.notifications.findIndex(n => n.id === id);
        if(index !== -1) {
            this.notifications[index].show = false;
            setTimeout(() => {
                this.notifications = this.notifications.filter(n => n.id !== id);
            }, 400);
        }
    }
}" @notify.window="add($event)"
    class="fixed top-6 right-6 z-[999] flex flex-col gap-3 w-full max-w-sm pointer-events-none">

    <template x-for="notification in notifications" :key="notification.id">
        <div x-show="notification.show" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-300 transform"
            x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-full opacity-0"
            class="pointer-events-auto relative overflow-hidden glass rounded-2xl shadow-2xl border border-white/20 dark:border-white/10 p-4 transition-all"
            :class="{
                'bg-emerald-500/10 dark:bg-emerald-500/20': notification.type === 'success',
                'bg-red-500/10 dark:bg-red-500/20': notification.type === 'error',
                'bg-amber-500/10 dark:bg-amber-500/20': notification.type === 'warning',
                'bg-indigo-500/10 dark:bg-indigo-500/20': notification.type === 'info'
            }">
            <div class="flex items-start gap-4">
                <!-- Icon -->
                <div class="p-2 rounded-xl flex-shrink-0" :class="{
                        'bg-emerald-500/20 text-emerald-500': notification.type === 'success',
                        'bg-red-500/20 text-red-500': notification.type === 'error',
                        'bg-amber-500/20 text-amber-500': notification.type === 'warning',
                        'bg-indigo-500/20 text-indigo-500': notification.type === 'info'
                    }">

                    <template x-if="notification.type === 'success'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </template>
                    <template x-if="notification.type === 'error'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </template>
                    <template x-if="notification.type === 'warning'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </template>
                    <template x-if="notification.type === 'info'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                </div>

                <!-- Content -->
                <div class="flex-1 pt-1">
                    <p class="text-sm font-semibold txt-primary" x-text="notification.message"></p>
                </div>

                <!-- Close -->
                <button @click="remove(notification.id)"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Progress Line -->
            <div class="absolute bottom-0 left-0 h-0.5 bg-current opacity-20" :class="{
                    'text-emerald-500': notification.type === 'success',
                    'text-red-500': notification.type === 'error',
                    'text-amber-500': notification.type === 'warning',
                    'text-indigo-500': notification.type === 'info'
                }" style="animation: toast-progress 5s linear forwards">
            </div>
        </div>
    </template>

    <style>
        @keyframes toast-progress {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }
    </style>
</div>