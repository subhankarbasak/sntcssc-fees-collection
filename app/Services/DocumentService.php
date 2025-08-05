<?php

// app/Services/DocumentService.php
namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Events\DocumentUploaded;
use App\Events\DocumentVerified;
use App\Events\DocumentRejected;

class DocumentService
{
    public function uploadDocument($model, array $data)
    {
        try {
            DB::beginTransaction();
            
            $file = $data['file'];
            $path = $file->store('documents', 'public');
            
            $document = Document::create([
                'title' => $data['title'],
                'type' => $data['type'],
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'extension' => $file->getClientOriginalExtension(),
                'tags' => $data['tags'] ?? [],
                'mime_type' => $file->getMimeType(),
                'expires_at' => $data['expires_at'] ?? null,
                'is_public' => $data['is_public'] ?? false,
                'description' => $data['description'] ?? null,
                'source' => $data['source'] ?? 'upload',
                'uploaded_at' => now(),
                'verification_status' => 'pending',
                'status' => true,
                'created_by' => auth()->id(),
            ]);
            
            // Attach the document to the model
            $model->documents()->save($document);
            
            DB::commit();
            
            DocumentUploaded::dispatch($document);
            
            return $document;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function updateDocument(Document $document, array $data)
    {
        try {
            DB::beginTransaction();
            
            $updateData = [
                'title' => $data['title'] ?? $document->title,
                'type' => $data['type'] ?? $document->type,
                'tags' => $data['tags'] ?? $document->tags,
                'expires_at' => $data['expires_at'] ?? $document->expires_at,
                'is_public' => $data['is_public'] ?? $document->is_public,
                'description' => $data['description'] ?? $document->description,
                'updated_by' => auth()->id(),
            ];
            
            // If a new file is uploaded
            if (isset($data['file'])) {
                $file = $data['file'];
                $path = $file->store('documents', 'public');
                
                // Delete old file
                Storage::disk('public')->delete($document->file_path);
                
                $updateData['file_path'] = $path;
                $updateData['file_name'] = $file->getClientOriginalName();
                $updateData['size'] = $file->getSize();
                $updateData['extension'] = $file->getClientOriginalExtension();
                $updateData['mime_type'] = $file->getMimeType();
                $updateData['uploaded_at'] = now();
                $updateData['verification_status'] = 'pending';
                $updateData['verified_at'] = null;
            }
            
            $document->update($updateData);
            
            DB::commit();
            
            return $document;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function verifyDocument(Document $document, string $status, string $notes = null)
    {
        try {
            DB::beginTransaction();
            
            $document->update([
                'verification_status' => $status,
                'verified_at' => now(),
                'notes' => $notes,
                'updated_by' => auth()->id(),
            ]);
            
            DB::commit();
            
            if ($status === 'verified') {
                DocumentVerified::dispatch($document);
            } else if ($status === 'rejected') {
                DocumentRejected::dispatch($document);
            }
            
            return $document;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function deleteDocument(Document $document)
    {
        try {
            DB::beginTransaction();
            
            // Delete the file from storage
            Storage::disk('public')->delete($document->file_path);
            
            // Delete the document record
            $document->delete();
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}