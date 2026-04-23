<x-front-layout>
    <div class="row">
        <div class="col-md-12 order-0 order-md-1">
            <!-- Activity Timeline -->
            <div class="card mb-6">
                <h5 class="card-header">عمليات المستخدمين على النظام</h5>
                <div class="card-body pt-1">
                    <ul class="timeline mb-0">
                        @foreach ($logs as $log)
                            @php
                                $color = 'primary';
                                $type = '';
                                if ($log->event_type == 'Created') {
                                    $color = 'success';
                                    $type = 'اضافة';
                                } elseif ($log->event_type == 'Updated') {
                                    $color = 'info';
                                    $type = 'تعديل';
                                } elseif ($log->event_type == 'Deleted') {
                                    $color = 'danger';
                                    $type = 'حذف';
                                } elseif ($log->event_type == 'Login') {
                                    $color = 'success';
                                    $type = 'تسجيل دخول';
                                } elseif ($log->event_type == 'Access Denied') {
                                    $color = 'warning';
                                    $type = 'التحقق من صلاحيات الوصول';
                                }
                            @endphp
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-{{ $color }}"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-3">
                                        <h6 class="mb-0 d-flex align-items-center">
                                            <a class="nav-link view_log" href="#" type="button" data-id="{{ $log->id }}">
                                                <i class="ti ti-eye me-1"></i>
                                                {{ $log->message }}
                                            </a>
                                        </h6>
                                        <div class="badge bg-label-{{ $color }} rounded d-flex align-items-center">
                                            <span class="h6 mb-0 text-body">{{ $type }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="badge bg-label-{{ $color }} rounded d-flex align-items-center">
                                            <a href="{{ route('dashboard.users.show', $log->user_id) }}" class="h6 mb-0 text-body">{{ $log->user ? $log->user->name : 'ضيف' }}</a>
                                        </div>
                                        <div class="badge bg-lighter rounded d-flex align-items-center">
                                            <span
                                                class="h6 mb-0 text-body">{{ Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</span>
                                        </div>
                                        <p class="mb-2">Ip : {{ $log->ip_request }}</p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div>
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalViewLog" tabindex="-1" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="timeline-event">
                        <div class="timeline-header mb-3">
                            <div class="badge bg-label-success rounded d-flex align-items-center" id="type_log_view">
                                <span class="h6 mb-0 text-body" id="message_log_view"></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="badge bg-lighter rounded d-flex align-items-center">
                                <span class="h6 mb-0 text-body" id="date_log_view"></span>
                            </div>
                            <div class="badge bg-lighter rounded d-flex align-items-center">
                                <span class="h6 mb-0 text-body mx-2" id="text_log_view"></span>
                            </div>
                        </div>
                    </div>
                    <div id="created_log" class="d-none">
                        <h5 class="pb-4 border-bottom mb-4">البيانات</h5>
                        <div class="info-container">
                            <ul class="list-unstyled mb-6" style="text-align: left;" id="data_log_view">
                                
                            </ul>
                        </div>
                    </div>
                    <div id="updated_log" class="d-none">
                        <h5 class="pb-4 border-bottom mb-4">البيانات الأساسية</h5>
                        <div class="info-container">
                            <ul class="list-unstyled mb-6" style="text-align: left;" id="data_last_log_view">
                            
                            </ul>
                        </div>
                        <hr />
                        <h5 class="pb-4 border-bottom mb-4">البيانات المحدثة</h5>
                        <div class="info-container">
                            <ul class="list-unstyled mb-6" style="text-align: left;" id="data_updated_log_view">
                                
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <div class="info-container">
                        <ul class="list-unstyled mb-6">
                            <li class="mb-2">
                                <span class="h6">اسم المستخدم :</span>
                                <span id="username_log_view"></span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">وقت العملية :</span>
                                <span id="time_log_view"></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('assets/js/modal-edit-user.js') }}"></script>
        <script src="{{ asset('assets/js/app-user-view.js') }}"></script>
        <script src="{{ asset('assets/js/app-user-view-account.js') }}"></script>
        <script src="{{ asset('assets/js/pages-profile.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('.view_log').on('click', function() {
                    let id = $(this).data('id');
                    $.ajax({
                        url: "{{ route('dashboard.logs.getLogs') }}",
                        type: 'GET',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            $('#data_last_log_view').empty();
                            $('#data_updated_log_view').empty();
                            $('#data_log_view').empty();
                            $('#created_log').addClass('d-none');
                            $('#updated_log').addClass('d-none');
                            // $('#updated_log').empty();
                            let color = 'primary';
                            let type = '';
                            if (response.event_type == 'Created') {
                                color = 'success';
                                type = 'اضافة';
                            } else if (response.event_type == 'Updated') {
                                color = 'info';
                                type = 'تعديل';
                            } else if (response.event_type == 'Deleted') {
                                color = 'danger';
                                type = 'حذف';
                            } else if (response.event_type == 'Login') {
                                color = 'success';
                                type = 'تسجيل دخول';
                            } else if (response.event_type == 'Access Denied') {
                                color = 'warning';
                                type = 'التحقق من صلاحيات الوصول';
                            }

                            // Add the data event in the view
                            $('#message_log_view').text(response.message);
                            $('#date_log_view').text(response.created_at_diff);
                            $('#text_log_view').text(type);
                            $('#type_log_view').removeClass('bg-label-success').addClass('bg-label-' + color);

                            $('#username_log_view').text(response.user_name);
                            $('#time_log_view').text(response.created_at);

                            // Add the data event in the view
                            if(response.event_type == 'Updated'){
                                $('#updated_log').removeClass('d-none');
                                $('#data_last_log_view').empty();
                                Object.entries(response.old_data).forEach(([key, value]) => {
                                    $('#data_last_log_view').append(`
                                        <li class="mb-2">
                                            <span class="h6">${key}:</span>
                                            <span>${value}</span>
                                        </li>
                                    `);
                                });

                                $('#data_updated_log_view').empty();

                                // معالجة new_data
                                Object.entries(response.new_data).forEach(([key, value]) => {
                                    $('#data_updated_log_view').append(`
                                        <li class="mb-2">
                                            <span class="h6">${key}:</span>
                                            <span>${value}</span>
                                        </li>
                                    `);
                                });
                            }
                            if(response.event_type == 'Created'){
                                $('#created_log').removeClass('d-none');
                                $('#data_log_view').empty();
                                Object.entries(response.new_data).forEach(([key, value]) => {
                                    $('#data_log_view').append(`
                                        <li class="mb-2">
                                            <span class="h6">${key}:</span>
                                            <span>${value}</span>
                                        </li>
                                    `);
                                });
                            }

                            $('#modalViewLog').modal('show');
                            // $('#modalViewLog .modal-body').html(response);
                        }
                    });
                })
            });
        </script>
    @endpush
</x-front-layout>
