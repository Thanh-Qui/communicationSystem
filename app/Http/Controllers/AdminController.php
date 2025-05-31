<?php

namespace App\Http\Controllers;

use App\Models\ChChannel;
use App\Models\ChMessage;
use App\Models\User;
use App\Models\News;
use App\Models\Folder;
use App\Models\File;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Chatify\Facades\ChatifyMessenger as Chatify;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;



class AdminController extends Controller
{
    public function index(Request $request) {

        $count_user = User::where('user_type', '!=', 'admin')->count();
        $user_online = User::where('active_status', 1)->whereNot('user_type', 'LIKE', '%admin%')->whereNot('email_verified_at', null)->count();
        $user_offline = User::whereIn('active_status', [0,2])->whereNot('user_type', 'LIKE', '%admin%')->whereNotNull('email_verified_at')->count();
        $count_message = ChMessage::count();

        $imageExtensions = ['%.jpg', '%.png', '%.jpeg', '%.gif', '%.tiff', '%.tif', '%.svg', '%.webp', '%.mp4', '%.mp3'];
        $fileExtensions = ['%.zip', '%.rar', '%.txt', '%.docx', '%.xlsx', '%.pdf', '%.sql', '%.json', '%.pptx'];

        // tổng số lượng hình ảnh và video
        $count_img = ChMessage::where(function($query) use ($imageExtensions) {
            foreach ($imageExtensions as $ext) {
                $query->orWhereLike('attachment->old_name', $ext);
            }
        })->count();

        // tổng số lượng file
        $count_file = ChMessage::where(function($query) use ($fileExtensions) {
            foreach ($fileExtensions as $ext) {
                $query->orWhereLike('attachment->old_name', $ext);
            }
        })->count();

        $list_userOnline = User::where('active_status', 1)->whereNot('user_type', 'LIKE', '%admin%')->whereNotNull('email_verified_at')->paginate(8);

        $lockUser = User::where('active_status', 2)->whereNot('user_type', 'LIKE', '%admin%')->count();
        $userNotVerified = User::where('email_verified_at', null)->whereNot('user_type', 'LIKE', '%admin%')->count();

        // Tổng số nhóm đã được tạo ra
        $countGroup = ChChannel::whereNot("owner_id", null)->count();

        if($request->ajax()){
            return response()->json([
                'user_online' => $user_online,
                'count_user' => $count_user,
                'user_offline' => $user_offline,
                'count_message' => $count_message,
                'count_img' => $count_img,
                'count_file' => $count_file,
                'list_userOnline' => $list_userOnline,
                'lockUser' => $lockUser,
                'userNotVerified' => $userNotVerified,
                'countGroup' => $countGroup,
            ]);
        }

        return view('admin.index', compact('user_online', 'count_user', 'user_offline', 'count_message', 'count_img', 'count_file', 'list_userOnline', 'lockUser', 'userNotVerified', 'countGroup'));
    }


    public function user_manager(Request $request) {

        $user = User::whereNot('user_type', 'LIKE', '%admin%')->where('email_verified_at', '!=', null)->paginate(8);
        $user_ban = User::whereNot('user_type', 'LIKE', '%admin%')->where('active_status', 2)->paginate(8);
        $user_notverified = User::whereNot('user_type', 'LIKE', '%admin%')->where('email_verified_at', null)->paginate(8);

        if($request->ajax()) {
            return response()->json([
                'user' => $user,
                'user_ban' => $user_ban,
                'user_notverified' => $user_notverified,
            ]);
        }

        return view('admin.managerUser.Manager_User', compact('user', 'user_ban', 'user_notverified'));

    }

    // Chức năng quản lý nhóm người dùng
    public function group_manager() {

        $countGroup = DB::table('ch_channels')
                        ->join('ch_channel_user', 'ch_channels.id', '=', 'ch_channel_user.channel_id')
                        ->whereNot('ch_channels.owner_id', null)
                        ->select('ch_channels.id', 'ch_channels.name', 'ch_channels.avatar' , DB::raw('COUNT(ch_channel_user.channel_id) as member_count'))
                        ->groupBy('ch_channels.id', 'ch_channels.name', 'ch_channels.avatar')
                        ->paginate(8, ['*'], 'group_page');

        return view('admin.managerUser.Manager_Group', compact('countGroup'));
    }

