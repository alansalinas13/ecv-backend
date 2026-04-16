<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AiService;

class AiController extends Controller {
    public function test(Request $request, AiService $aiService) {
        $validated = $request->validate([
            'message' => ['required', 'string'],
        ]);

        $systemPrompt = <<<PROMPT
                        Eres un asistente orientativo sobre enfermedades cardiovasculares.
                        Debes responder de forma clara, breve y segura.
                        No debes diagnosticar.
                        Siempre debes aclarar que tu respuesta no reemplaza la consulta médica profesional.
                        PROMPT;

        $result = $aiService->generate(
            $validated['message'],
            $systemPrompt
        );

        return response()->json([
            'message'  => 'Consulta IA procesada correctamente',
            'response' => $result,
        ]);
    }

    public function assistant(Request $request, AiService $aiService)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $systemPrompt = <<<PROMPT
                        Eres un asistente orientativo sobre enfermedades cardiovasculares.
                        Tu función es brindar información general, educativa y preventiva.
                        Debes responder:
                        - en español claro
                        - con tono empático
                        - en texto plano
                        - sin markdown
                        - sin asteriscos
                        - sin negritas
                        - sin símbolos decorativos

                        No debes:
                        - diagnosticar enfermedades
                        - indicar que una persona tiene una enfermedad
                        - reemplazar a un profesional de la salud
                        - recomendar medicación específica como sustituto de consulta médica

                        Siempre debes aclarar, de forma natural, que la orientación no reemplaza la consulta médica profesional.
                        Si el usuario menciona síntomas graves como dolor en el pecho intenso, dificultad para respirar, desmayo o signos de urgencia, debes sugerir buscar atención médica inmediata.
                        PROMPT;

        $result = $aiService->generate(
            $validated['message'],
            $systemPrompt
        );

        return response()->json([
            'message' => 'Consulta procesada correctamente',
            'response' => $result,
        ]);
    }

}
