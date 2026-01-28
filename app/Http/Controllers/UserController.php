<?php

namespace App\Http\Controllers;





use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:20',
                'password' => 'required|string|confirmed|min:8',
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'user',
            ]);

            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            \Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create user. Please try again.');
        }
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
                'phone' => 'required|string|max:20',
                'password' => 'nullable|string|confirmed|min:8',
            ]);

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ];

            if ($request->password) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update user. Please try again.');
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete user. Please try again.');
        }
    }
}
