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
        $this->database = app('firebase.database');
    }

    public function index()
    {
        return view('admin.prueba');
    }
    public function detectedFaceImage(Request $request) 
    {  
        ////////////////////////////////////////////////

        error_log('Pruebaaa1');
      /*  $path = $request->file('image')->getPathname();
        $path = $request->getPathname();
        $image= fopen($path,'r');
        $img = fread($image, filesize($path));
        fclose($image);*/
        $img =$this->getImage($request);
       
        error_log('Pruebaaa2');
        ///validar si en la foto recibida hay un rostro
        $bounding="";
        $faces=$this->validatePhotos($img);
            if (count($faces['FaceDetails'])>1 ||count($faces['FaceDetails'])<=0 ) {
                return response()->json(
                    ["message" => "Foto inválida, es posible que haya mas de un rostro o ningún rostro"],
                    Response::HTTP_BAD_REQUEST);
            }

        
        $resultado = $this->listFaces($img);
            error_log($resultado);
    /////////////////////////////////////////////
        if (count($resultado["FaceMatches"]) > 0) {
            $policeID=$resultado['FaceMatches'][0]['Face']['ExternalImageId'];
            $confidence= $resultado['FaceMatches'][0]['Face']['Confidence'];            
            $bounding=  $faces['FaceDetails'][0]['BoundingBox'];
            $policeInfo =$this->database
                                ->getReference('police/'.$policeID)
                                ->getValue();
            $resultado= [
                    'id'=>$policeID,
                    'confidence'=>$confidence,
                    'bounding' =>$bounding,
                    'info'=>$policeInfo
            ];
                    
            return response()->json(["data" => $resultado]);
        }
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

    private function validatePhotos($photo)
    {
       // foreach ($photos as $photo) {
            //$image = $this->getImage($photo);
            $result = $this->faceDetected($photo);
            if (count($result['FaceDetails']) > 1 || count($result['FaceDetails']) <= 0) {
                return $result;
            }
            return $result;
       // }
    }

    private function faceDetected($imageBase64)
    {
        $client = new RekognitionClient(
            [
                'region' => env('AWS_DEFAULT_REGION', 'us-west-2'),
                'version' => 'latest'
            ]
        );
        $result = $client->detectFaces([
            'Attributes' => ['ALL'],
            'Image' => [
                'Bytes' => $imageBase64,
            ],
        ]);
        return $result;
    }
    public function getImage($photo)
    {
        $imagePath =$photo-> file('image')->getPathname();
        $fp_image = fopen($imagePath, 'r');
        $image = fread($fp_image, filesize($imagePath));
        fclose($fp_image);
        return $image;
    }




}
