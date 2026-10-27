<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department_id',
        'university_id',
        'field_of_study',
        'description',
        'requirements',
        'status',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function internships(): HasMany
    {
        return $this->hasMany(Internship::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function isOpen(): bool
    {
        return ($this->status ?? 'open') === 'open';
    }

    public function getFieldsOfStudyListAttribute(): array
    {
        if (! $this->field_of_study) {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', preg_split('/[,;\n]+/', $this->field_of_study))
        ));
    }

    public function getFieldsOfStudyCountAttribute(): int
    {
        return count($this->fields_of_study_list);
    }

    public function addFieldOfStudy(string $field): void
    {
        $current = $this->fields_of_study_list;
        $newItems = array_filter(array_map('trim', preg_split('/[,;\n]+/', $field)));

        $combined = array_unique(array_merge($current, $newItems));
        $this->field_of_study = ! empty($combined) ? implode(', ', $combined) : null;
        $this->save();
    }

    public function removeFieldOfStudy(string $field): void
    {
        $current = $this->fields_of_study_list;
        $updated = array_values(array_filter($current, fn ($item) => strcasecmp($item, trim($field)) !== 0));
        $this->field_of_study = ! empty($updated) ? implode(', ', $updated) : null;
        $this->save();
    }

    public function getFieldOfStudyApplicationsCount(string $field): int
    {
        $target = strtolower(trim($field));
        $apps = $this->relationLoaded('applications') ? $this->applications : $this->applications()->get();

        return $apps->filter(function ($app) use ($target) {
            return strtolower(trim((string) $app->field_of_study)) === $target;
        })->count();
    }

    public function getFieldsOfStudyBreakdownAttribute(): \Illuminate\Support\Collection
    {
        $fields = $this->fields_of_study_list;
        $apps = $this->relationLoaded('applications') ? $this->applications : $this->applications()->get();
        $totalCourseApps = $apps->count();

        $breakdown = collect($fields)->map(function ($field) use ($apps, $totalCourseApps) {
            $target = strtolower(trim($field));
            $matching = $apps->filter(function ($app) use ($target) {
                return strtolower(trim((string) $app->field_of_study)) === $target;
            });

            $total = $matching->count();
            $percentage = $totalCourseApps > 0 ? round(($total / $totalCourseApps) * 100, 1) : 0;

            return [
                'name' => $field,
                'total_applications' => $total,
                'pending' => $matching->where('status', 'pending')->count(),
                'shortlisted' => $matching->where('status', 'shortlisted')->count(),
                'rejected' => $matching->where('status', 'rejected')->count(),
                'info_requested' => $matching->where('status', 'info_requested')->count(),
                'placed' => $matching->whereNotNull('internship_id')->count(),
                'percentage' => $percentage,
            ];
        });

        // Also check for applications registered under fields not explicitly in the active fields list
        $knownFieldsLower = collect($fields)->map(fn ($f) => strtolower(trim($f)))->all();
        $otherApps = $apps->filter(function ($app) use ($knownFieldsLower) {
            $field = strtolower(trim((string) $app->field_of_study));
            return !in_array($field, $knownFieldsLower);
        });

        if ($otherApps->isNotEmpty()) {
            $otherGrouped = $otherApps->groupBy(fn ($app) => trim((string) $app->field_of_study) ?: 'General / Other');
            foreach ($otherGrouped as $otherName => $group) {
                $total = $group->count();
                $percentage = $totalCourseApps > 0 ? round(($total / $totalCourseApps) * 100, 1) : 0;
                $breakdown->push([
                    'name' => $otherName,
                    'total_applications' => $total,
                    'pending' => $group->where('status', 'pending')->count(),
                    'shortlisted' => $group->where('status', 'shortlisted')->count(),
                    'rejected' => $group->where('status', 'rejected')->count(),
                    'info_requested' => $group->where('status', 'info_requested')->count(),
                    'placed' => $group->whereNotNull('internship_id')->count(),
                    'percentage' => $percentage,
                ]);
            }
        }

        return $breakdown;
    }
}
