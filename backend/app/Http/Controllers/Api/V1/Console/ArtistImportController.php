<?php

namespace App\Http\Controllers\Api\V1\Console;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Backend\ArtistImportController as BackendArtistImportController;
use App\Imports\ArtistsImport;
use App\Services\ArtistRegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Bulk artist import, as JSON. Extends the Blade controller so parsing,
 * validation and registration logic stay in one place.
 *
 *   GET  /artists/import/meta                       types + max rows
 *   GET  /artists/import/template/{xlsx|csv}?type=  file download (inherited)
 *   POST /artists/import                            multipart: registration_type, file -> { token, ... }
 *   GET  /artists/import/{token}                    session rows + counts
 *   POST /artists/import/{token}/process            imports the next batch (inherited JSON)
 *   GET  /artists/import/{token}/report?format=     file download (inherited)
 */
class ArtistImportController extends BackendArtistImportController
{
    use ApiResponse;

    public function meta(): JsonResponse
    {
        return $this->success([
            'types'    => ArtistRegistrationService::TYPE_LABELS,
            'max_rows' => ArtistsImport::MAX_ROWS,
        ]);
    }

    public function upload(Request $request): JsonResponse
    {
        $token = $this->createImportSession($request);

        return $this->state($token, 'File checked. Review the rows below, then start the import.', 201);
    }

    public function showState(string $token): JsonResponse
    {
        return $this->state($token);
    }

    public function processBatch(string $token): JsonResponse
    {
        $res = parent::process($token);
        $body = $res->getData(true);

        if ($res->getStatusCode() !== 200) {
            return $this->error($body['message'] ?? 'Import failed.', $res->getStatusCode(), 'import_busy');
        }

        return $this->success($body, $body['done'] ? 'Import completed.' : 'Batch imported.');
    }

    private function state(string $token, string $message = 'OK', int $status = 200): JsonResponse
    {
        $state = $this->loadState($token);
        unset($state['user_id']);
        $state['rows'] = array_map(function ($r) {
            unset($r['data']);
            return $r;
        }, $state['rows']);

        return $this->success([
            'token'  => $token,
            'state'  => $state,
            'counts' => $this->counts($state),
        ], $message, $status);
    }
}
