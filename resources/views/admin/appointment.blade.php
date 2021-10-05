<x-app-layout>

    <x-slot name="customcss">
        <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.6.0/main.min.css' rel='stylesheet' />
        <link href='https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css' rel='stylesheet' />
        <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">

        <style>
            .calendar-container{
                position:relative;
                display: flex;
                flex: 1;
                flex-wrap: wrap;
                justify-content: center;
                align-items: baseline;
                align-content: flex-start;
            }

            #calendar{
                flex: 0 0 100%;
            }

            .disabled {
                pointer-events: none;
                opacity: 0.4;
            }

            #loader, .loader{
                position:absolute;
                width:100%;
                height:100%;
                display: flex;
                align-items: center;
                justify-content: center;
                align-content: center;
                opacity:0;
                transition:opacity .3s linear;
            }

            .loader.show{
                opacity:1;
            }

            @media(max-width: 768px){
                .fc .fc-toolbar-title{
                    font-size:1.2em !important;
                }
                .fc .fc-button{
                    padding: 1px 6px;
                }
            }
            .comment, .activity{
                position:relative;
                padding:.3em;
                margin:0;
                border:0;
            }
            .comment p,
            .activity p{
                padding:0;
                margin-bottom:.5em;
            }

            .comment hr
            .activity hr{
                padding:0;
                margin:0;
            }

            @media(max-width: 992px) {
                .modal-dialog{
                    max-width: 85% !important;
                }
            }
        </style>
        <link rel="stylesheet" href="{{ mix('/css/datatable/datatable.css') }}">
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reuniones') }}

            <button type="button" class="text-indigo-600 hover:text-indigo-900 inline-flex" id="btn-create-appointment-link">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(Session::has('message'))
                        @if(Session::get('alert-type') == 'success')
                            <x-alerts.success>{{ Session::get('message') }}</x-alerts.success>
                        @elseif(Session::get('alert-type') == 'error')
                            <x-alerts.error>{{ Session::get('message') }}</x-alerts.error>
                        @endif
                    @endif

                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="shadow overflow-hidden sm:rounded-lg">

                                    <div class="card shadow mb-4 h-sm-100">
                                        <div class="card-body">
                                            <div class="calendar-container">
                                                <div id="calendar">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--
                    <div class="modal fade" id="appointmentModal" data-backdrop="static" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <span>Actualizar reunión</span>
                                    </h5>
                                    <div class="modal-title-additional">
                                        <div class="custom-control custom-switch custom-control-inline"
                                             id="input-finished-toggle">
                                            <input type="checkbox" class="custom-control-input"
                                                   id="finished-appointment">
                                            <label class="custom-control-label" for="finished-appointment"></label>
                                        </div>
                                    </div>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <div class="container">
                                        <form class="row appointment-form">
                                            <div class="col col-12 col-sm-9 col-lg-10">
                                                <div class="form-group row" id="row-input-duration">
                                                    <label for="appointment-duration"
                                                           class="col-sm-2 col-form-label col-form-label-sm">Duracion</label>
                                                    <div class="col-sm-10">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                   name="appointment-duration"
                                                                   id="appointment-duration0" value="60" required="">
                                                            <label class="form-check-label" for="appointment-duration0">1
                                                                hora</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                   name="appointment-duration"
                                                                   id="appointment-duration1" value="120" required="">
                                                            <label class="form-check-label" for="appointment-duration1">2
                                                                horas</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                   name="appointment-duration"
                                                                   id="appointment-duration2" value="180" required="">
                                                            <label class="form-check-label" for="appointment-duration2">3
                                                                horas</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row" id="row-input-title">
                                                    <label for="appointment-name"
                                                           class="col-sm-2 col-form-label col-form-label-sm">Titulo</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control form-control-sm"
                                                               name="appointment-name" value="" placeholder="Titulo123"
                                                               required="">
                                                    </div>
                                                </div>
                                                <div class="form-group row" id="row-input-id">
                                                    <label for="appointment-id"
                                                           class="col-sm-2 col-form-label col-form-label-sm">ID</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control form-control-sm"
                                                               name="appointment-id" value="" placeholder="ID123ASD">
                                                    </div>
                                                </div>
                                                <div class="form-group row" id="row-input-datetime">
                                                    <div class="col-sm-2">
                                                        <label class="col-form-label col-form-label-sm">Horario</label>
                                                    </div>
                                                    <div class="col-sm-10">
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <input type="date" class="form-control form-control-sm"
                                                                       min="{{ \Carbon\Carbon::yesterday()->toDateString() }}"
                    value="{{ \Carbon\Carbon::yesterday()->toDateString() }}"
                    name="appointment-date" required="">
                </div>
                <div class="col-sm-4">
                    <input type="time" min="09:00" max="18:00"
                           name="appointment-time-start" value="10:30"
                           step="1800" class="form-control form-control-sm"
                           required="">
                </div>
                <div class="col-sm-4">
                    <input type="time" min="09:00" max="18:00"
                           name="appointment-time-end" value="12:00"
                           step="1800" class="form-control form-control-sm"
                           required="">
                </div>
            </div>
        </div>
    </div>
    <div class="form-group row" id="row-input-attendee">
        <label for="appointment-attendee"
               class="col-sm-2 col-form-label col-form-label-sm">Encargado</label>
        <div class="col-sm-10">
            <select class="custom-select custom-select-sm"
                    name="appointment-attendee">
                <option value="0" selected="" disabled="">Selecciona una
                    opción
                </option>
                @foreach($users as $user)
                <option value="{{$user->rut}}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group" id="row-input-description">
        <label class="col-form-label">Descripcion</label>
        <textarea class="form-control form-control-sm"
                  name="appointment-detail" rows="15"
                  placeholder="Detalle de la cita"></textarea>
    </div>
    <div class="input-group input-group-sm mb-2" id="input-comment-section">
        <hr>
        <div class="input-group-prepend">
            <span class="input-group-text">Comentario</span>
        </div>
        <input type="text" class="form-control" name="appointment-comment">
        <div class="input-group-append">
            <button class="btn btn-success comment-appointment"
                    type="button">Guardar
            </button>
        </div>
    </div>
    <div class="comment-section" id="modal-comments-section">
        <h5 class="font-weight-bold text-dark">Comentarios</h5>
    </div>
    <div class="activity-section" id="modal-activities-section">
        <h5 class="font-weight-bold text-dark">Movimientos</h5>
    </div>
    </div>
    <div class="col col-12 col-sm-3 col-lg-2">
        <div>
            <button
                class="btn my-1 btn-sm text-white btn-block btn-success save-appointment"
                id="button-appointment-save" type="submit">Guardar
            </button>
            <button
                class="btn my-1 btn-sm text-white btn-block btn-danger archive-appointment"
                id="button-appointment-archive" type="button">Archivar
            </button>
            <button class="btn my-1 btn-sm btn-block destroy-appointment"
                    id="button-appointment-delete" type="button">Eliminar
            </button>
        </div>
        <p id="appointment-creator">Creador</p>
        <p id="appointment-archiver">Archivador</p>
    </div>
    </form>
    </div>
    </div>
    </div>
    </div>
    </div>--}}
                </div>
            </div>
        </div>
    </div>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.6.0/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.6.0/locales/es-us.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "500",
        "hideDuration": "500",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    class App {
        static setPageRequirements(){
            window.modal = new ModalHelper();
            window.appointmentController = new AppointmentController();
            window.currentAppointment = new Appointment();

            document.querySelector('#btn-create-appointment-link').addEventListener('click', function () {
                AppointmentPartialViews.createLink();
            });
        }
        static tryRenderPlugins(){
            Promise.all([User.getUsers(), User.getPermissions()])
                .then(function (response) {
                    window.usersList = response[0].data;
                    window.permissions = response[1].data;

                    window.calendarContainer = new CalendarContainer('calendar');
                    window.calendarContainer.render();

                    window.table = new DataTableHelper('dataTable');
                    Promise.all([table.render()])
                        .then(function(response){
                            table.configActions();
                        });
                });
        }
        static randomChars(){
            return Math.random().toString(36).substring(2);
        }
        static appointmentsUrl(){
            return '{{ env('MIX_APP_URL').'/reunion/' }}';
        }
        static ajaxUrl(){
            return '{{ env('MIX_APP_URL').'/reunion/ajax' }}';
        }
        static csrf(){
            return document.querySelector('meta[name="csrf-token"]').content;
        }
        static htmlEntities(str) {
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    }
    class Comment{
        static print( creator, text, time ){
            let element = new DOMParser().parseFromString(`<div class="comment"><strong class="comment-heading">${creator}</strong>, ${time}<p>${ App.htmlEntities(text) }</p><hr></div>`, 'text/html');
            return element.body.firstChild;
        }
    }
    class Activity{
        static print( creator, text, time){
            let element = new DOMParser().parseFromString(`<div class="activity"><strong class="activity-creator">${creator}</strong> ${text}<p>${time}</p><hr></div>`, 'text/html');
            return element.body.firstChild;
        }
    }
    class User{
        static getUsers(){
            let url = '{{ env('MIX_APP_URL').'/usuarios/list' }}';
            return axios.get(url);
        }
        static getPermissions(){
            let url = '{{ env('MIX_APP_URL').'/usuario/properties/permissions' }}';
            return axios.get(url);
        }
        static can( permission ){
            for( let i = 0; i < window.permissions.length; i++){
                if( window.permissions[i].name === permission){
                    return true;
                }
            }
            return false;
        }
    }
    class Loader{
        static element(){
            let spinner = '<div class="spinner-border text-primary" role="status"><span class="sr-only"></span></div>';
            let container = document.createElement('div');
            container.className = 'loader';
            container.innerHTML = spinner.trim();

            return container.cloneNode(true);
        }
    }
    class Appointment {
        constructor() {
            this.id;
            this.title;
            this.description;
            this.slug;
            this.scheduled;
            this.finished;
            this.date_start
            this.date_end
            this.hour_start;
            this.hour_end;
            this.creator;
            this.duration;
            this.priority = 2;
        }

        static collectData( action ){
            currentAppointment.title = document.getElementsByName('appointment-name')[0].value;
            currentAppointment.detail = document.getElementsByName('appointment-detail')[0].value;
            currentAppointment.newId = document.getElementsByName('appointment-id')[0].value;

            currentAppointment.duration = null;
            if(action == 'createLink' || currentAppointment.scheduled == 0){
                if(document.querySelector('input[name="appointment-duration"]:checked')) {
                    currentAppointment.duration = document.querySelector('input[name="appointment-duration"]:checked').value;
                    return;
                }
            }
            if( currentAppointment.scheduled == 1) {
                currentAppointment.assistant = document.getElementsByName('appointment-attendee')[0].value;
                currentAppointment.date_start = document.getElementsByName('appointment-date')[0].value;
                currentAppointment.date_end = document.getElementsByName('appointment-date')[0].value;
                currentAppointment.hour_start = document.getElementsByName('appointment-time-start')[0].value;
                currentAppointment.hour_end = document.getElementsByName('appointment-time-end')[0].value;
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////
    class AppointmentModal{
        constructor(id = '#appointmentModal', action = 'show') {
            this.modalId = id;
            this.modal = $(id);
            $(this.modal).appendTo("body");
            this.action = action;
            this.body = $(this.modal).find('.modal-body .container')[0];
            this.title = $(this.modal).find('.modal-title')[0];
            this.titleAdditionals = $(this.modal).find('.modal-title-additional')[0];
        }
        toUpdateScheduled(){
            this.resetDisplayPropertyInRows();
            this.setTitle('Actualizar reunión');
            document.getElementById('row-input-duration').style.display = 'none';
            document.getElementById('appointment-archiver').style.display = 'none';
        }
        toUpdateArchived(){
            this.resetDisplayPropertyInRows();
            this.setTitle('Actualizar reunión');
            document.getElementById('row-input-datetime').style.display = 'none';
        }
        toCreateLink(){
            this.resetDisplayPropertyInRows();
            this.setTitle('Crear enlace');
            document.getElementById('input-finished-toggle').style.display = 'none';
            document.getElementById('row-input-datetime').style.display = 'none';
            document.getElementById('row-input-attendee').style.display = 'none';
            document.getElementById('input-comment-section').style.display = 'none';
            document.getElementById('modal-comments-section').style.display = 'none';
            document.getElementById('modal-activities-section').style.display = 'none';
            document.getElementById('button-appointment-archive').style.display = 'none';
            document.getElementById('button-appointment-delete').style.display = 'none';
            document.getElementById('appointment-creator').style.display = 'none';
            document.getElementById('appointment-archiver').style.display = 'none';
        }
        resetDisplayPropertyInRows() {
            document.getElementById('row-input-duration').style.removeProperty('display');
            document.getElementById('appointment-archiver').style.removeProperty('display');
            document.getElementById('input-finished-toggle').style.removeProperty('display');
            document.getElementById('row-input-datetime').style.removeProperty('display');
            document.getElementById('input-comment-section').style.removeProperty('display');
            document.getElementById('modal-comments-section').style.removeProperty('display');
            document.getElementById('modal-activities-section').style.removeProperty('display');
            document.getElementById('button-appointment-archive').style.removeProperty('display');
            document.getElementById('button-appointment-delete').style.removeProperty('display');
            document.getElementById('appointment-creator').style.removeProperty('display');
            document.getElementById('appointment-archiver').style.removeProperty('display');
        }
        resetInputs(){
            document.getElementsByName('appointment-name')[0].value='';
            document.getElementsByName('appointment-id')[0].value='';
            document.getElementsByName('appointment-date')[0].value = '';
            document.getElementsByName('appointment-time-start')[0].value = '';
            document.getElementsByName('appointment-time-end')[0].value = '';
            document.getElementsByName('appointment-detail')[0].value='';
            document.getElementsByName('appointment-attendee')[0].value=0;
            document.getElementsByName('appointment-comment')[0].value='';
        }
        removePreviousComments(){
            let container = document.getElementById('modal-comments-section');
            while (container.childNodes.length > 2) {
                container.removeChild(container.lastChild);
            }
        }
        removePreviousActivities(){
            let container = document.getElementById('modal-activities-section');
            while (container.childNodes.length > 2) {
                container.removeChild(container.lastChild);
            }
        }
        setTitle( title ){
            $(this.title).text(title)
        }
        show(){
            $(this.modal).modal();
        }
    }
    class AppointmentServices{
        create(){
            window.calendarContainer.loading();
            modal.hide();
            Appointment.collectData(currentAppointment.action);

            axios.post(App.ajaxUrl(), currentAppointment)
                .then(function (response) {
                    toastr.success(`${response.data.title} creado`, '¡Listo!');
                })
                .catch( error => {
                    window.calendarContainer.loading(false);
                    modal.show();
                    toastr.error(error.response.data.message, 'Error '+ error.response.status);
                });
        }
        update(){}
        delete(){}
        updateProperty(){}
    }
    class AppointmentControllers{
        static eventClick(){}
        static eventDrop(){}
        static eventResize(){}
        static eventReceive(){}
    }
    class AppointmentForm{
        static retrieveDataForCreate(){
            let formData = {};
            formData.title = document.getElementsByName('appointment-name')[0].value;
            formData.detail = document.getElementsByName('appointment-detail')[0].value;
            formData.newId = document.getElementsByName('appointment-id')[0].value;
            formData.duration = null;
            formData.scheduled = 1;
            formData.assistant = document.getElementsByName('appointment-attendee')[0].value;
            formData.date_start = document.getElementsByName('appointment-date')[0].value;
            formData.date_end = document.getElementsByName('appointment-date')[0].value;
            formData.hour_start = document.getElementsByName('appointment-time-start')[0].value;
            formData.hour_end = document.getElementsByName('appointment-time-end')[0].value;

            return formData;
        }
        static retrieveDataForCreateByLink(){
            let formData = {};
            formData.title = document.getElementsByName('appointment-name')[0].value;
            formData.detail = document.getElementsByName('appointment-detail')[0].value;
            formData.newId = document.getElementsByName('appointment-id')[0].value;
            if(document.querySelector('input[name="appointment-duration"]:checked')) {
                formData.duration = document.querySelector('input[name="appointment-duration"]:checked').value;
            }
            formData.scheduled = 0;
            return formData;
        }
        static retrieveDataForUpdateScheduled(){
            formData.title = document.getElementsByName('appointment-name')[0].value;
            formData.detail = document.getElementsByName('appointment-detail')[0].value;
            formData.newId = document.getElementsByName('appointment-id')[0].value;

            formData.duration = null;
            if(action == 'createLink' || currentAppointment.scheduled == 0){
                if(document.querySelector('input[name="appointment-duration"]:checked')) {
                    formData.duration = document.querySelector('input[name="appointment-duration"]:checked').value;
                    return;
                }
            }
            if( formData.scheduled == 1) {
                formData.assistant = document.getElementsByName('appointment-attendee')[0].value;
                formData.date_start = document.getElementsByName('appointment-date')[0].value;
                formData.date_end = document.getElementsByName('appointment-date')[0].value;
                formData.hour_start = document.getElementsByName('appointment-time-start')[0].value;
                formData.hour_end = document.getElementsByName('appointment-time-end')[0].value;
            }
        }
    }
    /////////////////////////////////////////////



    class AppointmentPartialViews{
        static create(eventInfo){
            window.currentAppointment = new Appointment();
            currentAppointment.action = 'create';
            currentAppointment.scheduled = 1;
            currentAppointment.date_start = eventInfo.startStr.substring(0,10);
            currentAppointment.hour_start = eventInfo.startStr.substring(11,16);
            let difference = (new Date(eventInfo.endStr)-new Date(eventInfo.startStr))/60/1000;
            if(difference > 30 ){
                currentAppointment.hour_end = eventInfo.endStr.substring(11,16);
            } else{
                currentAppointment.hour_end = new Date(new Date(eventInfo.endStr).getTime() + (difference*60000)).toTimeString().substring(0,5);
            }
            modal.appointment();
            modal.setTitle('Crear reunión');
            modal.configButtons();
            modal.show();
        }
        static createLink(){
            window.currentAppointment = new Appointment();
            currentAppointment.action = 'createLink';

            modal.appointment();
            modal.setTitle('Crear enlace');
            modal.configButtons();
            modal.show();
        }
        static update( slug ){
            window.calendarContainer.loading();
            table.loading();

            axios.get( App.appointmentsUrl() + slug )
                .then( response => {
                    window.currentAppointment = new Appointment();
                    currentAppointment.action = 'update';
                    currentAppointment.id = response.data.id;
                    currentAppointment.title = response.data.title;
                    currentAppointment.assistant = response.data.assistant;
                    currentAppointment.creatorName = response.data.creator_name;
                    currentAppointment.archiverName = response.data.archiver_name;
                    currentAppointment.detail = response.data.detail;
                    currentAppointment.finished = response.data.finished;
                    currentAppointment.comments = response.data.comments;
                    currentAppointment.activities = response.data.activities;
                    currentAppointment.scheduled = response.data.scheduled;
                    currentAppointment.duration = response.data.duration;

                    if(currentAppointment.scheduled == 1) {
                        currentAppointment.date_start = response.data.date_start;
                        currentAppointment.date_end = response.data.date_end;
                        currentAppointment.hour_start = response.data.hour_start;
                        currentAppointment.hour_end = response.data.hour_end;
                    }
                    modal.appointment();
                    if(currentAppointment.assistant !== 111111111) {
                        $(modal.modal).find('select[name="appointment-attendee"]').val(currentAppointment.assistant);
                    }

                    let finished = (currentAppointment.finished == 1) ? 'checked':'';
                    let finishedSwitch =
                        `<div class="custom-control custom-switch custom-control-inline">` +
                        `<input type="checkbox" class="custom-control-input" id="finished-appointment" ${finished}>` +
                        `<label class="custom-control-label" for="finished-appointment"></label>` +
                        `</div>`;

                    modal.setTitle('Actualizar reunión', finishedSwitch);
                    modal.configButtons();
                    modal.show();
                    window.calendarContainer.loading(false);
                    table.loading(false);
                })
                .catch( error => {
                    console.log(error);
                    /*
                    if(typeof error.response.data.message !== undefined) {
                        toastr.error(error.response.data.message, 'Error ' + error.response.status);
                    }
                    */
                });
        }
        static delete( data ){
            let slug = ( data.id ) ? data.id : data.slug;

            Swal.fire({
                title: `¿Seguro de eliminar: ${data.title}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    appointmentController.destroy(slug);
                }
            })
        }
    }
    class AppointmentController {
        action(action){
            let actions = {
                'create': this.create,
                'createLink': this.createLink,
                'update': this.update,
            }

            return actions[action]();
        }
        constructor(){
            this.ajaxURL = App.ajaxUrl();// tecnico/reunion/ajax
            this.csrf = document.querySelector('meta[name="csrf-token"]').content;
            this.request = new XMLHttpRequest();
        }
        create( appointment ) {
            window.calendarContainer.loading();
            modal.hide();
            Appointment.collectData(currentAppointment.action);

            axios.post(App.ajaxUrl(), currentAppointment)
                .then(function (response) {
                    //window.calendarContainer.refetchEvents();
                    //window.calendarContainer.loading(false);
                    //table.refetchAjax();
                    //table.loading(false);
                    toastr.success(`${response.data.title} creado`, '¡Listo!');
                })
                .catch( error => {
                    window.calendarContainer.loading(false);
                    modal.show();
                    toastr.error(error.response.data.message, 'Error '+ error.response.status);
                });
        }
        createLink() {
            table.loading();
            modal.hide();
            Appointment.collectData(currentAppointment.action);

            axios.post(App.ajaxUrl(), currentAppointment)
                .then(function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Listo!',
                        html: '<p>El cliente debe ingresar con este enlace para poder agendar su reunión:</p>' +
                            '<a href="'+'{{ env('MIX_APP_URL').'/reunion/'}}'+response.data.slug+'" >'+'{{ env('MIX_APP_URL').'/reunion/'}}'+response.data.slug+'</a>',
                    });

                    //table.refetchAjax();
                    //table.loading(false);
                })
                .catch( error => {
                    Swal.fire({
                        icon:'error',
                        title: 'Ocurrió un error',
                        text: error.response.data.message,
                    });
                    table.loading(false);
                });
        }
        update() {
            modal.hide();
            Appointment.collectData(currentAppointment.action);

            axios.post(App.ajaxUrl(), currentAppointment)
                .then(function (response) {
                    toastr.success(`${response.data.title} actualizado`, '¡Listo!');

                    //window.calendarContainer.refetchEvents();
                    //window.calendarContainer.loading(false);
                    //table.loading(false);
                    //table.refetchAjax();
                })
                .catch( error => {
                    window.calendarContainer.loading(false);
                    table.loading(false);
                    modal.show();
                    toastr.error(error.response.data.message, 'Error '+ error.response.status);
                });
        }
        resize( event ){
            window.calendarContainer.loading();
            axios.post(App.ajaxUrl(), {
                action: 'resize',
                id: event.id,
                start: event.startStr,
                end: event.endStr
            })
                .then(function (response) {
                    let date = new Date(response.data.date_start+"T04:00").toLocaleDateString('es-cl');

                    let hour_start = response.data.hour_start.substring(0,5);
                    let hour_end = response.data.hour_end.substring(0,5);

                    toastr.success(`Ahora será el ${date} desde las ${hour_start} hasta las ${hour_end} horas`, 'Reunión actualizada');

                    window.calendarContainer.loading(false);
                })
                .catch( error => {
                    window.calendarContainer.loading(false);
                    toastr.error(error.response.data.message, 'Error '+ error.response.status);
                });
        }
        archive() {
            window.calendarContainer.loading();
            table.loading();
            modal.hide();
            currentAppointment.action = 'archive';

            axios.post(App.ajaxUrl(), currentAppointment)
                .then(function (response) {
                    toastr.success(`${response.data.title} actualizado`, '¡Listo!');
                })
                .catch( error => {
                    window.calendarContainer.loading(false);
                    table.loading(false);
                    modal.show();
                    toastr.error(error.response.data.message, 'Error '+ error.response.status);
                });

        }
        finish() {
            window.calendarContainer.loading();
            table.loading();

            let request = {};
            request.id = currentAppointment.id;
            request.action = 'finish';

            axios.post(App.ajaxUrl(), request)
                .then(function (response) {
                    let text = (response.data.finished === 1)  ? ' terminado': ' no terminado';
                    currentAppointment.finished = response.data.finished;
                    toastr.success(`${response.data.title} marcado como` + text, '¡Listo!');
                    //window.calendarContainer.refetchEvents();
                    window.calendarContainer.loading(false);
                    table.loading(false);
                    //table.refetchAjax();
                })
                .catch( error => {
                    window.calendarContainer.loading(false);
                    table.loading(false);
                    modal.show();
                    toastr.error(error.response.data.message, 'Error '+ error.response.status);
                });

        }
        destroy( slug ) {
            modal.hide();
            axios.post(App.ajaxUrl(), {
                action: 'destroy',
                id: slug
            })
                .then(function (response) {
                    toastr.success(`${response.data.title} eliminada`, '¡Listo!');
                })
                .catch( error => {
                    toastr.error(error.response.data.message, 'Error '+ error.response.status);
                });
        }
        comment( comment ){
            let currentId = currentAppointment.id;

            axios.post(App.ajaxUrl(), {
                action: 'comment',
                id: currentId,
                comment: comment,
            })
                .then(function (response) {
                    let section = document.getElementById('comment-section');
                    let element = section.getElementsByTagName('h5')[0];
                    element.parentNode.insertBefore(Comment.print(response.data.user,response.data.text,response.data.created_at), element.nextSibling);
                    document.querySelector('input[name="appointment-comment"]').value = '';

                    toastr.success('Listo');
                })
                .catch( error => {
                    toastr.error(error.response.data.message, 'Error '+ error.response.status);
                });
        }
        receive(calendarAppointment){
            table.loading();

            axios.post(App.ajaxUrl(), {
                action: 'receive',
                id: calendarAppointment.id,
                start: calendarAppointment.startStr,
                end: calendarAppointment.endStr
            })
                .then(function (response) {
                    let date = new Date(response.data.date_start+"T04:00").toLocaleDateString('es-cl');

                    let hour_start = response.data.hour_start.substring(0,5);
                    let hour_end = response.data.hour_end.substring(0,5);

                    toastr.success(`Agendado el ${date} desde las ${hour_start} hasta las ${hour_end} horas`, 'Reunión agendada');
                })
                .catch( error => {
                    table.refetchAjax();
                    table.loading(false);
                    toastr.error(error.response.data.message, 'Error '+ error.response.status);
                });
        }
    }
    class ModalHelper{
        constructor(id = '#appointmentModal', action = 'show') {
            this.modalId = id;
            this.modal = $(id);
            $(this.modal).appendTo("body");
            this.action = action;
            this.body = $(this.modal).find('.modal-body .container')[0];
            this.title = $(this.modal).find('.modal-title')[0];
            this.titleAdditionals = $(this.modal).find('.modal-title-additional')[0];
        }
        show(){
            $(this.modal).modal();
        }
        hide(){
            $(this.modal).modal('hide');
        }
        setTitle(title, html = ''){
            $(this.title).html('<span>'+title+'</span>'+html);
        }
        appointment(){
            $(this.body).html(AppointmentModalLayout.action(window.currentAppointment.action));
        }
        configButtons(){
            if(document.querySelector('.appointment-form')) {
                document.querySelector('.appointment-form').querySelector('button[type="submit"]').addEventListener('click', function (event) {
                    event.preventDefault();
                    // Se comprueba si el formulario es válido y
                    // el resultado se almacena en una variable
                    let validity = event.target.form.reportValidity();
                    // Se evalúa dicha variable y se envía la información
                    // al controlador a través de Ajax
                    if(validity) {
                        appointmentController.action(currentAppointment.action);
                    }
                });
            }
            if(document.querySelector('.archive-appointment')) {
                document.querySelector('.archive-appointment').addEventListener('click', function (event) {
                    event.preventDefault();
                    appointmentController.archive();
                });
            }
            if(document.querySelector('.destroy-appointment')) {
                document.querySelector('.destroy-appointment').addEventListener('click', function (event) {
                    event.preventDefault();
                    AppointmentPartialViews.delete(currentAppointment);
                });
            }
            if(document.querySelector('.comment-appointment')) {
                document.querySelector('.comment-appointment').addEventListener('click', function (event) {
                    event.preventDefault();
                    let parentContainer = this.parentElement.parentElement;
                    let input = parentContainer.querySelector('input[name="appointment-comment"]');
                    appointmentController.comment( input.value );
                });
            }
            if(document.querySelector('#finished-appointment')) {
                document.querySelector('#finished-appointment').addEventListener('click', function (event) {
                    appointmentController.finish();
                });
            }
        }
        static template(){
            return ""
                + "<div class='modal-dialog modal-dialog-centered modal-xl'>"
                + "<div class='modal-content'>"
                + "<div class='modal-header'>"
                + "<h5 class='modal-title'></h5>"
                + "<div class='modal-title-aditionals'></div>"
                + "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>"
                + "</div>"
                + "<div class='modal-body'>"
                + "<div class='container'>"
                + "</div>"
                + "</div>"
                + "</div>"
                + "</div>";
        }
        static availableButtons( action ){
            let buttons = document.createElement('div');

            if(action == 'createLink' || action == 'create') {
                if (User.can('REUNION CREAR')) {
                    buttons.appendChild(AppointmentModalPartials.buttons(action));
                }
                return buttons;
            }
            if(action == 'update') {
                if (User.can('REUNION MODIFICAR')) {
                    buttons.appendChild(AppointmentModalPartials.buttons('update'));
                }
                if (User.can('REUNION ARCHIVAR') && currentAppointment.scheduled == 1) {
                    buttons.appendChild(AppointmentModalPartials.buttons('archive'));
                }
                if (User.can('REUNION ELIMINAR')) {
                    buttons.appendChild(AppointmentModalPartials.buttons('delete'));
                }
            }
            return buttons;
        }
    }
    class AppointmentModalPartials{
        static baseForm(){
            let form = document.createElement('form');
            form.classList.add('row','appointment-form');
            return form;
        }
        static baseFormLeftColumn(){
            let leftColumn = document.createElement('div');
            leftColumn.classList.add('col', 'col-12', 'col-sm-9', 'col-lg-10');
            return leftColumn;
        }
        static baseFormRightColumn(){
            let rightColumn = document.createElement('div');
            rightColumn.classList.add('col', 'col-12', 'col-sm-3', 'col-lg-2');
            return rightColumn;
        }
        static commentInput(){
            return `<div class="input-group input-group-sm mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text">Comentario</span>
                </div>
                <input type="text" class="form-control"
                           name="appointment-comment">
                <div class="input-group-append">
                    <button class="btn btn-success comment-appointment" type="button">Guardar</button>
                </div>
            </div>`;
        }
        static commentSection(){
            let section = document.createElement('div');
            section.classList.add('comment-section');

            let title = document.createElement('h5');
            title.classList.add('font-weight-bold');
            title.classList.add('text-dark');
            title.innerHTML = 'Comentarios';
            section.appendChild(title);

            for( let i = 0; i < currentAppointment.comments.length; i++ ) {
                let current = currentAppointment.comments[i];
                section.appendChild(Comment.print(current['user'], current['text'], current['created_at']));
            }
            return section;
        }
        static activitySection(){
            let section = document.createElement('div');
            section.classList.add('activity-section');

            let title = document.createElement('h5');
            title.classList.add('font-weight-bold');
            title.classList.add('text-dark');
            title.innerHTML = 'Movimientos';
            section.appendChild(title);

            for( let i = 0; i < currentAppointment.activities.length; i++ ) {
                let current = currentAppointment.activities[i];
                section.appendChild(Activity.print(current['user'], current['text'], current['time']));
            }
            return section;
        }
        static buttons(action){
            let button = document.createElement('button');
            button.classList.add('btn','my-1','btn-sm','text-white','btn-block');

            let buttons = {
                'create': () =>{
                    button.type = 'submit';
                    button.classList.add('btn-success','create-appointment');
                    button.innerHTML='Guardar';
                    return button;
                },
                'createLink': () =>{
                    button.type = 'submit';
                    button.classList.add('btn-success','createLink-appointment');
                    button.innerHTML='Guardar';
                    return button;
                },
                'update':  () =>{
                    button.type = 'submit';
                    button.classList.add('btn-success','save-appointment');
                    button.innerHTML='Guardar';
                    return button;
                },
                'archive': () => {
                    button.type = 'button';
                    button.classList.add('btn-danger','archive-appointment');
                    button.innerHTML='Archivar';
                    return button;
                },
                'delete': () => {
                    button.type = 'button';
                    button.classList.add('destroy-appointment');
                    button.classList.remove('text-white');
                    button.innerHTML='Eliminar';
                    return button;
                },
            }

            return buttons[action]().cloneNode(true);
        }
    }
    class AppointmentModalLayout{
        static action(action){
            let actions = {
                'create': this.create,
                'createLink': this.createLink,
                'update': this.update,
            }

            return actions[action]();
        }
        static create(){
            let row = AppointmentModalPartials.baseForm();

            let leftColumn = AppointmentModalPartials.baseFormLeftColumn();

            let inputs = document.createElement('div');
            inputs.innerHTML += InputSeudoHelper.text('Titulo', 'appointment-name', 'Titulo123', '', true);
            inputs.innerHTML += InputSeudoHelper.text('ID', 'appointment-id', 'ID123ASD');
            inputs.innerHTML += InputSeudoHelper.datetime({
                label: 'Horario',
                date_name: 'appointment-date',
                date_value: currentAppointment.date_start,
                date_required: true,
                time_name:'appointment-time',
                time_required: true,
                time_min:'09:00',
                time_max:'18:00',
                timeStart_value:currentAppointment.hour_start,
                timeEnd_value:currentAppointment.hour_end,
            })

            //inputs.innerHTML += InputSeudoHelper.date('Fecha', 'appointment-date', getCurrentDate(), currentAppointment.date_start, true);
            //inputs.innerHTML += InputSeudoHelper.timeMinAndMax('Inicio/Fin', 'appointment-time', '09:00', '18:00',currentAppointment.hour_start, currentAppointment.hour_end,true);
            inputs.innerHTML += InputSeudoHelper.select('Encargado', 'appointment-attendee', usersList);
            inputs.innerHTML += InputSeudoHelper.textarea('Descripcion', 'appointment-detail', 'Detalle de la cita', '');

            leftColumn.appendChild(inputs);

            let rightColumn = AppointmentModalPartials.baseFormRightColumn();

            rightColumn.appendChild(ModalHelper.availableButtons(currentAppointment.action));

            row.appendChild(leftColumn);
            row.appendChild(rightColumn);

            return row;
        }
        static createLink(){
            let row = AppointmentModalPartials.baseForm();

            let leftColumn = AppointmentModalPartials.baseFormLeftColumn();

            let inputs = document.createElement('div');
            let radioOptions = {
                0:{
                    key:'1 hora',
                    value: 60
                },
                1:{
                    key:'2 horas',
                    value: 120
                },
                2:{
                    key:'3 horas',
                    value: 180
                },
            };
            inputs.innerHTML += InputSeudoHelper.radio('Duracion','appointment-duration', radioOptions, '', true, true);
            inputs.innerHTML += InputSeudoHelper.text('Titulo', 'appointment-name', 'Titulo123', '', true);
            inputs.innerHTML += InputSeudoHelper.text('ID', 'appointment-id', 'ID123ASD');
            //inputs.innerHTML += InputSeudoHelper.select('Encargado', 'appointment-attendee', usersList);
            inputs.innerHTML += InputSeudoHelper.textarea('Descripcion', 'appointment-detail', 'Detalle de la cita', '');

            leftColumn.appendChild(inputs);

            let rightColumn = AppointmentModalPartials.baseFormRightColumn();

            rightColumn.appendChild(ModalHelper.availableButtons(currentAppointment.action));

            row.appendChild(leftColumn);
            row.appendChild(rightColumn);

            return row;
        }
        static update(){
            let row = AppointmentModalPartials.baseForm();
            let leftColumn = AppointmentModalPartials.baseFormLeftColumn();

            if( currentAppointment.scheduled == 0) {
                let radioOptions = {
                    0:{
                        key:'1 hora',
                        value: 60
                    },
                    1:{
                        key:'2 horas',
                        value: 120
                    },
                    2:{
                        key:'3 horas',
                        value: 180
                    },
                };
                leftColumn.innerHTML += InputSeudoHelper.radio('Duracion','appointment-duration', radioOptions,currentAppointment.duration, true, true);
            }

            leftColumn.innerHTML += InputSeudoHelper.text('Titulo', 'appointment-name', 'Titulo123', currentAppointment.title, true);
            leftColumn.innerHTML += InputSeudoHelper.text('ID', 'appointment-id', 'ID123ASD', currentAppointment.id, true);

            if( currentAppointment.scheduled == 1) {
                leftColumn.innerHTML += InputSeudoHelper.datetime({
                    label: 'Horario',
                    date_name: 'appointment-date',
                    date_value: currentAppointment.date_start,
                    date_required: true,
                    time_name:'appointment-time',
                    time_required: true,
                    time_min:'09:00',
                    time_max:'18:00',
                    timeStart_value:currentAppointment.hour_start,
                    timeEnd_value:currentAppointment.hour_end,
                })
            }

            leftColumn.innerHTML += InputSeudoHelper.select('Encargado', 'appointment-attendee', usersList);
            leftColumn.innerHTML += InputSeudoHelper.textarea('Descripcion', 'appointment-detail', 'Detalle de la cita', currentAppointment.detail);

            leftColumn.innerHTML += '<hr/>';
            leftColumn.innerHTML += AppointmentModalPartials.commentInput();
            leftColumn.appendChild(AppointmentModalPartials.commentSection());
            leftColumn.appendChild(AppointmentModalPartials.activitySection());

            let rightColumn = AppointmentModalPartials.baseFormRightColumn();
            rightColumn.appendChild(ModalHelper.availableButtons(currentAppointment.action));
            rightColumn.innerHTML += `<p>Creador: ${ currentAppointment.creatorName }</p>`;

            if(currentAppointment.scheduled == 0 && currentAppointment.archiverName != 'Sin usuario'){
                rightColumn.innerHTML += `<p>Archivado: ${ currentAppointment.archiverName }</p>`;
            }

            row.appendChild(leftColumn);
            row.appendChild(rightColumn);

            return row;
        }
    }
    class InputSeudoHelper{
        static bare_date(name, min = '', value ='', required = false){
            return `<input type="date" class="form-control form-control-sm"
                    min="${min}"
                    value="${value}"
                    name="${name}" ${ (required) ? 'required':'' }>`;
        }
        static bare_time(name, min = '', max = '', value= '', required = false){
            return `<input type="time" min="${min}" max="${max}"
                    name="${name}"
                    value="${value}"
                    step="1800"
                    class="form-control form-control-sm" ${ (required) ? 'required':'' }/>`;
        }
        static text(label, name, placeholder, value = '', required = false){
            return `<div class="form-group row">
            <label for="${name}" class="col-sm-2 col-form-label col-form-label-sm">${label}</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm"
                           name="${name}"
                           value="${value}"
                           placeholder="${placeholder}" ${ (required) ? 'required':'' }>
                </div>
            </div>`;
        }
        static radio(label, name, values, selected, required = false, inline = false){
            let radio ='<div class="form-group row">';
            radio += `<label for="${name}" class="col-sm-2 col-form-label col-form-label-sm">${label}</label>`;
            let length = Object.keys(values).length;
            if(length > 0) {
                for (let i = 0; i < length; i++) {
                    radio +=
                        `<div class="form-check ${(inline) ? 'form-check-inline' : ''}">
                    <input class="form-check-input" type="radio" name="${name}" id="${name + i}" value="${values[i].value}" ${(values[i].value == selected)?'checked="checked"':''} required>
                    <label class="form-check-label" for="${name + i}">${values[i].key}</label>
                </div>`
                }
            }
            radio +='</div>';
            return radio;
        }
        static date(label, name, min = '', value ='', required = false){
            return `<div class="form-group row">
            <label for="${name}" class="col-sm-2 col-form-label col-form-label-sm">${label}</label>
            <div class="col-sm-10">
                <input type="date" class="form-control form-control-sm"
                    min="${min}"
                    value="${value}"
                    name="${name}" ${ (required) ? 'required':'' }>
            </div>
        </div>`;
        }
        static datetime( args ){
            return '<div class="form-group row">' +
                '<div class="col-sm-2">' +
                '<label class="col-form-label col-form-label-sm">'+args.label+'</label>'+
                '</div>' +
                '<div class="col-sm-10">' +
                '<div class="row">' +
                '<div class="col-sm-4">' +
                this.bare_date(args.date_name, args.date_min, args.date_value, args.date_required) +
                '</div>' +
                '<div class="col-sm-4">' +
                this.bare_time(args.time_name+'-start', args.time_min, args.time_max, args.timeStart_value, args.time_required) +
                '</div>' +
                '<div class="col-sm-4">' +
                this.bare_time(args.time_name+'-end', args.time_min, args.time_max, args.timeEnd_value, args.time_required) +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>'
        }

        static timeMinAndMax(label, name, min = '', max = '', minval= '',maxval= '', required = false){
            return `<div class="form-group row">
            <label for="${name}" class="col-sm-2 col-form-label col-form-label-sm">${label}</label>
            <div class="col-sm-5">
                <input type="time" min="${min}" max="${max}"
                           name="${name}-start"
                           value="${minval}"
                           step="1800"
                           class="form-control form-control-sm" ${ (required) ? 'required':'' }/>
                </div>
                <div class="col-sm-5">
                    <input type="time" min="${min}" max="${max}"
                           name="${name}-end"
                           value="${maxval}"
                           step="1800"
                           class="form-control form-control-sm" ${ (required) ? 'required':'' }/>
            </div>
        </div>`;
        }
        static textarea(label, name, placeholder ='', value ='', rows = 15){
            return `<div class="form-group">
                    <label class="col-form-label">${label}</label>
                    <textarea class="form-control form-control-sm"
                              name="${name}" rows="${rows}" placeholder="${placeholder}">${value}</textarea>
                </div>`;
        }
        static select(label, name, collection = array(), required = false){
            let element = `<div class="form-group row">
                <label for="${name}" class="col-sm-2 col-form-label col-form-label-sm">${label}</label>
                <div class="col-sm-10">
                <select class="custom-select custom-select-sm" name="${name}"  ${ (required) ? 'required':'' }/>`;

            if(collection.length > 0) {
                element +=`<option value="" selected disabled hidden>Selecciona una opción</option>`;
                for (let i = 0; i < collection.length; i++) {
                    element += `<option value="${collection[i].id}">${collection[i].name}</option>`;
                }
            } else {
                element +=`<option disabled>No se encontraron opciones disponibles</option>`;
            }
            element +=`</select> </div></div>`;

            return element;
        }
    }
    class CalendarContainer{
        constructor( elementId ){
            this.elementId = elementId;
            this.htmlElement = document.getElementById(elementId);
            this.calendar = new FullCalendar.Calendar(this.htmlElement, this.properties());

            let parent = this.parent();
            this.loadingElement = Loader.element();
            parent.insertBefore(this.loadingElement, parent.childNodes[0]);
        }
        properties(){
            return {
                initialView: 'timeGridWeek',
                locale: 'es-us',
                allDaySlot: false,
                firstDay:1,
                weekends:false,
                stickyHeaderDates:true,
                nowIndicator:true,
                height: '75vh',
                slotMinTime: "09:00:00",
                slotMaxTime: "19:30:00",
                editable:true,
                selectable:true,
                selectConstraint:'businessHours',
                displayEventTime:false,
                eventDisplay:'block',
                eventOverlap: true,
                eventResizableFromStart:true,
                eventDurationEditable:true,
                eventLongPressDelay:3,
                lazyFetching:false,
                businessHours: [
                    {
                        daysOfWeek: [1, 2, 3, 4, 5],
                        startTime: '10:00',
                        endTime: '14:00'
                    },
                    {
                        daysOfWeek: [1, 2, 3, 4, 5],
                        startTime: '15:00',
                        endTime: '18:00',
                    }
                ],
                titleFormat: {
                    month: 'short',
                    day: 'numeric'
                },
                slotLabelFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    omitZeroMinute: false,
                    hour12:false
                },
                dayHeaderFormat: {
                    weekday: 'long',
                    day: 'numeric',
                    omitCommas: false
                },
                headerToolbar: {
                    left: 'prev,today,next',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                select: function(eventInfo) { AppointmentPartialViews.create(eventInfo) },
                dateClick: function( info ){
                    if(info.view.type === 'dayGridMonth'){
                        window.calendarContainer.calendar.changeView( 'timeGridWeek', info.dateStr );
                    }
                },
                events: {
                    url: App.appointmentsUrl() + 'events',
                    constraint:'businessHours',
                    borderColor:'#FFF',
                },
                eventResize: function (eventInfo ) { appointmentController.resize(eventInfo.event) },
                eventDrop: function (eventInfo) { appointmentController.resize(eventInfo.event) },
                eventClick: function (info) { AppointmentPartialViews.update( info.event.id ) },
                eventReceive: function (eventInfo){ appointmentController.receive( eventInfo.event ); eventInfo.revert() },
                eventDidMount: function (info){
                    if(info.event.extendedProps.finished === 1) {
                        info.event.setProp('backgroundColor', '#47cf73');
                        return;
                    }
                    let user = window.usersList.find(user => user.id === info.event.extendedProps.rut)
                    if (user) {
                        info.event.setProp('backgroundColor', user.color);
                        return;
                    }
                    info.event.setProp('backgroundColor', '#525252');
                },
                //loading:function(isLoading){ handleCalendarLoadingState(isLoading) },
            };
        }
        render(){
            this.calendar.render();
        }
        setProperty(property, value){
            this.calendar.setProp(property, value);
        }
        refetchEvents() {
            //this.calendar.removeAllEvents();
            return this.calendar.refetchEvents();
        }
        loading( isLoading = true ){
            if(isLoading){
                this.disabled(isLoading);
                this.loadingElement.classList.add('show');
                return isLoading;
            }

            this.loadingElement.classList.remove('show');
            this.disabled(isLoading);
            return isLoading;
        }
        disabled( isDisabled = true ){
            if(isDisabled) {
                this.htmlElement.classList.add('disabled');
                return isDisabled;
            }

            this.htmlElement.classList.remove('disabled');
            return isDisabled;
        }
        parent(){
            return document.getElementById(this.elementId).parentNode;
        }
        getElementById(id){
            return this.calendar.getEventById(id);
        }
    }
    class DataTableHelper{
        constructor( elementId ) {
            this.elementId = elementId;
            this.htmlElement = document.getElementById(this.elementId);
            this.jQueryElement = $('#'+this.elementId);
            this.datatable = null;
            this.loadingElement = Loader.element();

            let parent = this.parent();
            parent.insertBefore(this.loadingElement, parent.childNodes[0]);
        }
        language(){
            return {
                sProcessing: "Procesando...",
                sLengthMenu: "Mostrar _MENU_ registros",
                sZeroRecords: "No se encontraron resultados",
                sEmptyTable: "Ningún dato disponible en esta tabla",
                sInfo: "Mostrando del _START_ al _END_. Total: _TOTAL_",
                sInfoEmpty: "Total: 0",
                sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
                sInfoPostFix: "",
                sSearch: "Buscar:",
                sUrl: "",
                sInfoThousands: ",",
                sLoadingRecords: "Cargando...",
                oPaginate: {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                oAria: {
                    sSortAscending: ": Activar para ordenar la columna de manera ascendente",
                    sSortDescending: ": Activar para ordenar la columna de manera descendente"
                },
                buttons: {
                    copy: "Copiar",
                    colvis: "Visibilidad"
                }
            };
        }
        properties() {
            return {
                ajax: App.appointmentsUrl()+'eventsArchived',
                columns: [
                    /*{
                        "className":      'details-control',
                        "orderable":      false,
                        "data":           null,
                        "defaultContent": ''
                    },*/
                    {
                        title:'Título',
                        data: 'title',
                    },
                    {
                        title:'Link',
                        data: 'slug',
                        render: function(data, type, row){
                            return '<a href="'+'{{ env('MIX_APP_URL').'/reunion/' }}'+data+'">'+data+'</a>';
                        }
                    },
                    {
                        title:'Fecha creación',
                        data: 'created_date'
                    },
                    {
                        title:'Hora creación',
                        data: 'created_hour'
                    },
                    {
                        title:'Opción',
                        data: null,
                        defaultContent: this.availableActions()
                    }
                ],
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "Todos"]
                ],
                pageLength: 10,
                language: this.language(),
                createdRow: function ( row, data, index ) {
                    let draggable = $(row).find('.event-draggable')[0];

                    new FullCalendar.Draggable(draggable, {
                        eventData: {
                            title: data.title,
                            duration: {minutes:data.duration},
                            id: data.slug,
                            constraint:'businessHours'
                        }
                    });
                }
            };
        }
        render(){
            this.datatable = $(this.jQueryElement).DataTable(this.properties());
        }
        refetchAjax(){
            return this.datatable.ajax.reload();
        }
        configActions(){
            let datatableBodyTag = '#'+this.elementId+ ' tbody';
            let datatable = this.datatable;

            $(datatableBodyTag).on('click','.event-update', function () {
                let data = datatable.row( $(this).parents('tr') ).data();
                AppointmentPartialViews.update( data.slug );
            });

            $(datatableBodyTag).on('click','.event-destroy', function () {
                let data = datatable.row( $(this).parents('tr') ).data();
                AppointmentPartialViews.delete(data);
            });

        }
        availableActions(){
            let buttons = "<button class='btn btn-info btn-circle btn-sm text-white event-update'><i class='fas fa-eye'></i></button>";
            buttons += "<div class='btn btn-info btn-circle btn-sm text-white event-draggable'><i class='fas fa-mouse-pointer'></i></div>";

            if (User.can('REUNION ELIMINAR')) {
                buttons += "<button class='btn btn-danger btn-circle btn-sm text-white event-destroy'><i class='fas fa-trash'></i></button>";
            }

            return buttons;
        }
        loading( isLoading = true ){
            if(isLoading){
                this.disabled(isLoading);
                this.loadingElement.classList.add('show');
                return isLoading;
            }

            this.loadingElement.classList.remove('show');
            this.disabled(isLoading);
            return isLoading;
        }
        disabled( isDisabled = true ){
            if(isDisabled) {
                this.htmlElement.classList.add('disabled');
                return isDisabled;
            }

            this.htmlElement.classList.remove('disabled');
            return isDisabled;
        }
        parent(){
            return this.htmlElement.parentNode;
        }
    }
    function getCurrentDate(){
        let day = ((new Date().getDate()).toString().length == 1 ) ? '0' + new Date().getDate() : new Date().getDate();
        let month = (new Date().getMonth() + 1).toString() ;
        month = (month.length == 1 ) ? '0' + month : month;
        let year = new Date().getFullYear();

        let today = year +'-' + month + '-' + day;
        return today;
    }
    function parseResponseHours( dateString ){
        return ((new Date(dateString).getHours()).toString().length == 1 )
            ? '0' + new Date(dateString).getHours()
            : new Date(dateString).getHours();
    }
    function parseResponseMinutes( dateString ){
        return ((new Date(dateString).getMinutes()).toString().length == 1 )
            ? '0' + new Date(dateString).getMinutes()
            : new Date(dateString).getMinutes();
    }

    document.addEventListener('DOMContentLoaded', function() {
        $("#appointment-id").on({
            keydown: function(e) {
                if (e.which === 32)
                    return false;
            },
            change: function() {
                this.value = this.value.replace(/\s/g, "");
            }
        });

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });
        App.setPageRequirements();
        App.tryRenderPlugins();

        window.appointmentModal = new AppointmentModal();

        appointmentModal.show();
    });

</script>

</x-app-layout>
