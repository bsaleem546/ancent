<?php

namespace App\Http\Controllers;

use App\Http\Resources\EquipmentCollectionResource;
use App\Http\Resources\EquipmentLocationHistoryResource;
use App\Http\Resources\EquipmentManufacturerResource;
use App\Http\Resources\EquipmentMinResourceCollection;
use App\Http\Resources\EquipmentResource;
use App\Http\Resources\EquipmentTypeResource;
use App\Models\Equipment;
use App\Models\EquipmentLocationHistory;
use App\Models\EquipmentManufacturer;
use App\Models\EquipmentType;
use App\Traits\Filters\EquipmentFilters;
use App\Traits\Filters\EquipmentSearch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EquipmentController extends Controller
{
    private function equipmentValidator(array $data)
    {
        $validator = Validator::make($data, [
            'number' => ['required', 'string', 'max:200'],
            // 'manufacture_year' => ['required', 'string', 'max:10'],
            // 'manufacture_no' => ['required', 'string', 'max:100'],
            // 'next_uvv' => ['required', 'date'],
            // 'type_id' => ['required', 'integer'],
            // 'manufacturer_id' => ['required', 'integer'],
            // 'location_id' => ['required', 'integer'],
            // 'customer_id' => ['required',  'integer'],
            // 'operator_id' => ['required',  'integer'],
            // 'notes' => ['sometimes', 'string'],
            // 'internal_notes' => ['sometimes', 'string'],
            // 'active' => ['required', 'integer']
        ]);

        return $validator;
    }

    public function getAll(Request $request, EquipmentFilters $filters)
    {
        return new EquipmentCollectionResource(Equipment::filter($filters)
            ->paginate($request->pagination["per_page"], ['*'], 'page', $request->pagination["current_page"]));
    }

    public function getAllMinData(Request $request, EquipmentFilters $filters)
    {
        $query = Equipment::query();
        $repairs = $query->select('id', 'number', 'location_id');
        $repairs = $query->paginate($request->pagination["per_page"], ['*'], 'page', $request->pagination["current_page"]);

        return new EquipmentMinResourceCollection($repairs);
    }

    public function search(Request $request, EquipmentSearch $filters)
    {
        return new EquipmentCollectionResource(Equipment::filter($filters)
            ->paginate($request->pagination["per_page"], ['*'], 'page', $request->pagination["current_page"]));
    }

    public function show($id)
    {
        $equipment = Equipment::findOrFail($id);
        $loggedUser = Auth::user();
        $apiData = array(
            'success' => false,
            'message' => 'forbidden_no_permission'
        );

        $customer_id = $loggedUser->userDetails->customer_id;
        $operator_id = $loggedUser->userDetails->operator_id;

        if ($customer_id != 0 && $operator_id != 0) {
            $operator = $loggedUser->userDetails->customer_operator_filter;
            if ($operator == 'OR') {
                if ($equipment->customer_id == $customer_id || $equipment->operator_id == $operator_id) {
                    return new EquipmentResource($equipment);
                } else {
                    return response()->json($apiData, 403);
                }
            } else {
                if ($equipment->customer_id == $customer_id && $equipment->operator_id == $operator_id) {
                    return new EquipmentResource($equipment);
                } else {
                    return response()->json($apiData, 403);
                }
            }
        } else if ($customer_id != 0) {
            if ($equipment->customer_id == $customer_id) {
                return new EquipmentResource($equipment);
            } else {
                return response()->json($apiData, 403);
            }
        } else if ($operator_id != 0) {
            if ($equipment->operator_id == $operator_id) {
                return new EquipmentResource($equipment);
            } else {
                return response()->json($apiData, 403);
            }
        } else {
            return new EquipmentResource($equipment);
        }
    }

    public function update(Request $request)
    {
        $apiResponse = array(
            'success' => false
        );

        $validator = $this->equipmentValidator($request->all());
        if ($validator->fails()) {
            $apiResponse['errors'] = $validator->messages();
            return $apiResponse;
        }

        if (isset($request->id) && $request->id != 0) {
            $equipment = Equipment::findOrFail($request->id);

            //Add to equipment location history if the location has changed
            if (isset($request->location_id) && $equipment->location_id != $request->location_id) {

                $equipmentLocationHistory = EquipmentLocationHistory::updateOrCreate([
                    "equipment_id" => $equipment->id,
                    "location_id" => $equipment->location_id,
                    "customer_id" => $equipment->customer_id,
                    "operator_id" => $equipment->operator_id,
                    "from" => $equipment->equipment_location_from,
                    "to" => Carbon::now()
                ]);

                $equipment->equipment_location_from = Carbon::now();
            }

            $equipment->number = $request->number;

            if (isset($request->manufacture_year)) $equipment->manufacture_year = $request->manufacture_year;
            if (isset($request->manufacture_no)) $equipment->manufacture_no = $request->manufacture_no;
            /* if(isset($request->next_uvv)) */
            $equipment->next_uvv = isset($request->next_uvv) ? $request->next_uvv : null;
            /* if(isset($request->next_checkup)) */
            $equipment->next_checkup = isset($request->next_checkup) ? $request->next_checkup : null;
            if (isset($request->model)) $equipment->model = $request->model;
            if (isset($request->type_id)) $equipment->type_id = $request->type_id;
            if (isset($request->manufacturer_id)) $equipment->manufacturer_id = $request->manufacturer_id;
            if (isset($request->location_id)) $equipment->location_id = $request->location_id;
            if (isset($request->customer_id)) $equipment->customer_id = $request->customer_id;
            if (isset($request->operator_id)) $equipment->operator_id = $request->operator_id;
            if (isset($request->notes)) $equipment->notes = $request->notes;
            if (isset($request->internal_notes) && Auth::user()->hasPermissionTo('access_internal_notes')) $equipment->internal_notes = $request->internal_notes;
            if (isset($request->active)) $equipment->active = $request->active;
            if (isset($request->maintenance_contract)) $equipment->maintenance_contract = $request->maintenance_contract;

            $equipment->save();

            return new EquipmentResource($equipment);
        } else {
            $apiResponse = array(
                'success' => false
            );
            $apiResponse['errors'] = "equipment id missing";

            return $apiResponse;
        }
    }

    public function updateHistory(Request $request)
    {
        if (isset($request->id) && $request->id != 0) {
            $equipmentLocationHistory = EquipmentLocationHistory::updateOrCreate([
                "id" => $request->id,
            ], [
                    "from" => $request->from,
                    "to" => $request->to
                ]
            );

            return new EquipmentLocationHistoryResource($equipmentLocationHistory);
        }
        return true;
    }

    public function updateMaintenanceContract(Request $request)
    {
        if (isset($request->id) && $request->id != 0) {
            $equipment = Equipment::findOrFail($request->id);

            if (isset($request->maintenance_contract)) $equipment->maintenance_contract = $request->maintenance_contract;

            $equipment->save();

            return new EquipmentResource($equipment);
        } else {
            $apiResponse = array(
                'success' => false
            );
            $apiResponse['errors'] = "equipment id missing";

            return $apiResponse;
        }
    }

    public function store(Request $request)
    {
        $apiResponse = array(
            'success' => false
        );

        $validator = $this->equipmentValidator($request->all());
        if ($validator->fails()) {
            $apiResponse['errors'] = $validator->messages();
            return response()->json($apiResponse, 422);
        }

        $equipment = new Equipment;

        $equipment->number = $request->number;

        if (isset($request->manufacture_year)) $equipment->manufacture_year = $request->manufacture_year;
        if (isset($request->manufacture_no)) $equipment->manufacture_no = $request->manufacture_no;
        /* if(isset($request->next_uvv)) */
        $equipment->next_uvv = isset($request->next_uvv) ? $request->next_uvv : null;
        /* if(isset($request->next_checkup)) */
        $equipment->next_checkup = isset($request->next_checkup) ? $request->next_checkup : null;
        if (isset($request->model)) $equipment->model = $request->model;
        if (isset($request->type_id)) $equipment->type_id = $request->type_id;
        if (isset($request->manufacturer_id)) $equipment->manufacturer_id = $request->manufacturer_id;
        if (isset($request->location_id)) $equipment->location_id = $request->location_id;
        if (isset($request->customer_id)) $equipment->customer_id = $request->customer_id;
        if (isset($request->operator_id)) $equipment->operator_id = $request->operator_id;
        if (isset($request->notes)) $equipment->notes = $request->notes;
        if (isset($request->internal_notes) && Auth::user()->hasPermissionTo('access_internal_notes')) $equipment->internal_notes = $request->internal_notes;
        if (isset($request->active)) $equipment->active = $request->active;
        if (isset($request->maintenance_contract)) $equipment->maintenance_contract = $request->maintenance_contract;

        $equipment->equipment_location_from = Carbon::now();

        $equipment->save();

        return new EquipmentResource($equipment);
    }

    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);

        $equipment->delete();
        return new EquipmentResource($equipment);
    }

    public function getEquipmentTypes()
    {
        return EquipmentTypeResource::collection(EquipmentType::orderBy('description')->get());
    }

    public function getEquipmentManufacturers()
    {
        return EquipmentManufacturerResource::collection(EquipmentManufacturer::orderBy('description')->get());
    }

    public function getAllEquipmentManufactureYears()
    {
        $apiResponse = (object) [];
        $apiResponse->data = Equipment::getAllManufactureYears();

        return response()->json($apiResponse, 200);
    }
}
