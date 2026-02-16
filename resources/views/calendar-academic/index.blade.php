@extends('template.app')

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    :root { 
        --primary-theme: #01c293; 
        --card-radius: 20px; 
    }
    
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
        border-radius: 10px !important; font-weight: 600 !important;
    }
    .fc-button-active { background: var(--primary-theme) !important; color: #fff !important; }

    /* --- PERBAIKAN BADGE --- */
    .fc-event { 
        background: transparent !important; /* Menghilangkan warna background pada badge */
        border: none !important; 
        box-shadow: none !important;
        padding: 2px 5px !important; 
        cursor: pointer; 
        z-index: 5;
    }

    /* Warna teks badge agar tetap kontras (Gelap agar terbaca di background sel yang terang) */
    .fc-event-main, .fc-event-title, .fc-event-time { 
        color: #2c3e50 !important; 
        font-weight: 700 !important;
    }

    /* Angka Tanggal */
    .fc-daygrid-day-number {
        position: relative;
        z-index: 10;
        font-weight: bold;
        color: #2c3e50 !important;
    }

    /* Highlight Hari Minggu */
    .fc-day-sun { background-color: rgba(255, 0, 0, 0.05) !important; }
    .fc-day-sun .fc-daygrid-day-number { color: #e74c3c !important; }

    .fc-daygrid-day { transition: background-color 0.2s ease; position: relative; }
</style>

@section('content')
<div class="content">
    <div class="panel-header" style="background: var(--primary-theme) !important">
        <div class="page-inner py-5">
            <div class="d-flex align-items-center">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Academic Calendar</h2>
                    <h5 class="text-white op-8">Badge transparan, background sel solid.</h5>
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

<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('calendar-academic.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title fw-bold">✨ Create Event</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body px-4">
                    <div class="form-group mb-3">
                        <label>Event Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6"><label>Start</label><input type="datetime-local" name="start" class="form-control" required></div>
                        <div class="col-6"><label>End</label><input type="datetime-local" name="end" class="form-control" required></div>
                    </div>
                    <div class="form-group mt-3">
                        <label>Category</label>
                        <select name="category" class="form-control">
                            <option value="Event">📅 Academic Event</option>
                            <option value="Exam">📝 Examination</option>
                            <option value="Holiday">🏖️ School Holiday</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-round btn-block">Save Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editEventModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-top border-primary">
            <div class="modal-header">
                <h4 class="modal-title fw-bold">📝 Edit Event</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body px-4">
                <input type="hidden" id="edit_id">
                <div class="form-group mb-3"><label>Title</label><input type="text" id="edit_title" class="form-control"></div>
                <div class="row mb-3">
                    <div class="col-6"><label>Start</label><input type="datetime-local" id="edit_start" class="form-control"></div>
                    <div class="col-6"><label>End</label><input type="datetime-local" id="edit_end" class="form-control"></div>
                </div>
                <div class="form-group mb-3">
                    <label>Category</label>
                    <select id="edit_category" class="form-control">
                        <option value="Event">📅 Academic Event</option>
                        <option value="Exam">📝 Examination</option>
                        <option value="Holiday">🏖️ School Holiday</option>
                    </select>
                </div>
            </div>
            @if(Auth::guard('staff')->check())
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-danger btn-round" id="btnDelete">Delete</button>
                <button type="button" class="btn btn-primary btn-round" id="btnUpdate">Update Changes</button>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: "{{ route('calendar.events') }}",
            
            eventSourceSuccess: function() {
                setTimeout(() => colorizeFullRange(), 150);
            },
            datesSet: function() {
                colorizeFullRange();
            },

            eventClick: function(info) {
                const event = info.event;
                $('#edit_id').val(event.id);
                $('#edit_title').val(event.title);
                $('#edit_start').val(event.start.toLocaleString('sv-SE').replace(' ', 'T').slice(0, 16));
                $('#edit_end').val(event.end ? event.end.toLocaleString('sv-SE').replace(' ', 'T').slice(0, 16) : '');
                $('#edit_category').val(event.extendedProps.category);
                $('#editEventModal').modal('show');
            }
        });

        calendar.render();

        function colorizeFullRange() {
            document.querySelectorAll('.fc-daygrid-day').forEach(el => el.style.backgroundColor = '');

            const allEvents = calendar.getEvents();
            allEvents.forEach(event => {
                const badgeColor = event.backgroundColor || '#01c293';
                
                let curr = new Date(event.start);
                curr.setHours(0,0,0,0);
                
                let last = event.end ? new Date(event.end) : new Date(event.start);
                last.setHours(0,0,0,0);

                if (event.end && event.end.getHours() === 0 && event.end.getMinutes() === 0) {
                    last.setDate(last.getDate() - 1);
                }

                while (curr <= last) {
                    let dateStr = curr.toLocaleString('sv-SE').split(' ')[0];
                    let cell = document.querySelector(`.fc-daygrid-day[data-date="${dateStr}"]`);
                    
                    if (cell) {
                        // Menggunakan opacity 0.3 agar teks hitam di atasnya terbaca jelas
                        cell.style.backgroundColor = badgeColor + '44'; 
                    }
                    curr.setDate(curr.getDate() + 1);
                }
            });
        }

        // Handler Update & Delete (AJAX)
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
                    category: $('#edit_category').val()
                },
                success: function() {
                    $('#editEventModal').modal('hide');
                    calendar.refetchEvents();
                    Swal.fire('Updated!', 'Success', 'success');
                }
            });
        });

        $('#btnDelete').click(function() {
            const id = $('#edit_id').val();
            $.ajax({
                url: `/calendar-academic/${id}`,
                type: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                success: function() {
                    $('#editEventModal').modal('hide');
                    calendar.refetchEvents();
                    Swal.fire('Deleted!', 'Success', 'success');
                }
            });
        });
    });
</script>