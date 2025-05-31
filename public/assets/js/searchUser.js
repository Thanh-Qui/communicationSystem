// function searchUser() {
//     $('#searchUser').on('input', function () {
//         let searchValue = $(this).val();
//         let searchUrl = $(this).data('search-user');
//         let lockAcount = $(this).data('lock-acount');
//         $.ajax({
//             url: searchUrl,
//             method: 'GET',
//             data: { searchUser: searchValue },
//             success: function (response) {
//                 if (response.user && response.user.data) {
//                     let users = response.user.data;
//                     let tbody = $('#userTableBody');
//                     tbody.empty(); // Xóa dữ liệu cũ

//                     $.each(users, function (index, data) {
//                         let gender = data.gender === 'nam' ? 'Nam' : 'Nữ';
//                         tbody.append(`
//                             <tr>
//                                 <td>${index + 1}</td>
//                                 <td>${data.name}</td>
//                                 <td>${data.email}</td>
//                                 <td>${gender}</td>
//                                 <td>${data.address}</td>
//                                 <td>${data.phone}</td>
//                                 <td>
//                                     <a class="me-3" href="editproduct.html">
//                                         <i class="fa-regular fa-pen-to-square" style="font-size: 20px"></i>
//                                     </a>
//                                     <a class="confirm-text" href="${lockAcount}/${data.id}">
//                                         <i class="fa-solid fa-lock" style="color: red; font-size: 20px"></i>
//                                     </a>
//                                 </td>
//                             </tr>
//                         `);
//                     });

//                     // Cập nhật liên kết phân trang nếu cần thiết
//                     $('#paginationLinks').html(response.user.links);
//                 } else {
//                     console.error("Lỗi, không đúng định dạng", response);

//                 }

//             }
//         });
//     });

// }

function searchUser() {
    function fetchUsers(searchValue, page = 1) {
        let searchUrl = $('#searchUser').data('search-user');
        let lockAcount = $('#searchUser').data('lock-acount');
        
        $.ajax({
            url: searchUrl,
            method: 'GET',
            data: { searchUser: searchValue, page: page },
            success: function(response) {
                if (response.user && response.user.data) {
                    let users = response.user.data;
                    let tbody = $('#userTableBody');
                    tbody.empty(); // Clear extisting data

                    // tính số trang khi phân trang
                    let sn = (response.user.current_page - 1) * response.user.per_page;
                    $.each(users, function(index, data) {
                        let gender = data.gender === 'nam' ? 'Nam' : 'Nữ';
                        tbody.append(`
                            <tr>
                                <td>${sn + index + 1}</td>
                                <td>${data.name}</td>
                                <td>${data.email}</td>
                                <td>${gender}</td>
                                <td>${data.address}</td>
                                <td>${data.phone}</d>
                                <td>
                                    <a class="confirm-text" href="${lockAcount}/${data.id}">
                                        <i class="fa-solid fa-lock" style="color: red; font-size: 20px"></i>
                                    </a>
                                </td>
                            </tr>
                        `);
                    });

                    // Update pagination links
                    $('#paginationLinks').html(response.links);
                } else {
                    console.error("Error: Unexpected format", response);
                }
            }
        });
    }

    // Trigger the search on input change
    $('#searchUser').on('input', function() {
        let searchValue = $(this).val();
        fetchUsers(searchValue);
    });

    // Event delegation for pagination links
    $(document).on('click', '#paginationLinks a', function(e) {
        e.preventDefault(); // Prevent default link behavior
        let page = $(this).attr('href').split('page=')[1];
        let searchValue = $('#searchUser').val(); // Get current search value
        fetchUsers(searchValue, page); // Fetch the selected page
    });
}

