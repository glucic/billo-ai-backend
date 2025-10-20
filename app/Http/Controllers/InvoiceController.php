<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequest;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class InvoiceController extends Controller
{
    /**
     * Display a paginated listing of Invoices
     */
    public function index(IndexRequest $request): JsonResponse
    {
        try {
            if (! Auth::check()) {
                throw new AuthenticationException('User must be authenticated');
            }

            $validated = $request->validated();

            $perPage = (int) ($validated['per_page'] ?? 15);
            $sortBy = $validated['sort_by'] ?? 'created_at';
            $sortOrder = $validated['sort_order'] ?? 'desc';
            $search = $validated['search'] ?? null;

            $allowedSortColumns = ['created_at', 'invoice_number', 'client_name', 'total'];
            if (! in_array($sortBy, $allowedSortColumns)) {
                $sortBy = 'created_at';
            }

            $organisationIds = Auth::user()->organisations()->pluck('organisations.id');
            $invoices = Invoice::whereIn('organisation_id', $organisationIds)
                ->search($search)
                ->orderBy($sortBy, $sortOrder)
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $invoices,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to fetch invoices: '.$exception->getMessage(), [
                'stack' => $exception->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch invoices',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function store(InvoiceRequest $request): JsonResponse
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $invoice = Invoice::create([
                'invoice_number' => $data['invoiceDetails']['invoiceNumber'],
                'invoice_date' => $data['invoiceDetails']['invoiceDate'],
                'due_date' => $data['invoiceDetails']['dueDate'] ?? null,
                'reference' => $data['invoiceDetails']['reference'] ?? null,
                'issuer' => $data['issuer'],
                'client' => $data['client'],
                'items' => $data['items'],
                'totals' => $data['totals'],
                'user_id' => Auth::id(),
                'organisation_id' => Auth::user()->organisations()->first()->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'invoice' => $invoice,
            ], 201);
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Invoice creation failed: '.$e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'payload' => $data,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            if (! Auth::check()) {
                throw new AuthenticationException('User must be authenticated');
            }

            DB::beginTransaction();

            $user = Auth::user();
            $organisationIds = $user->organisations()->pluck('organisations.id');

            // Only allow access to invoices belonging to user's organisations
            $invoice = Invoice::whereIn('organisation_id', $organisationIds)
                ->where('id', $id)
                ->first();

            DB::commit();

            if (! $invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found or access denied.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $invoice,
            ]);
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('Failed to fetch invoice: '.$exception->getMessage(), [
                'id' => $id,
                'stack' => $exception->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch invoice details',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }
}
