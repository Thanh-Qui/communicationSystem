<?php

namespace App\Http\Controllers;

use App\Models\ChChannel;
use App\Models\ChMessage;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Chatify\Facades\ChatifyMessenger as Chatify;



class DriveController extends Controller
{
    /**
     *  Trang lưu trữ tài liệu cá nhân
     */

    //  Thư mục gốc
    public function myStorage(Request $request) {
        $userId = Auth::id();
        $filter = $request->query('filter'); // Lấy giá trị filter từ URL
        $type = $request->query('type');

        // Truy vấn thư mục và file của người dùng. Chỉ đặt đk chưa lấy dữ liệu
        $folders = Folder::where('user_id', $userId)->whereNull('parent_id')->where('status', 0);
        $files = File::where('user_id', $userId)->whereNull('folder_id')->where('status', 0);

        // Xử lý lọc theo thời gian
        if ($filter) {
            $date = now();
            switch ($filter) {
                case 'today':
                    $folders->whereDate('updated_at', $date);
                    $files->whereDate('updated_at', $date);
                    break;
                case 'last_7_days':
                    $folders->where('updated_at', '>=', $date->subDays(7));
                    $files->where('updated_at', '>=', $date->subDays(7));
                    break;
                case 'last_30_days':
                    $folders->where('updated_at', '>=', $date->subDays(30));
                    $files->where('updated_at', '>=', $date->subDays(30));
                    break;
                case 'last_year':
                    $folders->whereYear('updated_at', '=', $date->subYear()->year);
                    $files->whereYear('updated_at', '=', $date->subYear()->year);
                    break;
            }
        }

        // lọc dữ liệu theo từng loại file, Nếu có dữ liệu lọc thì thỏa đk if. Ngược lại không thoải thì lấy all vào else
        // Lấy dữ liệu sau khi áp dụng bộ lọc, cộng 2 điều kiện lại với nhau
        if($type) {
            if($type === 'folders') {
                $folders = $folders->get();
                $files = collect();
            } else {
                switch($type) {
                    case 'documents':
                        // lọc theo danh sách giá trị cụ thể
                        $files->whereIn('type', ['doc', 'docx', 'txt']);
                        break;
                    case 'pdfs':
                        $files->where('type', 'pdf');
                        break;
                    case 'excel':
                        $files->where('type', 'xlsx');
                        break;
                    case 'images':
                        $files->whereIn('type', ['jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp']);
                        break;
                    case 'videos':
                        $files->whereIn('type', ['mp4', 'mkv', 'avi', 'mov', 'mp3']);
                        break;
                }
                $folders = collect();
                $files = $files->get();
            }
            
        }else {
            $folders = $folders->get();
            $files = $files->get();
        }


        // Gộp và sắp xếp theo ngày cập nhật
        $items = collect($folders)->merge($files)->sortByDesc('updated_at');
        // Lấy danh sách user
        $userNames = User::pluck('name', 'id');
        // tính tổng dung lượng kho lưu trữ
        $storageData = $this->calculateStorageUsage($userId);

        return view('userFiles.body', compact('items', 'userNames', 'storageData'));
    }


