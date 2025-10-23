<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequest;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class InvoiceController extends Controller
{
    /**
     * Display a paginated listing of Invoices
     */
    public function index(IndexRequest $request)
    {
        try {
            $this->authorize('viewAny', Invoice::class);

            $user = Auth::user();
            $validated = $request->validated();

            $perPage = (int) ($validated['per_page'] ?? 15);
            $sortBy = $validated['sort_by'] ?? 'created_at';
            $sortOrder = $validated['sort_order'] ?? 'desc';
            $search = $validated['search'] ?? null;

            $allowedSortColumns = ['created_at', 'invoice_number', 'client_name', 'total'];
            if (! in_array($sortBy, $allowedSortColumns)) {
                $sortBy = 'created_at';
            }
            
            $organisationIds = $user->organisations()->pluck('organisations.id')->toArray();

            if (count($organisationIds) > 0) {
                $invoices = Invoice::whereIn('organisation_id', $organisationIds)
                    ->search($search)
                    ->orderBy($sortBy, $sortOrder)
                    ->paginate($perPage);
            } else {
                $invoices = Invoice::where('user_id', $user->id)
                    ->search($search)
                    ->orderBy($sortBy, $sortOrder)
                    ->paginate($perPage);
            }

            return InvoiceResource::collection($invoices);
        } catch (Throwable $exception) {
            Log::error('Failed to fetch invoices: '.$exception->getMessage(), [
                'stack' => $exception->getTraceAsString(),
            ]);

            $status = ($exception instanceof AuthenticationException) ? 403 : 500;

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch invoices',
                'error' => $exception->getMessage()
            ], $status);
        }
    }

    public function store(InvoiceRequest $request)
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
                'legal' => $data['legal'] ?? null,
                'footer' => $data['footer'] ?? null,
                'user_id' => Auth::id(),
                'organisation_id' => Auth::user()->organisations()->first()->id ?? null,
            ]);

            DB::commit();

            return new InvoiceResource($invoice);
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

    public function update(InvoiceRequest $request, int $id)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $invoice = Invoice::findOrFail($id);

            $this->authorize('update', $invoice);

            $invoice->update([
                'invoice_number' => $data['invoiceDetails']['invoiceNumber'],
                'invoice_date' => $data['invoiceDetails']['invoiceDate'],
                'due_date' => $data['invoiceDetails']['dueDate'] ?? null,
                'reference' => $data['invoiceDetails']['reference'] ?? null,
                'issuer' => $data['issuer'],
                'client' => $data['client'],
                'items' => $data['items'],
                'totals' => $data['totals'],
                'legal' => $data['legal'] ?? null,
                'footer' => $data['footer'] ?? null,
            ]);

            DB::commit();

            return new InvoiceResource($invoice->fresh());
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Invoice update failed: '.$e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'payload' => $data,
                'id' => $id,
            ]);

            $status = ($e instanceof AuthenticationException) ? 403 : 500;

            return response()->json([
                'success' => false,
                'message' => 'Failed to update invoice',
                'error' => $e->getMessage(),
            ], $status);
        }
    }

    public function show(int $id)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            $invoice = Invoice::where('id', $id)->first();

            if (! $invoice) {
                DB::commit();
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found.',
                ], 404);
            }

            $this->authorize('view', $invoice);

            DB::commit();

            return new InvoiceResource($invoice);
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('Failed to fetch invoice: '.$exception->getMessage(), [
                'id' => $id,
                'stack' => $exception->getTraceAsString(),
            ]);

            $status = ($exception instanceof AuthenticationException) ? 403 : 500;

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch invoice details',
                'error' => $exception->getMessage(),
            ], $status);
        }
    }
}
