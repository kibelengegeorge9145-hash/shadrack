<?php

namespace App\Http\Requests;

use App\Models\Result;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcademicReportRequest extends FormRequest
{
    public const TYPE_STUDENT_CARD = 'student';

    public const TYPE_CLASS = 'class';

    public const TYPE_SUBJECT = 'subject';

    public const TYPE_TERM = 'term';

    public const TYPE_YEAR = 'year';

    public const TYPE_SCHOOL = 'school';

    public const TYPE_OLEVEL = 'olevel';

    public const TYPE_ALEVEL = 'alevel';

    public const TYPES = [
        self::TYPE_STUDENT_CARD => 'Student Report Card',
        self::TYPE_CLASS => 'Class Results',
        self::TYPE_SUBJECT => 'Subject Performance',
        self::TYPE_TERM => 'Term Performance',
        self::TYPE_YEAR => 'Academic Year Performance',
        self::TYPE_SCHOOL => 'Whole School Performance',
        self::TYPE_OLEVEL => 'O-Level Performance',
        self::TYPE_ALEVEL => 'A-Level Performance',
    ];

    /**
     * The report types that need a resolved status scope.
     */
    public const STAFF_ONLY_STATUSES = [
        Result::STATUS_DRAFT,
        Result::STATUS_SUBMITTED,
        Result::STATUS_REVIEWED,
    ];

    public function authorize(): bool
    {
        // Authorization is enforced by the controller + policy per action.
        return true;
    }

    public function rules(): array
    {
        $type = $this->input('report_type', $this->input('type'));

        return [
            'report_type' => ['sometimes', 'required_without:type', Rule::in(array_keys(self::TYPES))],
            'type' => ['sometimes', 'required_without:report_type', Rule::in(array_keys(self::TYPES))],
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'term_id' => ['nullable', 'integer', 'exists:terms,id'],
            'school_class_id' => ['nullable', 'integer', 'exists:school_classes,id'],
            'level' => ['nullable', 'string', Rule::in(['O-Level', 'A-Level'])],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'status' => ['nullable', 'string', Rule::in([...Result::STATUSES, 'all'])],
            'student' => ['nullable', 'integer', 'exists:students,id'],
            'class' => ['nullable', 'integer', 'exists:school_classes,id'],
            'subject' => ['nullable', 'integer', 'exists:subjects,id'],
            'year' => ['nullable', 'integer', 'exists:academic_years,id'],
            'term' => ['nullable', 'integer', 'exists:terms,id'],
        ];
    }
}
