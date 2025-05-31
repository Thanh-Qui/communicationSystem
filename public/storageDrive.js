// khai báo và khởi tạo
const csrfToken = $('meta[name="csrf-token"]').attr("content");


function submitForm() {
    let formData = new FormData(document.getElementById("uploadForm"));

    let folderId = document.getElementById("folderId").value;
    let uploadUrl = document.getElementById("uploadUrl").value;
    let csrfToken = document.getElementById("csrfToken").value;

    // console.log("Folder ID khi tải lên:", folderId);

    fetch(uploadUrl, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // console.log(data.file); // Kiểm tra dữ liệu file lưu trong CSDL
        location.reload(); // Reload để hiển thị file mới
    })
    .catch(error => console.error("Lỗi:", error));
}

function handleItemClick(isFile, url) {
    if (isFile) {
        const form = document.getElementById('downloadForm');
        form.action = url;
        form.submit(); // gửi form GET để trình duyệt hiểu là tải về
    } else {
        window.location.href = url; // là folder thì chuyển trang
    }
}

function openModalShareFile() {
    // Mở modal và set file_id khi nhấn nút chia sẻ
    document.querySelectorAll('.share-btn').forEach(button => {
      button.addEventListener('click', function (e) {
        e.preventDefault();
        const fileId = this.getAttribute('data-id');
        document.getElementById('shareFileId').value = fileId;
  
        const modal = new bootstrap.Modal(document.getElementById('shareFileModal'));
        modal.show();
      });
    });
  
}


// Tìm kiếm cả nhóm và người dùng (1-1)
// function searchUserDrive(inputId, suggestionsId) {
//   const userSearch = document.getElementById(inputId);
//   const userSuggestions = document.getElementById(suggestionsId);

//   if (!userSearch || !userSuggestions) {
//     console.error("Element not found:", inputId, suggestionsId);
//     return;
//   }  

//   userSearch.addEventListener('input', function () {
//     const query = userSearch.value.trim();

//     if (query.length >= 1) {
//       fetch(`/searchUserDrive?query=${encodeURIComponent(query)}`)
//         .then(response => {
//           if (!response.ok) throw new Error('Network response was not ok');
//           return response.json();
//         })
//         .then(data => {
//           userSuggestions.innerHTML = '';

//           const users = data.users || [];
//           const groups = data.groups || [];

//           if (users.length === 0 && groups.length === 0) {
//             userSuggestions.style.display = 'none';
//             return;
//           }

//           // Hiển thị gợi ý người dùng
//           users.forEach(user => {
//             const item = document.createElement('a');
//             item.classList.add('list-group-item', 'list-group-item-action');
//             item.href = '#';
//             item.innerText = `${user.name} (${user.email})`;

//             item.addEventListener('click', function () {
//               userSearch.value = `${user.name} (${user.email})`;
//               const hiddenInput = document.getElementById('selectedListId');
//               if (hiddenInput) {
//                 hiddenInput.value = user.id;
//                 hiddenInput.dataset.type = 'user'; // Lưu loại là 'user'
//               }
//               document.getElementById('listType').value = 'user';
//               userSuggestions.innerHTML = '';
//             });

//             userSuggestions.appendChild(item);
//           });

//           // Hiển thị gợi ý nhóm
//           groups.forEach(group => {
//             const item = document.createElement('a');
//             item.classList.add('list-group-item', 'list-group-item-action');
//             item.href = '#';
//             item.innerText = `${group.name} (Nhóm)`;

//             item.addEventListener('click', function () {
//               userSearch.value = `${group.name}`;
//               const hiddenInput = document.getElementById('selectedListId');
//               if (hiddenInput) {
//                 hiddenInput.value = group.id;
//                 hiddenInput.dataset.type = 'group'; // Lưu loại là 'group'
//               }
//               document.getElementById('listType').value = 'group';
//               userSuggestions.innerHTML = '';
//             });

//             userSuggestions.appendChild(item);
//           });

