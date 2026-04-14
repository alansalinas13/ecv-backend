<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller {
    // Crear comentario en un post
    public function store(Request $request, $postId) {
        $post = Post::find($postId);

        if (!$post) {
            return response()->json([
                'message' => 'Post no encontrado'
            ], 404);
        }

        $validated = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);

        return response()->json([
            'message' => 'Comentario creado correctamente',
            'comment' => $comment->load('user'),
        ], 201);
    }

    // Eliminar comentario
    public function destroy(Request $request, $id) {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'message' => 'Comentario no encontrado'
            ], 404);
        }

        if ($comment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No autorizado para eliminar este comentario'
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comentario eliminado correctamente'
        ]);
    }
}
