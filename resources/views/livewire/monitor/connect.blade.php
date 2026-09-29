<div class="flex h-full items-center justify-center overflow-y-auto p-8">
    <div class="grid w-full max-w-3xl grid-cols-1 gap-12 lg:grid-cols-2">
        <div>
            <p class="mb-3 font-mono text-[11px] tracking-widest text-primary uppercase">
                Web monitor
            </p>
            <h1 class="font-display text-6xl leading-none font-extrabold tracking-wide text-foreground uppercase">
                Connect
            </h1>
            <p class="font-display text-6xl leading-none font-extrabold tracking-wide text-muted-foreground uppercase">
                Your garage
            </p>

            <p class="mt-6 max-w-sm font-sans text-sm leading-relaxed text-muted-foreground">
                Open the Carport mobile app on your phone, then navigate to
                <span class="font-medium text-foreground">Settings → Web Monitor</span>.
                Scan the QR code to the right to begin a live session.
            </p>

            <ol class="mt-8 space-y-3" aria-label="Pairing steps">
                <li class="flex gap-3">
                    <span class="w-5 shrink-0 font-mono text-[10px] text-primary">01</span>
                    <span class="font-sans text-[13px] text-muted-foreground">Open Carport on iOS or Android</span>
                </li>
                <li class="flex gap-3">
                    <span class="w-5 shrink-0 font-mono text-[10px] text-primary">02</span>
                    <span class="font-sans text-[13px] text-muted-foreground">Tap Settings → Connectivity → QR Scanner</span>
                </li>
                <li class="flex gap-3">
                    <span class="w-5 shrink-0 font-mono text-[10px] text-primary">03</span>
                    <span class="font-sans text-[13px] text-muted-foreground">Point your camera at the QR code</span>
                </li>
            </ol>
        </div>

        <div class="flex flex-col items-center gap-4">
            <div class="flex w-full max-w-[280px] flex-col items-center gap-4 rounded-xl border border-border bg-card p-6">
                <div
                    class="h-48 w-48 overflow-hidden rounded-lg bg-white [&_svg]:h-full [&_svg]:w-full"
                    role="img"
                    aria-label="Scan with Carport app to connect"
                >
                    {!! $qrCodeSvg !!}
                </div>

                <div class="w-full border-t border-border"></div>

                <div class="text-center">
                    <p class="font-display text-xl font-bold tracking-wide text-foreground uppercase">
                        Carport
                    </p>
                    <p class="font-display text-xl font-bold tracking-wide text-muted-foreground uppercase">
                        Monitor
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2" aria-live="polite">
                <span class="h-2 w-2 rounded-full bg-muted-foreground" aria-hidden="true"></span>
                <span class="font-mono text-[10px] tracking-widest text-muted-foreground uppercase">
                    Waiting for scan…
                </span>
            </div>
        </div>
    </div>
</div>
