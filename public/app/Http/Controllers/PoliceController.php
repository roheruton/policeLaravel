<?php

namespace App\Http\Controllers;

use App\Http\Requests\PoliceRequest;
use App\Models\Avatar;
use App\Models\Police;
use Aws\Rekognition\RekognitionClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PoliceController extends Controller
{

    private $endPoint;
    public function __construct()
    {
        $this->endPoint = "https://sw-evento.s3.us-west-2.amazonaws.com/";
    }
    public function index()
    {
        $policies = Police::with('avatars')->get();
        return view('admin.police.index')->with('policies', $policies);
    }

    public function create()
    {
        return view('admin.police.create');
    }

    public function store(PoliceRequest $request)
    {

        try {
            DB::beginTransaction();
            $police = new Police();
            $police->ci = $request->ci;
            $police->name = $request->name;
            $police->last_name = $request->last_name;
            $police->dateOfBirth = $request->dateOfBirth;
            $police->save();
            if (!$this->validatePhotos($request->photos)) {
                return back()->with('error', "detector de fotos invalidas");
            }
            $this->createCollectionAws('police');
            $pathSavePhoto = $this->savePhotos($request->photos, $police->id);
            $arrayIndexFaces = $this->getIndexFaces($request->photos);
            //dd($arrayIndexFace);

            if (count($pathSavePhoto) == count($arrayIndexFaces)) {
                $i = 0;
                $avatar = [];
                foreach ($arrayIndexFaces as $arrayIndexFace) {
                    foreach ($arrayIndexFace['FaceRecords'] as $faceRecords) {
                        $avatar[] = [
                            'code_image' => $faceRecords['Face']['ImageId'],
                            'url' =>  $this->endPoint . $pathSavePhoto[$i],
                            'police_id' => $police->id,
                        ];
                        //dd($faceRecords['Face']['ImageId']);
                    }
                    $i = $i + 1;
                }
                Avatar::insert($avatar);
            }
            DB::commit();
            return redirect('police');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', "no se pudo registrar el usuario");
        }
    }

    private function savePhotos($photos, $user_id)
    {
        $pathPhotos = [];
        foreach ($photos as $photo) {
            $path = $this->savePhotoAws($photo, $user_id);
            array_push($pathPhotos, $path);
        }
        return $pathPhotos;
    }

    private function savePhotoAws($photo, $user_id)
    {
        $path = Storage::disk('s3')->put('police_avatar/' . $user_id, $photo, 'public');
        return $path;
    }

    private function getIndexFaces($photos)
    {
        $arrayIndexFace = [];
        foreach ($photos as $photo) {
            $imageBase64 = $this->getImage($photo);
            $result = $this->indexFaceAws($imageBase64);
            array_push($arrayIndexFace, $result);
        }
        return $arrayIndexFace;
    }

    private function indexFaceAws($imageBase64)
    {
        $client = new RekognitionClient(
            [
                'region' => env('AWS_DEFAULT_REGION', 'us-west-2'),
                'version' => 'latest'
            ]
        );
        $result = $client->indexFaces([
            'CollectionId' => 'police',
            'DetectionAttributes' => [],
            'ExternalImageId' => '1',
            "MaxFaces" => 1,
            'Image' => [
                'Bytes' => $imageBase64,
            ],
        ]);
        return $result;
    }

    private function createCollectionAws($name)
    {
        $client = new RekognitionClient(
            [
                'region' => env('AWS_DEFAULT_REGION', 'us-west-2'),
                'version' => 'latest'
            ]
        );

        try {
            $result = $client->createCollection([
                'CollectionId' => $name,
            ]);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    private function validatePhotos($photos)
    {
        foreach ($photos as $photo) {
            $image = $this->getImage($photo);
            $result = $this->faceDetected($image);
            if (count($result['FaceDetails']) > 1 || count($result['FaceDetails']) <= 0) {
                return false;
            }
            return true;
        }
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
        $imagePath = $photo->getPathName();
        $fp_image = fopen($imagePath, 'r');
        $image = fread($fp_image, filesize($imagePath));
        fclose($fp_image);
        return $image;
    }
}
