<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f172a">
    <title>500 - Server Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-16">
        <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-br from-indigo-950 via-slate-950 to-slate-950"></div>
        <div aria-hidden="true" class="absolute -left-24 top-1/4 h-72 w-72 rounded-full bg-indigo-500/10 blur-3xl"></div>
        <div aria-hidden="true" class="absolute -right-20 bottom-0 h-80 w-80 rounded-full bg-rose-500/10 blur-3xl"></div>

        <section class="relative w-full max-w-lg rounded-3xl border border-white/10 bg-white/[0.04] p-8 text-center shadow-2xl shadow-black/30 backdrop-blur sm:p-12">
            <div class="mx-auto mb-7 flex h-16 w-16 items-center justify-center rounded-2xl border border-rose-300/20 bg-rose-400/10 text-rose-300">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.3 3.75L13.3 3.75a1.5 1.5 0 0 0-2.6 0L2.7 16.5a1.5 1.5 0 0 0 1.3 2.25h16a1.5 1.5 0 0 0 1.3-2.25ZM12 16.5h.008v.008H12V16.5Z" />
                </svg>
            </div>

            <p class="mb-3 text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Server error</p>
            <h1 class="text-7xl font-bold tracking-tight text-white sm:text-8xl">500</h1>
            <h2 class="mt-5 text-2xl font-semibold tracking-tight text-white sm:text-3xl">Something went wrong</h2>
            <p class="mx-auto mt-4 max-w-sm leading-7 text-slate-400">An unexpected error occurred on our end. Please try again in a little while.</p>

            <a href="{{ route('login') }}" class="mt-8 inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-500 px-5 py-3 font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:ring-offset-2 focus:ring-offset-slate-950">
                Return to login
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.69L10.22 5.03a.75.75 0 1 1 1.06-1.06l5.5 5.5a.75.75 0 0 1 0 1.06l-5.5 5.5a.75.75 0 1 1-1.06-1.06l4.22-4.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                </svg>
            </a>
        </section>
    </main>
</body>
</html>
