<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Aws\Rekognition\RekognitionClient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
//use Illuminate\Http\Client\Request;

class DetectingFaceController extends Controller
{
    private $endPoint;
    public function __construct()
    {
        $this->endPoint = "https://sw-evento.s3.us-west-2.amazonaws.com/";
        $this->database = app('firebase.database');
        $this-> client = new RekognitionClient(
            [
                'region' => env('AWS_DEFAULT_REGION', 'us-west-2'),
                'version' => 'latest'
            ]
        );
    }

    public function index()
    {
        return view('admin.prueba');
    }
    public function detectedFaceImage(Request $request) 
    {   
        if ($request != null):
            $img =$this->getImage($request);
            $bounding="";
            $faces=$this->validatePhotos($img); 
          
            if(empty($faces->FaceDetails)):
                return response()->json(["data" => 'No se encontro rostros en la imagen enviada o hay mas de un rostro']);
            else:                       
            $resultado = $this->listFaces($img);

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
                ["data" => "no se encontró coincidencia"],
                Response::HTTP_BAD_REQUEST);
                
            endif;
        else: 
            
            return response()->json(
                $request,
                 Response::HTTP_BAD_REQUEST);
            
        endif;
    }

    private function listFaces($imagebytes)
    {
        $result = $this->client->searchFacesByImage([
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
            $result = $this->faceDetected($photo);
            if (count($result['FaceDetails']) > 1 || count($result['FaceDetails']) <= 0) {
                return $result;
            }else {
            return $result;}
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
        $imagePath =$photo->file('image')->getPathname();//error_log('sss'.$imagePath);
        $fp_image = fopen($imagePath, 'r');
        $image = fread($fp_image, filesize($imagePath));
        fclose($fp_image);
        return $image;
    }





}
