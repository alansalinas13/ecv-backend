<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller {
    // Listar usuarios
    public function index() {
        $users = User::orderBy('id', 'desc')->get();

        return response()->json($users);
    }

    // Ver usuario
    public function show($id) {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json($user);
    }

    // Crear usuario
    public function store(Request $request) {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
            'role'     => ['required', 'integer', 'in:1,2,3'],
            'phone'    => ['nullable', 'string', 'max:50'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ]);

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'user'    => $user,
        ], 201);
    }

    // Actualizar usuario
    public function update(Request $request, $id) {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role'     => ['required', 'integer', 'in:1,2,3'],
            'password' => ['nullable', 'confirmed', Password::min(6)],
            'phone'    => ['nullable', 'string', 'max:50'],
        ]);

        $data = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
            'phone' => $validated['phone'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'user'    => $user,
        ]);
    }

    // Eliminar usuario
    public function destroy(Request $request, $id) {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        if ($request->user()->id === $user->id) {
            return response()->json([
                'message' => 'No puedes eliminar tu propio usuario'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado correctamente'
        ]);
    }
}
