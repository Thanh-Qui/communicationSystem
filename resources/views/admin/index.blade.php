{{-- <!DOCTYPE html>
<html lang="en">
    <head>
        @include('admin.css')
    </head>
    <body>

    <div class="main-wrapper">

        @include('admin.header')

        @include('admin.sidebar')

        @include('admin.body')

    </div>

    @include('admin.footer')
    </body>
</html> --}}

<x-layout-admin>
    <div class="page-wrapper" data-url-user="{{url('admin/index')}}">
        <div class="content">
            <div class="row">
    
                <!-- Dashboard Widgets -->
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="dash-widget">
                        <div class="dash-widgetimg">
                            {{-- <span><img src="{{ asset('assets/img/icons/dash1.svg') }}" alt="img"></span> --}}
                            <span><i data-feather="message-circle"></i></span>
                        </div>
                        <div class="dash-widgetcontent">
                            <h5><span class="counters" id="count_message">{{$count_message}}</span></h5>
                            <h6>Tổng số lượng tin nhắn</h6>
                        </div>
                    </div>
                </div>
    
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="dash-widget dash1">
                        <div class="dash-widgetimg">
                            {{-- <span><img src="{{ asset('assets/img/icons/dash2.svg') }}" alt="img"></span> --}}
                            <span><i data-feather="image"></i></span>
                        </div>
                        <div class="dash-widgetcontent">
                            <h5><span class="counters" id="count_img">{{$count_img}}</span></h5>
                            <h6>Tổng số hình ảnh được gửi</h6>
                        </div>
                    </div>
                </div>
    
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="dash-widget dash2">
                        <div class="dash-widgetimg">
                            {{-- <span><img src="{{ asset('assets/img/icons/dash3.svg') }}" alt="img"></span> --}}
                            <span><i data-feather="file-text"></i></span>
                        </div>
                        <div class="dash-widgetcontent">
                            <h5><span class="counters" id="count_file">{{$count_file}}</span></h5>
                            <h6>Tổng số file được gửi</h6>
                        </div>
                    </div>
                </div>
    
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="dash-widget dash3">
                        <div class="dash-widgetimg">
                            {{-- <span><img src="{{ asset('assets/img/icons/dash4.svg') }}" alt="img"></span> --}}
                            <span><i data-feather="x-square"></i></span>
                        </div>
                        <div class="dash-widgetcontent">
                            <h5><span class="counters" id="userNotVerified">{{$userNotVerified}}</span></h5>
                            <h6>Tổng số T/K chưa xác thực</h6>
                        </div>
                    </div>
                </div>
    
                <!-- Customer and Supplier Counts -->
                <div class="col-lg-3 col-sm-6 col-12 d-flex">
                    <div class="dash-count">
                        <div class="dash-counts">
                            <h4 id="count_user" >{{$count_user}}</h4>
                            <h5>Số lượng người dùng</h5>
                        </div>
                        <div class="dash-imgs">
                            <i data-feather="user"></i>
                        </div>
                    </div>
                </div>
    
                <div class="col-lg-3 col-sm-6 col-12 d-flex">
                    <div class="dash-count das1">
                        <div class="dash-counts">
                            <h4  id="user_online">{{$user_online}}</h4>
                            <h5>Người dùng Online</h5>
                        </div>
                        <div class="dash-imgs">
                            <i data-feather="user-check"></i>
                        </div>
                    </div>
                </div>
    
                <div class="col-lg-3 col-sm-6 col-12 d-flex">
                    <div class="dash-count das2">
                        <div class="dash-counts">
                            <h4 id="user_offline">{{$user_offline}}</h4>
                            <h5>Người dùng Offline</h5>
                        </div>
                        <div class="dash-imgs">
                            <i data-feather="user-minus"></i>
                        </div>
                    </div>
                </div>
    
                <div class="col-lg-3 col-sm-6 col-12 d-flex">
                    <div class="dash-count" style="background-color: #c0392b">
                        <div class="dash-counts">
                            <h4 id="lockUser">{{$lockUser}}</h4>
                            <h5>Số tài khoản bị khóa</h5>
                        </div>
                        <div class="dash-imgs">
                            <i data-feather="user-x"></i>
                        </div>
                    </div>
                </div>
    
                <div class="col-lg-3 col-sm-6 col-12 d-flex" style="max-height: 78px">
                    <div class="dash-count  das3">
                        <div class="dash-counts">
                            <h4 id="countGroup">{{$countGroup}}</h4>
                            <h5>Tổng số nhóm đã được tạo</h5>
                        </div>
                        <div class="dash-imgs">
                            <i data-feather="users"></i>
                        </div>
                    </div>
                </div>
    
                <div class="col-lg-8">
                    <div class="chartPie">
                        <div class="header-chart">
                        <i class="fa-solid fa-chart-simple" style="margin: 5px"></i><p> Biểu đồ thể hiện trạng thái của người dùng</p>
                        </div>
                        <hr>
                        
                        <div class="body-chart">
                            <canvas id="userChart" class="userChart"></canvas>
                        </div>
                    </div>
                    
                    
                </div>
                
    
            </div>
    
            {{-- Danh sách người dùng đang online --}}
            <div class="card mb-0">
                <div class="card-body">
                    <h4 class="card-title">Danh sách người dùng đang online</h4>
                    <div class="table-responsive dataview">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>SNo</th>
                                    <th>Nick Name</th>
                                    <th>Email</th>
                                    <th>Giới tính</th>
                                    <th>Địa chỉ</th>
                                    <th>Số điện thoại</th>
                                    <th>Đăng xuất</th>
                                </tr>
                            </thead>
                            <tbody id="list_userOnline">
                                @php
                                    $sn = ($list_userOnline->currentPage() - 1) * $list_userOnline->perPage() + 1;
                                @endphp
                                @foreach ($list_userOnline as $data)
                                    <tr>
                                        <td>{{$sn++}}</td>
                                        <td>{{$data->name}}</td>
                                        <td>{{$data->email}}</td>
                                        <td>{{$data->gender}}</td>
                                        <td>{{$data->address}}</td>
                                        <td>{{$data->phone}}</td>
    
                                        <td>
                                            <a onclick="confirmationUser(event, 'Bạn có muốn mở khóa cho tài khoản này?')" href="{{ url('expulsion', $data->id) }}" class="btn btn-danger" style="color: white">
                                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                
                                
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- phân trang cho danh sách --}}
                <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 10px">
                    {{$list_userOnline->onEachSide(1)->links()}}
                </div>
            </div>
            
    
        </div>
    </div>
    
    <script src="{{ asset('assets/js/searchUser.js') }}"></script>
    
    <script>
        var userChart;
        document.addEventListener("DOMContentLoaded", function () {
            var ctx = document.getElementById('userChart').getContext('2d');
        
            // Khởi tạo biểu đồ với dữ liệu ban đầu
            userChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Tổng Tài khoản', 'Online', 'Offline', 'Tài khoản chưa xác thực', 'Tài khoản bị khóa'],
                    datasets: [{
                        data: [{{$count_user}}, {{$user_online}}, {{$user_offline}}, {{$userNotVerified}}, {{$lockUser}}],
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#f39c12', '#e74c3c'],
                        hoverBackgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#f39c12', '#e74c3c']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        datalabels: {
                            color: '#fff',  // Màu chữ
                            font: {
                                weight: 'bold',
                                size: 16
                            },
                            formatter: function(value, context) {
                                // Hiển thị giá trị chỉ khi giá trị lớn hơn 0
                                if (value === 0) {
                                    return ''; // Trả về chuỗi rỗng nếu giá trị là 0
                                }
                                return value; // Hiển thị giá trị nếu lớn hơn 0
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        
            loadListUser();
        });
    </script>
</x-layout-admin>