function searchGroup() {
    function fetchGroups(searchValue, page = 1) {
        let searchUrl = $('#searchGroup').data('search-group');
        
        $.ajax({
            url: searchUrl,
            method: 'GET',
            data: { searchGroup: searchValue, page: page },
            success: function(response) {
                if (response.countGroup && response.countGroup.data) {
                    let countGroups = response.countGroup.data;
                    let tbody = $('#groupTableBody');
                    tbody.empty(); // Clear extisting data

                    // tính số trang khi phân trang
                    let sn = (response.countGroup.current_page - 1) * response.countGroup.per_page;
                    $.each(countGroups, function(index, data) {
                        let avatar = data.avatar !== 'avatar.png' ? `storage/channels-avatar/${data.avatar}` : `storage/users-avatar/avatar.png`;
                        tbody.append(`
                            <tr>
                                <td>${sn + index + 1}</td>
                                <td>${data.name}</td>
                                <td>${data.member_count}</td>
                                <td>
                                    <img style="height: 50px; width: 50px" src="${avatar}" alt=""> 
                                </td>
                                <td>
                                    <a href="javascript:void(0);" onclick="showMembers('${data.id}')">
                                        <i class="fa-solid fa-eye" style="color: green; font-size: 20px"></i>
                                    </a>
                                </td>
                            </tr>
                        `);
                    });

                    // Update pagination links
                    // $('#paginationLinks').html(response.links);
                } else {
                    console.error("Error: Unexpected format", response);
                }
            }
        });
    }

    // Trigger the search on input change
    $('#searchGroup').on('input', function() {
        let searchValue = $(this).val();
        fetchGroups(searchValue);
    });

    // Event delegation for pagination links
    // $(document).on('click', '#paginationLinks a', function(e) {
    //     e.preventDefault(); // Prevent default link behavior
    //     let page = $(this).attr('href').split('page=')[1];
    //     let searchValue = $('#searchGround').val(); // Get current search value
    //     fetchGrounds(searchValue, page); // Fetch the selected page
    // });
}

function searchLock() {
    function fetchLockedAccounts(searchValue, page = 1) {
        const searchUrl = $('#searchLock').data('user-lock');
        const unLockAcount = $('#searchLock').data('user-unlock');

        $.ajax({
            url: searchUrl,
            method: 'GET',
            data: { searchLock: searchValue, page: page }, // Bao gồm số trang trong yêu cầu
            success: function (response) {
                if (response.user_ban && response.user_ban.data) {
                    let userLock = response.user_ban.data;
                    let tbody = $('#bodyUserLockAcount');
                    tbody.empty(); // Xóa dữ liệu cũ

                    // Tính chỉ số bắt đầu
                    let startIndex = (response.user_ban.current_page - 1) * response.user_ban.per_page;

                    $.each(userLock, function (index, data) {
                        let gender = data.gender === 'nam' ? 'Nam' : 'Nữ';
                        tbody.append(`
                            <tr>
                                <td>${startIndex + index + 1}</td> <!-- Cập nhật chỉ số -->
                                <td>${data.name}</td>
                                <td>${data.email}</td>
                                <td>${gender}</td>
                                <td>${data.address}</td>
                                <td>${data.phone}</td>
                                <td>
                                    <a class="confirm-text" href="${unLockAcount}/${data.id}">
                                        <i class="fa-solid fa-unlock" style="color: green; font-size: 20px"></i>
                                    </a>
                                </td>
                            </tr>
                        `);
                    });

                    // Cập nhật liên kết phân trang
                    $('#userBanLinks').html(response.links);
                } else {
                    console.error("Lỗi: Định dạng không đúng", response);
                }
            }
        });
    }

    // Kích hoạt tìm kiếm khi người dùng thay đổi đầu vào
    $('#searchLock').on('input', function () {
        const searchValue = $(this).val();
        fetchLockedAccounts(searchValue); // Fetch trang đầu tiên với giá trị tìm kiếm
    });

    // Sử dụng sự kiện phân trang
    $(document).on('click', '#userBanLinks a', function (e) {
        e.preventDefault(); // Ngăn chặn hành vi mặc định của liên kết
        let page = $(this).attr('href').split('page=')[1]; // Lấy số trang
        let searchValue = $('#searchLock').val(); // Lấy giá trị tìm kiếm hiện tại
        fetchLockedAccounts(searchValue, page); // Fetch trang đã chọn
    });
}

