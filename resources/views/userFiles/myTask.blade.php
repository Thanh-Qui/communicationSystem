<x-layout-drive :storageData="$storageData">
  <div class="main-panel">
    <div class="content-wrapper" style="background-color: #F2F7F8">
      <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">

              {{-- title --}}
              <div class="d-flex align-items-center mb-2">
                <h1 class="card-title" style="font-size: 24px">To-do List</h1>
                <button type="button" class="btn btn-primary ms-auto btn-todo">
                  <span class="mdi mdi-plus"></span> <span class="mobile-none">Thêm công việc</span>
                </button>
              </div>
              
              {{-- Các danh sách công việc cần thực hiện --}}
              <div class="row">
                <!-- Cột Chưa làm -->
                <div class="col-md-4">
                  <div class="card border mb-3">
                    <div class="card-header text-white position-relative d-flex justify-content-center align-items-center" style="background: #717171">
                      <span class="text-center">Chưa làm</span>
                    
                      <div class="dropstart position-absolute end-0 me-2">
                        <span class="mdi mdi-dots-vertical" data-bs-toggle="dropdown" aria-expanded="false" role="button"></span>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="#">Chia sẻ</a></li>
                          
                        </ul>
                      </div>
                    </div>
                    
                    <div class="card-body" style="min-height: 400px">
                      <div class="task-card">
                        <ul id="todo" class="list-group">
                          <li class="list-group-item">Task 1</li>
                          <li class="list-group-item">Task 2</li>
                          <li class="list-group-item">Task 5</li>
                          <li class="list-group-item">Task 6</li>
                
                        </ul>
                      </div>
                      
                    </div>
                  </div>
                </div>

                <!-- Cột Đang làm -->
                <div class="col-md-4">
                  <div class="card border mb-3">
                    <div class="card-header bg-primary text-white position-relative d-flex justify-content-center align-items-center">
                      <span class="text-center">Đang làm</span>
                    
                      <div class="dropstart position-absolute end-0 me-2">
                        <span class="mdi mdi-dots-vertical" data-bs-toggle="dropdown" aria-expanded="false" role="button"></span>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="#">Chia sẻ</a></li>
                          
                        </ul>
                      </div>
                    </div>
                    
                    
                    <div class="card-body" style="min-height: 400px;">
                      <div class="task-card">
                         <ul id="inprogress" class="list-group">
                            <li class="list-group-item">Task 3</li>
                          </ul>
                      </div>
                     
                    </div>
                  </div>
                </div>

                <!-- Cột Đã hoàn thành -->
                <div class="col-md-4">
                  <div class="card border mb-3">
                    <div class="card-header bg-success text-white position-relative d-flex justify-content-center align-items-center">
                      <span class="text-center">Đã hoàn thành</span>
                    
                      <div class="dropstart position-absolute end-0 me-2">
                        <span class="mdi mdi-dots-vertical" data-bs-toggle="dropdown" aria-expanded="false" role="button"></span>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="#">Chia sẻ</a></li>
                          
                        </ul>
                      </div>
                    </div>

                    <div class="card-body" style="min-height: 400px;">

                      <div class="task-card">
                        <ul id="done" class="list-group">
                          
                            <li class="list-group-item">Task 4</li>
                        </ul>
                      </div>

                    </div>
                  </div>
                </div>

              </div> <!-- End Row -->

            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
  <script>
    // Khởi tạo cho từng danh sách
    const todo = document.getElementById('todo');
    const inprogress = document.getElementById('inprogress');
    const done = document.getElementById('done');
  
    // Nhóm tất cả các danh sách
    [todo, inprogress, done].forEach(el => {
      new Sortable(el, {
        group: 'shared', // Cho phép kéo giữa các danh sách
        animation: 150, // Hiệu ứng mượt
        ghostClass: 'opacity-50', // Class cho item khi kéo
      });
    });
  </script>

</x-layout-drive>
