<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganisationRequest;
use App\Http\Resources\OrganisationResource;
use App\Models\Organisation;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrganisationController extends Controller
{
    /**
     * Display a listing of organisations.
     */
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', Organisation::class);
            
            // Get user's organisations or return empty collection if none exist
            $organisations = $request->user()->organisations()
                ->with('users')
                ->paginate(10);

            return OrganisationResource::collection($organisations);
        } catch (Throwable $e) {
            Log::error('Failed to fetch organisations: '.$e->getMessage(), [
                'stack' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch organisations',
                'error' => $e->getMessage()
            ], ($e instanceof AuthenticationException) ? 403 : 500);
        }
    }

    /**
     * Store a new organisation.
     */
    public function store(OrganisationRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $organisation = Organisation::create($data);

            // Always attach the creating user as admin
            $organisation->users()->attach(Auth::id(), ['role' => 'admin']);

            DB::commit();

            return new OrganisationResource($organisation->load('users'));
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Organisation creation failed: '.$e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'payload' => $data,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create organisation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified organisation.
     */
    public function show(Request $request, Organisation $organisation)
    {
        try {
            $this->authorize('view', $organisation);

            return new OrganisationResource($organisation->load('users'));
        } catch (Throwable $e) {
            Log::error('Failed to fetch organisation: '.$e->getMessage(), [
                'id' => $organisation->id ?? null,
                'stack' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch organisation',
                'error' => $e->getMessage(),
            ], ($e instanceof AuthenticationException) ? 403 : 500);
        }
    }

    /**
     * Update the specified organisation.
     */
    public function update(OrganisationRequest $request, Organisation $organisation)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $this->authorize('update', $organisation);

            $organisation->update($data);

            DB::commit();

            return new OrganisationResource($organisation->load('users'));
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Organisation update failed: '.$e->getMessage(), [
                'id' => $organisation->id,
                'stack' => $e->getTraceAsString(),
                'payload' => $data,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update organisation',
                'error' => $e->getMessage(),
            ], ($e instanceof AuthenticationException) ? 403 : 500);
        }
    }

    /**
     * Remove the specified organisation.
     */
    public function destroy(Request $request, Organisation $organisation)
    {
        DB::beginTransaction();

        try {
            $this->authorize('delete', $organisation);

            $organisation->delete();

            DB::commit();

            return response()->json(null, 204);
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Organisation delete failed: '.$e->getMessage(), [
                'id' => $organisation->id,
                'stack' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete organisation',
                'error' => $e->getMessage(),
            ], ($e instanceof AuthenticationException) ? 403 : 500);
        }
    }

    /**
     * Join an organisation.
     */
    public function join(Request $request, Organisation $organisation)
    {
        try {
            $this->authorize('join', $organisation);

            if (! $organisation->users()->where('user_id', $request->user()->id)->exists()) {
                $organisation->users()->attach($request->user()->id);
            }

            return new OrganisationResource($organisation->load('users'));
        } catch (Throwable $e) {
            Log::error('Failed to join organisation: '.$e->getMessage(), [
                'id' => $organisation->id,
                'stack' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to join organisation',
                'error' => $e->getMessage(),
            ], ($e instanceof AuthenticationException) ? 403 : 500);
        }
    }

    /**
     * Leave an organisation.
     */
    public function leave(Request $request, Organisation $organisation)
    {
        try {
            $this->authorize('leave', $organisation);

            $organisation->users()->detach($request->user()->id);
            return response()->json(null, 204);
        } catch (Throwable $e) {
            Log::error('Failed to leave organisation: '.$e->getMessage(), [
                'id' => $organisation->id,
                'stack' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to leave organisation',
                'error' => $e->getMessage(),
            ], ($e instanceof AuthenticationException) ? 403 : 500);
        }
    }
}
