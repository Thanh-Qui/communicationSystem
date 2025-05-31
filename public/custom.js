// function searchUsers() {
//     const username = document.getElementById('username').value;

//     // Gửi yêu cầu tìm kiếm
//     fetch(`{{url('chatify/' . $channel->id . '/search')}}`, {
//         method: 'POST',
//         headers: {
//             'X-CSRF-TOKEN': '{{ csrf_token() }}',
//             'Content-Type': 'application/json'
//         },
//         body: JSON.stringify({ username })
//     })
//     .then(response => response.json())
//     .then(data => {
//         const resultsDiv = document.getElementById('searchResults');
//         resultsDiv.innerHTML = ''; // Xóa kết quả cũ

//         // Kiểm tra nếu có người dùng
//         if (data.users.length > 0) {
//             data.users.forEach(user => {
//                 const userDiv = document.createElement('div');
//                 userDiv.textContent = user.name; // Hoặc user.email
//                 userDiv.style.cursor = 'pointer';
//                 userDiv.className = 'user-item'; // Thêm class để dễ quản lý

//                 // Thêm sự kiện click
//                 userDiv.addEventListener('click', function() {
//                     // Hiển thị thông tin người dùng đã chọn
//                     const selectedUserDiv = document.getElementById('selectedUser');
//                     selectedUserDiv.innerHTML = `Selected: ${user.name}`; // Hiển thị tên người dùng đã chọn
//                     document.getElementById('user_id').value = user.id; // Lưu id người dùng
//                     document.getElementById('addUserForm').style.display = 'block'; // Hiển thị form thêm người dùng
//                 });

//                 resultsDiv.appendChild(userDiv);
//             });
//         } else {
//             resultsDiv.innerHTML = '<p>No users found</p>'; // Thông báo không tìm thấy người dùng
//         }
//     })
//     .catch(error => console.error('Error:', error)); // Bắt lỗi
// }



// function loadPhotosByChannel(channelId) {
//     console.log(`Loading photos for channel: ${channelId}`); // Kiểm tra xem hàm có chạy không
//     const photosListDiv = document.querySelector('.photos-lists');
    
//       if (!photosListDiv) {
//           console.error('Element .photos-lists not found!');
//           return; // Dừng hàm nếu không tìm thấy phần tử
//       }

//       fetch(`/photos/${channelId}`)
//           .then(response => {
//               console.log(response); // Kiểm tra phản hồi từ server
//               if (!response.ok) {
//                   throw new Error('Network response was not ok');
//               }
//               return response.json();
//           })
//           .then(photos => {
//               console.log(photos); // In ra dữ liệu nhận được
//               photosListDiv.innerHTML = ''; // Xóa nội dung cũ nếu có

//               photos.forEach(photo => {
//                   const imgElement = document.createElement('img');
//                   imgElement.src = `/storage/attachments/${photo.new_name}`; // Sử dụng new_name cho đường dẫn
//                   imgElement.alt = photo.old_name || 'Photo'; // Sử dụng old_name làm alt nếu có
//                   imgElement.style.width = '100px';
//                   imgElement.style.margin = '5px';

//                   photosListDiv.appendChild(imgElement);
//               });
//           })
//           .catch(error => console.error('Lỗi khi tải hình ảnh:', error));
//   }

//   document.addEventListener('DOMContentLoaded', function() {
//     console.log(document.body.innerHTML); // In ra nội dung HTML
//     const toChannelId = '9874a4da-fd44-4bed-a2dd-deb64f9c9252'; // Thay bằng ID thực tế
//     const photosListDiv = document.querySelector('.photos-lists');
//     if (photosListDiv) {
//         loadPhotosByChannel(toChannelId);
//     } else {
//         console.error('Element .photos-lists not found!');
//     }
//   });



