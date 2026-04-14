<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Evaluation;

class EvaluationController extends Controller
{
    public function index(Request $request)
    {
        $evaluations = Evaluation::where('user_id', $request->user()->id)
                                 ->latest()
                                 ->get();

        return response()->json($evaluations);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.age' => ['required', 'integer', 'min:1'],
            'answers.smoker' => ['required', 'boolean'],
            'answers.hypertension' => ['required', 'boolean'],
            'answers.diabetes' => ['required', 'boolean'],
            'answers.obesity' => ['required', 'boolean'],
            'answers.exercise' => ['required', 'boolean'],
        ]);

        $answers = $validated['answers'];

        $score = 0;

        if ($answers['age'] >= 60) {
            $score += 2;
        } elseif ($answers['age'] >= 45) {
            $score += 1;
        }

        if ($answers['smoker']) {
            $score += 2;
        }

        if ($answers['hypertension']) {
            $score += 2;
        }

        if ($answers['diabetes']) {
            $score += 2;
        }

        if ($answers['obesity']) {
            $score += 1;
        }

        if (!$answers['exercise']) {
            $score += 1;
        }

        $riskLevel = match (true) {
            $score >= 7 => 'high',
            $score >= 4 => 'medium',
            default => 'low',
        };

        $evaluation = Evaluation::create([
            'user_id' => $request->user()->id,
            'answers' => $answers,
            'risk_level' => $riskLevel,
        ]);

        return response()->json([
            'message' => 'Evaluación registrada correctamente',
            'evaluation' => $evaluation,
            'score' => $score,
        ], 201);
    }
}
