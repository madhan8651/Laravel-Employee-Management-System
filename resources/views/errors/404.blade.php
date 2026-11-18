<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f8fafc">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-16">
        <div aria-hidden="true" class="absolute -left-24 -top-32 h-96 w-96 rounded-full bg-blue-100/70 blur-3xl"></div>
        <div aria-hidden="true" class="absolute -bottom-40 -right-24 h-96 w-96 rounded-full bg-indigo-100/70 blur-3xl"></div>

        <section class="relative w-full max-w-xl rounded-3xl border border-white/80 bg-white/90 p-8 text-center shadow-xl shadow-slate-200/60 backdrop-blur sm:p-12">
            <div class="mx-auto mb-7 flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 ring-1 ring-blue-100">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14 21 3m0 0h-7m7 0v7M14 10l-4 4m-2-9H6a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2" />
                </svg>
            </div>

            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Page not found</p>
            <h1 class="mt-3 text-7xl font-black tracking-tight text-slate-900 sm:text-8xl">404</h1>
            <h2 class="mt-3 text-2xl font-bold tracking-tight text-slate-800 sm:text-3xl">Looks like you're lost</h2>
            <p class="mx-auto mt-4 max-w-md text-base leading-7 text-slate-500">
                Sorry, the page you’re looking for doesn’t exist or may have been moved. Let’s get you back on track.
            </p>

            <a href="{{ route('login') }}" class="mt-8 inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700 hover:shadow-blue-700/25 focus:outline-none focus:ring-4 focus:ring-blue-200">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m10 19-7-7 7-7M3 12h18" />
                </svg>
                Return to Login
            </a>
        </section>
    </main>
</body>
</html>