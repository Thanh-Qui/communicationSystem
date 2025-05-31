<x-layout-drive :storageData="$storageData">
  <div class="main-panel">
          <div class="content-wrapper">
            <div class="row">

                <div class="col-lg-12 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <h1 class="card-title" style="font-size: 24px">Trash</h1>
                    
                      <div class="d-flex align-items-center">
                        <div class="dropdown me-2">
                            <button class="btn btn-light dropdown-toggle" type="button" id="dropdownType" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                                Type 
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownType">
                                <a class="dropdown-item" href="{{ route('drive.trash') }}">
                                  All
                                </a>
                                <a class="dropdown-item" href="{{ route('drive.trash', ['type' => 'folders']) }}">
                                  <i class="fa-solid fa-folder me-2" style="color: #f1c40f"></i> Folders
                                </a>
                                <a class="dropdown-item" href="{{ route('drive.trash', ['type' => 'documents']) }}">
                                  <i class="fa-solid fa-file-word me-2" style="color: #0652DD"></i> Documents
                                </a>
                                <a class="dropdown-item" href="{{ route('drive.trash', ['type' => 'pdfs']) }}">
                                    <i class="fa-solid fa-file-pdf me-2" style="color: #EA2027"></i> PDFs
                                </a>
                                <a class="dropdown-item" href="{{ route('drive.trash', ['type' => 'excel']) }}">
                                  <i class="fa-solid fa-file-excel me-2" style="color: #009432"></i> Excel
                                </a>
                                <a class="dropdown-item" href="{{ route('drive.trash', ['type' => 'images']) }}">
                                    <i class="fa-solid fa-file-image me-2" style="color: #EA2027"></i> Photos & images
                                </a>
                                <a class="dropdown-item" href="{{ route('drive.trash', ['type' => 'videos']) }}">
                                    <i class="fa-solid fa-file-video me-2" style="color: #EA2027"></i> Videos
                                </a>
                            </div>
                        </div>
                    
                        <div class="dropdown me-2">
                            <button class="btn btn-light dropdown-toggle" type="button" id="dropdownModified" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                                Modified 
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownModified">
                                <a class="dropdown-item" href="{{ route('drive.trash') }}">All</a>
                                <a class="dropdown-item" href="{{ route('drive.trash', ['filter' => 'today']) }}">Today</a>
                                <a class="dropdown-item" href="{{ route('drive.trash', ['filter' => 'last_7_days']) }}">Last 7 days</a>
                                <a class="dropdown-item" href="{{ route('drive.trash', ['filter' => 'last_30_days']) }}">Last 30 days</a>
                                <a class="dropdown-item" href="{{ route('drive.trash', ['filter' => 'last_year']) }}">Last year</a>
                            </div>
                        </div>
                        
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
                              <th scope="col">Original location</th>
                              <th scope="col"><span class="mdi mdi-dots-vertical" style="font-size: 16px; padding: 4px 7px;"></span></th>
                            </tr>
                          </thead>
                          <tbody>
                              @if (isset($items) && $items->isNotEmpty())
                                @foreach ($items as $item)
                                    <tr
                                      {{-- @if (!isset($item->size))
                                          onclick="window.location.href='{{ route('folders.show', $item->id) }}'"
                                          style="cursor: pointer;"
                                      @else
                                          onclick="window.location.href='{{ route('files.download', $item->id) }}'"
                                          style="cursor: pointer; color: blue; text-decoration: none;"
                                      @endif --}}
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

                                        <td>
                                          @if (isset($item->parent_id))
                                              {{-- giống như việc truy cập phần tử trong mảng $folderNames[] --}}
                                              {{ $folderNames[$item->parent_id] ?? '--' }}
                                          @elseif (isset($item->folder_id))
                                              {{ $folderNames[$item->folder_id] ?? '--' }}
                                          @else
                                              --
                                          @endif
                                        </td>

                                        <td>
                                          {{-- @if (!isset($item->size))
                                              <a onclick="confirmation(event, 'Bạn có muốn khôi phục thông tin!')" href="{{ route('drive.store', ['id' => $item->id, 'size' => $item->size]) }}" class="status-delete">
                                                  <span class="mdi mdi-file-restore-outline"></span>
                                              </a>
                                            
                                          @else
                                              <a onclick="confirmation(event, 'Bạn có muốn khôi phục thông tin!')" href="{{ route('drive.store', ['id' => $item->id, 'size' => $item->size]) }}" class="status-delete">
                                                  <span class="mdi mdi-file-restore-outline"></span>
                                              </a>
                                            
                                          @endif --}}

                                          <div class="dropstart text-drive-span">
                                            <a class="mdi mdi-dots-vertical dot-func"href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"></a>
                                            <ul class="dropdown-menu">

                                              <li class="dropdown-item cs-text">
                                                @if (!isset($item->size))
                                                    <a onclick="confirmation(event, 'Bạn có muốn khôi phục thông tin!', ['Hủy', 'Khôi phục'])" href="{{ route('drive.store', ['id' => $item->id, 'size' => $item->size]) }}">
                                                        <span class="mdi mdi-file-restore-outline" style="margin-right: 6px"></span> <span>Khôi phục dữ liệu</span>
                                                    </a>
                                                  
                                                @else
                                                    <a onclick="confirmation(event, 'Bạn có muốn khôi phục thông tin!', ['Hủy', 'Khôi phục'])" href="{{ route('drive.store', ['id' => $item->id, 'size' => $item->size]) }}">
                                                        <span class="mdi mdi-file-restore-outline" style="margin-right: 6px"></span> <span>Khôi phục dữ liệu</span>
                                                    </a>
                                                  
                                                @endif
                                              </li>

                                              <li class="dropdown-item cs-text">
                                                @if (!isset($item->size))
                                                    <a onclick="confirmation(event, 'Bạn muốn xóa vĩnh viễn thông tin!')" href="{{ route('drive.deleteForever', ['id' => $item->id, 'size' => $item->size]) }}">
                                                        <span class="mdi mdi-trash-can-outline" style="margin-right: 6px"></span> <span>Xóa dữ liệu vĩnh viễn</span>
                                                    </a>
                                                  
                                                @else
                                                    <a onclick="confirmation(event, 'Bạn muốn xóa vĩnh viễn thông tin!')" href="{{ route('drive.deleteForever', ['id' => $item->id, 'size' => $item->size]) }}">
                                                        <span class="mdi mdi-trash-can-outline" style="margin-right: 6px"></span> <span>Xóa dữ liệu vĩnh viễn</span>
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
                            <a href="#" class="flex-grow-1">
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
                    
                            <div class="dropstart text-drive-span">
                              <a class="mdi mdi-dots-vertical" style="color: black; font-size: 16px" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"></a>
                              <ul class="dropdown-menu">

                                <li class="dropdown-item cs-text">
                                  @if (!isset($item->size))
                                      <a onclick="confirmation(event, 'Bạn có muốn khôi phục thông tin!', ['Hủy', 'Khôi phục'])" href="{{ route('drive.store', ['id' => $item->id, 'size' => $item->size]) }}">
                                          <span class="mdi mdi-file-restore-outline" style="margin-right: 6px"></span> <span>Khôi phục dữ liệu</span>
                                      </a>
                                    
                                  @else
                                      <a onclick="confirmation(event, 'Bạn có muốn khôi phục thông tin!', ['Hủy', 'Khôi phục'])" href="{{ route('drive.store', ['id' => $item->id, 'size' => $item->size]) }}">
                                          <span class="mdi mdi-file-restore-outline" style="margin-right: 6px"></span> <span>Khôi phục dữ liệu</span>
                                      </a>
                                    
                                  @endif
                                </li>

                                <li class="dropdown-item cs-text">
                                  @if (!isset($item->size))
                                      <a onclick="confirmation(event, 'Bạn muốn xóa vĩnh viễn thông tin!')" href="{{ route('drive.deleteForever', ['id' => $item->id, 'size' => $item->size]) }}">
                                          <span class="mdi mdi-trash-can-outline" style="margin-right: 6px"></span> <span>Xóa dữ liệu vĩnh viễn</span>
                                      </a>
                                    
                                  @else
                                      <a onclick="confirmation(event, 'Bạn muốn xóa vĩnh viễn thông tin!')" href="{{ route('drive.deleteForever', ['id' => $item->id, 'size' => $item->size]) }}">
                                          <span class="mdi mdi-trash-can-outline" style="margin-right: 6px"></span> <span>Xóa dữ liệu vĩnh viễn</span>
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