// function searchNotVerified() {
//     $('#searchUserNotVer').on('input', function () {
//         const searchValue = $(this).val();
//         const searchUrl = $(this).data('not-verifired');
//         const deleteNotVer = $(this).data('delete-verified');
//         $.ajax({
//             url: searchUrl,
//             method: 'GET',
//             data: { searchUserNotVer: searchValue },
//             success: function (response) {
//                 const userNotVer = response.user_notverified.data;
//                 const tbody = $('#bodyUserNotVer');
//                 tbody.empty();

//                 $.each(userNotVer, function (index, data) {
//                     let gender = data.gender === 'nam' ? 'Nam' : 'Nữ';
//                     tbody.append(`
//                         <tr>
//                             <td>${index + 1}</td>
//                             <td>${data.name}</td>
//                             <td>${data.email}</td>
//                             <td>${gender}</td>
//                             <td>${data.address}</td>
//                             <td>${data.phone}</td>
//                             <td>
//                                 <a onclick="confirmation(event)" class="confirm-text" href="${deleteNotVer}/${data.id}">
//                                     <i class="fa-solid fa-trash-can" style="color: red; font-size: 20px"></i>
//                                 </a>
//                             </td>
//                         </tr>
//                     `);
//                 });
//                 // Cập nhật liên kết phân trang nếu cần thiết
//                 $('#paginationLinks').html(response.user_notverified.links);
//             }
//         });
//     });
// }

function searchNotVerified() {
    function fetchNotVerifiedUsers(searchValue, page = 1) {
        const searchUrl = $('#searchUserNotVer').data('not-verifired');
        const deleteNotVer = $('#searchUserNotVer').data('delete-verified');
        
        $.ajax({
            url: searchUrl,
            method: 'GET',
            data: { searchUserNotVer: searchValue, page: page }, // Bao gồm số trang trong yêu cầu
            success: function (response) {
                if (response.user_notverified && response.user_notverified.data) {
                    const userNotVer = response.user_notverified.data;
                    const tbody = $('#bodyUserNotVer');
                    tbody.empty(); // Xóa dữ liệu cũ

                    $.each(userNotVer, function (index, data) {
                        let gender = data.gender === 'nam' ? 'Nam' : 'Nữ';
                        tbody.append(`
                            <tr>
                                <td>${(response.user_notverified.current_page - 1) * response.user_notverified.per_page + index + 1}</td> <!-- Chỉ số bắt đầu -->
                                <td>${data.name}</td>
                                <td>${data.email}</td>
                                <td>${gender}</td>
                                <td>${data.address}</td>
                                <td>${data.phone}</td>
                                <td>
                                    <a onclick="confirmation(event)" class="confirm-text" href="${deleteNotVer}/${data.id}">
                                        <i class="fa-solid fa-trash-can" style="color: red; font-size: 20px"></i>
                                    </a>
                                </td>
                            </tr>
                        `);
                    });

                    // Cập nhật liên kết phân trang
                    $('#userNotVefi').html(response.links);
                } else {
                    console.error("Lỗi: Định dạng không đúng", response);
                }
            }
        });
    }

    // Kích hoạt tìm kiếm khi người dùng thay đổi đầu vào
    $('#searchUserNotVer').on('input', function () {
        const searchValue = $(this).val();
        fetchNotVerifiedUsers(searchValue); // Fetch trang đầu tiên với giá trị tìm kiếm
    });

    // Sử dụng sự kiện phân trang
    $(document).on('click', '#userNotVefi a', function (e) {
        e.preventDefault(); // Ngăn chặn hành vi mặc định của liên kết
        let page = $(this).attr('href').split('page=')[1]; // Lấy số trang
        let searchValue = $('#searchUserNotVer').val(); // Lấy giá trị tìm kiếm hiện tại
        fetchNotVerifiedUsers(searchValue, page); // Fetch trang đã chọn
    });
}

