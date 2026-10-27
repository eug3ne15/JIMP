<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UniversityController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $search = trim((string) $request->query('search', ''));

        $totalCount = University::count();
        $registeredCount = University::registered()->count();
        $pendingCount = University::pending()->count();

        $query = University::query()
            ->with('requester')
            ->withCount('courses');

        if ($status === 'registered') {
            $query->registered();
        } elseif ($status === 'pending') {
            $query->pending();
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $universities = $query
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('hr.universities.index', compact(
            'universities',
            'totalCount',
            'registeredCount',
            'pendingCount',
            'status',
            'search'
        ));
    }

    public function create(): View
    {
        return view('hr.universities.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('universities', 'name')],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        University::create([
            'name' => trim($validated['name']),
            'location' => isset($validated['location']) ? trim($validated['location']) : null,
            'status' => 'registered',
        ]);

        return redirect()->route('hr.universities.index')
            ->with('status', "University '{$validated['name']}' has been registered successfully.");
    }

    public function approve(University $university): RedirectResponse
    {
        $university->update([
            'status' => 'registered',
        ]);

        return redirect()->back()
            ->with('status', "University '{$university->name}' has been approved and registered.");
    }

    public function edit(University $university): View
    {
        return view('hr.universities.edit', compact('university'));
    }

    public function update(Request $request, University $university): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('universities', 'name')->ignore($university->id)],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:registered,pending'],
        ]);

        $university->update([
            'name' => trim($validated['name']),
            'location' => isset($validated['location']) ? trim($validated['location']) : null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('hr.universities.index')
            ->with('status', "University '{$university->name}' updated successfully.");
    }

    public function destroy(University $university): RedirectResponse
    {
        if ($university->courses()->exists()) {
            return redirect()->back()->withErrors([
                'university' => "Cannot delete '{$university->name}' because it has associated courses.",
            ]);
        }

        $name = $university->name;
        $university->delete();

        return redirect()->route('hr.universities.index')
            ->with('status', "University '{$name}' deleted successfully.");
    }
}