    public function seenMember($id) {
        $channel = ChChannel::find($id);
    
        if (!$channel) {
            return response()->json(['error' => 'Nhóm không tồn tại'], 404);
        }
    
        $members = DB::table('ch_channel_user')
                    ->join('users', 'ch_channel_user.user_id', '=', 'users.id') // Lấy thêm thông tin user
                    ->where('ch_channel_user.channel_id', $id)
                    ->select('users.id', 'users.name', 'users.avatar')
                    ->get();
    
        return response()->json(['members' => $members]);
    }

    public function searchGroup(Request $request) {
        $value = $request->searchGroup;

        $countGroup = DB::table('ch_channels')
                        ->join('ch_channel_user', 'ch_channels.id', '=', 'ch_channel_user.channel_id')
                        ->whereLike('ch_channels.name', '%'.$value.'%')
                        ->whereNot('ch_channels.owner_id', null)
                        ->select('ch_channels.id', 'ch_channels.name', 'ch_channels.avatar' , DB::raw('COUNT(ch_channel_user.channel_id) as member_count'))
                        ->groupBy('ch_channels.id', 'ch_channels.name', 'ch_channels.avatar')
                        ->paginate(8, ['*'], 'group_page');


        return response()->json([
            'countGroup' => $countGroup,
        ]);

    }

    public function conversationGroup(Request $request) {
        $value = $request->searchConversation;

        $countGroup = DB::table('ch_channels')
                        ->join('ch_channel_user', 'ch_channels.id', '=', 'ch_channel_user.channel_id')
                        ->whereNot('ch_channels.owner_id', null)
                        ->select('ch_channels.id', 'ch_channels.name', 'ch_channels.avatar' , DB::raw('COUNT(ch_channel_user.channel_id) as member_count'))
                        ->groupBy('ch_channels.id', 'ch_channels.name', 'ch_channels.avatar')
                        ->paginate(8, ['*'], 'ground_page');

        if($value) {
            $messages = ChMessage::with(['fromUser', 'usersInChannel'])
                                    ->join('users', 'ch_messages.from_id', '=', 'users.id')
                                    ->join('ch_channels', 'ch_messages.to_channel_id' , '=', 'ch_channels.id')
                                    ->whereLike('ch_channels.name', '%'.$value.'%' )
                                    ->orderBy('ch_messages.created_at', 'desc')
                                    ->select('ch_messages.*', 'ch_channels.name')
                                    ->paginate(15, ['*'], 'conversationGroup_page')
                                    ->appends(['searchConversation' => $value]);
            
            
            return view('admin.managerUser.Manager_Group', compact('messages', 'value', 'countGroup'));
        }

        return view('admin.managerUser.Manager_Group', compact('messages', 'value'));
    }
    

    // trục xuất những tài khoản ra ngoài
    public function expulsion($id) {
        $userId = User::find($id);

        if(isset($userId)) {
            $userId->active_status = 0;
            $userId->save();
            
            DB::table('sessions')->where('user_id', $userId->id)->delete();
            toastr()->closeButton()->success('Đăng xuất tài khoản thành công');
            return redirect()->back();
        }

        toastr()->closeButton()->error('Đăng xuất tài khoản không thành công');
        return redirect()->back();
    }

    public function lockAcount($userId) {
        
        $user = User::find($userId);

        if (isset($user)) {   
            $user->active_status = 2;
            $user->save();

            DB::table('sessions')->where("user_id", $user->id)->delete();

            toastr()->closeButton()->success('Khóa tài khoản thành công');
            return redirect()->back();
        }

        toastr()->closeButton()->error('Khóa tài khoản không thành công');
        return redirect()->back();

    }

