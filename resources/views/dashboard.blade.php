<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f8fc">
    <title>Taskflow · Your daily workspace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body data-today="{{ now()->toDateString() }}" class="min-h-screen bg-[#f7f8fc] font-sans text-slate-900 antialiased">
    @php
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();
        $dayAfter = now()->addDays(2)->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $greeting = match (true) {
            now()->hour < 12 => 'Good morning',
            now()->hour < 18 => 'Good afternoon',
            default => 'Good evening',
        };
        $seedTasks = [
            ['id' => 'seed-1', 'title' => 'Finish the Q4 marketing report', 'description' => 'Pull together campaign results and final numbers.', 'date' => $today, 'time' => '10:30', 'project' => 'Marketing', 'priority' => 'High', 'done' => false],
            ['id' => 'seed-2', 'title' => 'Review homepage design feedback', 'description' => 'Leave notes on the latest round from the design team.', 'date' => $today, 'time' => '13:00', 'project' => 'Website refresh', 'priority' => 'Medium', 'done' => false],
            ['id' => 'seed-3', 'title' => 'Send the proposal to Northstar', 'description' => 'Include the updated timeline and project estimate.', 'date' => $today, 'time' => '15:30', 'project' => 'Client work', 'priority' => 'High', 'done' => false],
            ['id' => 'seed-4', 'title' => 'Tidy up project notes', 'description' => 'Move decisions from the call into the project brief.', 'date' => $today, 'time' => '09:15', 'project' => 'Personal', 'priority' => 'Low', 'done' => true],
            ['id' => 'seed-8', 'title' => 'Clear the morning inbox', 'description' => 'A quick reset before the day gets busy.', 'date' => $today, 'time' => '08:45', 'project' => 'Personal', 'priority' => 'Low', 'done' => true],
            ['id' => 'seed-5', 'title' => 'Book a room for the planning session', 'description' => 'Find a space for Thursday afternoon.', 'date' => $tomorrow, 'time' => '11:00', 'project' => 'Operations', 'priority' => 'Low', 'done' => false],
            ['id' => 'seed-6', 'title' => 'Prepare next week’s content calendar', 'description' => 'Confirm owners and add the launch posts.', 'date' => $dayAfter, 'time' => '14:00', 'project' => 'Marketing', 'priority' => 'Medium', 'done' => false],
            ['id' => 'seed-7', 'title' => 'Share the sprint recap', 'description' => 'Send a short update with decisions and next steps.', 'date' => $yesterday, 'time' => '16:00', 'project' => 'Website refresh', 'priority' => 'High', 'done' => false],
        ];
    @endphp

    <div class="min-h-screen">
        <button id="sidebar-backdrop" type="button" aria-label="Close navigation" class="fixed inset-0 z-30 hidden bg-slate-950/40 backdrop-blur-[2px] lg:hidden"></button>

        <aside id="app-sidebar" class="fixed inset-y-0 left-0 z-40 flex w-[268px] -translate-x-full flex-col bg-[#11172f] px-5 py-6 text-white shadow-2xl shadow-slate-950/10 transition-transform duration-300 lg:translate-x-0">
            <a href="{{ route('dashboard') }}" class="mb-8 flex items-center gap-3 px-1" aria-label="Taskflow home">
                <span class="flex size-10 items-center justify-center rounded-[14px] bg-gradient-to-br from-violet-400 to-indigo-500 shadow-lg shadow-indigo-950/30">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12.5 4.2 4.2L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </span>
                <span>
                    <span class="block text-[17px] font-bold tracking-tight">taskflow</span>
                    <span class="mt-0.5 block whitespace-nowrap text-[11px] font-semibold uppercase tracking-[0.16em] text-indigo-200/60">Make room to focus</span>
                </span>
            </a>

            <button type="button" class="mb-8 flex items-center gap-3 rounded-2xl border border-white/[0.08] bg-white/[0.045] p-3 text-left transition hover:bg-white/[0.08]">
                <span class="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-amber-300 to-orange-400 text-xs font-bold text-orange-950">W</span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-xs font-semibold">My workspace</span>
                    <span class="mt-1 block text-[12px] text-slate-400">Personal plan</span>
                </span>
                <svg class="size-4 text-slate-400" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m6 8 4 4 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /></svg>
            </button>

            <nav aria-label="Main navigation" class="space-y-1">
                <p class="mb-3 px-3 text-[11px] font-bold uppercase tracking-[0.2em] text-slate-500">Workspace</p>
                <button type="button" data-view="today" class="sidebar-link is-active group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-[14px] font-semibold text-slate-300 transition hover:bg-white/[0.06] hover:text-white">
                    <svg class="size-[17px] text-slate-400 group-[.is-active]:text-violet-200" viewBox="0 0 20 20" fill="none" aria-hidden="true"><rect x="3" y="4" width="14" height="13" rx="2.5" stroke="currentColor" stroke-width="1.5" /><path d="M6.5 2.5v3M13.5 2.5v3M3.5 8h13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" /><path d="m7.5 12 1.4 1.4 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    <span class="flex-1">Today</span><span data-count="today" class="rounded-md bg-white/[0.08] px-1.5 py-0.5 text-[12px] text-slate-400">5</span>
                </button>
                <button type="button" data-view="upcoming" class="sidebar-link group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-[14px] font-medium text-slate-400 transition hover:bg-white/[0.06] hover:text-white">
                    <svg class="size-[17px]" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 5.5h12M4 10h8M4 14.5h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" /><circle cx="14.5" cy="13.5" r="3" stroke="currentColor" stroke-width="1.4" /><path d="M14.5 12v1.7l1.1.7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" /></svg>
                    <span class="flex-1">Upcoming</span><span data-count="upcoming" class="rounded-md bg-white/[0.06] px-1.5 py-0.5 text-[12px] text-slate-500">2</span>
                </button>
                <button type="button" data-view="all" class="sidebar-link group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-[14px] font-medium text-slate-400 transition hover:bg-white/[0.06] hover:text-white">
                    <svg class="size-[17px]" viewBox="0 0 20 20" fill="none" aria-hidden="true"><rect x="3" y="3" width="5.5" height="5.5" rx="1.3" stroke="currentColor" stroke-width="1.5" /><rect x="11.5" y="3" width="5.5" height="5.5" rx="1.3" stroke="currentColor" stroke-width="1.5" /><rect x="3" y="11.5" width="5.5" height="5.5" rx="1.3" stroke="currentColor" stroke-width="1.5" /><rect x="11.5" y="11.5" width="5.5" height="5.5" rx="1.3" stroke="currentColor" stroke-width="1.5" /></svg>
                    <span class="flex-1">All tasks</span><span data-count="all" class="rounded-md bg-white/[0.06] px-1.5 py-0.5 text-[12px] text-slate-500">8</span>
                </button>
                <button type="button" data-view="completed" class="sidebar-link group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-[14px] font-medium text-slate-400 transition hover:bg-white/[0.06] hover:text-white">
                    <svg class="size-[17px]" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5" /><path d="m6.8 10 2.2 2.2 4.4-4.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    <span class="flex-1">Completed</span><span data-count="completed" class="rounded-md bg-white/[0.06] px-1.5 py-0.5 text-[12px] text-slate-500">2</span>
                </button>
            </nav>

            <div class="my-7 h-px bg-white/[0.08]"></div>

            <div>
                <div class="mb-3 flex items-center justify-between px-3">
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-500">Your projects</p>
                    <button type="button" aria-label="Add project" class="flex size-6 items-center justify-center rounded-md text-slate-500 transition hover:bg-white/[0.08] hover:text-white"><span class="text-lg leading-none">+</span></button>
                </div>
                <div class="space-y-1">
                    <button type="button" data-project-filter="Marketing" class="project-link flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-[13px] font-medium text-slate-400 transition hover:bg-white/[0.06] hover:text-white"><span class="size-2 rounded-full bg-violet-400"></span><span class="flex-1">Marketing</span><span data-project-count="Marketing" class="text-[12px] text-slate-600">2</span></button>
                    <button type="button" data-project-filter="Website refresh" class="project-link flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-[13px] font-medium text-slate-400 transition hover:bg-white/[0.06] hover:text-white"><span class="size-2 rounded-full bg-sky-400"></span><span class="flex-1">Website refresh</span><span data-project-count="Website refresh" class="text-[12px] text-slate-600">2</span></button>
                    <button type="button" data-project-filter="Client work" class="project-link flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-[13px] font-medium text-slate-400 transition hover:bg-white/[0.06] hover:text-white"><span class="size-2 rounded-full bg-amber-400"></span><span class="flex-1">Client work</span><span data-project-count="Client work" class="text-[12px] text-slate-600">1</span></button>
                    <button type="button" data-project-filter="Personal" class="project-link flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-[13px] font-medium text-slate-400 transition hover:bg-white/[0.06] hover:text-white"><span class="size-2 rounded-full bg-emerald-400"></span><span class="flex-1">Personal</span><span data-project-count="Personal" class="text-[12px] text-slate-600">2</span></button>
                </div>
            </div>

            <div class="mt-auto pt-8">
                <div class="rounded-2xl border border-indigo-300/10 bg-gradient-to-br from-indigo-500/15 to-violet-500/[0.04] p-4">
                    <div class="mb-2 flex items-center gap-2 text-violet-200">
                        <svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m10 2 1.7 5.4L17 9l-5.3 1.7L10 16l-1.7-5.3L3 9l5.3-1.6L10 2Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round" /><path d="m15.5 13 .8 1.7L18 15.5l-1.7.8-.8 1.7-.8-1.7-1.7-.8 1.7-.8.8-1.7Z" fill="currentColor" /></svg>
                        <span class="text-[12px] font-bold">A little reminder</span>
                    </div>
                    <p class="text-[12px] leading-relaxed text-slate-400">Small steps still move you forward. Focus on one thing at a time.</p>
                </div>
                <button type="button" class="mt-5 flex w-full items-center gap-3 rounded-xl px-2 py-2 text-left transition hover:bg-white/[0.06]">
                    <span class="flex size-9 items-center justify-center rounded-full bg-gradient-to-br from-rose-300 to-orange-300 text-[12px] font-bold text-rose-950">JD</span>
                    <span class="min-w-0 flex-1"><span class="block truncate text-[13px] font-semibold">Jamie Dawson</span><span class="mt-0.5 block text-[11px] text-slate-500">Personal workspace</span></span>
                    <svg class="size-4 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><circle cx="4" cy="10" r="1.2" /><circle cx="10" cy="10" r="1.2" /><circle cx="16" cy="10" r="1.2" /></svg>
                </button>
            </div>
        </aside>

        <main class="min-h-screen lg:pl-[268px]">
            <div class="mx-auto max-w-[1600px] px-5 pb-10 pt-5 sm:px-7 lg:px-9 lg:pt-7 2xl:px-12">
                <header class="mb-8 flex items-center justify-between gap-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <button id="sidebar-toggle" type="button" aria-label="Open navigation" class="flex size-10 shrink-0 items-center justify-center rounded-xl border border-slate-200/80 bg-white text-slate-600 shadow-sm transition hover:border-violet-200 hover:text-violet-700 lg:hidden">
                            <svg class="size-5" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /></svg>
                        </button>
                        <div class="hidden items-center gap-2 text-[12px] font-medium text-slate-400 sm:flex">
                            <span>Workspace</span><span class="text-slate-300">/</span><span data-breadcrumb class="font-semibold text-slate-600">Today</span>
                        </div>
                        <div class="sm:hidden">
                            <span class="block text-[13px] font-bold text-slate-800">Taskflow</span>
                            <span data-breadcrumb-mobile class="text-[11px] text-slate-400">Today</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 sm:gap-3">
                        <label class="relative hidden w-52 md:block xl:w-64">
                            <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="8.7" cy="8.7" r="5.7" stroke="currentColor" stroke-width="1.5" /><path d="m13 13 4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" /></svg>
                            <input id="task-search" type="search" placeholder="Search tasks..." class="h-10 w-full rounded-xl border border-slate-200/80 bg-white pl-9 pr-3 text-[12px] font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-violet-300 focus:ring-4 focus:ring-violet-100/70">
                            <kbd class="absolute right-2.5 top-1/2 -translate-y-1/2 rounded border border-slate-200 bg-slate-50 px-1 py-0.5 text-[10px] font-semibold text-slate-400">Ctrl K</kbd>
                        </label>
                        <button type="button" aria-label="Notifications" class="relative flex size-10 items-center justify-center rounded-xl border border-slate-200/80 bg-white text-slate-500 shadow-sm transition hover:border-violet-200 hover:text-violet-700">
                            <svg class="size-[17px]" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M15.5 7.8a5.5 5.5 0 0 0-11 0c0 6-2 6-2 7.2h15c0-1.2-2-1.2-2-7.2ZM8 17h4" stroke="currentColor" stroke-width="1.45" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            <span class="absolute right-2.5 top-2 size-1.5 rounded-full border border-white bg-rose-500"></span>
                        </button>
                        <button type="button" data-open-task-modal class="inline-flex h-10 items-center gap-2 rounded-xl bg-violet-600 px-3.5 text-[12px] font-bold text-white shadow-lg shadow-violet-600/20 transition hover:-translate-y-0.5 hover:bg-violet-700 sm:px-4 sm:text-[13px]">
                            <svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" /></svg><span class="hidden sm:inline">New task</span><span class="sm:hidden">Add</span>
                        </button>
                    </div>
                </header>

                <section class="mb-7 flex flex-col justify-between gap-5 md:flex-row md:items-end">
                    <div>
                        <div class="mb-2 flex items-center gap-2 text-[12px] font-semibold text-violet-600">
                            <span class="flex size-6 items-center justify-center rounded-lg bg-violet-100/80"><svg class="size-3.5" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M10 2.5v2M10 15.5v2M17.5 10h-2M4.5 10h-2m12.8-5.3-1.4 1.4M6.1 13.9l-1.4 1.4m10.6 0-1.4-1.4M6.1 6.1 4.7 4.7" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" /><circle cx="10" cy="10" r="3.2" stroke="currentColor" stroke-width="1.4" /></svg></span>
                            <span>{{ now()->format('l, F j') }}</span>
                        </div>
                        <h1 class="text-[26px] font-bold tracking-[-0.045em] text-slate-900 sm:text-[32px]"><span data-greeting>{{ $greeting }}</span>, Jamie <span class="inline-block origin-bottom-right animate-[wave_2s_ease-in-out_1]">👋</span></h1>
                        <p class="mt-1.5 text-[13px] font-medium text-slate-500 sm:text-xs">Here’s your day at a glance. Let’s make it count.</p>
                    </div>
                    <div class="flex items-center gap-2 self-start rounded-xl border border-slate-200/80 bg-white px-3 py-2.5 text-[12px] font-semibold text-slate-500 shadow-sm md:self-auto">
                        <span class="size-2 rounded-full bg-emerald-400 ring-4 ring-emerald-50"></span><span>Saved on this device</span>
                    </div>
                </section>

                <section aria-label="Today summary" class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-3 xl:gap-4">
                    <article class="group relative overflow-hidden rounded-2xl border border-violet-100 bg-white p-4 shadow-[0_8px_30px_-20px_rgba(41,37,90,0.25)] sm:p-5">
                        <div class="absolute -right-6 -top-8 size-24 rounded-full bg-violet-50 transition-transform duration-500 group-hover:scale-125"></div>
                        <div class="relative flex items-start justify-between">
                            <div><p class="text-[12px] font-semibold text-slate-500">Today’s tasks</p><p class="mt-2 text-2xl font-bold tracking-tight text-slate-900" data-stat="today-total">5</p><p class="mt-1 text-[11px] font-medium text-slate-400"><span data-stat="today-open">3</span> still to do</p></div>
                            <span class="flex size-9 items-center justify-center rounded-xl bg-violet-50 text-violet-600"><svg class="size-[17px]" viewBox="0 0 20 20" fill="none" aria-hidden="true"><rect x="3" y="4" width="14" height="13" rx="2.5" stroke="currentColor" stroke-width="1.5" /><path d="M6.5 2.5v3M13.5 2.5v3M3.5 8h13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" /><path d="M7 11h2m2 0h2m-6 3h2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" /></svg></span>
                        </div>
                    </article>
                    <article class="group relative overflow-hidden rounded-2xl border border-emerald-100/80 bg-white p-4 shadow-[0_8px_30px_-20px_rgba(41,37,90,0.25)] sm:p-5">
                        <div class="absolute -right-6 -top-8 size-24 rounded-full bg-emerald-50 transition-transform duration-500 group-hover:scale-125"></div>
                        <div class="relative flex items-start justify-between">
                            <div><p class="text-[12px] font-semibold text-slate-500">Completed today</p><p class="mt-2 text-2xl font-bold tracking-tight text-slate-900"><span data-stat="today-done">2</span><span class="ml-1 text-base font-semibold text-slate-300">/ <span data-stat="today-total-small">5</span></span></p><p class="mt-1 text-[11px] font-medium text-emerald-600"><span data-stat="today-percent">40</span>% of your daily plan</p></div>
                            <span class="flex size-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><svg class="size-[17px]" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5" /><path d="m6.7 10.1 2.2 2.2 4.4-4.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /></svg></span>
                        </div>
                    </article>
                    <article class="group relative overflow-hidden rounded-2xl border border-amber-100/80 bg-white p-4 shadow-[0_8px_30px_-20px_rgba(41,37,90,0.25)] sm:p-5">
                        <div class="absolute -right-6 -top-8 size-24 rounded-full bg-amber-50 transition-transform duration-500 group-hover:scale-125"></div>
                        <div class="relative flex items-start justify-between">
                            <div><p class="text-[12px] font-semibold text-slate-500">Past due</p><p class="mt-2 text-2xl font-bold tracking-tight text-slate-900" data-stat="overdue">1</p><p class="mt-1 text-[11px] font-medium text-amber-600">Needs a little attention</p></div>
                            <span class="flex size-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><svg class="size-[17px]" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M10 3 18 17H2L10 3Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" /><path d="M10 8v4m0 2.5h.01" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" /></svg></span>
                        </div>
                    </article>
                </section>

                <section class="grid grid-cols-1 items-start gap-5 xl:grid-cols-[minmax(0,1fr)_320px] 2xl:grid-cols-[minmax(0,1fr)_350px] 2xl:gap-6">
                    <article class="overflow-hidden rounded-[20px] border border-slate-200/75 bg-white shadow-[0_14px_45px_-30px_rgba(41,37,90,0.22)]">
                        <div class="flex flex-col gap-4 border-b border-slate-100 px-5 pb-4 pt-5 sm:flex-row sm:items-center sm:justify-between sm:px-6 sm:pt-6">
                            <div><div class="flex items-center gap-2"><h2 data-view-title class="text-[14px] font-bold tracking-tight text-slate-900">Today’s focus</h2><span class="rounded-md bg-violet-50 px-1.5 py-0.5 text-[11px] font-bold text-violet-600" data-visible-count>5 tasks</span></div><p class="mt-1.5 text-[11px] font-medium text-slate-400">A clear plan makes room for your best work.</p></div>
                            <button type="button" data-open-task-modal class="inline-flex h-8 items-center justify-center gap-1.5 self-start rounded-lg border border-slate-200 px-2.5 text-[11px] font-bold text-slate-600 transition hover:border-violet-200 hover:bg-violet-50 hover:text-violet-700 sm:self-auto"><span class="text-sm leading-none">+</span> Add task</button>
                        </div>
                        <div class="flex items-center gap-1 overflow-x-auto border-b border-slate-100 px-5 sm:px-6" role="tablist" aria-label="Filter tasks">
                            <button type="button" data-status-filter="all" role="tab" aria-selected="true" class="status-tab is-selected relative shrink-0 px-3 py-3 text-[11px] font-bold text-violet-700">All tasks <span class="ml-1 text-[10px] text-violet-400" data-tab-count="all">5</span></button>
                            <button type="button" data-status-filter="open" role="tab" aria-selected="false" class="status-tab relative shrink-0 px-3 py-3 text-[11px] font-semibold text-slate-400 transition hover:text-slate-700">To do <span class="ml-1 text-[10px] text-slate-300" data-tab-count="open">3</span></button>
                            <button type="button" data-status-filter="done" role="tab" aria-selected="false" class="status-tab relative shrink-0 px-3 py-3 text-[11px] font-semibold text-slate-400 transition hover:text-slate-700">Done <span class="ml-1 text-[10px] text-slate-300" data-tab-count="done">2</span></button>
                            <span class="ml-auto hidden items-center gap-1.5 text-[11px] font-semibold text-slate-400 sm:inline-flex"><svg class="size-3.5" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 6h12M6.5 10h7M8.5 14h3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" /></svg>Priority</span>
                        </div>
                        <div id="task-list" class="divide-y divide-slate-100/90 px-5 sm:px-6" aria-live="polite">
                            @foreach ($seedTasks as $task)
                                <div data-seed-task data-id="{{ $task['id'] }}" data-title="{{ $task['title'] }}" data-description="{{ $task['description'] }}" data-date="{{ $task['date'] }}" data-time="{{ $task['time'] }}" data-project="{{ $task['project'] }}" data-priority="{{ strtolower($task['priority']) }}" data-done="{{ $task['done'] ? 'true' : 'false' }}"></div>
                            @endforeach
                        </div>
                        <div id="task-empty" class="hidden px-6 py-12 text-center">
                            <span class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-violet-50 text-violet-500"><svg class="size-5" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M5 10h10m-5-5v10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /></svg></span>
                            <p class="mt-3 text-[13px] font-bold text-slate-700">Nothing on this list</p>
                            <p class="mt-1 text-[11px] text-slate-400">Try another filter or add a task to get started.</p>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-100 px-5 py-3.5 sm:px-6">
                            <p class="text-[11px] font-medium text-slate-400">Keep your momentum going ✨</p>
                            <button type="button" data-view="all" class="text-[11px] font-bold text-violet-600 transition hover:text-violet-800">View all tasks <span aria-hidden="true">→</span></button>
                        </div>
                    </article>

                    <div class="space-y-5">
                        <article class="rounded-[20px] border border-slate-200/75 bg-white p-5 shadow-[0_14px_45px_-30px_rgba(41,37,90,0.22)] sm:p-6">
                            <div class="mb-5 flex items-start justify-between">
                                <div><h2 class="text-[14px] font-bold text-slate-900">Daily progress</h2><p class="mt-1 text-[11px] font-medium text-slate-400">You’re building a good rhythm.</p></div>
                                <button type="button" aria-label="More progress options" class="flex size-7 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-50"><svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><circle cx="4" cy="10" r="1.2" /><circle cx="10" cy="10" r="1.2" /><circle cx="16" cy="10" r="1.2" /></svg></button>
                            </div>
                            <div class="flex items-center gap-4">
                                <div data-progress-ring class="progress-ring relative flex size-[98px] shrink-0 items-center justify-center rounded-full" style="--progress: 40">
                                    <div class="flex size-[76px] flex-col items-center justify-center rounded-full bg-white"><span data-stat="today-percent-ring" class="text-[23px] font-bold tracking-tight text-slate-900">40%</span><span class="mt-0.5 text-[10px] font-semibold text-slate-400">complete</span></div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[12px] font-bold text-slate-800"><span data-stat="today-done-copy">2</span> of <span data-stat="today-total-copy">5</span> tasks done</p>
                                    <p class="mt-1.5 text-[11px] leading-relaxed text-slate-400">One step at a time. You’ve got this.</p>
                                    <div class="mt-3 flex items-center gap-1.5"><span class="flex -space-x-1"><i class="size-4 rounded-full border-2 border-white bg-violet-300"></i><i class="size-4 rounded-full border-2 border-white bg-sky-300"></i><i class="size-4 rounded-full border-2 border-white bg-amber-300"></i></span><span class="text-[10px] font-semibold text-slate-400">Focus time well spent</span></div>
                                </div>
                            </div>
                        </article>

                        <article class="rounded-[20px] border border-slate-200/75 bg-white p-5 shadow-[0_14px_45px_-30px_rgba(41,37,90,0.22)] sm:p-6">
                            <div class="mb-4 flex items-center justify-between"><div><h2 class="text-[14px] font-bold text-slate-900">Coming up</h2><p class="mt-1 text-[11px] font-medium text-slate-400">A peek at what’s next</p></div><span class="flex size-8 items-center justify-center rounded-xl bg-sky-50 text-sky-600"><svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 5.5h12M4 10h8M4 14.5h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" /><path d="m13 13 2 2 3-4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" /></svg></span></div>
                            <div id="upcoming-list" class="space-y-3">
                                <div class="flex gap-3"><span class="mt-0.5 flex size-8 shrink-0 flex-col items-center justify-center rounded-lg bg-sky-50 text-sky-700"><span class="text-[10px] font-bold uppercase">{{ now()->addDay()->format('M') }}</span><span class="text-[13px] font-bold leading-none">{{ now()->addDay()->format('d') }}</span></span><div class="min-w-0 flex-1 border-b border-slate-100 pb-3"><p class="truncate text-[11px] font-bold text-slate-700">Book a room for the planning session</p><p class="mt-1 text-[10px] font-medium text-slate-400">Tomorrow · 11:00 AM</p></div><span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-amber-400"></span></div>
                                <div class="flex gap-3"><span class="mt-0.5 flex size-8 shrink-0 flex-col items-center justify-center rounded-lg bg-violet-50 text-violet-700"><span class="text-[10px] font-bold uppercase">{{ now()->addDays(2)->format('M') }}</span><span class="text-[13px] font-bold leading-none">{{ now()->addDays(2)->format('d') }}</span></span><div class="min-w-0 flex-1 border-b border-slate-100 pb-3"><p class="truncate text-[11px] font-bold text-slate-700">Prepare next week’s content calendar</p><p class="mt-1 text-[10px] font-medium text-slate-400">{{ now()->addDays(2)->format('l') }} · 2:00 PM</p></div><span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-sky-400"></span></div>
                            </div>
                            <button type="button" data-view="upcoming" class="mt-4 text-[11px] font-bold text-violet-600 transition hover:text-violet-800">See upcoming tasks <span aria-hidden="true">→</span></button>
                        </article>

                        <article class="relative overflow-hidden rounded-[20px] bg-gradient-to-br from-[#5b5ce2] via-[#6656df] to-[#7d50d9] p-5 text-white shadow-lg shadow-indigo-500/15 sm:p-6">
                            <div class="absolute -right-7 -top-8 size-28 rounded-full border border-white/10"></div><div class="absolute -right-1 -top-1 size-16 rounded-full border border-white/[0.08]"></div>
                            <div class="relative"><span class="mb-3 flex size-8 items-center justify-center rounded-xl bg-white/15"><svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m10 2 1.7 5.4L17 9l-5.3 1.7L10 16l-1.7-5.3L3 9l5.3-1.6L10 2Z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round" /></svg></span><p class="text-[14px] font-bold">Make space for focus</p><p class="mt-1.5 max-w-[245px] text-[11px] leading-relaxed text-white/75">Pick one important task and give it your full attention for 25 minutes.</p><div class="mt-4 flex items-center gap-2"><span class="flex size-5 items-center justify-center rounded-full bg-white/15"><svg class="size-2.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7 4.5v11l9-5.5-9-5.5Z" /></svg></span><span class="text-[10px] font-bold text-white/90">Start a focus session</span></div></div>
                        </article>
                    </div>
                </section>

                <footer class="mt-8 flex flex-col items-center justify-between gap-2 border-t border-slate-200/70 pt-4 text-[10px] font-medium text-slate-400 sm:flex-row">
                    <span>Make today a little lighter.</span><span>Taskflow <span aria-hidden="true">·</span> Your personal task space</span>
                </footer>
            </div>
        </main>
    </div>

    <div id="task-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="task-modal-title">
        <div class="w-full max-w-[440px] rounded-[22px] bg-white p-5 shadow-2xl shadow-slate-950/20 sm:p-7">
            <div class="mb-5 flex items-start justify-between"><div><p class="text-[11px] font-bold uppercase tracking-[0.16em] text-violet-600">Your next step</p><h2 id="task-modal-title" class="mt-1.5 text-lg font-bold tracking-tight text-slate-900">Create a task</h2><p class="mt-1 text-[11px] text-slate-400">Add a little clarity to your day.</p></div><button type="button" data-close-task-modal aria-label="Close dialog" class="flex size-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"><svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m5 5 10 10M15 5 5 15" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" /></svg></button></div>
            <form id="task-form" class="space-y-4">
                <label class="block"><span class="mb-1.5 block text-[11px] font-bold text-slate-600">Task name</span><input name="title" required maxlength="120" placeholder="What needs to get done?" class="h-11 w-full rounded-xl border border-slate-200 px-3 text-[12px] font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-violet-300 focus:ring-4 focus:ring-violet-100/70"></label>
                <label class="block"><span class="mb-1.5 block text-[11px] font-bold text-slate-600">A few details <span class="font-medium text-slate-400">· optional</span></span><textarea name="description" rows="2" maxlength="240" placeholder="Add a note to help you get started..." class="w-full resize-none rounded-xl border border-slate-200 px-3 py-2.5 text-[12px] font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-violet-300 focus:ring-4 focus:ring-violet-100/70"></textarea></label>
                <div class="grid grid-cols-2 gap-3"><label class="block"><span class="mb-1.5 block text-[11px] font-bold text-slate-600">Due date</span><input name="date" type="date" required value="{{ $today }}" class="h-10 w-full rounded-xl border border-slate-200 px-3 text-[11px] font-semibold text-slate-600 outline-none focus:border-violet-300 focus:ring-4 focus:ring-violet-100/70"></label><label class="block"><span class="mb-1.5 block text-[11px] font-bold text-slate-600">Time <span class="font-medium text-slate-400">· optional</span></span><input name="time" type="time" class="h-10 w-full rounded-xl border border-slate-200 px-3 text-[11px] font-semibold text-slate-600 outline-none focus:border-violet-300 focus:ring-4 focus:ring-violet-100/70"></label></div>
                <div class="grid grid-cols-2 gap-3"><label class="block"><span class="mb-1.5 block text-[11px] font-bold text-slate-600">Project</span><select name="project" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-[11px] font-semibold text-slate-600 outline-none focus:border-violet-300 focus:ring-4 focus:ring-violet-100/70"><option>Personal</option><option>Marketing</option><option>Website refresh</option><option>Client work</option><option>Operations</option></select></label><label class="block"><span class="mb-1.5 block text-[11px] font-bold text-slate-600">Priority</span><select name="priority" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-[11px] font-semibold text-slate-600 outline-none focus:border-violet-300 focus:ring-4 focus:ring-violet-100/70"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option></select></label></div>
                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4"><button type="button" data-close-task-modal class="h-9 rounded-lg px-3 text-[11px] font-bold text-slate-500 transition hover:bg-slate-100">Cancel</button><button type="submit" class="h-9 rounded-lg bg-violet-600 px-4 text-[11px] font-bold text-white shadow-md shadow-violet-600/20 transition hover:bg-violet-700">Create task</button></div>
            </form>
        </div>
    </div>

    <template id="task-row-template">
        <article data-task-row class="task-row group flex items-center gap-3 py-4 sm:gap-4">
            <button type="button" data-task-toggle class="task-check flex size-[18px] shrink-0 items-center justify-center rounded-full border-[1.5px] border-slate-300 text-white transition hover:border-violet-500" aria-label="Mark task complete" aria-pressed="false"><svg data-check-icon class="hidden size-3" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m4.5 10.3 3.6 3.5 7.4-7.6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" /></svg></button>
            <div class="min-w-0 flex-1"><div class="flex min-w-0 items-center gap-2"><h3 data-task-title class="truncate text-[12px] font-bold text-slate-700 transition sm:text-[13px]"></h3><span data-task-priority class="priority-badge hidden shrink-0 rounded-md px-1.5 py-0.5 text-[9px] font-bold sm:inline-flex"></span></div><p data-task-description class="mt-1 truncate text-[10px] font-medium text-slate-400"></p></div>
            <div class="hidden min-w-[92px] items-center gap-1.5 sm:flex"><span data-project-dot class="size-1.5 shrink-0 rounded-full"></span><span data-task-project class="truncate text-[10px] font-semibold text-slate-500"></span></div>
            <div class="flex min-w-[72px] items-center justify-end gap-1.5 text-right"><svg class="size-3 shrink-0 text-slate-300" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.4" /><path d="M10 6v4.2l2.5 1.4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" /></svg><span data-task-due class="text-[10px] font-semibold text-slate-500"></span></div>
        </article>
    </template>
</body>

</html>
