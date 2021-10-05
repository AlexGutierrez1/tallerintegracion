<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Pusher\Pusher;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * //return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $loginPermissionId = Permission::select('id')->where('name','user_login')->first();
        $users = User::query()
            ->select('users.name','users.rut')
            ->join('model_has_permissions','users.id','=','model_has_permissions.model_id')
            ->where('model_has_permissions.permission_id',$loginPermissionId->id)
            ->orderBy('users.name')
            ->get();

        $links = Appointment::query()
            ->where('scheduled', 0)
            ->get();

        return view('admin.appointment')
            ->with('users',$users)
            ->with('links',$links);
    }

    /**
     * Display a listing of the resource.
     *
     * return \Illuminate\Http\Response
     */
    public function events(Request $request)
    {
        $query= Appointment::query();

        if( !is_null($request->start) && !is_null($request->end)){

            $start = Carbon::parse($request->start);
            $end = Carbon::parse($request->end);

            $query->whereDate('date_start', '>=', $start->toDateString())
                ->whereDate('date_end',   '<=', $end->toDateString());
        }

        $query->where('scheduled','=', 1)
            ->select('slug','title','date_start','date_end','hour_start','hour_end','user_asigned','finished');

        $appointments = $query->get()
            ->map(function (Appointment $appointment) {
                return [
                    'id' => $appointment->slug,
                    'title' => $appointment->title,
                    'start' => $appointment->date_start .'T'.$appointment->hour_start,
                    'end' => $appointment->date_start .'T'.$appointment->hour_end,
                    'finished' => $appointment->finished,
                    'rut' => $appointment->assigned->rut,
                    'color'=> $appointment->assigned->preferred_color
                ];
            });

        return response()->json($appointments);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @ return \Illuminate\Http\Response
     */
    public function show($slug) //: Collection
    {
        $appointment = Appointment::query()
            ->where('slug','=',$slug)
            ->get()
            ->map(function (Appointment $appointment) {
                return [
                    'id' => $appointment->slug,
                    'title' => $appointment->title,
                    'detail' => $appointment->description,
                    'date_start' => $appointment->date_start,
                    'hour_start' => $appointment->hour_start,
                    'hour_end' => $appointment->hour_end,
                    'activities' => $appointment->activities()
                        ->orderBy('created_at','DESC')->get()
                        ->map(function (AppointmentActivity $activity) {
                            return  [
                                'user' => ($activity->user->id == 0) ? '' : $activity->user->name,
                                'text' => $activity->text,
                                'time' => Carbon::parse($activity->created_at)->diffForHumans()
                            ];
                        }),
                    'finished' => $appointment->finished,
                    'scheduled' => $appointment->scheduled,
                    'duration' => $appointment->duration,
                    'user_asigned' => $appointment->assigned->rut,
                ];
            })
            ->first();

        if(!$appointment) {
            return response()->json(array(
                'message' => 'Recurso no encontrado'
            ), 404);
        }

        return response()->json($appointment);
    }


    public function ajax(Request $request)
    {
        $pusher = new Pusher(
            env("PUSHER_APP_KEY"),
            env("PUSHER_APP_SECRET"),
            env("PUSHER_APP_ID"),
            array('cluster' => env('PUSHER_APP_CLUSTER'))
        );

        switch($request->action) {
            case 'create':
                $request->validate([
                    'title' => 'required',
                ]);

                if ($request->newId) {
                    $slug = str_replace(' ', '', $request->newId);
                    $slug = preg_replace('/[^A-Za-z0-9\-]/', '', $slug);
                } else {
                    $slug = Str::random(8);
                }

                if($slug == ''){
                    return response()->json(array(
                        'code' => 422,
                        'message' => 'ID vacío'
                    ), 422);
                }

                $slugExists = Appointment::where('slug',$slug)->first();

                if($slugExists){
                    return response()->json(array(
                        'code' => 422,
                        'message' => 'ID ya existe'
                    ), 422);
                }

                $startTimestamp = Carbon::parse($request->date_start . ' ' . $request->hour_start);
                $endTimestamp = Carbon::parse($request->date_start . ' ' . $request->hour_end);
                $duration = $endTimestamp->diffInMinutes($startTimestamp);

                if ($startTimestamp->gt($endTimestamp)) {
                    return response()->json(array(
                        'code' => 422,
                        'message' => 'Hora de término no puede ser menor a la de inicio'
                    ), 422);
                }

                if($request->assistant !== null) {
                    $user = User::where('rut', $request->assistant)->first();
                }

                $appointment = Appointment::create([
                    'user_asigned' => ($user->id)?:null,
                    'title' => $request->title,
                    'slug' => $slug,
                    'scheduled' => 1,
                    'finished' => 0,
                    'description' => ($request->detail) ?: '',
                    'date_start' => $startTimestamp->toDateString(),
                    'date_end' => $startTimestamp->toDateString(),
                    'hour_start' => $startTimestamp->toTimeString(),
                    'hour_end' => $endTimestamp->toTimeString(),
                    'duration' => $duration,
                ]);

                AppointmentActivity::create([
                    'old' => serialize([]),
                    'new' => serialize([]),
                    'text' => AppointmentActivity::ACTIVITY_EVENT[AppointmentActivity::APPOINTMENT_CREATED],
                    'appointment_id' => $appointment->id,
                    'user_id' => auth()->user()->id
                ]);

                $returnAppointment = [
                    "id"=> $appointment->slug,
                    "title"=> $appointment->title,
                    "start"=> $appointment->date_start . ' ' . $appointment->hour_start,
                    "end"=> $appointment->date_start . ' ' . $appointment->hour_end,
                    "rut"=> $user->rut,
                    "constraint" => "businessHours",
                ];

                $pusher->trigger('appointments', 'created', [
                        'message' => 'Appointment created',
                        'data' => $returnAppointment
                    ]
                );

                return response()->json($returnAppointment);

                break;
            case 'createLink':
                $request->validate([
                    'title' => 'required',
                    'duration' => 'required|numeric',
                ]);

                if( isset($request->newId) && $request->newId != '' ) {
                    $slug = str_replace(' ', '', $request->newId);
                    $slug = preg_replace('/[^A-Za-z0-9\-]/', '', $slug);
                    $slug = substr($slug, 0, 50);
                } else {
                    $slug = Str::random(10);
                }

                if($slug == ''){
                    return response()->json(array(
                        'code' => 422,
                        'message' => 'ID vacío'
                    ), 422);
                }

                $slugExists = Appointment::where('slug',$slug)->first();

                if($slugExists){
                    return response()->json(array(
                        'code' => 422,
                        'message' => 'ID ya existe'
                    ), 422);
                }

                $appointment = Appointment::create([
                    'user_id' => 0,
                    'title' => $request->title,
                    'slug' => $slug,
                    'description' => ($request->detail)?:'',
                    'duration' => ($request->duration)?:60,
                    'start' => '2021-01-01 00:00:01',
                    'end' => '2021-01-01 00:00:01',
                ]);

                AppointmentActivity::create([
                    'old' => serialize([]),
                    'new' => serialize([]),
                    'text' => AppointmentActivity::ACTIVITY_EVENT[AppointmentActivity::APPOINTMENT_CREATED_BY_LINK],
                    'appointment_id' => $appointment->id_appointments,
                    'user_id' => auth()->user()->id
                ]);

                $pusher->trigger('appointments', 'createdLink', array('message' => 'Appointment created'));

                return response()->json($appointment);
                break;
            case 'update':
                $request->validate([
                    'title' => 'required',
                    'id' => 'exists:App\Models\Darkside\Appointments\Appointment,slug',
                    'newId' => 'min:4|regex:/^[\w.-]+$/'
                ]);

                $appointment = Appointment::where('slug', $request->id)->first();

                if (!$appointment) {
                    return response()->json(array(
                        'code' => 500,
                        'message' => 'Error al actualizar'
                    ), 500);
                }

                $slug = $request->id;
                if ($request->newId && $request->newId != $request->id) {
                    $slug = $request->newId;
                }

                $slugExists = Appointment::where('slug',$slug)->first();

                if($slugExists && $slugExists->id_appointments != $appointment->id_appointments){
                    return response()->json(array(
                        'code' => 422,
                        'message' => 'ID ya existe'
                    ), 422);
                }

                $user_id = 0;
                if($request->assistant) {
                    $user = User::where('rut', $request->assistant)->first();
                    $user_id = ($user) ? $user->id : 0;
                }

                $event = '';

                $appointment->title = $request->title;
                $appointment->slug = $slug;
                $appointment->description = ($request->detail) ?: '';
                $appointment->user_id = $user_id;

                if($request->scheduled == 1) {
                    $startTimestamp = Carbon::parse($request->date_start . ' ' . $request->hour_start);
                    $endTimestamp = Carbon::parse($request->date_start . ' ' . $request->hour_end);
                    $duration = $endTimestamp->diffInMinutes($startTimestamp);

                    if ($startTimestamp->gt($endTimestamp)) {
                        return response()->json(array(
                            'code' => 422,
                            'message' => 'Hora de término no puede ser menor a la de inicio'
                        ), 422);
                    }

                    $appointment->date_start = $request->date_start;
                    $appointment->date_end = $request->date_end;
                    $appointment->hour_start = $request->hour_start;
                    $appointment->hour_end = $request->hour_end;
                    $appointment->duration = $duration;
                    $appointment->scheduled = 1;
                    $appointment->archived_by = 0;

                    $event = 'updateScheduled';
                }
                if($request->scheduled == 0) {
                    $appointment->duration = $request->duration;

                    $event = 'updateArchived';
                }

                $changes = $appointment->saveAndReturnChanges();

                $returnAppointment = [
                    "oldId"=> ( isset($changes["oldSlug"]) )?$changes["oldSlug"]:'',
                    "id"=> $appointment->slug,
                    "title"=> $appointment->title,
                    "start"=> (isset($startTimestamp))?substr($startTimestamp,0,19):'',
                    "end"=> (isset($endTimestamp))?substr($endTimestamp,0,19):'',
                    "rut"=> $appointment->assigned->rut,
                    "finished"=> $appointment->finished,
                    "scheduled"=> $appointment->scheduled,
                ];

                if(count($changes['old']) > 0 ) {
                    AppointmentActivity::create([
                        'old' => serialize($changes['old']),
                        'new' => serialize($changes['new']),
                        'text' => sprintf(AppointmentActivity::ACTIVITY_EVENT[AppointmentActivity::APPOINTMENT_UPDATED],$changes['fields']),
                        'appointment_id' => $appointment->id_appointments,
                        'user_id' => auth()->user()->id
                    ]);
                }

                $pusher->trigger('appointments', $event, [
                        'message' => 'Appointment updated',
                        'data' => $returnAppointment
                    ]
                );

                return response()->json($returnAppointment);

                break;
            case 'destroy':
                $appointment = Appointment::where('slug', $request->id)->first();
                if(!$appointment) {
                    return response()->json(array(
                        'code' => 500,
                        'message' => 'No se encontró o se eliminó antes de esta acción'
                    ), 500);
                }

                $event = '';
                if($appointment->scheduled == 1){
                    $event = 'deletedScheduled';
                }
                if($appointment->scheduled == 0){
                    $event = 'deletedArchived';
                }

                $appointment->save();
                $appointment->delete();

                AppointmentActivity::create([
                    'old' => serialize([]),
                    'new' => serialize([]),
                    'text' => AppointmentActivity::ACTIVITY_EVENT[AppointmentActivity::APPOINTMENT_DELETED],
                    'appointment_id' => $appointment->id_appointments,
                    'user_id' => auth()->user()->id
                ]);

                $pusher->trigger('appointments', $event, [
                    'message' => 'Appointment deleted',
                    'data' => [
                        'id'=>$appointment->slug
                    ]
                ]);

                return response()->json($appointment);

                break;
            case 'archive':
                $appointment = Appointment::where('slug',$request->id)->first();
                if($appointment) {
                    $appointment->scheduled = 0;
                    $appointment->user_id = 0;
                    $appointment->archived_by = auth()->user()->id;
                    $appointment->save();

                    AppointmentActivity::create([
                        'old' => serialize([]),
                        'new' => serialize([]),
                        'text' => AppointmentActivity::ACTIVITY_EVENT[AppointmentActivity::APPOINTMENT_ARCHIVED],
                        'appointment_id' => $appointment->id_appointments,
                        'user_id' => auth()->user()->id
                    ]);

                    $pusher->trigger('appointments', 'archived', [
                        'message' => 'Appointment archived',
                        'data' => [
                            'id'=>$appointment->slug
                        ]
                    ]);

                    return response()->json($appointment);
                }

                return response()->json(array(
                    'code' => 500,
                    'message' => 'Error al archivar'
                ), 500);

                break;
            case 'finish':
                $appointment = Appointment::where('slug',$request->id)->first();
                if(! $appointment) {
                    return response()->json(array(
                        'code' => 500,
                        'message' => 'Error al finalizar'
                    ), 500);
                }

                if($appointment->finished == 1){
                    $appointment->finished = 0;
                    $appointment->finished_by = 0;
                    $eventText = AppointmentActivity::ACTIVITY_EVENT[AppointmentActivity::APPOINTMENT_UNFINISHED];
                } else {
                    $appointment->finished = 1;
                    $appointment->finished_by = auth()->user()->id;
                    $eventText = AppointmentActivity::ACTIVITY_EVENT[AppointmentActivity::APPOINTMENT_FINISHED];
                }

                $appointment->save();

                $returnAppointment = [
                    "id"=> $appointment->id,
                    "title"=> $appointment->title,
                    "start"=> $appointment->date_start . 'T' . $appointment->hour_start,
                    "end"=> $appointment->date_start . 'T' . $appointment->hour_end,
                    "rut"=> $appointment->rut,
                    "finished" => $appointment->finished
                ];

                AppointmentActivity::create([
                    'old' => serialize([]),
                    'new' => serialize([]),
                    'text' => $eventText,
                    'appointment_id' => $appointment->id_appointments,
                    'user_id' => auth()->user()->id
                ]);

                $pusher->trigger('appointments',
                    'finished',
                    [
                        'message' => 'Appointment finished state changed',
                        'data' => [
                            'id'=>$appointment->slug,
                            'finished' => $appointment->finished
                        ]
                    ]
                );

                return response()->json($returnAppointment);

                break;
            case 'resize':
                $appointment = Appointment::where('slug',$request->id)->first();

                if(!$appointment){
                    return response()->json(array(
                        'code'      =>  500,
                        'message'   =>  'Error al actualizar horario y fecha'
                    ), 500);
                }

                $startTimestamp = Carbon::parse($request->start);
                $endTimestamp = Carbon::parse($request->end);
                $duration = $endTimestamp->diffInMinutes($startTimestamp);

                $appointment->date_start = $startTimestamp->toDateString();
                $appointment->date_end = $startTimestamp->toDateString();
                $appointment->hour_start = $startTimestamp->toTimeString();
                $appointment->hour_end = $endTimestamp->toTimeString();
                $appointment->duration = $duration;

                $changes = $appointment->saveAndReturnChanges();

                $returnAppointment = [
                    "oldId"=> ( isset($changes["oldSlug"]) )?$changes["oldSlug"]:'',
                    "id"=> $appointment->slug,
                    "title"=> $appointment->title,
                    "start"=> (isset($startTimestamp))?substr($startTimestamp,0,19):'',
                    "end"=> (isset($endTimestamp))?substr($endTimestamp,0,19):'',
                    "rut"=> $appointment->assigned->rut,
                    "finished"=> $appointment->finished,
                ];

                if(count($changes['old']) > 0 ) {
                    AppointmentActivity::create([
                        'old' => serialize($changes['old']),
                        'new' => serialize($changes['new']),
                        'text' => sprintf(AppointmentActivity::ACTIVITY_EVENT[AppointmentActivity::APPOINTMENT_UPDATED],$changes['fields']),
                        'appointment_id' => $appointment->id_appointments,
                        'user_id' => auth()->user()->id
                    ]);
                }

                $pusher->trigger('appointments', 'updateScheduled', [
                        'message' => 'Appointment resized or moved',
                        'data' => $returnAppointment
                    ]
                );


                return response()->json($appointment);

                break;
            case 'comment':
                if($request->comment == ''){
                    return response()->json(array(
                        'code' => 400,
                        'message' => 'Comentario vacío shushetumare'
                    ), 400);
                }

                $appointment = Appointment::where('slug',$request->id)->select('id_appointments as id')->first();
                $recentComment = AppointmentComment::create([
                    'appointment_id' => $appointment->id,
                    'user_id' => auth()->user()->id,
                    'text' => $request->comment
                ]);

                $returnComment = [
                    'text' => $recentComment->text,
                    'user' => $recentComment->user->name,
                    'created_at' => Carbon::parse($recentComment->created_at)->diffForHumans()
                ];

                return response()->json($returnComment);
                break;
            case 'receive':
                $appointment = Appointment::where('slug', $request->id)->first();

                if (!$appointment) {
                    return response()->json(array(
                        'code' => 500,
                        'message' => 'Error al actualizar'
                    ), 500);
                }

                $startTimestamp = Carbon::parse($request->start);
                $endTimestamp = Carbon::parse($request->end);
                $duration = $endTimestamp->diffInMinutes($startTimestamp);

                $appointment->date_start = $startTimestamp->toDateString();
                $appointment->date_end = $startTimestamp->toDateString();
                $appointment->hour_start = $startTimestamp->toTimeString();
                $appointment->hour_end = $endTimestamp->toTimeString();
                $appointment->duration = $duration;
                $appointment->scheduled = 1;
                $appointment->archived_by = null;

                $appointment->save();

                $returnAppointment = [
                    "id"=> $appointment->slug,
                    "title"=> $appointment->title,
                    "start"=> $appointment->date_start .'T'.$appointment->hour_start,
                    "end"=> $appointment->date_start .'T'.$appointment->hour_end,
                    "date_start" => $appointment->date_start,
                    "date_end" => $appointment->date_end,
                    "hour_start" => $appointment->hour_start,
                    "hour_end"=> $appointment->hour_end,
                    "rut"=> $appointment->assigned->rut,
                    "finished" => $appointment->finished
                ];
                /*
                                Appointment::query()
                                    ->where('id_appointments', $appointment->id_appointments)
                                    ->join('users', 'users.id', '=', 'crm_appointments.user_id')
                                    ->select('slug as id', 'title', 'date_start','date_end','hour_start','hour_end', 'users.rut as rut')
                                    ->first();
                */
                AppointmentActivity::create([
                    'old' => serialize([]),
                    'new' => serialize([]),
                    'text' => sprintf(
                        AppointmentActivity::ACTIVITY_EVENT[AppointmentActivity::APPOINTMENT_SCHEDULED],
                        Carbon::parse($startTimestamp->toDateString())->format('d-m-Y'),
                        substr($startTimestamp->toTimeString(), 0, -3),
                        substr($endTimestamp->toTimeString(), 0, -3)
                    ),
                    'appointment_id' => $appointment->id_appointments,
                    'user_id' => auth()->user()->id
                ]);

                $pusher->trigger('appointments', 'scheduled',
                    [
                        'message' => 'Appointment received',
                        'data' => $returnAppointment
                    ]
                );

                return response()->json($returnAppointment);
                break;
            default:
                return response()->json(array(
                    'code'      =>  400,
                    'message'   =>  'Acción no recibida'
                ), 400);
                break;
        }
    }
}
