<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('tasks', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'task' => 'required|unique:tasks|max:255',
        ]);

        $task = Task::create([
            'task' => $request->task,
        ]);

        return response()->json($task);
    }

    public function update(Request $request)
    {
        $task = Task::find($request->id);

        if ($task) {
            $task->completed = $request->completed ? 1 : 0;
            $task->save();

            return response()->json(['success' => true, 'completed' => $task->completed]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function destroy(Request $request)
    {
        $task = Task::find($request->id);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }

}

