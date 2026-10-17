const greeting = document.querySelector('[data-greeting]');
if (greeting) {
    const hour = new Date().getHours();
    greeting.textContent = hour < 12 ? 'Good morning' : hour < 18 ? 'Good afternoon' : 'Good evening';
}

const taskList = document.querySelector('#task-list');

if (taskList) {
    const today = document.body.dataset.today;
    const storageKey = 'taskflow.tasks.v1';
    const template = document.querySelector('#task-row-template');
    const taskSeed = [...taskList.querySelectorAll('[data-seed-task]')].map((element) => ({
        id: element.dataset.id,
        title: element.dataset.title,
        description: element.dataset.description,
        date: element.dataset.date,
        time: element.dataset.time,
        project: element.dataset.project,
        priority: element.dataset.priority,
        done: element.dataset.done === 'true',
    }));

    let tasks = taskSeed;
    try {
        const savedTasks = JSON.parse(localStorage.getItem(storageKey) || 'null');
        if (Array.isArray(savedTasks) && savedTasks.every((task) => task && typeof task.title === 'string' && typeof task.date === 'string')) {
            tasks = savedTasks;
        }
    } catch {
        // The dashboard remains usable when browser storage is unavailable.
    }

    let currentView = 'today';
    let statusFilter = 'all';
    let projectFilter = '';
    let searchQuery = '';
    let toastTimer;

    const viewLabels = {
        today: 'Today',
        upcoming: 'Upcoming',
        all: 'All tasks',
        completed: 'Completed',
    };
    const projectColors = {
        Marketing: 'bg-violet-400',
        'Website refresh': 'bg-sky-400',
        'Client work': 'bg-amber-400',
        Personal: 'bg-emerald-400',
        Operations: 'bg-rose-400',
    };
    const priorityStyles = {
        high: { label: 'High', badge: ['bg-rose-50', 'text-rose-600'], dot: 'bg-rose-400' },
        medium: { label: 'Medium', badge: ['bg-amber-50', 'text-amber-700'], dot: 'bg-amber-400' },
        low: { label: 'Low', badge: ['bg-slate-100', 'text-slate-500'], dot: 'bg-slate-300' },
    };

    function dateLabel(date) {
        if (date === today) return 'Today';
        const dateKey = (value) => `${value.getFullYear()}-${String(value.getMonth() + 1).padStart(2, '0')}-${String(value.getDate()).padStart(2, '0')}`;
        const tomorrow = new Date(`${today}T12:00:00`);
        tomorrow.setDate(tomorrow.getDate() + 1);
        if (date === dateKey(tomorrow)) return 'Tomorrow';
        const yesterday = new Date(`${today}T12:00:00`);
        yesterday.setDate(yesterday.getDate() - 1);
        if (date === dateKey(yesterday)) return 'Yesterday';
        return new Intl.DateTimeFormat('en-US', { weekday: 'short', month: 'short', day: 'numeric' }).format(new Date(`${date}T12:00:00`));
    }

    function timeLabel(time) {
        if (!time) return '';
        const [hour, minute] = time.split(':').map(Number);
        const suffix = hour >= 12 ? 'PM' : 'AM';
        return `${hour % 12 || 12}:${String(minute).padStart(2, '0')} ${suffix}`;
    }

    function taskDueLabel(task) {
        const date = dateLabel(task.date);
        const time = timeLabel(task.time);
        return time ? `${date} · ${time}` : date;
    }

    function sortedTasks(items) {
        return [...items].sort((first, second) => `${first.date} ${first.time || '23:59'}`.localeCompare(`${second.date} ${second.time || '23:59'}`));
    }

    function visibleScope() {
        let scoped = tasks;
        if (currentView === 'today') scoped = scoped.filter((task) => task.date === today);
        if (currentView === 'upcoming') scoped = scoped.filter((task) => task.date > today && !task.done);
        if (currentView === 'completed') scoped = scoped.filter((task) => task.done);
        if (projectFilter) scoped = scoped.filter((task) => task.project === projectFilter);
        return scoped;
    }

    function makeTaskRow(task) {
        const row = template.content.firstElementChild.cloneNode(true);
        const priority = priorityStyles[task.priority] || priorityStyles.low;
        const priorityBadge = row.querySelector('[data-task-priority]');
        const toggle = row.querySelector('[data-task-toggle]');
        const taskTitle = row.querySelector('[data-task-title]');

        row.dataset.id = task.id;
        row.dataset.date = task.date;
        row.dataset.done = String(Boolean(task.done));
        row.dataset.project = task.project;
        taskTitle.textContent = task.title;
        row.querySelector('[data-task-description]').textContent = task.description || 'A little progress goes a long way.';
        row.querySelector('[data-task-project]').textContent = task.project;
        row.querySelector('[data-task-due]').textContent = taskDueLabel(task);
        row.querySelector('[data-project-dot]').classList.add(...(projectColors[task.project] || 'bg-slate-300').split(' '));
        priorityBadge.textContent = priority.label;
        priorityBadge.classList.add(...priority.badge);
        toggle.setAttribute('aria-pressed', String(Boolean(task.done)));
        toggle.setAttribute('aria-label', task.done ? 'Mark task as not complete' : 'Mark task complete');

        if (task.done) {
            row.classList.add('opacity-60');
            taskTitle.classList.add('text-slate-400', 'line-through');
            toggle.classList.remove('border-slate-300');
            toggle.classList.add('border-violet-600', 'bg-violet-600');
            row.querySelector('[data-check-icon]').classList.remove('hidden');
        }

        return row;
    }

    function saveTasks() {
        try {
            localStorage.setItem(storageKey, JSON.stringify(tasks));
        } catch {
            showToast('Your changes are available for this session.');
        }
    }

    function setActiveView() {
        document.querySelectorAll('.sidebar-link').forEach((link) => {
            const active = link.dataset.view === currentView && !projectFilter;
            link.classList.toggle('is-active', active);
            link.classList.toggle('font-semibold', active);
            link.classList.toggle('text-slate-300', !active);
        });
    }

    function updateUpcomingList() {
        const upcomingList = document.querySelector('#upcoming-list');
        const upcoming = sortedTasks(tasks.filter((task) => task.date > today && !task.done)).slice(0, 3);
        upcomingList.replaceChildren();

        if (!upcoming.length) {
            const empty = document.createElement('p');
            empty.className = 'rounded-xl bg-slate-50 px-3 py-4 text-center text-[11px] font-medium text-slate-400';
            empty.textContent = 'Nothing scheduled next. Enjoy the breathing room.';
            upcomingList.append(empty);
            return;
        }

        upcoming.forEach((task, index) => {
            const item = document.createElement('div');
            item.className = 'flex gap-3';
            const date = new Date(`${task.date}T12:00:00`);
            const dateBadge = document.createElement('span');
            dateBadge.className = `mt-0.5 flex size-8 shrink-0 flex-col items-center justify-center rounded-lg ${index % 2 ? 'bg-violet-50 text-violet-700' : 'bg-sky-50 text-sky-700'}`;
            const month = document.createElement('span');
            month.className = 'text-[10px] font-bold uppercase';
            month.textContent = new Intl.DateTimeFormat('en-US', { month: 'short' }).format(date);
            const day = document.createElement('span');
            day.className = 'text-[13px] font-bold leading-none';
            day.textContent = new Intl.DateTimeFormat('en-US', { day: '2-digit' }).format(date);
            dateBadge.append(month, day);

            const copy = document.createElement('div');
            copy.className = 'min-w-0 flex-1 border-b border-slate-100 pb-3';
            const title = document.createElement('p');
            title.className = 'truncate text-[11px] font-bold text-slate-700';
            title.textContent = task.title;
            const detail = document.createElement('p');
            detail.className = 'mt-1 text-[10px] font-medium text-slate-400';
            detail.textContent = taskDueLabel(task);
            copy.append(title, detail);

            const dot = document.createElement('span');
            dot.className = `mt-1.5 size-1.5 shrink-0 rounded-full ${priorityStyles[task.priority]?.dot || 'bg-slate-300'}`;
            item.append(dateBadge, copy, dot);
            upcomingList.append(item);
        });
    }

    function updateSummary() {
        const todayTasks = tasks.filter((task) => task.date === today);
        const todayDone = todayTasks.filter((task) => task.done).length;
        const todayTotal = todayTasks.length;
        const todayPercent = todayTotal ? Math.round((todayDone / todayTotal) * 100) : 0;
        const overdue = tasks.filter((task) => task.date < today && !task.done).length;
        const counts = {
            today: todayTotal,
            upcoming: tasks.filter((task) => task.date > today && !task.done).length,
            all: tasks.length,
            completed: tasks.filter((task) => task.done).length,
        };

        Object.entries(counts).forEach(([key, value]) => {
            document.querySelectorAll(`[data-count="${key}"]`).forEach((element) => { element.textContent = value; });
        });
        document.querySelector('[data-stat="today-total"]').textContent = todayTotal;
        document.querySelector('[data-stat="today-total-small"]').textContent = todayTotal;
        document.querySelector('[data-stat="today-total-copy"]').textContent = todayTotal;
        document.querySelector('[data-stat="today-open"]').textContent = todayTotal - todayDone;
        document.querySelector('[data-stat="today-done"]').textContent = todayDone;
        document.querySelector('[data-stat="today-done-copy"]').textContent = todayDone;
        document.querySelector('[data-stat="today-percent"]').textContent = todayPercent;
        document.querySelector('[data-stat="today-percent-ring"]').textContent = `${todayPercent}%`;
        document.querySelector('[data-stat="overdue"]').textContent = overdue;
        document.querySelector('[data-progress-ring]').style.setProperty('--progress', todayPercent);
        document.querySelectorAll('[data-project-count]').forEach((element) => {
            element.textContent = tasks.filter((task) => task.project === element.dataset.projectCount).length;
        });
        updateUpcomingList();
    }

    function render() {
        const scoped = visibleScope();
        const statusScoped = scoped.filter((task) => statusFilter === 'all' || (statusFilter === 'open' ? !task.done : task.done));
        const filtered = sortedTasks(statusScoped.filter((task) => {
            const haystack = `${task.title} ${task.description} ${task.project} ${task.priority}`.toLowerCase();
            return haystack.includes(searchQuery);
        }));
        const label = projectFilter || viewLabels[currentView];
        const title = projectFilter ? `${projectFilter} tasks` : {
            today: 'Today’s focus',
            upcoming: 'Upcoming tasks',
            all: 'All tasks',
            completed: 'Completed tasks',
        }[currentView];
        document.querySelector('[data-tab-count="all"]').textContent = scoped.length;
        document.querySelector('[data-tab-count="open"]').textContent = scoped.filter((task) => !task.done).length;
        document.querySelector('[data-tab-count="done"]').textContent = scoped.filter((task) => task.done).length;
        document.querySelector('[data-view-title]').textContent = title;
        document.querySelector('[data-breadcrumb]').textContent = label;
        document.querySelector('[data-breadcrumb-mobile]').textContent = label;
        document.querySelector('[data-visible-count]').textContent = `${filtered.length} ${filtered.length === 1 ? 'task' : 'tasks'}`;
        taskList.replaceChildren(...filtered.map(makeTaskRow));
        document.querySelector('#task-empty').classList.toggle('hidden', filtered.length > 0);
        document.querySelectorAll('[data-status-filter]').forEach((tab) => {
            const selected = tab.dataset.statusFilter === statusFilter;
            tab.classList.toggle('is-selected', selected);
            tab.classList.toggle('text-violet-700', selected);
            tab.classList.toggle('font-bold', selected);
            tab.classList.toggle('text-slate-400', !selected);
            tab.classList.toggle('font-semibold', !selected);
            tab.setAttribute('aria-selected', String(selected));
        });
        setActiveView();
        updateSummary();
    }

    function showToast(message) {
        let toast = document.querySelector('#task-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'task-toast';
            toast.setAttribute('role', 'status');
            toast.setAttribute('aria-live', 'polite');
            toast.className = 'fixed bottom-5 left-1/2 z-[60] -translate-x-1/2 rounded-xl bg-slate-900 px-4 py-3 text-[12px] font-semibold text-white shadow-xl shadow-slate-900/20';
            document.body.append(toast);
        }
        toast.textContent = message;
        toast.classList.remove('hidden');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.add('hidden'), 2600);
    }

    function closeSidebar() {
        document.querySelector('#app-sidebar').classList.remove('translate-x-0');
        document.querySelector('#app-sidebar').classList.add('-translate-x-full');
        document.querySelector('#sidebar-backdrop').classList.add('hidden');
    }

    function openModal() {
        const modal = document.querySelector('#task-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
        modal.querySelector('[name="title"]').focus();
    }

    function closeModal() {
        document.querySelector('#task-modal').classList.add('hidden');
        document.querySelector('#task-modal').classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    taskList.addEventListener('click', (event) => {
        const toggle = event.target.closest('[data-task-toggle]');
        if (!toggle) return;
        const task = tasks.find((item) => item.id === toggle.closest('[data-task-row]').dataset.id);
        if (!task) return;
        task.done = !task.done;
        saveTasks();
        render();
    });

    document.querySelectorAll('[data-view]').forEach((button) => {
        button.addEventListener('click', () => {
            currentView = button.dataset.view;
            projectFilter = '';
            statusFilter = 'all';
            render();
            closeSidebar();
        });
    });

    document.querySelectorAll('[data-project-filter]').forEach((button) => {
        button.addEventListener('click', () => {
            projectFilter = button.dataset.projectFilter;
            currentView = 'all';
            statusFilter = 'all';
            render();
            closeSidebar();
        });
    });

    document.querySelectorAll('[data-status-filter]').forEach((tab) => {
        tab.addEventListener('click', () => {
            statusFilter = tab.dataset.statusFilter;
            render();
        });
    });

    document.querySelector('#task-search').addEventListener('input', (event) => {
        searchQuery = event.target.value.trim().toLowerCase();
        render();
    });

    document.querySelectorAll('[data-open-task-modal]').forEach((button) => button.addEventListener('click', openModal));
    document.querySelectorAll('[data-close-task-modal]').forEach((button) => button.addEventListener('click', closeModal));
    document.querySelector('#task-modal').addEventListener('click', (event) => {
        if (event.target === event.currentTarget) closeModal();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeModal();
        if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            document.querySelector('#task-search').focus();
        }
    });

    document.querySelector('#sidebar-toggle').addEventListener('click', () => {
        document.querySelector('#app-sidebar').classList.remove('-translate-x-full');
        document.querySelector('#app-sidebar').classList.add('translate-x-0');
        document.querySelector('#sidebar-backdrop').classList.remove('hidden');
    });
    document.querySelector('#sidebar-backdrop').addEventListener('click', closeSidebar);

    document.querySelector('#task-form').addEventListener('submit', (event) => {
        event.preventDefault();
        const formData = new FormData(event.currentTarget);
        const task = {
            id: `task-${Date.now()}`,
            title: String(formData.get('title')).trim(),
            description: String(formData.get('description')).trim(),
            date: String(formData.get('date')),
            time: String(formData.get('time')),
            project: String(formData.get('project')),
            priority: String(formData.get('priority')),
            done: false,
        };
        if (!task.title || !task.date) return;
        tasks.push(task);
        saveTasks();
        event.currentTarget.reset();
        event.currentTarget.elements.date.value = today;
        closeModal();
        currentView = task.date === today ? 'today' : task.date > today ? 'upcoming' : 'all';
        statusFilter = 'all';
        projectFilter = '';
        render();
        showToast('Task added to your list.');
    });

    render();
}
