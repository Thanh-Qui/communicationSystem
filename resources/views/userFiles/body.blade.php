<x-layout-drive :storageData="$storageData">

  <div class="main-panel">
    <div class="content-wrapper" style="background-color: #F2F7F8">
      <div class="row">

                <div class="col-lg-12 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <h1 class="card-title" style="font-size: 24px">My drive</h1>
                    
                      {{-- View chức năng lọc dữ liệu và upload file --}}
                      <div class="d-flex align-items-center">
                        <div class="dropdown me-2">
                            <button class="btn btn-light dropdown-toggle" type="button" id="dropdownType" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                                Type 
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownType">
                                <a class="dropdown-item" href="{{ route('myStorage') }}">
                                   All
                                </a>
                                <a class="dropdown-item" href="{{ route('myStorage', ['type' => 'folders']) }}">
                                  <i class="fa-solid fa-folder me-2" style="color: #f1c40f"></i> Folders
                                </a>
                                <a class="dropdown-item" href="{{ route('myStorage', ['type' => 'documents']) }}">
                                  <i class="fa-solid fa-file-word me-2" style="color: #0652DD"></i> Documents
                                </a>
                                <a class="dropdown-item" href="{{ route('myStorage', ['type' => 'pdfs']) }}">
                                    <i class="fa-solid fa-file-pdf me-2" style="color: #EA2027"></i> PDFs
                                </a>
                                <a class="dropdown-item" href="{{ route('myStorage', ['type' => 'excel']) }}">
                                  <i class="fa-solid fa-file-excel me-2" style="color: #009432"></i> Excel
                                </a>
                                <a class="dropdown-item" href="{{ route('myStorage', ['type' => 'images']) }}">
                                    <i class="fa-solid fa-file-image me-2" style="color: #EA2027"></i> Photos & images
                                </a>
                                <a class="dropdown-item" href="{{ route('myStorage', ['type' => 'videos']) }}">
                                    <i class="fa-solid fa-file-video me-2" style="color: #EA2027"></i> Videos
                                </a>
                    
                            </div>
                        </div>
                    
                        <div class="dropdown me-2">
                            <button class="btn btn-light dropdown-toggle" type="button" id="dropdownModified" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                                Modified 
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownModified">
                              <a class="dropdown-item" href="{{ route('myStorage') }}">All</a>
                              <a class="dropdown-item" href="{{ route('myStorage', ['filter' => 'today']) }}">Today</a>
                              <a class="dropdown-item" href="{{ route('myStorage', ['filter' => 'last_7_days']) }}">Last 7 days</a>
                              <a class="dropdown-item" href="{{ route('myStorage', ['filter' => 'last_30_days']) }}">Last 30 days</a>
                              <a class="dropdown-item" href="{{ route('myStorage', ['filter' => 'last_year']) }}">Last year</a>
                            </div>
                        </div>
                        
                        {{-- Tải File lên --}}
                        <form id="uploadForm" action="{{ route('file.upload') }}" method="POST" enctype="multipart/form-data">
                          @csrf
                          <input type="file" id="fileInput" name="file" style="display: none;" onchange="submitForm()">
                          <input type="hidden" id="folderId" name="folder_id" value="">

                           <!-- Truyền URL và Token vào data-attribute -->
                           <input type="hidden" id="uploadUrl" value="{{ route('file.upload') }}">
                           <input type="hidden" id="csrfToken" value="{{ csrf_token() }}">
                      
                          <button style="padding: 13px 17px" class="btn btn-primary ms-auto" type="button" onclick="document.getElementById('fileInput').click()">
                              <span class="mobile-none">Tải lên</span> <span style="font-size: 16px">+</span>
                          </button>
                        </form>
                        
                      </div>
                    
                      {{-- Hiển thị trên PC --}}
                      <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                        <table class="table table-hover">
                          <thead class="table-header" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                              <th scope="col">Name</th>
                              <th scope="col">Owner</th>
                              <th scope="col">Last modified</th>
                              <th scope="col">File size</th>
                              <th scope="col"><span class="mdi mdi-dots-vertical" style="font-size: 16px; padding: 4px 7px;"></span></th>
                            </tr>
                          </thead>

                          {{-- Phần thêm vào để thực hiện download trên mobile --}}
                          <form id="downloadForm" method="GET" style="display: none;"></form>
                          <tbody>
                            @if (isset($items) && $items->isNotEmpty())
                                @foreach ($items as $item)
                                    <tr
                                    {{-- chỉ sử dụng được trên pc --}}
                                      {{-- @if (!isset($item->size))
                                          onclick="window.location.href='{{ route('folders.show', $item->id) }}'"
                                          style="cursor: pointer;"
                                      @else
                                          onclick="window.location.href='{{ route('files.download', $item->id) }}'"
                                          style="cursor: pointer; color: blue; text-decoration: none;"
                                      @endif --}}

                                      {{-- Phần thêm vào để thực hiện download trên mobile --}}
                                      onclick="handleItemClick({{ isset($item->size) ? 'true' : 'false' }}, '{{ route(isset($item->size) ? 'files.download' : 'folders.show', $item->id) }}')"
                                      style="cursor: pointer; {{ isset($item->size) ? 'color: blue; text-decoration: none;' : '' }}"
                                    >
                                    
                                        <td>
                                            @if(isset($item->size))
                                              @if (in_array($item->type, ['docx', 'doc', 'txt']))
                                                <i class="fa-solid fa-file-word" style="margin-right: 5px; color: blue; font-size: 16px"></i> {{ $item->name }}  <!-- Nếu là file -->
                                              @elseif ($item->type == 'xlsx')
                                                <i class="fa-solid fa-file-excel" style="margin-right: 5px; color: green; font-size: 16px"></i> {{ $item->name }}  <!-- Nếu là file -->
                                              @elseif ($item->type == 'pdf')
                                                <i class="fa-solid fa-file-pdf" style="margin-right: 5px; color: red; font-size: 16px"></i> {{ $item->name }}  <!-- Nếu là file -->
                                              @elseif (in_array($item->type, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp']))
                                                <i class="fa-solid fa-file-image" style="margin-right: 5px; color: red; font-size: 16px"></i> {{ $item->name }}  <!-- Nếu là file -->
                                              @elseif (in_array($item->type, ['mp4', 'mkv', 'avi', 'mov', 'mp3']))
                                                <i class="fa-solid fa-file-video" style="margin-right: 5px; color: red; font-size: 16px"></i> {{ $item->name }}  <!-- Nếu là file -->
                                              @elseif (in_array($item->type, ['json']))
                                                <i class="fa-solid fa-file-code" style="margin-right: 5px; color: #f1c40f; font-size: 16px"></i> {{ $item->name }}
                                              @elseif (in_array($item->type, ['pptx']))
                                                <i class="fa-solid fa-file-powerpoint" style="margin-right: 5px; color: red; font-size: 16px"></i> {{ $item->name }}
                                              @elseif (in_array($item->type, ['zip', 'rar']))
                                                <i class="fa-solid fa-file-zipper" style="margin-right: 5px; color: #8e44ad; font-size: 16px"></i> {{ $item->name }}
                                              @endif
                                            @else 
                                                📁 <strong>{{ $item->name }}</strong>  <!-- Nếu là thư mục -->
                                            @endif
                                        </td>

                                        <td>
                                          {{ $userNames[$item->user_id] ?? 'Unknown' }}
                                        </td>

                                        <td>{{ $item->updated_at }}</td>

                                        <td>
                                          @if(isset($item->size) && is_numeric($item->size)) 
                                              {{ number_format($item->size) }} KB
                                          @else
                                              --
                                          @endif
                                        </td>

                                        {{-- <td>
                                          @if (!isset($item->size))
                                              <a onclick="confirmation(event, 'Bạn có muốn xóa thông tin này!')" href="{{ route('drive.delete', ['id' => $item->id, 'size' => $item->size]) }}" class="status-delete">
                                                <span class="mdi mdi-trash-can-outline"></span>
                                              </a>
                                            
                                          @else
                                              <a onclick="confirmation(event, 'Bạn có muốn xóa thông tin này!')" href="{{ route('drive.delete', ['id' => $item->id, 'size' => $item->size]) }}" class="status-delete">
                                                <span class="mdi mdi-trash-can-outline"></span>
                                              </a>
                                            
                                          @endif
                                          
                                        </td> --}}

                                        {{-- Thực hiện chức năng rename và chuyển vào thùng rác --}}
                                        <td>
                                          <div class="dropstart text-drive-span">
                                            <a class="mdi mdi-dots-vertical dot-func"href="#"
                                            role="button" data-bs-toggle="dropdown" aria-expanded="false"
                                            onclick="event.stopPropagation()"></a>

                                            <ul class="dropdown-menu" onclick="event.stopPropagation()">

                                              {{-- Rename --}}
                                              <li class="dropdown-item cs-text">
                                                @if (!isset($item->size))
                                                    <a data-bs-toggle="modal" data-bs-target="#renameModal" href="#"
                                                      data-id="{{ $item->id }}"
                                                      data-size="{{ $item->size ?? '' }}">
                                                      <span class="mdi mdi-rename-outline" style="margin-right: 6px"></span> <span>Thay đổi tên</span>
                                                    </a>
                                                  
                                                @else
                                                    <a data-bs-toggle="modal" data-bs-target="#renameModal" href="#"
                                                      data-id="{{ $item->id }}"
                                                      data-size="{{ $item->size ?? '' }}">
                                                      <span class="mdi mdi-rename-outline" style="margin-right: 6px"></span> <span>Thay đổi tên</span>
                                                    </a>
                                                  
                                                @endif

                                              </li>

                                              {{-- Share and move --}}
                                              @if (isset($item->size))
                                                {{-- Dành cho file --}}
                                                <li class="dropdown-item cs-text">
                                                    <a href="#" class="share-btn" data-id="{{ $item->id }}">
                                                        <span class="mdi mdi-share-outline" style="margin-right: 6px"></span> <span>Chia sẻ</span>
                                                    </a>
                                                </li>

                                                <li class="dropdown-item cs-text">
                                                  <a href="#" class="moveFile-btn" data-id="{{ $item->id }}" data-size="{{ $item->size }}">
                                                      <span class="mdi mdi-file-move-outline" style="margin-right: 6px"></span> <span>Di chuyển</span>
                                                  </a>
                                                </li>

                                              @else
                                              {{-- Dành cho folder --}}
                                                <li class="dropdown-item cs-text">
                                                  <a href="#" class="move-btn" data-id="{{ $item->id }}">
                                                      <span class="mdi mdi-file-move-outline" style="margin-right: 6px"></span> <span>Di chuyển</span>
                                                  </a>
                                                </li>
                                              @endif

                                              {{-- Thêm đều kiện size để tránh gây nhầm lẫn giữa 2 bảng file và folder có cùng id với nhau --}}
                                              <li class="dropdown-item cs-text">
                                                @if (!isset($item->size))
                                                    <a onclick="confirmation(event, 'Bạn có muốn xóa thông tin này!')" href="{{ route('drive.delete', ['id' => $item->id, 'size' => $item->size]) }}">
                                                      <span class="mdi mdi-trash-can-outline" style="margin-right: 6px"></span> <span>Chuyển vào thùng rác</span>
                                                    </a>
                                                  
                                                @else
                                                    <a onclick="confirmation(event, 'Bạn có muốn xóa thông tin này!')" href="{{ route('drive.delete', ['id' => $item->id, 'size' => $item->size]) }}">
                                                      <span class="mdi mdi-trash-can-outline" style="margin-right: 6px"></span> <span>Chuyển vào thùng rác</span>
                                                    </a>
                                                @endif
                                              </li>
                                              
                                            </ul>
                                          </div>
                                        </td>

                                    </tr>  
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center">Không có dữ liệu</td>
                                </tr>
                            @endif
                                      
                          </tbody>
                        </table>
                      </div>
                      {{-- Hiển thị trên PC --}}

                      <!-- Hiển thị dưới dạng danh sách trên mobile -->
                      <div class="file-list-mobile">
                        @if (isset($items) && $items->isNotEmpty())
                          @foreach ($items as $item)
                            <div class="file-item d-flex justify-content-between align-items-start">
                              <a href="{{ isset($item->size) ? route('files.download', $item->id) : route('folders.show', $item->id) }}" class="flex-grow-1">
                                <div class="file-name d-flex align-items-center">
                                  @if(isset($item->size))
                                    @if (in_array($item->type, ['docx', 'doc', 'txt']))
                                      <i class="fa-solid fa-file-word" style="margin-right: 5px; color: blue; font-size: 16px"></i>
                                    @elseif ($item->type == 'xlsx')
                                      <i class="fa-solid fa-file-excel" style="margin-right: 5px; color: green; font-size: 16px"></i>
                                    @elseif ($item->type == 'pdf')
                                      <i class="fa-solid fa-file-pdf" style="margin-right: 5px; color: red; font-size: 16px"></i>
                                    @elseif (in_array($item->type, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp']))
                                      <i class="fa-solid fa-file-image" style="margin-right: 5px; color: red; font-size: 16px"></i>
                                    @elseif (in_array($item->type, ['mp4', 'mkv', 'avi', 'mov', 'mp3']))
                                      <i class="fa-solid fa-file-video" style="margin-right: 5px; color: red; font-size: 16px"></i>
                                    @elseif (in_array($item->type, ['json']))
                                      <i class="fa-solid fa-file-code" style="margin-right: 5px; color: #f1c40f; font-size: 16px"></i>
                                    @elseif (in_array($item->type, ['pptx']))
                                      <i class="fa-solid fa-file-powerpoint" style="margin-right: 5px; color: red; font-size: 16px"></i>
                                    @elseif (in_array($item->type, ['zip', 'rar']))
                                      <i class="fa-solid fa-file-zipper" style="margin-right: 5px; color: #8e44ad; font-size: 16px"></i>
                                    @else
                                      <i class="fa-solid fa-file"></i>
                                    @endif
                                    <span>{{ $item->name }}</span>
                                  @else
                                    <strong>📁 {{ $item->name }}</strong>
                                  @endif
                                </div>
                                <div class="file-modified small text-muted">{{ $item->updated_at }}</div>
                              </a>
                      
                              {{-- Nút xoá (dùng confirmation giống desktop) --}}
                              {{-- @if (!isset($item->size))
                                              <a onclick="confirmation(event, 'Bạn có muốn xóa thông tin này!')" href="{{ route('drive.delete', ['id' => $item->id, 'size' => $item->size]) }}" class="status-delete">
                                                <span class="mdi mdi-trash-can-outline"></span>
                                              </a>
                                            
                                          @else
                                              <a onclick="confirmation(event, 'Bạn có muốn xóa thông tin này!')" href="{{ route('drive.delete', ['id' => $item->id, 'size' => $item->size]) }}" class="status-delete">
                                                <span class="mdi mdi-trash-can-outline"></span>
                                              </a>
                                            
                              @endif --}}

                              <div class="dropstart text-drive-span">
                                  <a class="mdi mdi-dots-vertical" style="color: black; font-size: 16px" href="#"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false"
                                    onclick="event.stopPropagation()"></a>

                                  <ul class="dropdown-menu" onclick="event.stopPropagation()">

                                    {{-- Chức năng thay đổi tên file, folder --}}
                                    <li class="dropdown-item cs-text">
                                      @if (!isset($item->size))
                                          <a data-bs-toggle="modal" data-bs-target="#renameModal" href="#"
                                            data-id="{{ $item->id }}"
                                            data-size="{{ $item->size ?? '' }}">
                                                <span class="mdi mdi-rename-outline" style="margin-right: 6px"></span> <span>Thay đổi tên</span>
                                          </a>
                                                  
                                      @else
                                          <a data-bs-toggle="modal" data-bs-target="#renameModal" href="#"
                                              data-id="{{ $item->id }}"
                                              data-size="{{ $item->size ?? '' }}">
                                                <span class="mdi mdi-rename-outline" style="margin-right: 6px"></span> <span>Thay đổi tên</span>
                                          </a>
                                                  
                                      @endif

                                    </li>

                                    {{-- Chức năng chia sẻ file tới người dùng, nhóm --}}
                                    @if (isset($item->size))
                                        <li class="dropdown-item cs-text">
                                              <a href="#" class="share-btn" data-id="{{ $item->id }}">
                                                  <span class="mdi mdi-share-outline" style="margin-right: 6px"></span> <span>Chia sẻ</span>
                                              </a>
                                        </li>
                                        <li class="dropdown-item cs-text">
                                          <a href="#" class="moveFile-btn" data-id="{{ $item->id }}" data-size="{{ $item->size }}">
                                              <span class="mdi mdi-file-move-outline" style="margin-right: 6px"></span> <span>Di chuyển</span>
                                          </a>
                                        </li>
                                    @else
                                        <li class="dropdown-item cs-text">
                                          <a href="#" class="move-btn" data-id="{{ $item->id }}">
                                              <span class="mdi mdi-file-move-outline" style="margin-right: 6px"></span> <span>Di chuyển</span>
                                          </a>
                                        </li>
                                    @endif

                                    {{-- Chức năng chuyển file, folder vào thùng rác --}}
                                    <li class="dropdown-item cs-text">
                                      @if (!isset($item->size))
                                        <a onclick="confirmation(event, 'Bạn có muốn xóa thông tin này!')" href="{{ route('drive.delete', ['id' => $item->id, 'size' => $item->size]) }}">
                                            <span class="mdi mdi-trash-can-outline" style="margin-right: 6px"></span> <span>Chuyển vào thùng rác</span>
                                        </a>
                                                  
                                      @else
                                        <a onclick="confirmation(event, 'Bạn có muốn xóa thông tin này!')" href="{{ route('drive.delete', ['id' => $item->id, 'size' => $item->size]) }}">
                                          <span class="mdi mdi-trash-can-outline" style="margin-right: 6px"></span> <span>Chuyển vào thùng rác</span>
                                        </a>
                                      @endif
                                    </li>    

                                  </ul>
                              </div>
                                      
                            </div>

                          @endforeach
                        @else
                          <div class="text-center">Không có dữ liệu</div>
                        @endif
                      </div>
                      <!-- Hiển thị dưới dạng danh sách trên mobile -->
                        

                    </div>
                  </div>
                </div>
      </div>
            
    </div>

  </div>
</x-layout-drive>
        
    