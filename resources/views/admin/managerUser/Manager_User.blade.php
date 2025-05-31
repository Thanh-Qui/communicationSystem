<x-layout-admin>
    <div class="page-wrapper" data-url-listUser = "{{ url('user_manager') }}">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Danh sách người dùng </h4>
                    <h6>Tổng danh sách người dùng</h6>
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
                                <form action="{{ url('search_user') }}" onsubmit="return false;">
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                        <label>
                                            <input type="search" name="searchUser" id="searchUser"
                                                data-search-user="{{ url('search_user') }}"
                                                data-lock-acount="{{ url('lockAcount') }}"
                                                class="form-control form-control-sm" placeholder="Search..."
                                                aria-controls="DataTables_Table_0">
                                        </label>
                                    </div>
                                </form>

                            </div>
                        </div>
                        <div class="wordset">
                            <ul>

                                <li>
                                    <a href="{{ url('user_manager') }}"><i class="fa-solid fa-rotate-left"
                                            style="font-size: 20px"></i></a>
                                </li>

                                <li>
                                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="print"
                                        aria-label="print">
                                        <img src="assets/img/icons/printer.svg" alt="img">
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <table class="table datanew no-footer" role="grid">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_asc" tabindex="0" rowspan="1" colspan="1"
                                            aria-sort="ascending">
                                            SL
                                        </th>
                                        <th class="sorting">Nick Name</th>
                                        <th class="sorting">Email</th>
                                        <th class="sorting">Giới tính</th>
                                        <th class="sorting">Địa chỉ</th>
                                        <th class="sorting">Số điện thoại</th>

                                        <th class="sorting">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="userTableBody">
                                    @php
                                        $sn = ($user->currentPage() - 1) * $user->perPage() + 1;
                                    @endphp
                                    @foreach ($user as $data)
                                        <tr class="odd">
                                            <td>{{ $sn++ }}</td>
                                            <td>{{ $data->name }}</td>
                                            <td>{{ $data->email }}</td>
                                            <td>{{ $data->gender == 'nam' ? 'Nam' : 'Nữ' }}</td>
                                            <td>{{ $data->address }}</td>
                                            <td>{{ $data->phone }}</td>

                                            <td>
                                                <button type="button" class="restore_button" title="Khôi phục mật khẩu"
                                                    data-user-id="{{ $data->id }}" data-user-email="{{$data->email}}">
                                                    <i class="fa-solid fa-repeat"
                                                        style="color: green; font-size: 20px"></i>
                                                </button>
                                                <a onclick="confirmation(event, 'Bạn có muốn khóa tài khoản này?')"
                                                    class="confirm-text p-2" href="{{ url('lockAcount', $data->id) }}"
                                                    data-user-id="{{ $data->id }}">
                                                    <i class="fa-solid fa-lock" style="color: red; font-size: 20px"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>


                        </div>
                    </div>

                    <div id="paginationLinks"
                        style="display: flex; justify-content: center; align-items: center; margin-top: 15px">
                        {{ $user->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>



            <div class="page-header">
                <div class="page-title">
                    <h6>Tổng danh sách người dùng bị khóa tài khoản</h6>
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
                                <form action="{{ url('SearchLockAcount') }}">
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                        <label>
                                            <input type="search" name="searchLock" id="searchLock"
                                                data-user-lock="{{ url('SearchLockAcount') }}"
                                                data-user-unlock="{{ url('unlockAcount') }}"
                                                class="form-control form-control-sm" placeholder="Search..."
                                                aria-controls="DataTables_Table_0">
                                        </label>
                                    </div>
                                </form>

                            </div>
                        </div>
                        <div class="wordset">
                            <ul>

                                <li>
                                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="print"
                                        aria-label="print">
                                        <img src="assets/img/icons/printer.svg" alt="img">
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <table class="table datanew no-footer" role="grid">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_asc" tabindex="0" rowspan="1" colspan="1"
                                            aria-sort="ascending">
                                            SL
                                        </th>
                                        <th class="sorting">Nick Name</th>
                                        <th class="sorting">Email</th>
                                        <th class="sorting">Giới tính</th>
                                        <th class="sorting">Địa chỉ</th>
                                        <th class="sorting">Số điện thoại</th>

                                        <th class="sorting">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyUserLockAcount">
                                    @php
                                        $sn = 1;
                                    @endphp
                                    @foreach ($user_ban as $data1)
                                        <tr>
                                            <td>{{ $sn++ }}</td>
                                            <td>{{ $data1->name }}</td>
                                            <td>{{ $data1->email }}</td>
                                            <td>{{ $data1->gender == 'nam' ? 'Nam' : 'Nữ' }}</td>
                                            <td>{{ $data1->address }}</td>
                                            <td>{{ $data1->phone }}</td>
                                            <td>
                                                <a onclick="confirmation(event, 'Bạn có muốn mở khóa cho tài khoản này?')"
                                                    class="confirm-text"
                                                    href="{{ url('unlockAcount', $data1->id) }}">
                                                    <i class="fa-solid fa-unlock"
                                                        style="color: green; font-size: 20px"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>


                        </div>
                    </div>
                    <div style="display: flex; justify-content: center; align-items: center; margin-top: 15px"
                        id="userBanLinks">
                        {{ $user_ban->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>


            <div class="page-header">
                <div class="page-title">
                    <h6>Tổng danh sách người dùng chưa xác thực tài khoản</h6>
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
                                <form action="{{ url('searchNotVerified') }}">
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                        <label>
                                            <input type="search" name="searchUserNotVer" id="searchUserNotVer"
                                                data-not-verifired="{{ url('searchNotVerified') }}"
                                                data-delete-verified="{{ url('delele_userNotVer') }}"
                                                class="form-control form-control-sm" placeholder="Search..."
                                                aria-controls="DataTables_Table_0">
                                        </label>
                                    </div>
                                </form>

                            </div>
                        </div>
                        <div class="wordset">
                            <ul>

                                <li>
                                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="print"
                                        aria-label="print">
                                        <img src="assets/img/icons/printer.svg" alt="img">
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <table class="table datanew no-footer" role="grid">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_asc" tabindex="0" rowspan="1" colspan="1"
                                            aria-sort="ascending">
                                            SL
                                        </th>
                                        <th class="sorting">Nick Name</th>
                                        <th class="sorting">Email</th>
                                        <th class="sorting">Giới tính</th>
                                        <th class="sorting">Địa chỉ</th>
                                        <th class="sorting">Số điện thoại</th>

                                        <th class="sorting">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyUserNotVer">
                                    @php
                                        $sn = 1;
                                    @endphp
                                    @foreach ($user_notverified as $data2)
                                        <tr>
                                            <td>{{ $sn++ }}</td>
                                            <td>{{ $data2->name }}</td>
                                            <td>{{ $data2->email }}</td>
                                            <td>{{ $data2->gender == 'nam' ? 'Nam' : 'Nữ' }}</td>
                                            <td>{{ $data2->address }}</td>
                                            <td>{{ $data2->phone }}</td>
                                            <td>
                                                <a onclick="confirmation(event)" class="confirm-text"
                                                    href="{{ url('delele_userNotVer', $data2->id) }}">
                                                    <i class="fa-solid fa-trash-can"
                                                        style="color: red; font-size: 20px"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>


                        </div>
                    </div>
                    <div id="userNotVefi"
                        style="display: flex; justify-content: center; align-items: center; margin-top: 15px">
                        {{ $user_notverified->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>


        </div>
    </div>


    {{-- Modal khôi phục mật khẩu --}}
    <div class="modal fade" id="restore-btn" tabindex="-1" aria-labelledby="restore-btn" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-restore">
                <form action="{{ route('admin.restore') }}" method="POST" id="pollForm">
                    @csrf

                    <div class="modal-header">
                        <h3 class="modal-title" id="restore-btn">Khôi phục mật khẩu</h3>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <span class="form-label">Email: </span>
                            <span class="form-label" id="modal-user-email"></span>
                        </div>

                        {{-- Quy tắc validation confirmed đặt confirmation: _confirmation là hậu tố. nếu trường input đặt là password thì thêm --}}
                        {{-- vào hậu tố là password_confirmation. nếu là password_new thì password_new_confirmation --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="password" name="password_new"
                                placeholder="Mật khẩu mới" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_new_confirmation" class="form-label">Nhập lại mật khẩu</label>
                            <input type="password" class="form-control" id="password_new_confirmation"
                                name="password_new_confirmation" placeholder="Nhập lại mật khẩu" required>
                        </div>

                        {{-- lưu id của người dùng --}}
                        <input type="hidden" name="user_id" id="modal-user-id">

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary ms-auto">Lưu</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</x-layout-admin>