function showMembers(channelId) {
    fetch(`/seenMember/${channelId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                alert(data.error); // Hiển thị lỗi nếu nhóm không tồn tại
                return;
            }

            let memberList = '';
            data.members.forEach(member => {
                memberList += `<li class="list-group-item">${member.name} <img src="/storage/users-avatar/${member.avatar}" style="width: 30px; height: 30px; float: right;"></li>`;
            });

            document.getElementById("memberModalBody").innerHTML = memberList;
            document.getElementById("memberModal").style.display = "block"; // Hiển thị modal
        })
        .catch(error => {
            console.error("Lỗi lấy thành viên:", error);
        });
}

function loadListUser() {
    const loadUrl = $(this).data('url-user');
    $.ajax({
        url: loadUrl,
        method: 'GET',
        success: function (response) {
            $('#count_user').text(response.count_user);
            $('#user_online').text(response.user_online);
            $('#user_offline').text(response.user_offline);
            $('#lockUser').text(response.lockUser);

            $('#count_message').text(response.count_message);
            $('#count_img').text(response.count_img);
            $('#count_file').text(response.count_file);
            $('#userNotVerified').text(response.userNotVerified);
            $('#countGroup').text(response.countGroup);
            

            // Xây dựng HTML cho tbody
            let html = '';
            let sn = (response.list_userOnline && response.list_userOnline.current_page ? 
                        (response.list_userOnline.current_page - 1) * response.list_userOnline.per_page + 1 : 1);

            if (response.list_userOnline && response.list_userOnline.data) {
                response.list_userOnline.data.forEach(function (data) {
                    html += '<tr>';
                    html += '<td>' + sn++ + '</td>';
                    html += '<td>' + data.name + '</td>';
                    html += '<td>' + data.email + '</td>';
                    html += '<td>' + data.gender + '</td>';
                    html += '<td>' + data.address + '</td>';
                    html += '<td>' + data.phone + '</td>';
                    // html += '<td>' + '<a href="{{ url("expulsion", $data->id) }}">' + "<i class='fa-solid fa-arrow-right-from-bracket'></id>" + '</a>' + '</td>';
                    html += '<td>' + 
                        '<a href="/expulsion/' + data.id + '" class="btn btn-danger" style="color: white">' + 
                            "<i class='fa-solid fa-arrow-right-from-bracket'></i>" + 
                        '</a>' + 
                    '</td>';
                    html += '</tr>';
                });
            } else {
                // Handle case where there are no users online
                html = '<tr><td colspan="6">No users online.</td></tr>';
            }

            $('#list_userOnline').html(html);

            // // Cập nhật dữ liệu cho biểu đồ
            // userChart.data.datasets[0].data = [
            //     response.count_user,
            //     response.user_online,
            //     response.user_offline,
            //     response.userNotVerified,
            //     response.lockUser
            // ];
            // userChart.update(); // Cập nhật biểu đồ
            if (typeof userChart !== 'undefined' && userChart !== null) {
                userChart.data.datasets[0].data = [
                    response.count_user,
                    response.user_online,
                    response.user_offline,
                    response.userNotVerified,
                    response.lockUser
                ];
                userChart.update(); // Cập nhật biểu đồ
            }
            
        },
        error: function (error) {
            console.error("Lỗi khi tải dữ liệu", error);

        }

    });
}

function openModalRestore() {
    const buttons = document.querySelectorAll('.restore_button');
    buttons.forEach(button => {
        if (!button.dataset.listenerAttached) {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                currentToId = this.dataset.toId; // Lưu to_id khi click

                const userId = button.getAttribute('data-user-id');
                const userEmail = button.getAttribute('data-user-email');
                document.getElementById('modal-user-id').value = userId;
                document.getElementById('modal-user-email').innerHTML = userEmail;

                const modal = new bootstrap.Modal(document.getElementById('restore-btn'));
                modal.show();
            });
            button.dataset.listenerAttached = "true";
        }
    });
}

setInterval(loadListUser, 8000);

$(document).ready(function () {
    searchUser();
    searchLock();
    searchNotVerified();
    searchGroup();
    openModalRestore();
});