    public function unlockAcount($userId) {
        $user = User::find($userId);

        if (isset($user)) {
            $user->active_status = 0;
            $user->save();

            toastr()->closeButton()->success('Mở khóa tài khoản thành công');
            return redirect()->back();
        }
        
        toastr()->closeButton()->error('Mở khóa tài khoản không thành công');
        return redirect()->back();
    }

    public function delele_userNotVer($userId) {
        $user = User::find($userId);

        if(isset($user)) {
            $delete_user = $user;
            $delete_user->delete();

            toastr()->closeButton()->success('Xóa khóa tài khoản thành công');
            return redirect()->back();
        }

        // $delete_user = User::where('id', $userId)->delete();

        toastr()->closeButton()->warning('xóa khóa tài khoản không thành công');
        return redirect()->back();
    }
    
    public function search_user(Request $request){
        $value = $request->searchUser;

        $user = User::whereLike('name', '%'.$value.'%')
                    ->where('email_verified_at', '!=', null)
                    ->whereNot('user_type', 'LIKE', '%admin%')->paginate(8);
        return response()->json([
            'user' => $user,
            'links' => (string) $user->links(),
        ]);
    }   

    public function SearchLockAcount(Request $request) {
        $value = $request->searchLock;

        $user_ban = User::whereLike('name', '%'.$value.'%')
                        ->whereNot('user_type', 'LIKE', '%admin%')
                        ->where('active_status', 2)->paginate(8);

        return response()->json(['user_ban' => $user_ban,
                            'links' => (string) $user_ban->links(),
                        ]);
    }

    public function searchNotVerified(Request $request) {
        $value = $request->searchUserNotVer;

        $user_notverified = User::whereLike('name', '%'.$value.'%')
                            ->whereNot('user_type', 'LIKE', '%admin%')
                            ->where('email_verified_at', null)->paginate(8);
        
        return response()->json([
            'user_notverified' => $user_notverified,
            'links' => (string) $user_notverified->links(),
        ]);
    }

    // public function list_conversation() {
    //     // $messages = ChMessage::with(['fromUser', 'usersInChannel'])->get();
    
    //     return view('admin.managerUser.list_conversation');
    // }

    public function list_conversation(Request $request) {
        $value = $request->searchMessage;

        if($value) {
            $messages = ChMessage::with(['fromUser', 'usersInChannel'])
                                    ->join('users', 'ch_messages.from_id', '=', 'users.id')
                                    ->whereLike('users.name', '%'.$value.'%' )
                                    ->orderBy('created_at', 'desc')
                                    ->select('ch_messages.*')
                                    ->paginate(15)
                                    ->appends(['searchMessage' => $value]);
            
            return view('admin.managerUser.list_conversation', compact('messages', 'value'));
        }

        return view('admin.managerUser.list_conversation');
    }

    public function list_imageFile() {

        $imageExtensions = ['%.jpg', '%.png', '%.jpeg', '%.gif', '%.tiff', '%.tif'];
        $fileExtensions = ['%.zip', '%.rar', '%.txt', '%.docx', '%.xlsx', '%.pdf', '%.sql', '%.json', '%.pptx'];
        $mediaExtensions = ['%.mp4', '%.mp3'];

        // lọc danh sách hình ảnh
        $list_image = ChMessage::where(function ($query) use ($imageExtensions) {
            foreach ($imageExtensions as $ext) {
                $query->orWhere('attachment->new_name', 'like', $ext);
            }
        })->paginate(16);

        // Lọc danh sách file không phải ảnh/video
        $list_file = ChMessage::where(function ($query) use ($fileExtensions) {
            foreach ($fileExtensions as $ext) {
                $query->orWhere('attachment->new_name', 'like', $ext);
            }
        })->orderBy('created_at', 'desc')->paginate(10, ['*'], 'file_page');

        // Lọc danh sách video
        $list_video = ChMessage::where(function ($query) use ($mediaExtensions) {
            foreach ($mediaExtensions as $ext) {
                $query->orWhere('attachment->new_name', 'like', $ext);
            }
        })->orderBy('created_at', 'desc')->paginate(10, ['*'], 'video_page');

    
        return view('admin.managerUser.list_imageFile', compact('list_image', 'list_file', 'list_video'));
    }

