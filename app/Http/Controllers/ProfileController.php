<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        $employee = \App\Models\Employee::with(['department', 'position', 'supervisor', 'role'])
            ->find($user->id);

        $latestResult = \App\Models\AssessmentResult::with('period')
            ->where('employee_id', $user->id)
            ->latest('created_at')
            ->first();

        $historicalResults = \App\Models\AssessmentResult::with('period')
            ->where('employee_id', $user->id)
            ->latest()
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        $aspectAverages = collect();
        if ($latestResult) {
            $aspectAverages = \Illuminate\Support\Facades\DB::table('assessment_scores')
                ->join('assessments', 'assessment_scores.assessment_id', '=', 'assessments.id')
                ->join('assessment_indicators', 'assessment_scores.indicator_id', '=', 'assessment_indicators.id')
                ->join('assessment_categories', 'assessment_indicators.category_id', '=', 'assessment_categories.id')
                ->where('assessments.employee_id', $user->id)
                ->where('assessments.period_id', $latestResult->period_id)
                ->where('assessments.status', 'SUBMITTED')
                ->select('assessment_categories.name', \Illuminate\Support\Facades\DB::raw('ROUND(AVG(assessment_scores.score) * 10, 1) as average_score'))
                ->groupBy('assessment_categories.id', 'assessment_categories.name', 'assessment_categories.display_order')
                ->orderBy('assessment_categories.display_order')
                ->get();
        }

        return view('profile.edit', [
            'user' => $user,
            'employee' => $employee,
            'latestResult' => $latestResult,
            'historicalResults' => $historicalResults,
            'aspectAverages' => $aspectAverages,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->only(['name', 'email', 'phone', 'address']));

        if ($request->hasFile('avatar')) {
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
