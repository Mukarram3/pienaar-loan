<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LoanDocumentController extends Controller
{
    public function upload(Request $request, $loanId)
    {
        $loan = Loan::findOrFail($loanId);

        $request->validate([
            'document'      => 'required|file|max:10240',
            'document_type' => 'required|in:original_agreement,supporting,other',
        ]);

        $file = $request->file('document');
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $path = 'loan_documents/' . $loan->id . '/' . $filename;

        Storage::disk('local')->put($path, file_get_contents($file));

        LoanDocument::create([
            'loan_id'           => $loan->id,
            'uploaded_by'       => auth('admin')->id(),
            'document_type'     => $request->document_type,
            'original_filename' => $file->getClientOriginalName(),
            'file_path'         => $path,
            'mime_type'         => $file->getClientMimeType(),
            'file_size'         => $file->getSize(),
            'notes'             => $request->notes,
        ]);

        $notify[] = ['success', 'Document uploaded successfully.'];
        return back()->withNotify($notify);
    }

    public function download($id)
    {
        $doc = LoanDocument::findOrFail($id);

        if (!Storage::disk('local')->exists($doc->file_path)) {
            abort(404, 'File not found.');
        }

        return response()->download(
            storage_path('app/' . $doc->file_path),
            $doc->original_filename
        );
    }

    public function delete($id)
    {
        $doc = LoanDocument::findOrFail($id);
        $loanId = $doc->loan_id;

        if (Storage::disk('local')->exists($doc->file_path)) {
            Storage::disk('local')->delete($doc->file_path);
        }
        $doc->delete();

        $notify[] = ['success', 'Document deleted.'];
        return back()->withNotify($notify);
    }
}