    // Hiển thị thư mục con
    public function showFolder($id, Request $request) {
        $folderId = $id; // Lấy ID của thư mục
        $userId = Auth::id();
        $type = $request->query('type');
        $filter = $request->query('filter');

        $folders = Folder::where('user_id', $userId)->where('parent_id', $id)->where('status', 0);
        $files = File::where('folder_id', $id)->where('user_id', $userId)->where('status', 0);

        if ($filter) {
            $date = now();
            switch ($filter) {
                case 'today':
                    $folders->whereDate('updated_at', $date);
                    $files->whereDate('updated_at', $date);
                    break;
                case 'last_7_days':
                    $folders->where('updated_at', '>=', $date->subDays(7));
                    $files->where('updated_at', '>=', $date->subDays(7));
                    break;
                case 'last_30_days':
                    $folders->where('updated_at', '>=', $date->subDays(30));
                    $files->where('updated_at', '>=', $date->subDays(30));
                    break;
                case 'last_year':
                    $folders->whereYear('updated_at', '=', $date->subYear()->year);
                    $files->whereYear('updated_at', '=', $date->subYear()->year);
                    break;
            }
        }

        if($type) {
            if($type === 'folders') {
                $folders = $folders->get();
                $files = collect();
            }else {
                switch($type) {
                    case 'documents':
                        // lọc theo danh sách giá trị cụ thể
                        $files->whereIn('type', ['doc', 'docx', 'txt']);
                        break;
                    case 'pdfs':
                        $files->where('type', 'pdf');
                        break;
                    case 'excel':
                        $files->where('type', 'xlsx');
                        break;
                    case 'images':
                        $files->whereIn('type', ['jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp']);
                        break;
                    case 'videos':
                        $files->whereIn('type', ['mp4', 'mkv', 'avi', 'mov', 'wmv']);
                        break;
                }
                $folders = collect();
                $files = $files->get();
            }
            
        }else {
            $folders = $folders->get();
            $files = $files->get();
        }

        $items = collect($folders)->merge($files)->sortByDesc('updated_at');
        $userNames = User::pluck('name', 'id');
        // tính tổng dung lượng kho lưu trữ
        $storageData = $this->calculateStorageUsage($userId);

        // gọi hàm truy vấn ngược
        $breadcrumbs = $this->getFolderBreadcrumb($folderId);
        
        return view('userFiles.userFiles', compact('userNames', 'folderId', 'items', 'storageData', 'breadcrumbs'));
    }


    // Hàm truy ngược cây thư mục
    private function getFolderBreadcrumb($folderId)
    {
        $breadcrumbs = [];

        // vòng lặp kiểm tra thư mục. Đưa vào mảng và đảo ngược
        // trước khi đảo ngược (id = 4)
        // [
        //     ['id' => 4, 'name' => 'E-commerce'],
        //     ['id' => 3, 'name' => 'Laravel11'],
        // ]
        // Sau khi đảo ngược
        // [
        //     ['id' => 3, 'name' => 'Laravel11'],
        //     ['id' => 4, 'name' => 'E-commerce'],
        // ]
        while ($folderId) {
            $folder = Folder::find($folderId);
            if (!$folder) break;

            $breadcrumbs[] = [
                'id' => $folder->id,
                'name' => $folder->name
            ];

            $folderId = $folder->parent_id;
        }

        return array_reverse($breadcrumbs); // Đảo ngược để từ gốc đến hiện tại
    }


