<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiService {
    protected string $baseUrl;
    protected string $model;
    protected int $timeout;

    public function __construct() {
        $this->baseUrl = config('ai.base_url');
        $this->model = config('ai.model');
        $this->timeout = (int)config('ai.timeout', 120);
    }

    public function generateEvaluationSummary(array $answers, int $score, string $riskLevel): string {
        $systemPrompt = <<<PROMPT
                        Eres un asistente orientativo sobre salud cardiovascular.
                        Debes explicar resultados de autoevaluación de forma clara, breve y responsable.
                        No debes diagnosticar.
                        No debes afirmar enfermedades.
                        Debes:
                        - explicar el nivel de riesgo en lenguaje simple
                        - mencionar los factores detectados
                        - sugerir hábitos saludables o seguimiento médico cuando corresponda
                        - aclarar siempre que esto no reemplaza la consulta médica profesional
                        Responde en español claro y en un tono empático.
                        El resumen debe ser breve, claro y útil.
                        El resumen debe tener entre 120 y 180 palabras como máximo.
                        No uses Markdown.
                        No uses asteriscos, negritas, títulos Markdown ni listas con formato especial.
                        PROMPT;

        $userPrompt = "Genera un resumen orientativo para esta autoevaluación cardiovascular.\n\n"
            ."Puntaje: {$score}\n"
            ."Nivel de riesgo: {$riskLevel}\n"
            ."Respuestas:\n"
            .json_encode($answers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return $this->generate($userPrompt, $systemPrompt);
    }

    public function generate(string $prompt, ?string $systemPrompt = null): string {
        $fullPrompt = $systemPrompt
            ? $systemPrompt."\n\nUsuario:\n".$prompt
            : $prompt;

        $response = Http::timeout($this->timeout)
                        ->post($this->baseUrl.'/api/generate', [
                            'model'  => $this->model,
                            'prompt' => $fullPrompt,
                            'stream' => false,
                        ]);

        if (!$response->successful()) {
            throw new \Exception('Error al consultar el servicio de IA');
        }

        return trim($response->json('response', ''));
    }
}
