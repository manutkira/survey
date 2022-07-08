<?php

namespace App\Http\Controllers;

use App\Http\Resources\SurveyAnswerResource;
use App\Http\Resources\SurveyResource;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request){
        $user = $request->user();
        $query = Survey::query();
        // Total number of surveys
        $total = $query->where('user_id', $user->id)->count();

        // latest survey
        $latest = $query->where('user_id', $user->id)->latest('created_at')->first();

        // total number of answer
        $totalAnswers = SurveyAnswer::query()->join('surveys', 'survey_answers.survey_id', '=', 'surveys.id')->where('surveys.user_id', $user->id)->count();

        // latest 5 answer
        $latestAnswers = SurveyAnswer::query()->join('surveys', 'survey_answers.survey_id', '=', 'surveys.id')->where('surveys.user_id', $user->id)->orderBy('end_date', 'DESC')->limit(5)->getModels('survey_answers.*');

        return [
            'totalSurveys'=> $total,
            'latestSurvey'=> $latest ? new SurveyResource($latest) : null,
            'totalAnswers' => $totalAnswers,
            'latestAnswer'=> SurveyAnswerResource::collection($latestAnswers)
        ];
    }
}
