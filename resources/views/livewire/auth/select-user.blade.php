<div class="flex min-h-screen items-center justify-center p-8">
    <div class="w-full max-w-sm">
        <div class="mb-10 text-center">
            <div class="mx-auto mb-5 flex p-4 items-center justify-center rounded-lg bg-primary/15 p-2 text-primary">
                <x-monitor.icon name="car" :size="32" />
            </div>
            <p class="mb-2 mt-4 font-mono text-[11px] tracking-widest text-primary uppercase">
                Carport Monitor
            </p>
            <h1 class="font-display text-4xl leading-none font-extrabold tracking-wide text-foreground uppercase">
                Select user
            </h1>
            <p class="mt-4 mb-4 font-sans text-sm leading-relaxed text-muted-foreground">
                Choose who is using this monitor. No password required on your local network.
            </p>
        </div>

        @if ($this->users->isEmpty())
            <x-monitor.card class="pt-6 text-center">
                <p class="font-sans text-sm text-muted-foreground">
                    No users found. Run the database seeder to create accounts.
                </p>
            </x-monitor.card>
        @else
            <div class="space-y-3 pt-6" role="list" aria-label="Users">
                @foreach ($this->users as $user)
                    <button
                        type="button"
                        wire:click="login({{ $user->id }})"
                        wire:loading.attr="disabled"
                        wire:target="login({{ $user->id }})"
                        class="group flex w-full min-h-11 items-center gap-4 rounded-xl border border-border bg-card px-4 py-4 text-left transition-colors duration-150 hover:border-primary/40 hover:bg-secondary disabled:opacity-50"
                        role="listitem"
                    >
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-secondary text-primary transition-colors duration-150 group-hover:bg-primary/15">
                            <x-monitor.icon name="user" :size="18" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-sans text-[15px] font-medium text-foreground">
                                {{ $user->name }}
                            </p>
                            <p class="truncate font-mono text-[10px] tracking-widest text-muted-foreground uppercase">
                                {{ $user->email }}
                            </p>
                        </div>
                        <x-monitor.icon
                            name="chevron-right"
                            :size="16"
                            class="shrink-0 text-muted-foreground transition-colors duration-150 group-hover:text-primary"
                        />
                    </button>
                @endforeach
            </div>
        @endif
    </div>
</div>
