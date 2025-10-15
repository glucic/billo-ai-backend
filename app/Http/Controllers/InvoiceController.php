<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class InvoiceController extends Controller
{
    /**
     * Display a paginated listing of Invoices
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $invoices = Invoice::latest()->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $invoices
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to fetch invoices: ' . $exception->getMessage(), [
                'stack' => $exception->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch invoices',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    public function store(InvoiceRequest $request): JsonResponse
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $invoice = Invoice::create([
                'invoice_number'    => $data['invoiceDetails']['invoiceNumber'],
                'invoice_date'      => $data['invoiceDetails']['invoiceDate'],
                'due_date'          => $data['invoiceDetails']['dueDate'] ?? null,
                'reference'         => $data['invoiceDetails']['reference'] ?? null,
                'issuer'            => $data['issuer'],
                'client'            => $data['client'],
                'items'             => $data['items'],
                'user_id'           => Auth::id(),
                'organisation_id'   => Auth::user()->organisations()->first()->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'invoice' => $invoice
            ], 201);
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Invoice creation failed: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'payload' => $data
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}