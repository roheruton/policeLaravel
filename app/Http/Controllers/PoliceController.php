<?php

namespace App\Http\Controllers;

use App\Http\Requests\PoliceRequest;
use App\Models\Avatar;
use App\Models\Police;
use Aws\Rekognition\RekognitionClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PoliceController extends Controller
{

    private $endPoint;
   /* public function __construct()
    {
        $this->endPoint = "https://sw-evento.s3.us-west-2.amazonaws.com/";
    }*/
                                //Database $database
    public function __construct()
    {
        $this->endPoint = "https://sw-evento.s3.us-west-2.amazonaws.com/";
        $this->database = app('firebase.database');
        $this->tablename = 'police';
        $this->tablename2 = 'avatar';
    }

    public function index()
    {   $avatars =$this->database->getReference($this->tablename2)->getValue();
        $policias =$this->database->getReference($this->tablename)->getValue();
       // $policias = $policias::with('avatars')->get();
        return view('admin.police.index',compact('policias'));
        //$policies = Police::with('avatars')->get();
        //return view('admin.police.index')->with('policies', $policies);
    }

    public function create()
    {
        return view('admin.police.create');
    }

    public function store2(Request $request)
    { $avatar = [];
        $postData = [
                    'ci' => $request->ci,
                    'nombre' => $request->name,
                    'apellidos' => $request->last_name,
                    'fecha_n' => $request->dateOfBirth,
                    'avatar' => $avatar
                    
                   ];

            if (!$this->validatePhotos($request->photos)) {
                return back()->with('error', "detector de fotos invalidas");
            }
            
            $postRef = $this->database ->getReference($this->tablename)->push($postData);

            if($postRef){
               // $postData =
                $id = Str::afterLast($postRef, 'police/');error_log($id );  
                        
                    $this->createCollectionAws('police');
                    $pathSavePhoto = $this->savePhotos($request->photos, $id);
                    $arrayIndexFaces = $this->getIndexFaces($request->photos);
                    
                    if (count($pathSavePhoto) == count($arrayIndexFaces)) {
                        $i = 0;
                        foreach ($arrayIndexFaces as $arrayIndexFace) {
                            foreach ($arrayIndexFace['FaceRecords'] as $faceRecords) {
                                $avatar[] = [
                                    'code_image' => $faceRecords['Face']['ImageId'],
                                    'url' =>  $this->endPoint . $pathSavePhoto[$i],
                                ];                            
                            }
                            $i = $i + 1;
                        }
                        $postData = [
                            'ci' => $request->ci,
                            'nombre' => $request->name,
                            'apellidos' => $request->last_name,
                            'fecha_n' => $request->dateOfBirth,
                            'avatar' => $avatar                            
                           ];
                        $postRef2 = $this->database ->
                                            getReference($this->tablename.'/'.$id)->
                                            update($postData);
                        if($postRef2){return redirect('police');
                        }else{return back()->with('Error', "no se pudo registrar el avatar");}
                    }
                    //return redirect()->with('status','added correctly');
            }else{return back()->with('Error', "no se pudo registrar el policia"); }   
    }

    public function edit($id){
        $key = $id;
        $editdata= $this->database->getReference($this->tablename)->getChild($key)->getValue();

        if($editdata){
            return view('admin.police.edit', compact('editdata'));
        }else{
            return back()->with('Error', "no se pudo encontrar el id del policia"); 
        }
    }
    public function update(Request $request,$id){
        $key=$id;
        $postData = [
            'ci' => $request->ci,
            'nombre' => $request->name,
            'apellidos' => $request->last_name,
            'fecha_n' => $request->dateOfBirth,
            
           ];
           $updatedata= $this->database->getReference($this->tablename.'/'.$key)->update($postData);

        if($updatedata){
            return redirect('police');
        }else{
            return back()->with('Error', "no se pudo actualizar el policia"); 
        }
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
