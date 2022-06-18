<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Aws\Rekognition\RekognitionClient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DetectingFaceController extends Controller
{
    private $endPoint;
    public function __construct()
    {
        $this->endPoint = "https://sw-evento.s3.us-west-2.amazonaws.com/";
    }

    public function index()
    {
        return view('admin.prueba');
    }
    public function detectedFaceImage(Request $request)
    {
        $request->validate([
            "images" => "required|mimes:png,jpg",
        ]);

        $resultado = $this->listFaces($request->image);
        if (count($resultado["Faces"]) > 0) {

            return response()->json(["data" => $resultado["Faces"]]);
        }
        return response()->json(["message" => "no se encontro"], Response::HTTP_BAD_REQUEST);
    }

    private function listFaces($request)
    {
        $client = new RekognitionClient(
            [
                'region' => env('AWS_DEFAULT_REGION', 'us-west-2'),
                'version' => 'latest'
            ]
        );

        $result = $client->listFaces([
            'CollectionId' => 'police', // REQUIRED
            'MaxResults' => 20,
        ]);

        return $result;
    }
}
