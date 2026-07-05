<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectContractController extends Controller
{
    public function show(Project $project, Request $request)
    {
        $files = $project->contract_file;

        if (empty($files)) {
            abort(404);
        }

        $targetFile = null;
        $fileIndex = null;

        if (is_string($files)) {
            $targetFile = $files;
        } elseif (is_array($files)) {
            $files = array_filter($files); // Remove empty
            
            if (empty($files)) {
                abort(404);
            }

            // Check if looking for specific index
            if ($request->has('file_index')) {
                $fileIndex = $request->query('file_index');
                if (isset($files[$fileIndex])) {
                    $targetFile = $files[$fileIndex];
                } else {
                    abort(404);
                }
            } elseif (count($files) === 1) {
                // If only 1 file and no index specified, strictly use it
                $targetFile = reset($files);
            } else {
                 // Multiple files and no index -> Show List View
                 return view('pages.project-contracts', compact('project', 'files'));
            }
        }

        if (!$targetFile) {
            abort(404);
        }

        if (!Storage::disk('local')->exists($targetFile)) {
             abort(404);
        }

        $mimeType = Storage::disk('local')->mimeType($targetFile);
        $filename = basename($targetFile);
        
        // If query param 'stream' is present, return the file stream (for iframe or download)
        if ($request->has('stream')) {
             return Storage::disk('local')->response(
                $targetFile,
                $filename,
                ['Content-Disposition' => 'inline']
            );
        }

        // Logic based on file type
        // Word / Excel / CSV -> Force Download
        $downloadMimes = [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv'
        ];

        if (in_array($mimeType, $downloadMimes)) {
             return Storage::disk('local')->download($targetFile, $filename);
        }

        // PDF -> Show Viewer
        if ($mimeType === 'application/pdf') {
            // Generate URL to this same route BUT with stream=1
            // We need to preserve file_index if present
            $params = ['project' => $project->id, 'stream' => 1];
            if (!is_null($fileIndex)) {
                $params['file_index'] = $fileIndex;
            }
            $url = route('projects.contract', $params);

            return view('pages.pdf-viewer', compact('url', 'filename'));
        }

        // Default -> Just stream generic file (images etc)
        return Storage::disk('local')->response(
            $targetFile,
            $filename,
            ['Content-Disposition' => 'inline']
        );
    }
}
