<?php

namespace App\Http\Controllers;

;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    
    public function index()
    {
        $tasks = Auth::user()->tasks;
        return response()->json($tasks);
    }

    
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return response()->json($task);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Pending,In-Progress,Rejected,Completed',
            'due_date' => 'required|date',
        ]);

        $task = Auth::user()->tasks()->create($request->all());

        return response()->json($task, 201);
    }


    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Pending,In-Progress,Rejected,Completed',
            'due_date' => 'nullable|date',
        ]);

        $task->update($request->all());

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();

        return response()->json(['message' => 'Task deleted'], 200);
    }
}

