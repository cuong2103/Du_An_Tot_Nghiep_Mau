<x-layouts.admin title="Lịch khám (Calendar View)">
    <!-- FullCalendar CSS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Lịch khám tổng quan</h2>
                <p class="text-sm text-gray-500 mt-1">Xem tổng quan các ca khám theo ngày, tuần, tháng.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.appointments.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center gap-2">
                    <i class="fa-solid fa-list"></i> Dạng danh sách
                </a>
                <a href="{{ route('admin.appointments.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Đặt lịch mới
                </a>
            </div>
        </div>

        <!-- Chú thích trạng thái -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex flex-wrap items-center gap-4 text-sm">
            <span class="font-medium text-gray-700">Chú giải:</span>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full" style="background-color: #eab308;"></span> Chờ khám</div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full" style="background-color: #3b82f6;"></span> Đã tiếp nhận</div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full" style="background-color: #a855f7;"></span> Đang khám</div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full" style="background-color: #22c55e;"></span> Hoàn thành</div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full" style="background-color: #6b7280;"></span> Vắng mặt</div>
        </div>

        <!-- Calendar Container -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- FullCalendar Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var events = @json($events);

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                locale: 'vi',
                buttonText: {
                    today: 'Hôm nay',
                    month: 'Tháng',
                    week: 'Tuần',
                    day: 'Ngày',
                    list: 'Danh sách'
                },
                allDaySlot: false,
                slotMinTime: '06:00:00',
                slotMaxTime: '21:00:00',
                events: events,
                eventClick: function(info) {
                    info.jsEvent.preventDefault(); // don't let the browser navigate
                    if (info.event.url) {
                        window.location.href = info.event.url;
                    }
                },
                height: 'auto',
                expandRows: true,
            });

            calendar.render();
        });
    </script>

    <style>
        .fc-event {
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .fc-event:hover {
            opacity: 0.8;
        }
        .fc-toolbar-title {
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            color: #1f2937;
        }
        .fc-button-primary {
            background-color: #f3f4f6 !important;
            border-color: #e5e7eb !important;
            color: #374151 !important;
            text-transform: capitalize;
        }
        .fc-button-primary:not(:disabled):active,
        .fc-button-primary:not(:disabled).fc-button-active {
            background-color: #16a34a !important; /* Green 600 */
            border-color: #15803d !important;
            color: #ffffff !important;
        }
        .fc-button-primary:hover {
            background-color: #e5e7eb !important;
        }
    </style>
</x-layouts.admin>
