<?php

namespace App\Http\Controllers;

use App\Models\AdminTodo;
use App\Models\Notification;
use App\Models\NotificationSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminTodoController extends Controller
{
    public function index(Request $request)
    {
        $this->abortIfNotAdmin();

        $adminUserId = session('admin_user_id');
        $todos = AdminTodo::where('user_id', $adminUserId)
            ->orderBy('is_completed')
            ->orderByRaw('COALESCE(remind_at, created_at) asc')
            ->orderByDesc('created_at')
            ->get();

        $pendingCount = $todos->where('is_completed', false)->count();
        $completedCount = $todos->where('is_completed', true)->count();

        return view('admin.todos.index', [
            'todos' => $todos,
            'pendingCount' => $pendingCount,
            'completedCount' => $completedCount,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->abortIfNotAdmin();

        $adminUserId = session('admin_user_id');

        $validator = Validator::make($request->all(), [
            'title' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'remind_at' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $title = $data['title'] ?? null;
        $note = $data['note'] ?? null;

        if (!$title && $note) {
            $title = Str::limit(trim(preg_replace('/\s+/', ' ', $note)), 80, '');
        }

        if (!$title) {
            $title = 'Untitled Note';
        }

        $todo = AdminTodo::create([
            'user_id' => $adminUserId,
            'title' => $title,
            'note' => $note,
            'remind_at' => $data['remind_at'] ?? null,
            'reminder_sent_at' => null,
            'is_completed' => false,
            'completed_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reminder note saved.',
            'todo' => $todo->fresh(),
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $this->abortIfNotAdmin();

        $adminUserId = session('admin_user_id');
        $todo = AdminTodo::where('id', $id)
            ->where('user_id', $adminUserId)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'title' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'remind_at' => ['nullable', 'date'],
            'is_completed' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $originalRemindAt = $todo->remind_at;
        if (array_key_exists('title', $data)) {
            $todo->title = $data['title'] ?: 'Untitled Note';
        }

        if (array_key_exists('note', $data)) {
            $todo->note = $data['note'];
        }

        if (array_key_exists('remind_at', $data)) {
            $todo->remind_at = $data['remind_at'];
            if ($this->dateHasChanged($originalRemindAt, $todo->remind_at)) {
                $todo->reminder_sent_at = null;
            }
        }

        if (array_key_exists('is_completed', $data)) {
            $isCompleted = (bool) $data['is_completed'];
            $todo->is_completed = $isCompleted;
            $todo->completed_at = $isCompleted ? now() : null;
        }

        $todo->save();

        return response()->json([
            'success' => true,
            'message' => 'Reminder note updated.',
            'todo' => $todo->fresh(),
        ]);
    }

    public function toggleComplete($id): JsonResponse
    {
        $this->abortIfNotAdmin();

        $adminUserId = session('admin_user_id');

        $todo = AdminTodo::where('id', $id)
            ->where('user_id', $adminUserId)
            ->firstOrFail();

        $todo->is_completed = !$todo->is_completed;
        $todo->completed_at = $todo->is_completed ? now() : null;
        $todo->save();

        return response()->json([
            'success' => true,
            'message' => $todo->is_completed ? 'Marked as completed.' : 'Marked as pending.',
            'todo' => $todo->fresh(),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $this->abortIfNotAdmin();

        $adminUserId = session('admin_user_id');

        $todo = AdminTodo::where('id', $id)
            ->where('user_id', $adminUserId)
            ->firstOrFail();
        $todo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reminder note deleted.',
        ]);
    }

    public function pollReminders(): JsonResponse
    {
        $this->abortIfNotAdmin();

        $adminUserId = session('admin_user_id');

        $dueTodos = AdminTodo::where('user_id', $adminUserId)
            ->dueForReminder()
            ->get();

        $notifications = [];

        foreach ($dueTodos as $todo) {
            if (NotificationSetting::isEnabled('todo_reminder')) {
                $notification = Notification::create([
                    'user_id' => $adminUserId,
                    'type' => 'todo_reminder',
                    'title' => 'Reminder: ' . $todo->title,
                    'message' => $todo->note ? Str::limit($todo->note, 160) : 'Reminder set for now.',
                    'data' => [
                        'todo_id' => $todo->id,
                        'remind_at' => $todo->remind_at,
                    ],
                ]);

                $notifications[] = $notification;
            }

            $todo->markReminderSent();
        }

        return response()->json([
            'success' => true,
            'notifications_sent' => count($notifications),
            'todos' => $dueTodos,
        ]);
    }

    private function abortIfNotAdmin(): void
    {
        if (!session()->has('admin_logged_in') || session('admin_logged_in') !== true) {
            abort(403, 'Unauthorized');
        }
    }

    private function dateHasChanged($original, $current): bool
    {
        if ($original && $current) {
            return !$original->equalTo($current);
        }

        return (bool) $original !== (bool) $current;
    }
}
