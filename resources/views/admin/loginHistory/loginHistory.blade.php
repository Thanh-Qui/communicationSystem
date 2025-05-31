<x-layout-admin>
    <div class="page-wrapper">
        
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Quản lý lịch sử đăng nhập </h4>
                    <h6>Lịch sử đăng nhập</h6>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-top">
                        <div class="search-set">
                            <div class="search-input">
                                <a class="btn btn-searchset">
                                    <img src="{{ asset('assets/img/icons/search-white.svg') }} " alt="img">
                                </a>
                                {{-- Form tìm kiếm người dùng --}}
                                <form action="{{ url('loginHistory') }}">
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                        <label>
                                            <input type="search" name="searchLoginHistory" value="{{request('searchLoginHistory')}}" class="form-control form-control-sm" placeholder="Search..." aria-controls="DataTables_Table_0">
                                        </label>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <div  class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <table class="table datanew no-footer" role="grid">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_asc" tabindex="0" rowspan="1" colspan="1" aria-sort="ascending" >
                                            SL
                                        </th>
                                        <th class="sorting" >Người đăng nhập</th>
                                        <th class="sorting" >Tên tài khoản</th>
                                        <th class="sorting">IP Address</th>
                                        <th class="sorting" >Thời gian đăng nhập gần nhất</th>

                                        <th class="sorting" >Action</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    @php
                                       $sn = ($login_histories->currentPage() - 1) * $login_histories->perPage() + 1;
                                    @endphp

                                    @if (isset($login_histories))
                                        @foreach ($login_histories as $login_history)
                                            <tr>
                                                <td>{{$sn++}}</td>
                                                @foreach ($user_login as $user)
                                                @if ($login_history->user_id == $user->id)
                                                    <td>{{$user->name}}</td>
                                                    <td>{{$user->email}}</td>
                                                @endif
                                                    
                                                @endforeach
                                                                                            
                                                <td>{{$login_history->ip_address}}</td>
                                                <td>{{$login_history->created_at}}</td>
                                                
                                                <td>
                                                    <a onclick="confirmation(event)" href="{{ url('deleteLoginHistory', $login_history->id) }}"><i style="color: red" class="fa-solid fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    
                                </tbody>
                                
                            </table>
                        </div>
                    </div>
                    {{-- phân trang --}}
                    <div style="display: flex; justify-content: center; align-items: center; margin-top: 10px">
                                @if (isset($login_histories))
                                    {!!$login_histories->appends(['searchLoginHistory' => request('searchLoginHistory')])->onEachSide(1)->links()!!}
                    @endif
                    </div>

                </div>
            </div>
        </div>

    </div>
</x-layout-admin>