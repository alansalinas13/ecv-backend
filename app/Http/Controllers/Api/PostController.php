<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\Comment;

class PostController extends Controller
{
    // Listar posts con autor y comentarios
    public function index()
    {
        $posts = Post::with(['user', 'comments.user'])
                     ->latest()
                     ->get();

        return response()->json($posts);
    }

    // Crear post
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $post = Post::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        return response()->json([
            'message' => 'Post creado correctamente',
            'post' => $post->load('user'),
        ], 201);
    }

    // Ver un post
    public function show($id)
    {
        $post = Post::with(['user', 'comments.user'])->find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post no encontrado'
            ], 404);
        }

        return response()->json($post);
    }

    // Actualizar post
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post no encontrado'
            ], 404);
        }

        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No autorizado para editar este post'
            ], 403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $post->update($validated);

        return response()->json([
            'message' => 'Post actualizado correctamente',
            'post' => $post,
        ]);
    }

    // Eliminar post
    public function destroy(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post no encontrado'
            ], 404);
        }

        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No autorizado para eliminar este post'
            ], 403);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post eliminado correctamente'
        ]);
    }
}
