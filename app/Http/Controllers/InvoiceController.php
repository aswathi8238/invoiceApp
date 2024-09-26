<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceSub;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('create'); // Render the form
    }

    public function store(Request $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'name' => 'required|alpha',
                'date' => 'required|date',
                'file_upload' => 'nullable|file|max:3072|mimes:jpg,png,pdf', // 3MB max, jpg/png/pdf only
                'rows' => 'required|array|min:1', // Ensure rows is an array
                'rows.*.qty' => 'required|numeric',
                'rows.*.amount' => 'required|numeric',
                'rows.*.taxAmount' => 'required|numeric',
            ]);

            // Handle file upload
          //  $filePath = '';
            if ($request->hasFile('file_upload') && $request->file('file_upload')->isValid()) {
                $file = $request->file('file_upload');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('invoices', $fileName, 'public');
            }
              // print_r($filePath);
            // Save invoice data within a transaction
             DB::transaction(function () use ($request, $filePath) {
                // Create the invoice
                $invoice = Invoice::create([
                     'name' => $request->name,
                    'date' => $request->date,
                 'file_upload' => $filePath, // This will be an empty string if no file was uploaded
                ]);
               // print_r($filePath);
             // Save invoice rows (InvoiceSub)
                 foreach ($request->rows as $row) {
                    InvoiceSub::create([
                        'invoice_id' => $invoice->id,
                        'qty' => $row['qty'],
                        'amount' => $row['amount'],
                        'total_amount' => $row['qty'] * $row['amount'],
                        'tax_amount' => $row['taxAmount'],
                        'net_amount' => ($row['qty'] * $row['amount']) + $row['taxAmount'],
                   ]);
                }
            });
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // Log the error for debugging
           \Log::error('Invoice submission failed: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Error submitting invoice', 'error' => $e->getMessage()], 500);
        }
    }
}