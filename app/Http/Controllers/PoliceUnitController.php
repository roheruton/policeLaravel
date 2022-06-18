<?php

namespace App\Http\Controllers;

use App\Models\PoliceUnit;
use Illuminate\Http\Request;

class PoliceUnitController extends Controller
{
    public function index()
    {
        $policeUnits = PoliceUnit::all();
        return view('admin.police-unit.index', compact('policeUnits'));
    }

    public function create()
    {
        return view('admin.police-unit.create');
    }

    public function store(Request $request)
    {
        $policeUnit = new PoliceUnit();
        $policeUnit->name = $request->name;
        $policeUnit->lat = $request->lat;
        $policeUnit->lng = $request->lng;
        $policeUnit->direction = $request->direction;
        $policeUnit->save();
        return redirect('police-unit');
    }
}