    public function list_map() {

        $list_map = ChMessage::whereLike('body', '%https://www.google.com/maps?q=%')->orderBy('created_at', 'desc')->paginate(3);

        return view('admin.managerUser.list_map', compact('list_map'));
    }

    // Trang Quản lý thông báo
    public function manager_news(Request $request) {
        $query = News::query();
    
        // Kiểm tra xem searchNews có thông tin tìm kiếm hay không. nếu có sẽ sử dụng $query với đk lọc
        // ngược lại nếu admin không nhập tìm kiếm thì nó sẽ hiển thị toàn bộ dữ liệu được lấy từ $query ở trên
        if ($request->has('searchNews') && !empty($request->searchNews)) {
            $query->where('title', 'like', '%' . $request->searchNews . '%');
        }
    
        $getNews = $query->paginate(8)->appends(['searchNews' => $request->searchNews]);
    
        return view('admin.managerNews.manager_news', compact('getNews'));
    }
    

    public function add_news(Request $request) {
        $news = new News();

        $news->title = $request->title;
        $news->content = $request->content;

        // Kiểm tra file hình ảnh
        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageExtension = strtolower($image->getClientOriginalExtension());

            // Chỉ cho phép các định dạng hình ảnh hợp lệ (ví dụ: jpg, png, jpeg)
            $allowedImageExtensions = ['jpg', 'png', 'jpeg', 'gif'];

            if (in_array($imageExtension, $allowedImageExtensions)) {
                // Lưu hình ảnh với tên duy nhất
                $newImageName = Str::uuid() . "." . $imageExtension;
                $oldImageName = $image->getClientOriginalName();

                $image->storeAs(config('chatify.attachments.folder'), $newImageName, config('chatify.storage_disk_name'));

                $news->img = json_encode([
                    'new_name' => $newImageName,
                    'old_name' => $oldImageName,
                ]);
            } else {
                return response()->json([
                    'status' => '400',
                    'error' => 'Only image files (jpg, png, jpeg, gif) are allowed!',
                ]);
            }
        } else {
            $news->img = null;
        }

