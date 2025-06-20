<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Paginate the users, showing the latest ones first
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // Ensure the email is unique, but ignore the current user's email
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:user,admin',
            // Password is optional, but if provided, it must be confirmed and at least 8 characters
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update user's name and role
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        // If a new password was entered, hash and update it
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent an admin from deleting their own account
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Optional: Prevent deletion of a primary admin (e.g., user with ID 1)
        if ($user->id === 1) {
             return back()->with('error', 'The primary administrator account cannot be deleted.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