//           userSuggestions.style.display = 'block';
//         })
//         .catch(error => {
//           console.error('Fetch error:', error);
//           userSuggestions.style.display = 'none';
//         });
//     } else {
//       userSuggestions.style.display = 'none';
//     }
//   });

//   document.addEventListener('click', function (e) {
//     if (!userSearch.contains(e.target) && !userSuggestions.contains(e.target)) {
//       userSuggestions.style.display = 'none';
//     }
//   });
// }

// Tìm kiếm cả nhóm và người dùng. có thể chọn nhiều
function searchUserDrive(inputId, suggestionsId) {
  const userSearch = document.getElementById(inputId);
  const userSuggestions = document.getElementById(suggestionsId);
  const selectedDisplay = document.getElementById('selectedListDisplay');
  window.selectedList = [];

  userSearch.addEventListener('input', function () {
    const query = userSearch.value.trim();

    if (query.length >= 1) {
      fetch(`/searchUserDrive?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
          userSuggestions.innerHTML = '';

          const users = data.users || [];
          const groups = data.groups || [];

          if (users.length === 0 && groups.length === 0) {
            userSuggestions.style.display = 'none';
            return;
          }

          [...users.map(u => ({...u, type: 'user'})), ...groups.map(g => ({...g, type: 'group'}))].forEach(itemData => {
            const item = document.createElement('a');
            item.classList.add('list-group-item', 'list-group-item-action');
            item.href = '#';
            item.innerText = itemData.type === 'user' ? `${itemData.name} (${itemData.email})` : `${itemData.name} (Nhóm)`;

            item.addEventListener('click', function () {
              const exists = selectedList.find(i => i.id === itemData.id && i.type === itemData.type);
              if (!exists) {
                selectedList.push({ id: itemData.id, type: itemData.type, label: item.innerText });

                const li = document.createElement('li');
                li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                li.textContent = item.innerText;

                const removeBtn = document.createElement('button');
                removeBtn.classList.add('btn', 'btn-sm', 'btn-danger');
                removeBtn.innerHTML = '&times;';
                removeBtn.onclick = () => {
                  const index = selectedList.findIndex(i => i.id === itemData.id && i.type === itemData.type);
                  if (index !== -1) {
                    selectedList.splice(index, 1);
                    li.remove();
                    updateHiddenInput(); //cập nhật lại input ẩn!
                  }
                };
                

                li.appendChild(removeBtn);
                selectedDisplay.appendChild(li);
              }

              userSearch.value = '';
              userSuggestions.innerHTML = '';
              updateHiddenInput();
            });

            userSuggestions.appendChild(item);
          });

          userSuggestions.style.display = 'block';
        });
    } else {
      userSuggestions.style.display = 'none';
    }
  });

  function updateHiddenInput() {
    const hiddenInput = document.getElementById('selectedListData');
    hiddenInput.value = JSON.stringify(selectedList);
  }
  

  document.addEventListener('click', function (e) {
    if (!userSearch.contains(e.target) && !userSuggestions.contains(e.target)) {
      userSuggestions.style.display = 'none';
    }
  });
}

// chia sẻ tệp tin
function shareFiles() {
  const shareButton = document.getElementById('shareFileButton');
  const shareUrl = shareButton?.dataset.shareUrl;

  if (!shareButton || !shareUrl) {
      console.warn('Không tìm thấy nút chia sẻ file hoặc URL!');
      return;
  }

  shareButton.addEventListener('click', function (e) {
      e.preventDefault();

      const fileId = document.getElementById('shareFileId').value;
      const selectedList = window.selectedList || [];

      if (selectedList.length === 0) {
          Swal.fire({
              icon: 'warning',
              title: 'Thông báo',
              text: 'Bạn chưa chọn người hoặc nhóm chia sẻ!',
          });
          return;
      }

      document.getElementById('selectedListData').value = JSON.stringify(selectedList);

      const formData = new FormData();
      formData.append("file_id", fileId);
      formData.append("list_data", JSON.stringify(selectedList));
      formData.append("_token", csrfToken);

      shareButton.classList.add('loading');

      $.ajax({
          url: shareUrl,
          method: "POST",
          data: formData,
          processData: false,
          contentType: false,
          success: function (response) {
              if (response.status === '200') {
                  Swal.fire({
                      icon: 'success',
                      title: 'Thành công',
                      text: response.message,
                  });

                  $('#shareFileModal').modal('hide');

                  response.results.forEach(item => {
                      if (item.channel_id) {
                          // updateContactItem(item.channel_id);
                      }
                  });

                  // sendContactItemUpdates(true);

                  document.getElementById('selectedListDisplay').innerHTML = '';
                  window.selectedList = [];
                  document.getElementById('selectedListData').value = '';
              } else {
                  Swal.fire({
                      icon: 'error',
                      title: 'Lỗi',
                      text: response.error || 'Có lỗi xảy ra!',
                  });
              }
          },
          error: function (xhr, status, error) {
              console.error("Lỗi khi chia sẻ file:", error);
              Swal.fire({
                  icon: 'error',
                  title: 'Lỗi',
                  text: 'Không thể chia sẻ tệp tin!',
              });
          },
          complete: function () {
              shareButton.classList.remove('loading');
          }
      });
  });
}


// Di chuyển thư mục
function moveFolder() {
  document.querySelectorAll(".move-btn").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      e.preventDefault();

      const folderId = this.dataset.id;
      document.getElementById('folder_id_to_move').value = folderId;

      const modal = new bootstrap.Modal(document.getElementById("moveFolderModal"));
      modal.show();
    });
  });

  // Xử lý chọn thư mục đích (bao gồm cả "Thư mục gốc")
  document.querySelectorAll(".folder-option").forEach(function (option) {
    option.addEventListener("click", function () {
      // Reset highlight
      document.querySelectorAll(".folder-option").forEach(el => el.style.background = "");
      this.style.background = "#e0f3ff";

      let selectedId = this.dataset.id;
      if (selectedId === "null") selectedId = ""; // Laravel sẽ hiểu là null

      document.getElementById("target_folder_input").value = selectedId;
    });
  });
}

// Di chuyển tệp tin
function moveFile() {
  document.querySelectorAll(".moveFile-btn").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      e.preventDefault();

      const fileId = this.dataset.id;
      document.getElementById('file_id_to_move').value = fileId;

      const modal = new bootstrap.Modal(document.getElementById("moveFileModal"));
      modal.show();
    });
  });

  // Xử lý chọn thư mục đích (bao gồm cả "Thư mục gốc")
  document.querySelectorAll(".file-option").forEach(function (option) {
    option.addEventListener("click", function () {
      // Reset highlight
      document.querySelectorAll(".file-option").forEach(el => el.style.background = "");
      this.style.background = "#e0f3ff";

      let selectedId = this.dataset.id;
      if (selectedId === "null") selectedId = ""; // Laravel sẽ hiểu là null

      document.getElementById("target_file_input").value = selectedId;
    });
  });
}

function openModalAddToDo() {
  // Mở modal và set file_id khi nhấn nút chia sẻ
  document.querySelectorAll('.btn-todo').forEach(button => {
    button.addEventListener('click', function (e) {
      e.preventDefault();

      const modal = new bootstrap.Modal(document.getElementById('addToDo'));
      modal.show();
    });
  });

}

  

document.addEventListener('DOMContentLoaded', function () {
    const renameModal = document.getElementById('renameModal');

    renameModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const size = button.getAttribute('data-size');

        // Gán dữ liệu vào input
        renameModal.querySelector('#modalItemId').value = id;
        renameModal.querySelector('#modalItemSize').value = size;

        // Cập nhật tiêu đề theo loại
        const titleEl = renameModal.querySelector('#renameModalLabel');
        if (size && size !== '') {
            titleEl.textContent = 'Thay đổi tên tệp tin';
        } else {
            titleEl.textContent = 'Thay đổi tên thư mục';
        }
    });

    openModalShareFile();
    
    searchUserDrive('userSearch', 'userSuggestions');

    moveFolder();

    moveFile();

    openModalAddToDo();

    shareFiles();
});