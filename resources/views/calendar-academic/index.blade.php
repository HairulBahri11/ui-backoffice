@extends('template.app')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    :root { --primary-theme: #01c293; --card-radius: 20px; }
    
    #calendar { 
        background: #ffffff; 
        padding: 25px; 
        border-radius: var(--card-radius); 
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: none;
    }

    /* Header Styling */
    .fc-toolbar-title { font-weight: 800 !important; color: #2c3e50; }
    .fc-button-primary { 
        background: #fff !important; border: 1px solid #ebedef !important; color: #5d6d7e !important;
        border-radius: 10px !important; font-weight: 600 !important; transition: 0.3s;
    }
    .fc-button-primary:hover { background: var(--primary-theme) !important; color: #fff !important; }
    .fc-button-active { background: var(--primary-theme) !important; border-color: var(--primary-theme) !important; color: #fff !important; }

    /* Event Styling */
    .fc-event { 
        border: none !important; padding: 5px 10px !important; border-radius: 8px !important; 
        font-weight: 500 !important; cursor: pointer; transition: 0.2s;
    }
    .fc-event:hover { transform: translateY(-2px); filter: brightness(0.9); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }

    /* Modal Styling */
    .modal-content { border-radius: 25px; border: none; overflow: hidden; }
    .modal-header { background: #f8f9fa; border-bottom: none; padding: 25px; }
    .form-control { border-radius: 12px; border: 1px solid #eef0f2; background: #fdfdfd; padding: 12px; }
    .form-control:focus { border-color: var(--primary-theme); box-shadow: none; background: #fff; }
    .btn-round { border-radius: 50px !important; padding: 10px 25px !important; font-weight: 600; }
</style>

@section('content')
<div class="content">
    <div class="panel-header bg-primary-gradient" style="background: linear-gradient(-45deg, #01c293, #01c293) !important">
        <div class="page-inner py-5">
            <div class="d-flex align-items-center">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Academic Calendar</h2>
                    <h5 class="text-white op-8">Plan and track your academic year seamlessly.</h5>
                </div>
                <div class="ml-auto">
                    @if(Auth::guard('staff')->check())
                    <button class="btn btn-secondary btn-round shadow" data-toggle="modal" data-target="#addEventModal">
                        <i class="fa fa-plus mr-2"></i> New Event
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div id="calendar"></div>
    </div>
</div>

<div class="modal fade" id="addEventModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">
            <form action="{{ route('calendar-academic.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title fw-bold">✨ Create Event</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body px-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Event Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Ex: Final Examination" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="small font-weight-bold">Start</label>
                            <input type="datetime-local" name="start" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="small font-weight-bold">End</label>
                            <input type="datetime-local" name="end" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label class="font-weight-bold">Category</label>
                        <select name="category" class="form-control">
                            <option value="Event">📅 Academic Event</option>
                            <option value="Exam">📝 Examination</option>
                            <option value="Holiday">🏖️ School Holiday</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Description</label>
                        <textarea name="detail" class="form-control" rows="3" placeholder="Additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary btn-round btn-block shadow">Save Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editEventModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-top border-primary">
            <div class="modal-header">
                <h4 class="modal-title fw-bold">📝 Edit Event</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body px-4">
                <input type="hidden" id="edit_id">
                <div class="form-group mb-3"><label class="font-weight-bold">Title</label><input type="text" id="edit_title" class="form-control"></div>
                <div class="row mb-3">
                    <div class="col-6"><label class="small font-weight-bold">Start</label><input type="datetime-local" id="edit_start" class="form-control"></div>
                    <div class="col-6"><label class="small font-weight-bold">End</label><input type="datetime-local" id="edit_end" class="form-control"></div>
                </div>
                <div class="form-group mb-3">
                    <label class="font-weight-bold">Category</label>
                    <select id="edit_category" class="form-control">
                        <option value="Event">📅 Academic Event</option>
                        <option value="Exam">📝 Examination</option>
                        <option value="Holiday">🏖️ School Holiday</option>
                    </select>
                </div>
                <div class="form-group"><label class="font-weight-bold">Description</label><textarea id="edit_detail" class="form-control" rows="3"></textarea></div>
            </div>

            @if(Auth::guard('staff')->check())
            <div class="modal-footer border-0 d-flex justify-content-between">
                <button type="button" class="btn btn-danger btn-round px-4" id="btnDelete">Delete</button>
                <button type="button" class="btn btn-primary btn-round px-4 shadow" id="btnUpdate">Update Changes</button>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listMonth' },
            events: "{{ route('calendar.events') }}",
            
            eventClick: function(info) {
                const event = info.event;
                $('#edit_id').val(event.id);
                $('#edit_title').val(event.title);
                $('#edit_start').val(event.start.toISOString().slice(0,16));
                $('#edit_end').val(event.end ? event.end.toISOString().slice(0,16) : event.start.toISOString().slice(0,16));
                $('#edit_category').val(event.extendedProps.category);
                $('#edit_detail').val(event.extendedProps.detail);
                $('#editEventModal').modal('show');
            }
        });
        calendar.render();

        // AJAX UPDATE
        $('#btnUpdate').click(function() {
            const id = $('#edit_id').val();
            $.ajax({
                url: `/calendar-academic/${id}`,
                type: 'PUT',
                data: {
                    _token: "{{ csrf_token() }}",
                    title: $('#edit_title').val(),
                    start: $('#edit_start').val(),
                    end: $('#edit_end').val(),
                    category: $('#edit_category').val(),
                    detail: $('#edit_detail').val(),
                },
                success: function(res) {
                    $('#editEventModal').modal('hide');
                    Swal.fire('Success!', res.message, 'success');
                    calendar.refetchEvents();
                    window.location.reload();
                }
            });
        });

        // AJAX DELETE
        $('#btnDelete').click(function() {
            const id = $('#edit_id').val();
            Swal.fire({
                title: 'Delete this event?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f3545d',
                confirmButtonText: 'Yes, delete!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/calendar-academic/${id}`,
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(res) {
                            $('#editEventModal').modal('hide');
                            Swal.fire('Deleted!', res.message, 'success');
                            calendar.refetchEvents();
                        }
                    });
                }
            });
        });
    });
</script>