    // lưu trữ tệp tin
    public function store(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:10240', // giới hạn file 10MB
                'folder_id' => 'nullable|exists:folders,id'
            ]);

            $file = $request->file('file');
            $fileSize = $file->getSize();
            $fileExtension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . $file->getClientOriginalName();
            $storagePath = 'myDrive/' . $fileName;

            // Tính giới hạn dung lượng tải
            $userId = Auth::id();
            $maxSize = 10 * 1024 * 1024; // đổi thành GB
            $currentSize = File::where('user_id', $userId)->sum('size');
            $fileSizeKB = $file->getSize() / 1024; // đổi thành KB

            if(($currentSize + $fileSizeKB) > $maxSize) {
                toastr()->closeButton()->error('Dung lượng đã đạt giới hạn');
                return response()->json(['error' => 'Dung lượng lưu trữ đã đầy, không thể tải thêm file!'], 400);
            }
        

            Storage::disk('public')->makeDirectory('myDrive');
            $file->storeAs('myDrive', $fileName, 'public');

            $savedFile = File::create([
                'user_id'   => $userId,
                'folder_id' => $request->folder_id,
                'name'      => $file->getClientOriginalName(),
                'path'      => $storagePath,
                'size'      => $fileSize / 1024, // đổi thành KB
                'type'      => $fileExtension,
            ]);
            toastr()->closeButton()->success('Tải File thành công');
            return response()->json(['message' => 'File uploaded successfully!', 'file' => $savedFile], 201);
        } catch (\Exception $e) {
            toastr()->closeButton()->error('Dung lượng File không vượt quá 10MB');
            return response()->json(['error' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    // public function download($id)
    // {
    //     $file = File::findOrFail($id);

    //     // Kiểm tra đường dẫn đúng
    //     $filePath = public_path($file->path);

    //     if (!file_exists($filePath)) {
    //         return redirect()->back()->with('error', 'File không tồn tại!');
    //     }

    //     return response()->download($filePath, $file->name, [
    //         'Content-Type' => mime_content_type($filePath),
    //         'Content-Disposition' => 'attachment; filename="' . $file->name . '"'
    //     ]);
    // }

    public function download(Request $request, $id)
    {
        // Xử lý blob và tạo file tạm thời trên server
        $file = File::find($id);  // Tìm file theo ID
        $filePath = storage_path("app/public/{$file->path}");
        
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        toastr()->closeButton()->warning('Tệp tin không tồn tại');
        return redirect()->back();
    }


    // Tạo thư mục
    public function createFolder(Request $request) {

        $userId = Auth::id();
        $folder = new Folder;
        
        $folder->name = $request->folderName;
        $folder->user_id = $userId;
        $folder->parent_id = $request->parent_id;

        $folder->save();

        toastr()->closeButton()->success('Tạo Thư mục thành công');
        return redirect()->back();
    }

    // Di chuyển vào thùng rác
    public function delete(Request $request) {
        
        $id = $request->query('id');
        $size = $request->query('size');

        if(isset($size)) {
            $fileId = File::find($id);

            if(isset($fileId)) {
                $fileId->status = 1;
                $fileId->save();
            }
        }else {
            $folderId = Folder::find($id);

            if(isset($folderId)) {
                $folderId->status = 1;
                $folderId->save();
            }
        }   

        toastr()->closeButton()->success('Chuyển vào thùng rác thành công');
        return redirect()->back();
    }

    // thay đổi tên file hoặc folder
    public function renameDrive(Request $request) {
        $id = $request->item_id;
        $size = $request->item_size;

        $request->validate([
            'new_name' => ['required', 'max:255']
        ]);

        if(isset($size)){
            $file = File::find($id);
            $file->name = $request->new_name;
            $file->save();
        }else {
            $folder = Folder::find($id);
            $folder->name = $request->new_name;
            $folder->save();
        }

        toastr()->closeButton()->success('Thay đổi tên thành công');
        return redirect()->back();
    }

    // Thùng rác chứa tệp tin
    public function trashDrive(Request $request) {
        $userId = Auth::id();
        $filter = $request->query('filter');
        $type = $request->query('type');

        $folders = Folder::where('user_id', $userId)->where('status', 1);
        $files = File::where('user_id', $userId)->where('status', 1);

        if($filter) {
            $date = now();
            switch($filter) {
                case 'today':
                    $folders->whereDate('updated_at', $date);
                    $files->whereDate('updated_at', $date);
                    break;
                case 'last_7_days':
                    $folders->where('updated_at', '>=', $date->subDays(7));
                    $files->where('updated_at', '>=', $date->subDays(7));
                    break;
                case 'last_30_days':
                    $folders->where('updated_at', '>=', $date->subDays(30));
                    $files->where('updated_at', '>=', $date->subDays(30));
                    break;
                case 'last_year':
                    $folders->whereYear('updated_at', '=', $date->subYear()->year);
                    $files->whereYear('updated_at', '=', $date->subYear()->year);
                    break;
            }
        }

        if($type) {
            if($type === 'folders') {
                $folders = $folders->get();
                $files = collect();
            }else {
                switch($type) {
                    case 'documents':
                        // lọc theo danh sách giá trị cụ thể
                        $files->whereIn('type', ['doc', 'docx', 'txt']);
                        break;
                    case 'pdfs':
                        $files->where('type', 'pdf');
                        break;
                    case 'excel':
                        $files->where('type', 'xlsx');
                        break;
                    case 'images':
                        $files->whereIn('type', ['jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp']);
                        break;
                    case 'videos':
                        $files->whereIn('type', ['mp4', 'mkv', 'avi', 'mov', 'wmv']);
                        break;
                }
                $folders = collect();
                $files = $files->get();
            }
            
        }else {
            $folders = $folders->get();
            $files = $files->get();
        }

        
        // Gộp thư mục & file vào 1 danh sách, đồng thời sắp xếp theo `updated_at`
        $items = collect($folders)->merge($files)->sortByDesc('updated_at');
        
        // Lấy danh sách thư mục kèm theo ID => Name để tra cứu nhanh
        $folderNames =  Folder::pluck('name', 'id'); // ['id' => 'name']

        // Lấy danh sách user để hiển thị chủ sở hữu
        $userNames = User::pluck('name', 'id');
        // tính tổng dung lượng kho lưu trữ
        $storageData = $this->calculateStorageUsage($userId);
        return view('userFiles.trashDrive', compact('items', 'userNames', 'folderNames', 'storageData'));
    }

    // Khôi phục file trong thùng rác
    public function restoreDrive(Request $request) {
        $id = $request->query('id');
        $size = $request->query('size');

        if(isset($size)) {
            // khôi phục file
            $file = File::find($id);
            
            if(isset($file)) {
                $file->status = 0;
                $file->save();
            }
        } else {
            // khôi phục folder
            $folder = Folder::find($id);
            
            if(isset($folder)) {
                $folder->status = 0;
                $folder->save();
            }
        }

        toastr()->closeButton()->success('Khôi phục tài liệu thành công');
        return redirect()->back();
    }

    // Tìm kiếm nhanh thông tin
    public function searchDrive(Request $request) {
        $value = $request->search;
        $userId = Auth::id();

        // Truy vấn thư mục và file của người dùng. Chỉ đặt đk chưa lấy dữ liệu
        $folders = Folder::where('user_id', $userId)->whereLike('name', '%'.$value.'%')->where('status', 0)->get();
        $files = File::where('user_id', $userId)->whereLike('name', '%'.$value.'%')->where('status', 0)->get();

        $items = collect($folders)->merge($files)->sortByDesc('updated_at');
        // tính tổng dung lượng kho lưu trữ
        $storageData = $this->calculateStorageUsage($userId);
        // Lấy danh sách user
        $userNames = User::pluck('name', 'id');

        return view('userFiles.body', compact('items', 'storageData', 'userNames'));
    }

    // Xóa vĩnh viện thông tin
    public function deleteForeverDrive(Request $request) {
        $id = $request->query('id');
        $size = $request->query('size');

        if(isset($size)) {
            $file = File::find($id);
            if(isset($file)){
                Storage::disk('public')->delete($file->path);
                $file->delete();
            }
            
        }else {
            $folder = Folder::find($id);
            $folder->delete();
        }
            
        toastr()->closeButton()->success('Xóa tài liệu thành công');
        return redirect()->back();
    }


    // Tìm kiếm người dùng để share file (1-1)
    // public function searchUserDrive(Request $request)
    // {
    //     try {
    //         $userId = Auth::id();
    //         $query = $request->input('query');
    
    //         // Kiểm tra nếu không có query
    //         if (empty($query)) {
    //             return response()->json(['error' => 'Query parameter is required'], 400);
    //         }
    
    //         $searchUserDrive = User::where('name', 'like', "%{$query}%")
    //                                 ->whereNot('user_type', 'admin')
    //                                 ->whereNot('id', $userId)
    //                                 ->whereNot('email_verified_at', null)
    //                                 ->whereNot('active_status', 2)
    //                                 ->get();
    
    //         return response()->json($searchUserDrive);
    //     } catch (\Exception $e) {
    //         // Bắt lỗi và trả về thông báo lỗi
    //         return response()->json(['error' => 'Something went wrong'], 500);
    //     }
    // }

    // Tìm kiếm người dùng để share file (cả nhóm và 1-1)
    public function searchUserDrive(Request $request)
    {
        try {
            $userId = Auth::id();
            $query = $request->input('query');

            if (empty($query)) {
                return response()->json(['error' => 'Query parameter is required'], 400);
            }

            // Tìm người dùng
            $users = User::where('name', 'like', "%{$query}%")
                        ->whereNot('user_type', 'admin')
                        ->where('id', '!=', $userId)
                        ->whereNotNull('email_verified_at')
                        ->where('active_status', '!=', 2)
                        ->get(['id', 'name', 'email']); // Giới hạn field cần thiết

            // Tìm nhóm (channel group)
            $groups = ChChannel::whereNotNull('name') // Chỉ nhóm mới có name
                            ->whereNotNull('owner_id') // Chỉ nhóm mới có owner
                            ->where('name', 'like', "%{$query}%")
                            ->whereHas('users', function ($q) use ($userId) {
                                $q->where('user_id', $userId); // Phải là thành viên
                            })
                            ->get(['id', 'name']); // Chỉ trả về id và name

            // Kết hợp kết quả
            return response()->json([
                'users' => $users,
                'groups' => $groups
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    
    // Chia sẻ tệp tin trong drive (cả nhóm và 1-1)
    // public function shareFiles(Request $request)
    // {
    //     // Lấy thông tin từ request
    //     $file_id = $request->file_id;
    //     $list_id = $request->list_id; // Có thể là user_id hoặc channel_id
    //     $list_type = $request->list_type; // 'user' hoặc 'group'
    //     $sender_id = Auth::id();
    //     $channel = null;
    
    //     // Kiểm tra xem list_id và list_type có hợp lệ không
    //     if (!$list_id || !$list_type) {
    //         toastr()->closeButton()->warning('Không tìm thấy người nhận hoặc nhóm');
    //         return redirect()->back();
    //     }
    
    //     // Nếu chia sẻ đến user
    //     if ($list_type === 'user') {
    //         // Kiểm tra xem người nhận có tồn tại không
    //         $receiver = User::find($list_id);
    
    //         if (!$receiver) {
    //             toastr()->closeButton()->warning('Người dùng không tồn tại');
    //             return redirect()->back();
    //         }
    
    //         // Tìm channel 1-1 giữa sender và receiver
    //         $channel = ChChannel::whereNull('name')
    //             ->whereNull('owner_id')
    //             ->whereHas('users', fn($q) => $q->where('user_id', $sender_id))
    //             ->whereHas('users', fn($q) => $q->where('user_id', $list_id))
    //             ->withCount('users')
    //             ->having('users_count', 2)
    //             ->first();
    
    //         // Nếu chưa có channel 1-1 thì tạo mới
    //         if (!$channel) {
    //             $channel = new ChChannel();
    //             $channel->name = null;
    //             $channel->owner_id = null;
    //             $channel->save();
    
    //             $channel->users()->attach([
    //                 $sender_id => ['can_add_users' => false],
    //                 $list_id => ['can_add_users' => false],
    //             ]);
    //         }
    //     }
    
    //     // Nếu chia sẻ đến group
    //     if ($list_type === 'group') {
    //         // Kiểm tra xem nhóm có tồn tại không
    //         $channel = ChChannel::where('id', $list_id)
    //             ->whereNotNull('name')
    //             ->whereNotNull('owner_id')
    //             ->first();
    
    //         if (!$channel) {
    //             toastr()->closeButton()->warning('Nhóm không tồn tại');
    //             return redirect()->back();
    //         }
    //     }
    
    //     // Kiểm tra tồn tại file
    //     $file = File::findOrFail($file_id);
    //     $relativePath = $file->path;
    //     $oldName = basename($relativePath);
    //     $oldPath = public_path('storage/' . $relativePath);
    
    //     $extension = pathinfo($oldName, PATHINFO_EXTENSION);
    //     $newName = Str::uuid() . '.' . $extension;
    //     $newPath = public_path('storage/attachments/' . $newName);
    
    //     if (!file_exists($oldPath)) {
    //         toastr()->error('Không tìm thấy file gốc để chia sẻ');
    //         return redirect()->back();
    //     }
    
    //     // Copy file từ myDrive sang thư mục attachments
    //     copy($oldPath, $newPath);
    
    //     // Lưu vào bảng ch_messages
    //     $shareFile = new ChMessage();
    //     $shareFile->from_id = $sender_id;
    //     $shareFile->to_channel_id = $channel->id;
    //     $shareFile->attachment = json_encode([
    //         'new_name' => $newName,
    //         'old_name' => $oldName
    //     ]);
    //     $shareFile->save();
    
    //     toastr()->closeButton()->success('Chia sẻ tài liệu thành công');
    //     return redirect()->back();
    // }

    // public function shareFiles(Request $request)
    // {
    //     $file_id = $request->file_id;
    //     $sender_id = Auth::id();

    //     $listData = json_decode($request->input('list_data'), true);
    //     if (empty($listData)) {
    //         toastr()->warning('Bạn chưa chọn người hoặc nhóm chia sẻ');
    //         return redirect()->back();
    //     }

    //     $file = File::findOrFail($file_id);
    //     $oldPath = public_path('storage/' . $file->path);
    //     $oldName = basename($file->path);

    //     if (!file_exists($oldPath)) {
    //         toastr()->error('Không tìm thấy file gốc để chia sẻ');
    //         return redirect()->back();
    //     }

    //     foreach ($listData as $target) {
    //         $list_id = $target['id'];
    //         $list_type = $target['type'];
    //         $channel = null;

    //         if ($list_type === 'user') {
    //             $receiver = User::find($list_id);
    //             if (!$receiver) continue;

    //             $channel = ChChannel::whereNull('name')
    //                 ->whereNull('owner_id')
    //                 ->whereHas('users', fn($q) => $q->where('user_id', $sender_id))
    //                 ->whereHas('users', fn($q) => $q->where('user_id', $list_id))
    //                 ->withCount('users')->having('users_count', 2)
    //                 ->first();
                
    //             // Kiểm tra phòng 1-1. nếu bị block thì không gửi được
    //             $channelId = $channel->id;
    //             $isBlocked = DB::table('channel_blocks')
    //                 ->where('channel_id', $channelId)
    //                 ->exists();
    //             if ($isBlocked) {
    //                 toastr()->warning('Đã bị chặn không thể chia sẻ tệp tin');
    //                 return redirect()->back();
    //             }

    //             // Tạo channel mới nếu người chat chưa nhắn tin
    //             if (!$channel) {
    //                 $channel = new ChChannel();
    //                 $channel->save();

    //                 $channel->users()->attach([
    //                     $sender_id => ['can_add_users' => false],
    //                     $list_id => ['can_add_users' => false],
    //                 ]);
    //             }
    //         }

    //         if ($list_type === 'group') {
    //             $channel = ChChannel::where('id', $list_id)
    //                 ->whereNotNull('name')
    //                 ->whereNotNull('owner_id')
    //                 ->first();

    //             if (!$channel) continue;
    //         }

    //         $extension = pathinfo($oldName, PATHINFO_EXTENSION);
    //         $newName = Str::uuid() . '.' . $extension;
    //         $newPath = public_path('storage/attachments/' . $newName);
    //         copy($oldPath, $newPath);

    //         $shareFile = new ChMessage();
    //         $shareFile->from_id = $sender_id;
    //         $shareFile->to_channel_id = $channel->id;
    //         $shareFile->attachment = json_encode([
    //             'new_name' => $newName,
    //             'old_name' => $oldName
    //         ]);
    //         $shareFile->save();
    //     }

    //     toastr()->success('Chia sẻ tài liệu thành công');
    //     return redirect()->back();
    // }

    public function shareFiles(Request $request)
    {
        $file_id = $request->file_id;
        $sender_id = Auth::id();

        $listData = json_decode($request->input('list_data'), true);
        if (empty($listData)) {
            return response()->json([
                'status' => '400',
                'error' => 'Bạn chưa chọn người hoặc nhóm chia sẻ',
            ]);
        }

        $file = File::findOrFail($file_id);
        $oldPath = public_path('storage/' . $file->path);
        $oldName = basename($file->path);

        if (!file_exists($oldPath)) {
            return response()->json([
                'status' => '404',
                'error' => 'Không tìm thấy file gốc để chia sẻ',
            ]);
        }

        $results = [];

        foreach ($listData as $target) {
            $list_id = $target['id'];
            $list_type = $target['type'];
            $channel = null;

            if ($list_type === 'user') {
                $receiver = User::find($list_id);
                if (!$receiver) continue;

                $channel = ChChannel::whereNull('name')
                    ->whereNull('owner_id')
                    ->whereHas('users', fn($q) => $q->where('user_id', $sender_id))
                    ->whereHas('users', fn($q) => $q->where('user_id', $list_id))
                    ->withCount('users')->having('users_count', 2)
                    ->first();

                if ($channel) {
                    $isBlocked = DB::table('channel_blocks')
                        ->where('channel_id', $channel->id)
                        ->exists();
                    if ($isBlocked) {
                        continue;
                    }
                }

                if (!$channel) {
                    $channel = new ChChannel();
                    $channel->save();

                    $channel->users()->attach([
                        $sender_id => ['can_add_users' => false],
                        $list_id => ['can_add_users' => false],
                    ]);
                }
            }

            if ($list_type === 'group') {
                $channel = ChChannel::where('id', $list_id)
                    ->whereNotNull('name')
                    ->whereNotNull('owner_id')
                    ->first();

                if (!$channel) continue;
            }

            // Copy file vào thư mục mới
            $extension = pathinfo($oldName, PATHINFO_EXTENSION);
            $newName = Str::uuid() . '.' . $extension;
            $newPath = public_path('storage/attachments/' . $newName);
            copy($oldPath, $newPath);

            // Tạo tin nhắn mới
            $newMessage = new ChMessage();
            $newMessage->from_id = $sender_id;
            $newMessage->to_channel_id = $channel->id;
            $newMessage->attachment = json_encode([
                'new_name' => $newName,
                'old_name' => $oldName
            ]);
            $newMessage->save();

            // Thêm thông tin người gửi
            $newMessage->user_avatar = Auth::user()->avatar;
            $newMessage->user_name = Auth::user()->name;
            $newMessage->user_email = Auth::user()->email;

            // Xử lý định dạng tin nhắn (gửi về frontend)
            $messageData = Chatify::parseMessage($newMessage, null, true);

            // Đẩy real-time qua event
            Chatify::push("private-chatify." . $channel->id, 'sharing', [
                'from_id' => $sender_id,
                'to_channel_id' => $channel->id,
                'message' => Chatify::messageCard($messageData, true),
            ]);

            $results[] = [
                'channel_id' => $channel->id,
                'message_id' => $newMessage->id,
            ];
        }

        return response()->json([
            'status' => '200',
            'message' => 'Tệp tin đã được chia sẻ thành công!',
            'results' => $results
        ]);
    }


    // Di chuyển thư mục
    public function move(Request $request)
    {
        
        // thực hiện di chuyển folder
        $folder = Folder::findOrFail($request->folder_id);
        // điều kiện không di chuyển vào chính nó
        if ((string)$folder->id === (string)$request->target_folder) {
            toastr()->warning('Không thể di chuyển vào cùng thư mục.');
            return redirect()->back();
        }

        // điều kiện không di chuyển vào thư mục con của nó
        if ($this->isDescendant($folder->id, $request->target_folder)) {
            toastr()->warning('Không thể di chuyển vào thư mục con của chính nó.');
            return redirect()->back();
        }

        $folder->parent_id = $request->target_folder ?: null;
        $folder->save();

        toastr()->success('Di chuyển thư mục thành công');
        return redirect()->back();
    }

    // di chuyển tệp tins
    public function moveFile(Request $request) {
        $file = File::findOrFail($request->file_id);
        $file->folder_id = $request->target_file;

        // dd($file);
        // exit();

        $file->save();
        toastr()->success('Di chuyển tệp tin thành công');
        return redirect()->back();
    }

    public function toDoList() {
        $userId = Auth::id();

        // tính tổng dung lượng kho lưu trữ
        $storageData = $this->calculateStorageUsage($userId);

        return view('userFiles.myTask', ['storageData' => $storageData]);
    }

    
    // Hàm kiểm tra thư mục con
    private function isDescendant($parentId, $targetId)
    {
        if (!$targetId) return false;
    
        $targetId = (int) $targetId;
        $parentId = (int) $parentId;
    
        $target = Folder::find($targetId);
    
        while ($target) {
            if ((int)$target->id === $parentId) {
                return true;
            }
            $target = $target->parent;
        }
    
        return false;
    }

    // tổng dung lượng lưu trữ (dùng chung)
    private function calculateStorageUsage($userId) {
        $totalSizeKB = File::where('user_id', $userId)->sum('size'); // Lấy tổng dung lượng (KB)
        
        // Giới hạn dung lượng (10GB = 10 * 1024 * 1024 KB)
        $maxSizeKB = 10 * 1024 * 1024;

        // Tính phần trăm đã sử dụng
        $usedPercentage = round(($totalSizeKB / $maxSizeKB) * 100, 2);
        $usedPercentage = min($usedPercentage, 100); // Không vượt quá 100%

        // Chuyển đổi sang GB, MB, KB
        $totalSizeGB = round($totalSizeKB / 1048576, 2);
        $totalSizeMB = round($totalSizeKB / 1024, 2);
        $totalSizeKB = round($totalSizeKB, 2);

        if ($totalSizeGB >= 1) {
            $displaySize = $totalSizeGB . ' GB';
        } elseif ($totalSizeMB >= 1) {
            $displaySize = $totalSizeMB . ' MB';
        } else {
            $displaySize = $totalSizeKB . ' KB';
        }

        return [
            'displaySize' => $displaySize,
            'usedPercentage' => $usedPercentage
        ];
    }
}
