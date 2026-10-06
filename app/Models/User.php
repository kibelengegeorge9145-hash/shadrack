<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use CanResetPassword, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_ACCOUNTANT = 'accountant';

    public const ROLE_TEACHER = 'teacher';

    public const ROLE_PARENT = 'parent';

    public const ROLE_STUDENT = 'student';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_ACCOUNTANT,
        self::ROLE_TEACHER,
        self::ROLE_PARENT,
        self::ROLE_STUDENT,
    ];

    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'name', 'username',
        'email', 'phone', 'role', 'admission_number', 'staff_id',
        'password', 'must_change_password', 'is_active', 'profile_photo',
        'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function parent(): HasOne
    {
        return $this->hasOne(MyParent::class);
    }

    public function accountant(): HasOne
    {
        return $this->hasOne(Accountant::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isAccountant(): bool
    {
        return $this->role === self::ROLE_ACCOUNTANT;
    }

    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function isParent(): bool
    {
        return $this->role === self::ROLE_PARENT;
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function hasRole(...$roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function getInitialsAttribute(): string
    {
        $parts = array_filter([$this->first_name, $this->last_name, $this->middle_name]);

        return collect($parts)
            ->map(fn ($p) => mb_strtoupper(mb_substr(trim((string) $p), 0, 1)))
            ->take(2)
            ->implode('');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->profile_photo) {
            return asset('storage/'.ltrim($this->profile_photo, '/'));
        }

        return null;
    }

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name, $this->middle_name, $this->last_name,
        ]))) ?: $this->name;
    }

    public function getRoleLabelAttribute(): string
    {
        return ucfirst((string) $this->role);
    }

    /** Find a user by username / email / phone for login. */
    public static function findForLogin(string $identifier): ?self
    {
        return static::query()
            ->where('is_active', true)
            ->where(function ($q) use ($identifier) {
                $q->where('email', $identifier)
                    ->orWhere('username', $identifier)
                    ->orWhere('phone', $identifier)
                    ->orWhere('admission_number', $identifier)
                    ->orWhere('staff_id', $identifier);
            })
            ->first();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getUsernameFallbackAttribute(): string
    {
        return $this->username ?? Str::slug($this->name, '');
    }
}
