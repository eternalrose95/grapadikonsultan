<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectReportController extends Controller
{
    public function show(Project $project, Request $request)
    {
        $files = $project->report_file;

        if (empty($files)) {
            abort(404);
        }

        $targetFile = null;
        $fileIndex = null;

        if (is_string($files)) {
            $targetFile = $files;
        } elseif (is_array($files)) {
            $files = array_filter($files); 
            
            if (empty($files)) {
                abort(404);
            }

            if ($request->has('file_index')) {
                $fileIndex = $request->query('file_index');
                if (isset($files[$fileIndex])) {
                    $targetFile = $files[$fileIndex];
                } else {
                    abort(404);
                }
            } elseif (count($files) === 1) {
                $targetFile = reset($files);
            } else {
                 return view('pages.project-reports', compact('project', 'files'));
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
        
        if ($request->has('stream')) {
             return Storage::disk('local')->response(
                $targetFile,
                $filename,
                ['Content-Disposition' => 'inline']
            );
        }

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

        if ($mimeType === 'application/pdf') {
            $params = ['project' => $project->id, 'stream' => 1];
            if (!is_null($fileIndex)) {
                $params['file_index'] = $fileIndex;
            }
            $url = route('projects.report', $params);

            return view('pages.pdf-viewer', compact('url', 'filename'));
        }

        return Storage::disk('local')->response(
            $targetFile,
            $filename,
            ['Content-Disposition' => 'inline']
        );
    }
}
