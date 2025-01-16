<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\DataStreamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DataStreamController extends Controller
{
    protected $dataStreamService;

    public function __construct(DataStreamService $dataStreamService)
    {
        $this->dataStreamService = $dataStreamService;
    }

    public function analyze(Request $request)
    {
        //echo "hello";die;
        try {
            $validated = $request->validate([
                'stream' => 'required|string',
                'k' => 'required|integer|min:1',
                'top' => 'required|integer|min:1',
                'exclude' => 'array',
            ]);
    
            $stream = $validated['stream'];
            $k = $validated['k'];
            $top = $validated['top'];
            $exclude = $validated['exclude'] ?? [];
    
            $result = $this->dataStreamService->analyzeStream($stream, $k, $top, $exclude);
    
            // Return successful response
            return response()->json(['data' => $result], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function analyzeFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:txt',
            'k' => 'required|integer|min:1',
            'top' => 'required|integer|min:1',
            'exclude' => 'array',
            'exclude.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $stream = '';
        $handle = fopen($file->getRealPath(), 'r');
        while (($line = fgets($handle)) !== false) {
            $stream .= trim($line);
        }
        fclose($handle);

        $data = $validator->validated();
        $result = $this->dataStreamService->analyzeStream(
            $stream,
            $data['k'],
            $data['top'],
            $data['exclude'] ?? []
        );

        return response()->json(['data' => $result]);
    }
}
