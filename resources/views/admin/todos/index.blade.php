@extends('layouts.admin')

@section('title', 'Todo Reminders')

@section('content')
<style>
    .todo-input {
        border: 2px solid #e5e7eb;
        background: #ffffff;
    }
    .todo-input:focus {
        border-color: #FF750F;
        box-shadow: 0 0 0 4px rgba(255, 117, 15, 0.1);
        background: #fff5ed;
    }
    .todo-item-card {
        border-left: 6px solid #FF750F;
        background: #fff5ed;
        border: 2px solid #FF750F;
        transition: all 0.2s ease;
    }
    .todo-item-card:hover {
        background: #fff0e6;
        box-shadow: 0 4px 12px rgba(255, 117, 15, 0.2);
        border-color: #ff8c3a;
    }
    .todo-item-completed {
        border-left-color: #10b981;
        background: #f0fdf4;
        border-color: #10b981;
    }
    .todo-item-completed:hover {
        background: #ecfdf5;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }
    .section-header {
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        border-bottom: 2px solid #e5e7eb;
    }
</style>
<div class="p-6 lg:p-10 space-y-6">
    <div class="bg-white rounded-3xl shadow-lg border-2 border-gray-200 overflow-hidden">
        <div class="bg-orange-500 px-6 py-8 text-white">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <p class="text-sm uppercase tracking-widest text-orange-100 font-semibold">Stay On Track</p>
                    <h1 class="text-3xl font-extrabold mt-2">Personal Todo & Reminder Board</h1>
                    <p class="text-orange-100 mt-3 max-w-2xl">
                        Capture quick notes, attach reminders, and get notified automatically when it's time.
                    </p>
                </div>
                <div class="flex items-center gap-6">
                    <div class="bg-white/20 rounded-2xl px-6 py-4 text-center backdrop-blur border border-white/30">
                        <p class="text-sm text-orange-100 font-semibold">Pending</p>
                        <p class="text-3xl font-bold text-white">{{ $pendingCount }}</p>
                    </div>
                    <div class="bg-white/20 rounded-2xl px-6 py-4 text-center backdrop-blur border border-white/30">
                        <p class="text-sm text-orange-100 font-semibold">Completed</p>
                        <p class="text-3xl font-bold text-white">{{ $completedCount }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-6 lg:p-8 bg-gray-50">
            <div id="todo-alert" class="hidden rounded-xl border-2 px-4 py-3 mb-6 text-sm font-semibold"></div>
            <form id="todo-form" class="grid gap-6 lg:grid-cols-12">
                <div class="lg:col-span-5 space-y-3">
                    <label for="todo-title" class="text-sm font-bold text-gray-800 block">Title <span class="text-red-500">*</span></label>
                    <input id="todo-title" name="title" type="text" placeholder="Give your note a clear title"
                        class="todo-input w-full rounded-xl px-4 py-3 transition font-medium text-gray-900">
                </div>
                <div class="lg:col-span-4 space-y-3">
                    <label for="todo-remind-at" class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        Reminder (optional)
                        <span class="text-xs font-normal text-gray-500">(date &amp; time)</span>
                    </label>
                    <input id="todo-remind-at" name="remind_at" type="datetime-local"
                        class="todo-input w-full rounded-xl px-4 py-3 transition font-medium text-gray-900">
                </div>
                <div class="lg:col-span-12 space-y-3">
                    <label for="todo-note" class="text-sm font-bold text-gray-800 block">Quick Note</label>
                    <textarea id="todo-note" name="note" rows="4" placeholder="What do you want to remember?"
                        class="todo-input w-full rounded-xl px-4 py-3 transition resize-y font-medium text-gray-900"></textarea>
                </div>
                <div class="lg:col-span-12 flex flex-wrap gap-3 justify-end pt-2">
                    <button type="button" id="todo-reset-button"
                        class="px-5 py-2.5 rounded-xl border-2 border-gray-300 text-gray-700 font-bold hover:bg-gray-100 hover:border-gray-400 transition">
                        Clear
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl font-bold text-white bg-gradient-to-r from-orange-500 to-orange-400 shadow-lg hover:shadow-xl transition flex items-center gap-2 border-2 border-orange-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        <span id="todo-submit-label">Add Reminder</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pending Section - Full Width -->
    <div class="bg-white rounded-3xl shadow-xl border-2 border-orange-200 overflow-hidden">
        <div class="bg-gradient-to-r from-orange-500 to-orange-400 px-6 py-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white">Pending Tasks</h2>
                    <p class="text-sm text-orange-100 font-medium mt-1">Items waiting for your action</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span id="pending-count-pill" class="text-lg font-bold bg-white text-orange-500 px-6 py-2 rounded-full shadow-lg">{{ $pendingCount }}</span>
            </div>
        </div>
        <div class="p-6 bg-gray-50 min-h-[300px]">
            <ul id="todo-pending-list" class="space-y-4">
                <li class="py-12 flex flex-col items-center justify-center text-sm text-gray-500 font-medium" data-empty-pending>
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-base">Nothing pending yet. Add a reminder above!</p>
                </li>
            </ul>
        </div>
    </div>

    <!-- Completed Section - Full Width -->
    <div class="bg-white rounded-3xl shadow-xl border-2 border-gray-300 overflow-hidden mt-6">
        <div class="bg-gradient-to-r from-gray-600 to-gray-500 px-6 py-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white">Completed Tasks</h2>
                    <p class="text-sm text-gray-200 font-medium mt-1">Completed reminders stay here</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span id="completed-count-pill" class="text-lg font-bold bg-white text-gray-600 px-6 py-2 rounded-full shadow-lg">{{ $completedCount }}</span>
            </div>
        </div>
        <div class="p-6 bg-gray-50 min-h-[300px]">
            <ul id="todo-completed-list" class="space-y-4">
                <li class="py-12 flex flex-col items-center justify-center text-sm text-gray-500 font-medium" data-empty-completed>
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-base">Marked items will appear here.</p>
                </li>
            </ul>
        </div>
    </div>
</div>

@php
    $initialTodos = $todos->map(function ($todo) {
        return [
            'id' => $todo->id,
            'title' => $todo->title,
            'note' => $todo->note,
            'is_completed' => $todo->is_completed,
            'remind_at_iso' => optional($todo->remind_at)->toIso8601String(),
            'remind_at_display' => optional($todo->remind_at)->toDayDateTimeString(),
            'completed_at_iso' => optional($todo->completed_at)->toIso8601String(),
            'completed_at_display' => optional($todo->completed_at)->diffForHumans(),
        ];
    });
@endphp

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = '{{ csrf_token() }}';
        const routes = {
            store: '{{ route("admin.todos.store") }}',
            update: id => '{{ route("admin.todos.update", ["id" => "__ID__"]) }}'.replace('__ID__', id),
            toggle: id => '{{ route("admin.todos.toggle-complete", ["id" => "__ID__"]) }}'.replace('__ID__', id),
            destroy: id => '{{ route("admin.todos.destroy", ["id" => "__ID__"]) }}'.replace('__ID__', id),
        };

        const initialTodos = @json($initialTodos);

        function formatDateTimeLocal(value) {
            if (!value) {
                return '';
            }
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) {
                return value;
            }
            const offset = date.getTimezoneOffset();
            const adjusted = new Date(date.getTime() - (offset * 60000));
            return adjusted.toISOString().slice(0, 16);
        }

        function formatDisplayDate(value) {
            if (!value) {
                return null;
            }
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) {
                return value || null;
            }
            return date.toLocaleString();
        }

        function normalizeTodo(raw) {
            const remindIso = raw.remind_at_iso ?? raw.remind_at ?? null;
            const completedIso = raw.completed_at_iso ?? raw.completed_at ?? null;

            return {
                id: raw.id,
                title: raw.title ?? 'Untitled Note',
                note: raw.note ?? '',
                is_completed: Boolean(raw.is_completed),
                remind_at_iso: remindIso,
                remind_at_input: formatDateTimeLocal(remindIso),
                remind_at_display: raw.remind_at_display ?? formatDisplayDate(remindIso),
                completed_at_iso: completedIso,
                completed_at_display: raw.completed_at_display ?? formatDisplayDate(completedIso),
            };
        }

        const state = {
            todos: initialTodos.map(normalizeTodo),
            editingId: null,
        };

        const form = document.getElementById('todo-form');
        const titleInput = document.getElementById('todo-title');
        const noteInput = document.getElementById('todo-note');
        const remindInput = document.getElementById('todo-remind-at');
        const resetButton = document.getElementById('todo-reset-button');
        const submitLabel = document.getElementById('todo-submit-label');
        const alertBox = document.getElementById('todo-alert');
        const pendingList = document.getElementById('todo-pending-list');
        const completedList = document.getElementById('todo-completed-list');
        const pendingCountPill = document.getElementById('pending-count-pill');
        const completedCountPill = document.getElementById('completed-count-pill');

        function showAlert(message, type = 'success') {
            alertBox.textContent = message;
            alertBox.classList.remove('hidden', 'bg-red-50', 'text-red-600', 'border-red-200', 'bg-green-50', 'text-green-600', 'border-green-200');
            if (type === 'error') {
                alertBox.classList.add('bg-red-50', 'text-red-600', 'border-red-200');
            } else {
                alertBox.classList.add('bg-green-50', 'text-green-600', 'border-green-200');
            }
            setTimeout(() => alertBox.classList.add('hidden'), 5000);
        }

        function resetForm() {
            state.editingId = null;
            form.reset();
            submitLabel.textContent = 'Add Reminder';
            titleInput.focus();
        }

        function upsertTodo(rawTodo) {
            const normalized = normalizeTodo(rawTodo);
            const existingIndex = state.todos.findIndex(item => item.id === normalized.id);

            if (existingIndex >= 0) {
                state.todos.splice(existingIndex, 1, normalized);
            } else {
                state.todos.push(normalized);
            }
            renderLists();
        }

        function removeTodo(id) {
            state.todos = state.todos.filter(todo => todo.id !== id);
            renderLists();
        }

        function handleEdit(id) {
            const todo = state.todos.find(item => item.id === id);
            if (!todo) {
                return;
            }
            state.editingId = id;
            titleInput.value = todo.title || '';
            noteInput.value = todo.note || '';
            remindInput.value = todo.remind_at_input || '';
            submitLabel.textContent = 'Update Reminder';
            titleInput.focus();
        }

        function toggleStatus(id) {
            fetch(routes.toggle(id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Unable to update status.');
                    }
                    upsertTodo(data.todo);
                    showAlert(data.message);
                })
                .catch(error => showAlert(error.message, 'error'));
        }

        function deleteTodo(id) {
            fetch(routes.destroy(id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'DELETE',
                },
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Unable to delete reminder.');
                    }
                    removeTodo(id);
                    showAlert(data.message);
                    if (state.editingId === id) {
                        resetForm();
                    }
                })
                .catch(error => showAlert(error.message, 'error'));
        }

        function renderLists() {
            const pending = state.todos.filter(todo => !todo.is_completed).sort((a, b) => {
                if (a.remind_at_iso && b.remind_at_iso) {
                    return new Date(a.remind_at_iso) - new Date(b.remind_at_iso);
                }
                if (a.remind_at_iso) return -1;
                if (b.remind_at_iso) return 1;
                return b.id - a.id;
            });

            const completed = state.todos.filter(todo => todo.is_completed).sort((a, b) => {
                if (a.completed_at_iso && b.completed_at_iso) {
                    return new Date(b.completed_at_iso) - new Date(a.completed_at_iso);
                }
                return b.id - a.id;
            });

            pendingCountPill.textContent = pending.length;
            completedCountPill.textContent = completed.length;

            pendingList.innerHTML = '';
            completedList.innerHTML = '';

            if (pending.length === 0) {
                const emptyLi = document.createElement('li');
                emptyLi.className = 'py-12 flex flex-col items-center justify-center text-sm text-gray-500 font-medium';
                emptyLi.innerHTML = `
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-base">Nothing pending yet. Add a reminder above!</p>
                `;
                pendingList.appendChild(emptyLi);
            } else {
                pending.forEach(todo => {
                    pendingList.appendChild(renderTodoItem(todo, false));
                });
            }

            if (completed.length === 0) {
                const emptyLi = document.createElement('li');
                emptyLi.className = 'py-12 flex flex-col items-center justify-center text-sm text-gray-500 font-medium';
                emptyLi.innerHTML = `
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-base">Marked items will appear here.</p>
                `;
                completedList.appendChild(emptyLi);
            } else {
                completed.forEach(todo => {
                    completedList.appendChild(renderTodoItem(todo, true));
                });
            }
        }

        function renderTodoItem(todo, completed = false) {
            const li = document.createElement('li');
            li.className = `todo-item-card ${completed ? 'todo-item-completed' : ''} px-6 py-5 rounded-xl`;

            const wrapper = document.createElement('div');
            wrapper.className = 'flex flex-col gap-4';

            const topRow = document.createElement('div');
            topRow.className = 'flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4';

            const textBlock = document.createElement('div');
            textBlock.className = 'space-y-3 flex-1';

            const title = document.createElement('h3');
            title.className = `text-lg font-bold ${completed ? 'text-gray-500 line-through' : 'text-gray-900'}`;
            title.textContent = todo.title || 'Untitled Note';
            textBlock.appendChild(title);

            if (todo.note) {
                const note = document.createElement('p');
                note.className = `text-sm leading-relaxed ${completed ? 'text-gray-400' : 'text-gray-700'}`;
                note.textContent = todo.note;
                textBlock.appendChild(note);
            }

            if (todo.remind_at_display) {
                const reminder = document.createElement('div');
                reminder.className = 'inline-flex items-center gap-2 text-xs font-bold text-orange-700 bg-orange-200 border border-orange-300 px-3 py-1.5 rounded-full shadow-sm';
                reminder.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Reminder: ${todo.remind_at_display}
                `;
                textBlock.appendChild(reminder);
            }

            if (completed && todo.completed_at_display) {
                const completedTag = document.createElement('div');
                completedTag.className = 'inline-flex items-center gap-2 text-xs font-bold text-emerald-700 bg-emerald-200 border border-emerald-300 px-3 py-1.5 rounded-full shadow-sm';
                completedTag.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 13l4 4L19 7" />
                    </svg>
                    Completed${todo.completed_at_display ? ' · ' + todo.completed_at_display : ''}
                `;
                textBlock.appendChild(completedTag);
            }

            topRow.appendChild(textBlock);

            const actionGroup = document.createElement('div');
            actionGroup.className = 'flex items-center gap-2 flex-wrap';

            const toggleButton = document.createElement('button');
            toggleButton.type = 'button';
            toggleButton.className = `px-5 py-2.5 rounded-xl font-bold text-sm transition flex items-center gap-2 border-2 ${completed
                ? 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400 shadow-sm'
                : 'bg-emerald-500 text-white border-emerald-600 shadow-lg hover:shadow-xl hover:bg-emerald-600'
                }`;
            toggleButton.innerHTML = completed
                ? `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16" />
                    </svg>
                    Mark Pending`
                : `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Mark Complete`;
            toggleButton.addEventListener('click', () => toggleStatus(todo.id));
            actionGroup.appendChild(toggleButton);

            const editButton = document.createElement('button');
            editButton.type = 'button';
            editButton.className = 'px-5 py-2.5 rounded-xl border-2 border-gray-300 text-sm font-bold text-gray-700 hover:bg-gray-100 hover:border-gray-400 transition flex items-center gap-2 shadow-sm';
            editButton.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5h2m2 0h2m-6 4h6m-6 4h6m-4 4h4m-14-8h2m-2 4h2m-2 4h2m-2-8h2" />
                </svg>
                Edit`;
            editButton.addEventListener('click', () => handleEdit(todo.id));
            actionGroup.appendChild(editButton);

            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = 'px-5 py-2.5 rounded-xl border-2 border-red-300 text-sm font-bold text-red-700 hover:bg-red-50 hover:border-red-400 transition flex items-center gap-2 shadow-sm';
            deleteButton.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
                Delete`;
            deleteButton.addEventListener('click', () => {
                if (confirm('Delete this reminder note?')) {
                    deleteTodo(todo.id);
                }
            });
            actionGroup.appendChild(deleteButton);

            topRow.appendChild(actionGroup);
            wrapper.appendChild(topRow);
            li.appendChild(wrapper);
            return li;
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(form);
            const isEditing = Boolean(state.editingId);

            const url = isEditing ? routes.update(state.editingId) : routes.store;
            const methodOverride = isEditing ? 'PUT' : null;

            if (methodOverride) {
                formData.append('_method', methodOverride);
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) {
                        const message = data?.message || 'Unable to save reminder.';
                        const firstError = data?.errors ? Object.values(data.errors)[0][0] : null;
                        throw new Error(firstError || message);
                    }
                    return data;
                })
                .then(data => {
                    upsertTodo(data.todo);
                    showAlert(data.message || (isEditing ? 'Reminder updated.' : 'Reminder added.'));
                    resetForm();
                })
                .catch(error => showAlert(error.message, 'error'));
        });

        resetButton.addEventListener('click', function () {
            resetForm();
        });

        renderLists();
    });
</script>
@endsection