        $news->save();
        toastr()->closeButton()->success('Thêm thông báo thành công');
        return redirect()->route('manager_news');

    }

    public function delete_news($id) {
        $news = News::find($id);
        if(isset($news)) {
            if($news->img) {
                $img = json_decode($news->img, true);
                $oldFilePath = public_path('storage/attachments/' . $img['new_name']);

                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath); // Xóa tệp cũ
                }
            }
            $news->delete();
            toastr()->closeButton()->success('xóa thông báo thành công');
        }

        return redirect()->back();
    }

    public function viewUpdate_news($id) {
        $news = News::find($id);

        return view('admin.managerNews.update_news', compact('news'));
    }
    
    public function update_news(Request $request, $id) {
        $news = News::find($id);

        $news->title = $request->title;
        $news->content = $request->content;

        // Kiểm tra file hình ảnh
        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageExtension = strtolower($image->getClientOriginalExtension());

            // Chỉ cho phép các định dạng hình ảnh hợp lệ (ví dụ: jpg, png, jpeg)
            $allowedImageExtensions = ['jpg', 'png', 'jpeg', 'gif'];

            if (in_array($imageExtension, $allowedImageExtensions)) {
                // Lưu hình ảnh với tên duy nhất
                $newImageName = Str::uuid() . "." . $imageExtension;
                $oldImageName = $image->getClientOriginalName();

                $image->storeAs(config('chatify.attachments.folder'), $newImageName, config('chatify.storage_disk_name'));

                // xóa hình ảnh cũ, thay đổi bằng ảnh mới
                if($news->img) {
                    $img = json_decode($news->img, true);
                    $oldFilePath = public_path('storage/attachments/' . $img['new_name']);
    
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath); // Xóa tệp cũ
                    }
                }

                $news->img = json_encode([
                    'new_name' => $newImageName,
                    'old_name' => $oldImageName,
                ]);
            } else {
                return response()->json([
                    'status' => '400',
                    'error' => 'Only image files (jpg, png, jpeg, gif) are allowed!',
                ]);
            }
        } else {
            $news->img = null;
        }

        $news->save();
        toastr()->closeButton()->success('Cập nhật thông báo thành công');
        return redirect('/manager_news');
    }


    // trang quản lý lịch sử đăng nhập
    public function loginHistory(Request $request) {
        $query = LoginHistory::query();

        // kiểm tra có dữ liệu tìm kiếm hay không
        if($request->has('searchLoginHistory') && !empty($request->searchLoginHistory)) {
            $query = DB::table('users')
                            ->join('login_histories', 'users.id', '=', 'login_histories.user_id')
                            ->whereLike('users.name', '%'. $request->searchLoginHistory .'%');                
        }

        // thêm appends để thực hiện việc tìm kiếm và chuyển trang vẫn giữ lại chức năng tìm kiếm
        $user_login = User::whereNot('user_type', 'admin')->get();
        $login_histories = $query->orderBy('login_histories.created_at', 'desc')
                            ->paginate(7)
                            ->appends(['searchLoginHistory' => $request->searchLoginHistory]);

        return view('admin.loginHistory.loginHistory', compact('login_histories', 'user_login'));
    }

    public function deleteLoginHistory($id) {
        $loginId = LoginHistory::find($id);

        if(isset($loginId)) {
            $loginId->delete();

            toastr()->closeButton()->success('xóa lịch sử đăng nhập thành công');
        }

        return redirect()->back();
    }

    // public function searchLoginHistory(Request $request) {
    //     $value = $request->searchLoginHistory;
    //     $user_login = User::whereNot('user_type', 'admin')->get();

    //     $login_histories = DB::table('users')
    //                         ->join('login_histories', 'users.id', '=', 'login_histories.user_id')
    //                         ->whereLike('users.name', '%'.$value.'%')
    //                         ->orderBy('login_histories.created_at', 'desc')
    //                         ->paginate(8);

    //     return view('admin.loginHistory.loginHistory', compact('login_histories', 'user_login'));
    // }


    // Trang quản lý kho lưu trữ tài liệu của người dùng
    public function managerDrive(Request $request) {
        $queryFolder = Folder::query();
        $queryFile = File::query();

        if($request->has('searchFolder') && !empty($request->searchFolder)) {
            $queryFolder->where('name', 'like', '%' . $request->searchFolder . '%');
        }

        if($request->has('searchFile') && !empty($request->searchFile)) {
            $queryFile->where('name', 'like', '%' . $request->searchFile . '%');
        }

        $folders = $queryFolder->paginate(8, ['*'], 'folder_page');
        $files = $queryFile->paginate(8, ['*'], 'file_page');
        $userNames = User::get();

        return view('admin.managerDrive.managerDrive', compact('folders', 'files', 'userNames'));
    }


    public function restorePassword(Request $request) {

        $validator = Validator::make($request->all(), [
            'password_new' => ['required', 'confirmed', 'min:8'],
            'user_id' => ['required', 'exists:users,id'],
        ]);

        if($validator->fails()) {
            if($validator->errors()->has('password_new')) {
                if (str_contains($validator->errors()->first('password_new'), 'does not match')) {
                    toastr()->closeButton()->error('Mật khẩu nhập lại không khớp');
                } else {
                    // toastr()->closeButton()->error($validator->errors()->first('password_new'));
                    toastr()->closeButton()->error('Mật khẩu phải lớn hơn 8 ký tự');
                }
            }else {
                toastr()->closeButton()->warning('Dữ liệu không hợp lệ');
            }
            
            return redirect()->back();
        }

        $user = User::find($request->user_id);
        $user->password = Hash::make($request->password_new);
        
        $user->save();

        toastr()->closeButton()->success('Khôi phục mật khẩu thành công');
        return redirect()->back();
    }
}
