<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    
    public function indexUsers()
    {
        $users = User::all();
        return response()->json($users);
    }

    
    public function indexUserTasks($userId)
    {
        $user = User::findOrFail($userId);
        $tasks = $user->tasks;
        return response()->json($tasks);
    }

    
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'is_admin' => 'sometimes|boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        return response()->json($user, 201);
    }


public function updateUser(Request $request, $id)
{
   
    $user = User::findOrFail($id);

    
    $validated = $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
        'password' => 'sometimes|required|string|min:8',
        'role' => 'sometimes|required|string|in:admin,user',
    ]);

    
    if ($request->has('password')) {
        $validated['password'] = Hash::make($validated['password']);
    }

    
    $user->update($validated);

    
    return response()->json($user);
}

     
    
    public function destroyUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();

        return response()->json(['message' => 'User deleted'], 200);
    }

    
    public function storeUserTask(Request $request, $userId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Pending,In-Progress,Rejected,Completed',
            'due_date' => 'required|date',
        ]);

        $user = User::findOrFail($userId);
        $task = $user->tasks()->create($request->all());

        return response()->json($task, 201);
    }

    
    public function updateTask(Request $request, $userId, Task $task)
    {
    
        $user = User::findOrFail($userId);
        if (!$user->tasks()->find($task->id)) {
            return response()->json(['message' => 'Task not found for this user'], 404);
        }

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

    
    public function destroyTask(Task $task)
    {
        $task->delete();
        return response()->json(['message' => 'Task deleted'], 200);
    }
}
