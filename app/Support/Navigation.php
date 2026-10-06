<?php

namespace App\Support;

use App\Models\User;

final class Navigation
{
    public static function menuFor(User $user): array
    {
        return match ($user->role) {
            User::ROLE_ADMIN => self::admin(),
            User::ROLE_ACCOUNTANT => self::accountant(),
            User::ROLE_TEACHER => self::teacher(),
            User::ROLE_PARENT => self::parent(),
            User::ROLE_STUDENT => self::student(),
            default => [],
        };
    }

    protected static function admin(): array
    {
        return [
            'Overview' => [
                ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'admin.dashboard'],
            ],
            'Management' => [
                ['label' => 'Students', 'icon' => 'users', 'route' => 'admin.students.index'],
                ['label' => 'Teachers', 'icon' => 'graduation-cap', 'route' => 'admin.teachers.index'],
                ['label' => 'Parents', 'icon' => 'user', 'route' => 'admin.parents.index'],
                ['label' => 'Accountants', 'icon' => 'briefcase', 'route' => 'admin.accountants.index'],
                ['label' => 'Classes', 'icon' => 'building', 'route' => 'admin.classes.index'],
            ],
            'Academics' => [
                ['label' => 'Subjects', 'icon' => 'book-open', 'route' => 'admin.subjects.index'],
                ['label' => 'Results', 'icon' => 'file-text', 'route' => 'admin.results.index'],
                ['label' => 'Grading Rules', 'icon' => 'target', 'route' => 'admin.academics.grading'],
                ['label' => 'Timetable', 'icon' => 'calendar', 'route' => 'admin.timetable.index'],
                ['label' => 'Attendance', 'icon' => 'check-square', 'route' => 'admin.attendance.index'],
            ],
            'Reports' => [
                ['label' => 'Academic Reports', 'icon' => 'bar-chart', 'route' => 'admin.reports.academic'],
                ['label' => 'Student Report Cards', 'icon' => 'file-text', 'route' => 'admin.results.index'],
                ['label' => 'Class Results', 'icon' => 'users', 'route' => 'admin.reports.academic'],
                ['label' => 'Subject Performance', 'icon' => 'book-open', 'route' => 'admin.reports.academic'],
                ['label' => 'School Performance', 'icon' => 'trophy', 'route' => 'admin.reports.academic'],
                ['label' => 'Fee Reports', 'icon' => 'credit-card', 'route' => 'accountant.reports.index'],
            ],
            'Finance' => [
                ['label' => 'Fee Structures', 'icon' => 'credit-card', 'route' => 'admin.fees.index'],
                ['label' => 'Payments', 'icon' => 'banknote', 'route' => 'admin.payments.index'],
            ],
            'Engagement' => [
                ['label' => 'Admissions', 'icon' => 'user-plus', 'route' => 'admin.admissions.index'],
                ['label' => 'Announcements', 'icon' => 'megaphone', 'route' => 'admin.announcements.index'],
                ['label' => 'News', 'icon' => 'newspaper', 'route' => 'admin.news.index'],
                ['label' => 'Events', 'icon' => 'calendar-check', 'route' => 'admin.events.index'],
                ['label' => 'Gallery', 'icon' => 'image', 'route' => 'admin.gallery.index'],
                ['label' => 'Inquiries', 'icon' => 'inbox', 'route' => 'admin.inquiries.index'],
                ['label' => 'Subscribers', 'icon' => 'users', 'route' => 'admin.subscribers.index'],
                ['label' => 'Comment Moderation', 'icon' => 'message-square', 'route' => 'admin.comments.index'],
            ],
            'System' => [
                ['label' => 'User Management', 'icon' => 'users', 'route' => 'admin.users.index'],
                ['label' => 'Audit Logs', 'icon' => 'history', 'route' => 'admin.audit.index'],
                ['label' => 'Settings', 'icon' => 'settings', 'route' => 'admin.settings.index'],
            ],
        ];
    }

    protected static function accountant(): array
    {
        return [
            'Overview' => [
                ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'accountant.dashboard'],
            ],
            'Finance' => [
                ['label' => 'Fees', 'icon' => 'credit-card', 'route' => 'accountant.fees.index'],
                ['label' => 'Payments', 'icon' => 'banknote', 'route' => 'accountant.payments.index'],
                ['label' => 'Receipts', 'icon' => 'receipt', 'route' => 'accountant.receipts.index'],
                ['label' => 'Reports', 'icon' => 'bar-chart', 'route' => 'accountant.reports.index'],
            ],
        ];
    }

    protected static function teacher(): array
    {
        return [
            'Overview' => [
                ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'teacher.dashboard'],
            ],
            'Teaching' => [
                ['label' => 'My Classes', 'icon' => 'building', 'route' => 'teacher.classes.index'],
                ['label' => 'Results', 'icon' => 'file-text', 'route' => 'teacher.results.index'],
                ['label' => 'Attendance', 'icon' => 'check-square', 'route' => 'teacher.attendance.index'],
            ],
            'Reports' => [
                ['label' => 'Academic Reports', 'icon' => 'bar-chart', 'route' => 'teacher.reports.academic'],
            ],
        ];
    }

    protected static function parent(): array
    {
        return [
            'Overview' => [
                ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'parent.dashboard'],
                ['label' => 'My Children', 'icon' => 'users', 'route' => 'parent.children.index'],
                ['label' => 'Results', 'icon' => 'file-text', 'route' => 'parent.results.index'],
                ['label' => 'Fees', 'icon' => 'credit-card', 'route' => 'parent.fees.index'],
            ],
        ];
    }

    protected static function student(): array
    {
        return [
            'Overview' => [
                ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'student.dashboard'],
            ],
            'My School' => [
                ['label' => 'My Results', 'icon' => 'file-text', 'route' => 'student.results.index'],
                ['label' => 'My Reports', 'icon' => 'bar-chart', 'route' => 'student.reports.index'],
                ['label' => 'My Attendance', 'icon' => 'check-square', 'route' => 'student.attendance.index'],
                ['label' => 'My Timetable', 'icon' => 'calendar', 'route' => 'student.timetable.index'],
                ['label' => 'Fees', 'icon' => 'credit-card', 'route' => 'student.fees.index'],
            ],
        ];
    }

    public static function iconFor(string $icon): string
    {
        return $icon;
    }
}