// $(document).ready(function() {
//     function searchUser() {
//     $('#searchUser').on('input', function() {
//         let searchValue = $(this).val();
//         $.ajax({
//             url: "{{ url('search_user') }}",
//             method: 'GET',
//             data: { searchUser: searchValue },
//             success: function(response) {
//                 let users = response.user.data;
//                 let tbody = $('#userTableBody');
//                 tbody.empty(); // Xóa dữ liệu cũ

//                 $.each(users, function(index, data) {
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
//                                 <a class="me-3" href="editproduct.html">
//                                     <i class="fa-regular fa-pen-to-square" style="font-size: 20px"></i>
//                                 </a>
//                                 <a class="confirm-text" href="{{url('lockAcount', ${data.id})}}">
//                                     <i class="fa-solid fa-lock" style="color: red; font-size: 20px"></i>
//                                 </a>
//                             </td>
//                         </tr>
//                     `);
//                 });

//                 // Cập nhật liên kết phân trang nếu cần thiết
//                 $('#paginationLinks').html(response.user.links);
//             }
//         });
//     });
// }
//     searchUser();
// });

    // Lấy thẻ a và div chứa input
    const searchIcon = document.getElementById('search-icon');
    const searchContainer = document.getElementById('search-container');

    // Thêm sự kiện click vào thẻ a
    searchIcon.addEventListener('click', function(event) {
        event.preventDefault(); // Ngừng hành động mặc định của thẻ a (chuyển hướng)
        
        // Kiểm tra xem div hiện tại có đang ẩn hay không và thay đổi display
        if (searchContainer.style.display === 'none' || searchContainer.style.display === '') {
            searchContainer.style.display = 'block'; // Hiển thị div
        } else {
            searchContainer.style.display = 'none'; // Ẩn div
        }
    });

    let currentIndex = -1; // Lưu vị trí hiện tại trong danh sách highlight
    const markInstance = new Mark(document.getElementById('content')); // Khởi tạo mark.js
    
    function highlightText() {
        var keyword = document.getElementById('searchKeyword').value;

        // Xóa các từ đã được highlight trước đó
        markInstance.unmark({
            done: function() {
                if (keyword) {
                    // Tìm kiếm và đánh dấu các từ khóa
                    markInstance.mark(keyword, {
                        "element": "span",
                        "className": "highlight",
                        "separateWordSearch": false,
                        "done": function() {
                            updateHighlights();
                        }
                    });
                } else {
                    document.getElementById('keywordCount').innerText = "Không tìm thấy kết quả";
                }
            }
        });
    }
        
    function updateHighlights() {
        // Tìm tất cả các phần tử được highlight
        highlightedElements = document.querySelectorAll('.highlight');
        currentIndex = -1; // Đặt lại vị trí hiện tại

        // Cập nhật bộ đếm
        const count = highlightedElements.length;
        if (count > 0) {
            document.getElementById('keywordCount').innerText = `Tìm thấy ${count} kết quả.`;
            navigateHighlight(0); // Di chuyển tới từ đầu tiên
        } else {
            document.getElementById('keywordCount').innerText = "Không tìm thấy kết quả.";
        }
    }
    
    function navigateHighlight(direction) {
        if (!highlightedElements || highlightedElements.length === 0) return;

        // Xóa class active của highlight hiện tại
        if (currentIndex >= 0) {
            highlightedElements[currentIndex].classList.remove('active-highlight');
        }

        // Cập nhật chỉ số hiện tại
        currentIndex += direction;

        // Nếu vượt giới hạn, quay lại đầu hoặc cuối
        if (currentIndex < 0) {
            currentIndex = highlightedElements.length - 1;
        } else if (currentIndex >= highlightedElements.length) {
            currentIndex = 0;
        }

        // Thêm class active và cuộn đến phần tử hiện tại
        highlightedElements[currentIndex].classList.add('active-highlight');
        highlightedElements[currentIndex].scrollIntoView({
            behavior: 'smooth',
            block: 'center',
        });
    }