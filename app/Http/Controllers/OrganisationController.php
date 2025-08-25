<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganisationRequest;
use App\Http\Resources\OrganisationResource;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganisationController extends Controller
{
    /**
     * Display a listing of organisations.
     */
    public function index(Request $request)
    {
        // Get user's organisations or return empty collection if none exist
        $organisations = $request->user()->organisations()
            ->with('users')
            ->paginate(10);

        return OrganisationResource::collection($organisations);
    }

    /**
     * Store a new organisation.
     */
    public function store(OrganisationRequest $request)
    {
        $organisation = Organisation::create($request->validated());
        
        // Always attach the creating user as admin
        $organisation->users()->attach(Auth::id(), ['role' => 'admin']);
        
        return new OrganisationResource($organisation->load('users'));
    }

    /**
     * Display the specified organisation.
     */
    public function show(Request $request, Organisation $organisation)
    {
        // Allow viewing if user belongs to organisation
        if ($organisation->users()->where('user_id', $request->user()->id)->exists()) {
            return new OrganisationResource($organisation->load('users'));
        }

        // For users without access, only return basic public info
        return new OrganisationResource($organisation->makeHidden(['users', 'email', 'phone']));
    }

    /**
     * Update the specified organisation.
     */
    public function update(OrganisationRequest $request, Organisation $organisation)
    {
        // Check if user belongs to the organisation and is an admin
        abort_if(!$organisation->users()->where('user_id', Auth::id())->where('role', 'admin')->exists(), 403);

        $organisation->update($request->validated());
        return new OrganisationResource($organisation->load('users'));
    }

    /**
     * Remove the specified organisation.
     */
    public function destroy(Request $request, Organisation $organisation)
    {
        // Check if user belongs to the organisation and is an admin
        abort_if(!$organisation->users()->where('user_id', Auth::id())->where('role', 'admin')->exists(), 403);

        $organisation->delete();
        return response()->json(null, 204);
    }

    /**
     * Join an organisation.
     */
    public function join(Request $request, Organisation $organisation)
    {
        $organisation->users()->attach($request->user()->id);
        return new OrganisationResource($organisation->load('users'));
    }

    /**
     * Leave an organisation.
     */
    public function leave(Request $request, Organisation $organisation)
    {
        $organisation->users()->detach($request->user()->id);
        return response()->json(null, 204);
    }
}
