<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Traits\UploadAssetTrait;

class DocumentController extends Controller
{
    use UploadAssetTrait;

    public function documents()
    {
        $documents = Document::with(['asset', 'menuItem', 'user.role'])->orderByDesc('id')->get();
        return view('backend.documents.list', compact('documents'));
    }

    public function createEdit($id = null)
    {
        $document = null;
        if ($id) {
            $documentId = Crypt::decrypt($id);
            $document = Document::with('asset')->findOrFail($documentId);
        }

        $type = MenuItem::where('type', 3)->where('active', 1)->pluck('title', 'id');
        $documentTypes = [1 => 'PDF Upload', 2 => 'External URL'];

        return view('backend.documents.addEdit', compact('document', 'type', 'documentTypes'));
    }

    public function saveUpdate(Request $request)
    {
        $document = null;
        if ($request->filled('id')) {
            try {
                $documentId = Crypt::decrypt($request->input('id'));
            } catch (\Throwable $e) {
                return back()->withErrors(['id' => 'Invalid document reference.'])->withInput();
            }
            $document = Document::findOrFail($documentId);
        }

        $documentType = (int) $request->input('document_type');
        $rules = [
            'id' => ['nullable', 'string'],
            'title' => ['required', 'regex:/^[A-Za-z0-9\s.\-_]+$/'],
            'description' => ['nullable'],
            'menu' => ['required', 'integer', 'exists:menu_items,id'],
            'document_type' => ['required', 'integer', 'in:1,2'],
            'publish_start_date' => ['required', 'date_format:Y-m-d\\TH:i'],
            'publish_end_date' => ['required', 'date_format:Y-m-d\\TH:i', 'after_or_equal:publish_start_date'],
            'status' => ['required', 'in:0,1'],
        ];

        if ($documentType === 1) {
            $rules['file'] = [(!$document || !$document->assets_id) ? 'required' : 'nullable', 'file', 'mimes:pdf', 'max:10240'];
        } elseif ($documentType === 2) {
            $rules['document_url'] = ['required', 'url', 'max:555'];
        }

        $validated = $request->validate($rules, [
            'title.required' => 'Document title is required.',
            'title.regex' => 'The title may only contain letters, numbers, spaces, dots, hyphens, and underscores.',
            'menu.required' => 'Please select Menu.',
            'menu.exists' => 'Invalid Menu selected.',
            'document_type.required' => 'Please select Document Type.',
            'document_type.in' => 'Invalid Document Type selected.',
            'file.required' => 'Please upload a PDF document.',
            'file.mimes' => 'Only PDF files are allowed.',
            'file.max' => 'Please upload a file smaller than or equal to 10MB.',
            'document_url.required' => 'Please enter Document URL.',
            'document_url.url' => 'Please enter a valid URL.',
            'publish_start_date.required' => 'Please select Publish Start Date.',
            'publish_end_date.required' => 'Please select Publish End Date.',
            'publish_end_date.after_or_equal' => 'Publish End Date must be after or equal to Publish Start Date.',
        ]);

        $menu = MenuItem::where('id', $validated['menu'])->where('type', 3)->where('active', 1)->firstOrFail();
        $oldAssetId = $document ? $document->assets_id : null;

        if (!$document) {
            $document = new Document();
            $document->created_by = Auth::id();
        } else {
            $document->updated_by = Auth::id();
        }

        $document->title = trim($validated['title']);
        $document->description = $validated['description'];
        $document->parent_menu_id = $menu->id;
        $document->document_type = $documentType;
        $document->publish_start_date = $validated['publish_start_date'];
        $document->publish_end_date = $validated['publish_end_date'];
        $document->active = $validated['status'];

        if ($documentType === 1) {
            $document->document_url = null;
            if ($request->hasFile('file')) {
                if ($oldAssetId) {
                    $this->updateAssetInPublic($request->file('file'), $oldAssetId, 'documents');
                } else {
                    $document->assets_id = $this->uploadAssetToPublic($request->file('file'), 'documents');
                }
            }
        } else {
            $document->document_url = trim($validated['document_url']);
            $document->assets_id = null;
        }

        $document->save();
        if ($documentType === 2 && $oldAssetId) {
            $this->deleteAssetById($oldAssetId);
        }

        $message = $document->wasRecentlyCreated ? 'Document created successfully!' : 'Document updated successfully!';
        return redirect()->route('documents.list')->with('success', $message);
    }

    public function documentstatus(Request $request)
    {
        $request->validate(['id' => 'required|exists:documents,id', 'status' => 'required|boolean']);
        $document = Document::findOrFail($request->id);
        $document->active = $request->status;
        $document->save();

        return response()->json([
            'success' => true,
            'status' => $document->active,
            'message' => $document->active ? 'Document activated successfully.' : 'Document deactivated successfully.',
        ]);
    }

    public function documentsdelete(Request $request)
    {
        try {
            $document = Document::findOrFail($request->id);
            if ($document->assets_id) $this->deleteAssetById($document->assets_id);
            $document->delete();
            return response()->json(['success' => true, 'message' => 'Document and related assets deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Document could not be deleted.'], 400);
        }
    }
}
