<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganisationRequest;
use App\Http\Resources\OrganisationResource;
use App\Models\Organisation;
use Illuminate\Http\Request;

class OrganisationController extends Controller
{
    /**
     * Display a listing of organisations.
     */
    public function index()
    {
        $organisations = Organisation::paginate(10);
        return OrganisationResource::collection($organisations);
    }

    /**
     * Store a new organisation.
     */
    public function store(OrganisationRequest $request)
    {
        $organisation = Organisation::create($request->validated());
        return new OrganisationResource($organisation);
    }

    /**
     * Display the specified organisation.
     */
    public function show(Organisation $organisation)
    {
        return new OrganisationResource($organisation);
    }

    /**
     * Update the specified organisation.
     */
    public function update(OrganisationRequest $request, Organisation $organisation)
    {
        $organisation->update($request->validated());
        return new OrganisationResource($organisation);
    }

    /**
     * Remove the specified organisation.
     */
    public function destroy(Organisation $organisation)
    {
        $organisation->delete();
        return response()->json(null, 204);
    }
}
