<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DivisionRule;
use App\Models\GradingRule;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class GradingController extends Controller
{
    public const LEVELS = ['O-Level', 'A-Level'];

    public function index()
    {
        $olevelGrades = GradingRule::forLevel('O-Level');
        $alevelGrades = GradingRule::forLevel('A-Level');
        $olevelDivisions = DivisionRule::forLevel('O-Level');
        $alevelDivisions = DivisionRule::forLevel('A-Level');

        return view('admin.grading', compact(
            'olevelGrades',
            'alevelGrades',
            'olevelDivisions',
            'alevelDivisions',
        ));
    }

    public function store(Request $request)
    {
        if ($request->has('divisions')) {
            return $this->divisions($request);
        }

        if (! $request->has('rules')) {
            return back()->with('error', 'No grading rules were submitted.');
        }

        $data = $request->validate([
            'rules' => ['required', 'array'],
            'rules.*.id' => ['required', 'integer'],
            'rules.*.min_mark' => ['required', 'integer', 'between:0,100'],
            'rules.*.max_mark' => ['required', 'integer', 'between:0,100', 'gte:rules.*.min_mark'],
            'rules.*.grade' => ['required', 'string', 'max:5'],
            'rules.*.points' => ['required', 'integer', 'min:0'],
            'rules.*.is_pass' => ['sometimes', 'boolean'],
        ]);

        $updated = 0;
        foreach ($data['rules'] as $ruleData) {
            GradingRule::whereKey($ruleData['id'])->update([
                'min_mark' => $ruleData['min_mark'],
                'max_mark' => $ruleData['max_mark'],
                'grade' => $ruleData['grade'],
                'points' => $ruleData['points'],
                'is_pass' => $request->boolean('rules.'.$ruleData['id'].'.is_pass'),
            ]);
            $updated++;
        }

        AuditLogService::log('grading_rules.updated', "Updated {$updated} grading rule(s)", null, ['rules' => $updated]);

        return back()->with('success', "Grading rules updated successfully ({$updated} rule(s)).");
    }

    public function divisions(Request $request)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.academics.grading');
        }

        if (! $request->has('divisions')) {
            return back()->with('error', 'No division rules were submitted.');
        }

        $data = $request->validate([
            'divisions' => ['required', 'array'],
            'divisions.*.id' => ['required', 'integer'],
            'divisions.*.division' => ['required', 'string', 'max:20'],
            'divisions.*.min_points' => ['required', 'integer', 'min:0'],
            'divisions.*.max_points' => ['required', 'integer', 'min:0', 'gte:divisions.*.min_points'],
            'divisions.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $updated = 0;
        foreach ($data['divisions'] as $divisionData) {
            DivisionRule::whereKey($divisionData['id'])->update([
                'division' => $divisionData['division'],
                'min_points' => $divisionData['min_points'],
                'max_points' => $divisionData['max_points'],
                'sort_order' => $divisionData['sort_order'],
            ]);
            $updated++;
        }

        AuditLogService::log('division_rules.updated', "Updated {$updated} division rule(s)", null, ['divisions' => $updated]);

        return back()->with('success', "Division rules updated successfully ({$updated} rule(s)).");
    }
}
