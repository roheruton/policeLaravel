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
        ////////////////////////////////////////////////

        error_log('Pruebaaa1');
        $path = $request->file('image')->getPathname();
        $image= fopen($path,'r');
        $img = fread($image, filesize($path));

        error_log('Pruebaaa2');

        $resultado = $this->listFaces($img);
            error_log($resultado);
    /////////////////////////////////////////////
        //if (count($resultado["Faces"]) > 0) {

            return response()->json(["data" => $resultado['FaceMatches']]);
        //}
        return response()->json(
            ["message" => "no se encontro"],
             Response::HTTP_BAD_REQUEST);
    }


    private function listFaces($imagebytes)
    {
        $client = new RekognitionClient(
            [
                'region' => env('AWS_DEFAULT_REGION', 'us-west-2'),
                'version' => 'latest'
            ]
        );

        $result = $client->searchFacesByImage([
            'CollectionId' => 'police', // REQUIRED
            'MaxFaces' => 3,
            'Image' => [
                'Bytes' =>$imagebytes
            ]
        ]);

        return $result;
    }
}
