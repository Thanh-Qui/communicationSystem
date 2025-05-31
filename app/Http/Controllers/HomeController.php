<?php

namespace App\Http\Controllers;

use App\Models\channel_blocks;
use App\Models\ChChannel;
use App\Models\ChMessage;
use App\Models\File;
use App\Models\Folder;
use App\Models\News;
use App\Models\User;
use App\Models\NewsUser;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Chatify\Facades\ChatifyMessenger as Chatify;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

use function Laravel\Prompts\table;

class HomeController extends Controller
{
    public function home() {
        return redirect()->route('login');
    }


    // public function joinGroup(Request $request, $id) {
    //     $channel_id = $id;
    
    //     $user_id = $request->user_id;
        
    //     // Tìm channel và user
    //     $channel = ChChannel::find($channel_id);
    //     $user = User::find($user_id);
    
    //     if (!$channel || !$user) {
    //         return redirect()->back()->with('error', 'Nhóm hoặc người dùng đó không tồn tại.');
    //     }
    
    //     // Kiểm tra xem user đã có trong channel chưa để tránh bị lặp
    //     if (!$channel->users()->where('user_id', $user_id)->exists()) {
    //         // Thêm user vào channel
    //         $channel->users()->attach($user->id);
    //         toastr()->closeButton()->success('Người dùng đã được thêm vào group.');
    //         return redirect()->back();
    //     } else {
    //         toastr()->closeButton()->error('Người dùng đã có trong group.');
    //         return redirect()->back();
    //     }
    // }
    
    public function joinGroup(Request $request, $id)
    {
        $channel = ChChannel::find($id);
        $userIds = json_decode($request->user_ids, true);

        if (!$channel || empty($userIds)) {
            return redirect()->back()->with('error', 'Nhóm hoặc danh sách người dùng không hợp lệ.');
        }

        $added = 0;
        foreach ($userIds as $userId) {
            if (!$channel->users()->where('user_id', $userId)->exists()) {
                $channel->users()->attach($userId);
                $added++;
            }
        }

        if ($added > 0) {
            toastr()->success("Đã thêm {$added} người dùng vào nhóm.");
        } else {
            toastr()->warning('Tất cả người dùng đã có trong nhóm.');
        }

        return redirect()->back();
    }

    
    public function searchUsers(Request $request, $channelId)
    {
        $channel_id = $channelId;
        $username = $request->input('username');

        // kiểm tra xem người dùng có trong nhóm hay không
        $existUserOnGroup = DB::table('ch_channel_user')->where('channel_id', $channel_id)->pluck('user_id');

        // Thực hiện tìm kiếm người dùng
        $users = User::where('name', 'LIKE', '%' . $username . '%')->whereNotIn('id', $existUserOnGroup)
                        ->whereNot('user_type', 'LIKE', '%admin%')
                        ->whereNot('email_verified_at', null)->get();

        return response()->json(['users' => $users]);
    }

    public function remove_group($user_id, $channel_id) {

        $user = User::find($user_id);
        $channel = ChChannel::find($channel_id);

        if (!$user || !$channel) {
            toastr()->closeButton()->error('Nhóm hoặc người dùng đó không tồn tại.');
            return redirect()->back();
        }else {
            $channel->users()->detach($user->id);
            toastr()->closeButton()->success('Đã xóa người dùng thành công');
            return redirect()->back();
        }
    }


    // phần quyền người dùng thêm thành viên vào nhóm
    public function toggleAddUserPermission(Request $request, $channelId, $userId)
    {
        $channel = ChChannel::find($channelId);

        // Kiểm tra quyền trưởng nhóm
        if ($channel->owner_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to modify permissions.');
        }

        // Cập nhật quyền trong bảng ch_channel_user
        $channelUser = DB::table('ch_channel_user')
            ->where('channel_id', $channelId)
            ->where('user_id', $userId)
            ->first();

        if ($channelUser) {
            $newStatus = !$channelUser->can_add_users; // Toggle trạng thái
            DB::table('ch_channel_user')
                ->where('channel_id', $channelId)
                ->where('user_id', $userId)
                ->update(['can_add_users' => $newStatus]);

            return redirect()->back()->with('success', 'Permission updated successfully.');
        }

        return redirect()->back()->with('error', 'User not found in the group.');
    }

    // public function showChannel($channelId)
    // {
    //     $channel = ChChannel::find($channelId);
    //     $currentUserId = Auth::id();

    //     // Kiểm tra quyền thêm thành viên
    //     $canAddUsers = $channel->owner_id === $currentUserId || DB::table('ch_channel_user')
    //         ->where('channel_id', $channelId)
    //         ->where('user_id', $currentUserId)
    //         ->value('can_add_users');

    //     return view('chat.channel', [
    //         'channel' => $channel,
    //         'isGroup' => $channel->is_group,
    //         'canAddUsers' => $canAddUsers,
    //     ]);
    // }


    public function seen(Request $request , $channel_id) {

        $countSeen = ChMessage::where('to_channel_id', $channel_id)->where('seen', 0)->count();

        return response()->json(['countSeen', $countSeen]);

    }


    // xử lý việc lưu và gửi đoạn ghi âm đó qua người nhận websocket pusher
    public function sendAudio(Request $request)
    {
        // Default response variables
        $error = (object)[
            'status' => 0,
            'message' => null
        ];
        $attachment = null;
        $attachment_title = null;

        // Kiểm tra kênh chat có bị chặn hay không
        $channelId = $request->input('channel_id');
        $isBlocked = DB::table('channel_blocks')
            ->where('channel_id', $channelId)
            ->exists();

        if ($isBlocked) {
            return response()->json([
                'status' => '403',
                'error' => 'Đã bị chặn không thể gửi tin nhắn!',
            ]);
        }

        // Kiểm tra file âm thanh
        if ($request->hasFile('audio')) {
            $file = $request->file('audio');
            $extension = strtolower($file->getClientOriginalExtension());

            if ($extension === 'mp3') {
                if ($file->getSize() <= Chatify::getMaxUploadSize()) {
                    // Lưu file với tên duy nhất
                    $attachment = Str::uuid() . "." . $extension;
                    $file->storeAs(config('chatify.attachments.folder'), $attachment, config('chatify.storage_disk_name'));
                } else {
                    return response()->json([
                        'status' => '400',
                        'error' => 'File size is too large!',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => '400',
                    'error' => 'Only MP3 files are allowed!',
                ]);
            }
        } else {
            return response()->json([
                'status' => '400',
                'error' => 'No file uploaded!',
            ]);
        }

        // Nếu không có lỗi, tạo tin nhắn
        if (!$error->status) {
            // Lưu tin nhắn vào database
            $message = Chatify::newMessage([
                'from_id' => Auth::user()->id,
                'to_channel_id' => $request['channel_id'],
                'body' => null,
                'attachment' => json_encode([
                    'new_name' => $attachment,
                    'old_name' => $file->getClientOriginalName(),
                ]),
            ]);

            // Lấy thông tin người gửi
            $message->user_avatar = Auth::user()->avatar;
            $message->user_name = Auth::user()->name;
            $message->user_email = Auth::user()->email;

            // Chuyển đổi thông điệp
            $messageData = Chatify::parseMessage($message, null, true);

            // Phát sự kiện gửi tin nhắn audio
            Chatify::push("private-chatify.".$request['channel_id'], 'messaging', [
                'from_id' => Auth::user()->id,
                'to_channel_id' => $request['channel_id'],
                'message' => Chatify::messageCard($messageData, true)
            ]);

            return response()->json([
                'status' => '200',
                'message' => 'Audio sent successfully!',
                'audio_url' => asset('storage/' . config('chatify.attachments.folder') . '/' . $attachment),
                'channel_id' => $request['channel_id'],
                'message_id' => $message->id, // ID của tin nhắn vừa lưu
            ]);
        } else {
            return response()->json([
                'status' => '400',
                'error' => $error,
            ]);
        }
    }


    public function sendVideo(Request $request)
    {
        // Default response variables
        $error = (object)[
            'status' => 0,
            'message' => null
        ];
        $attachment = null;
        $attachment_title = null;

        // Kiểm tra kênh chat có bị chặn hay không
        $channelId = $request->input('channel_id');
        $isBlocked = DB::table('channel_blocks')
            ->where('channel_id', $channelId)
            ->exists();

        if ($isBlocked) {
            return response()->json([
                'status' => '403',
                'error' => 'Đã bị chặn không thể gửi tin nhắn!',
            ]);
        }

        // Kiểm tra file video
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $extension = strtolower($file->getClientOriginalExtension());

            // Chỉ cho phép các định dạng video hợp lệ (ví dụ: mp4, avi)
            $allowedExtensions = ['mp4', 'avi', 'mov', 'mkv'];

            if (in_array($extension, $allowedExtensions)) {
                if ($file->getSize() <= Chatify::getMaxUploadSize()) {
                    // Lưu file với tên duy nhất
                    $attachment = Str::uuid() . "." . $extension;
                    $file->storeAs(config('chatify.attachments.folder'), $attachment, config('chatify.storage_disk_name'));
                } else {
                    return response()->json([
                        'status' => '400',
                        'error' => 'File size is too large!',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => '400',
                    'error' => 'Only video files (mp4, avi, mov, mkv) are allowed!',
                ]);
            }
        } else {
            return response()->json([
                'status' => '400',
                'error' => 'No file uploaded!',
            ]);
        }

        // Nếu không có lỗi, tạo tin nhắn
        if (!$error->status) {
            // Lưu tin nhắn vào database
            $message = Chatify::newMessage([
                'from_id' => Auth::user()->id,
                'to_channel_id' => $request['channel_id'],
                'body' => null,
                'attachment' => json_encode([
                    'new_name' => $attachment,
                    'old_name' => $file->getClientOriginalName(),
                ]),
            ]);

            // Lấy thông tin người gửi
            $message->user_avatar = Auth::user()->avatar;
            $message->user_name = Auth::user()->name;
            $message->user_email = Auth::user()->email;

            // Chuyển đổi thông điệp
            $messageData = Chatify::parseMessage($message, null, true);

            // Phát sự kiện gửi tin nhắn video
            Chatify::push("private-chatify.".$request['channel_id'], 'messaging', [
                'from_id' => Auth::user()->id,
                'to_channel_id' => $request['channel_id'],
                'message' => Chatify::messageCard($messageData, true)
            ]);

            return response()->json([
                'status' => '200',
                'message' => 'Video sent successfully!',
                'video_url' => asset('storage/' . config('chatify.attachments.folder') . '/' . $attachment),
                'channel_id' => $request['channel_id'],
                'message_id' => $message->id, // ID của tin nhắn vừa lưu
            ]);
        } else {
            return response()->json([
                'status' => '400',
                'error' => $error,
            ]);
        }
    }

    public function sendDrawing(Request $request)
    {
        // Default response variables
        $error = (object)[
            'status' => 0,
            'message' => null
        ];
        $attachment = null;

        // Kiểm tra kênh chat có bị chặn hay không
        $channelId = $request->input('channel_id');
        $isBlocked = DB::table('channel_blocks')
            ->where('channel_id', $channelId)
            ->exists();

        if ($isBlocked) {
            return response()->json([
                'status' => '403',
                'error' => 'Đã bị chặn không thể gửi tin nhắn!',
            ]);
        }

        // Kiểm tra dữ liệu hình vẽ (Base64 Image)
        $base64Image = $request->input('drawing_image');

        if ($base64Image) {
            try {
                // Decode base64 image
                $imageData = explode(',', $base64Image);
                $image = base64_decode($imageData[1]);

                // Tạo tên file duy nhất
                $attachment = Str::uuid() . '.png';
                $filePath = config('chatify.attachments.folder') . '/' . $attachment;

                // Lưu file vào thư mục
                Storage::disk(config('chatify.storage_disk_name'))->put($filePath, $image);
            } catch (Exception $e) {
                return response()->json([
                    'status' => '400',
                    'error' => 'Invalid drawing data!',
                ]);
            }
        } else {
            return response()->json([
                'status' => '400',
                'error' => 'No drawing data received!',
            ]);
        }

        // Nếu không có lỗi, tạo tin nhắn
        if (!$error->status) {
            // Lưu tin nhắn vào database
            $message = Chatify::newMessage([
                'from_id' => Auth::user()->id,
                'to_channel_id' => $request['channel_id'],
                'body' => null,
                'attachment' => json_encode([
                    'new_name' => $attachment,
                    'old_name' => 'drawing.png',
                ]),
            ]);

            // Lấy thông tin người gửi
            $message->user_avatar = Auth::user()->avatar;
            $message->user_name = Auth::user()->name;
            $message->user_email = Auth::user()->email;

            // Chuyển đổi thông điệp
            $messageData = Chatify::parseMessage($message, null, true);

            // Phát sự kiện gửi tin nhắn hình vẽ
            Chatify::push("private-chatify.".$request['channel_id'], 'messaging', [
                'from_id' => Auth::user()->id,
                'to_channel_id' => $request['channel_id'],
                'message' => Chatify::messageCard($messageData, true)
            ]);

            return response()->json([
                'status' => '200',
                'message' => 'Drawing sent successfully!',
                'image_url' => asset('storage/' . $filePath),
                'channel_id' => $request['channel_id'],
                'message_id' => $message->id, // ID của tin nhắn vừa lưu
            ]);
        } else {
            return response()->json([
                'status' => '400',
                'error' => $error,
            ]);
        }
    }

    public function sendLocation(Request $request)
    {
        // Khởi tạo đối tượng lỗi mặc định
        $error = (object)[
            'status' => 0,
            'message' => null
        ];

        // Kiểm tra kênh chat có bị chặn hay không
        $channelId = $request->input('channel_id');
        $isBlocked = DB::table('channel_blocks')
            ->where('channel_id', $channelId)
            ->exists();

        if ($isBlocked) {
            return response()->json([
                'status' => '403',
                'error' => 'Đã bị chặn không thể gửi tin nhắn!',
            ]);
        }

        // Kiểm tra dữ liệu định vị
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        if (!$latitude || !$longitude) {
            return response()->json([
                'status' => '400',
                'error' => 'Location data is missing!',
            ]);
        }

        // Nếu không có lỗi, tạo tin nhắn
        if (!$error->status) {
            try {
                // Tạo đường link đến Google Maps với tọa độ latitude và longitude
                $locationUrl = "https://www.google.com/maps?q=$latitude,$longitude";

                // Tạo nội dung tin nhắn với đường link vị trí
                $body = $locationUrl;

                // Lưu tin nhắn vào database
                $message = Chatify::newMessage([
                    'from_id' => Auth::user()->id,
                    'to_channel_id' => $channelId,
                    'body' => $body, // Đưa đường link vào body
                    'attachment' => null, // Không cần sử dụng attachment nữa
                ]);

                // Lấy thông tin người gửi
                $message->user_avatar = Auth::user()->avatar;
                $message->user_name = Auth::user()->name;
                $message->user_email = Auth::user()->email;

                // Chuyển đổi thông điệp
                $messageData = Chatify::parseMessage($message, null, true);

                // Phát sự kiện gửi tin nhắn định vị
                Chatify::push("private-chatify." . $channelId, 'messaging', [
                    'from_id' => Auth::user()->id,
                    'to_channel_id' => $channelId,
                    'message' => Chatify::messageCard($messageData, true)
                ]);

                return response()->json([
                    'status' => '200',
                    'message' => 'Location sent successfully!',
                    'location_url' => $locationUrl,
                    'channel_id' => $channelId,
                    'message_id' => $message->id, // ID của tin nhắn vừa lưu
                ]);
            } catch (\Exception $e) {
                Log::error('Error sending location:', [
                    'error' => $e->getMessage(),
                    'user_id' => Auth::user()->id,
                    'channel_id' => $channelId,
                ]);

                return response()->json([
                    'status' => '500',
                    'error' => 'Internal Server Error. Please try again later.',
                ]);
            }
        } else {
            return response()->json([
                'status' => '400',
                'error' => $error->message,
            ]);
        }
    }

    // gửi form bình chọn
    public function sendPoll(Request $request)
    {
        // Kiểm tra channel có bị block không
        $channelId = $request->input('channel_id');
        $isBlocked = DB::table('channel_blocks')
            ->where('channel_id', $channelId)
            ->exists();
    
        if ($isBlocked) {
            return response()->json([
                'status' => '403',
                'error' => 'Đã bị chặn không thể gửi tin nhắn!',
            ]);
        }
    
        // Validate bình chọn
        $request->validate([
            'title' => 'required|string|max:255',
            'options' => 'required|array|min:2|max:5',
            'options.*' => 'required|string|max:5000',
            'end_date' => 'required',
        ]);
    
        try {
            $pollData = [
                'type' => 'poll',
                'title' => $request->title,
                'options' => $request->options,
                'end_date' => $request->end_date,
            ];

            // Mã hóa thành JSON
            $jsonData = json_encode($pollData);

            // Kiểm tra độ dài của JSON
            $dataSizeInBytes = strlen($jsonData);
            $dataSizeInKB = $dataSizeInBytes / 1024;  // Chuyển đổi từ bytes sang KB

            // In ra kích thước để kiểm tra
            Log::info("Poll Data Size: " . $dataSizeInKB . " KB");

            if ($dataSizeInKB > 10) {
                return response()->json([
                    'status' => '400',
                    'error' => 'Dữ liệu quá lớn, vui lòng giảm bớt thông tin.',
                ]);
            }
    
            $message = Chatify::newMessage([
                'from_id' => Auth::id(),
                'to_channel_id' => $channelId,
                'body' => json_encode($pollData),
                'attachment' => null,
            ]);
    
            $message->user_avatar = Auth::user()->avatar;
            $message->user_name = Auth::user()->name;
            $message->user_email = Auth::user()->email;
    
            $messageData = Chatify::parseMessage($message, null, true);
    
            Chatify::push("private-chatify." . $channelId, 'messaging', [
                'from_id' => Auth::user()->id,
                'to_channel_id' => $channelId,
                'message' => Chatify::messageCard($messageData, true)
            ]);
    
            return response()->json([
                'status' => '200',
                'message' => 'Poll sent successfully!',
                'channel_id' => $channelId,
                'message_id' => $message->id,
                'poll_data' => $pollData,
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending poll:', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'channel_id' => $channelId,
            ]);
    
            return response()->json([
                'status' => '500',
                'error' => 'Internal Server Error. Please try again later.',
            ]);
        }
    }

    // thực hiện bình chọn
    public function vote(Request $request)
    {
        $request->validate([
            'message_id' => 'required',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string',
        ]);
    
        try {
            $messageId = $request->message_id;
            $userId = Auth::id();

            // Lấy thông tin message
            $message = DB::table('ch_messages')->where('id', $messageId)->first();
            if (!$message) {
                return response()->json([
                    'status' => '404',
                    'error' => 'Không tìm thấy bình chọn này!',
                ]);
            }

            // kiểm tra thời gian kết thúc của bình chọn
            $expDate = json_decode($message->body);
            if(isset($expDate->end_date)) {
                $endDate = new DateTime($expDate->end_date);
                $currentDate = new DateTime;
                
                if($currentDate > $endDate) {
                    return response()->json([
                        'status' => '403',
                        'error' => 'Thời gian bình chọn đã kết thúc',
                    ]);
                }
            }

            $channelId = $message->to_channel_id;
    
            // Kiểm tra channel có bị block không
            $isBlocked = DB::table('channel_blocks')
                ->where('channel_id', $channelId)
                ->exists();
            
            if ($isBlocked) {
                return response()->json([
                    'status' => '403',
                    'error' => 'Đã bị chặn không thể bình chọn!',
                ]);
            }
    
            // Xoá bình chọn cũ
            PollVote::where('message_id', $messageId)
                ->where('user_id', $userId)
                ->delete();
    
            // Lưu các bình chọn mới
            foreach ($request->options as $option) {
                PollVote::create([
                    'message_id' => $messageId,
                    'user_id' => $userId,
                    'option' => $option,
                ]);
            }
    
            // Lấy dữ liệu cập nhật
            $voteCounts = PollVote::where('message_id', $messageId)
                ->select('option', DB::raw('count(*) as total'))
                ->groupBy('option')
                ->pluck('total', 'option')
                ->toArray();
    
            // Lấy thông tin người bình chọn (nếu cần)
            $voters = [];
            $pollOptions = PollVote::where('message_id', $messageId)
                ->get()
                ->groupBy('option');
                
            foreach ($pollOptions as $option => $votes) {
                $voters[$option] = $votes->map(function ($vote) {
                    $user = User::find($vote->user_id);
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'avatar' => asset($user->avatar ?? 'path/to/default-avatar.png')
                    ];
                })->toArray();
            }
    
            // Ghi log để kiểm tra
            // Log::info("Sending poll-vote to channel: private-chatify.{$channelId}", [
            //     'message_id' => $messageId,
            //     'vote_counts' => $voteCounts
            // ]);
    
            Chatify::push("private-chatify.{$channelId}", 'messaging', [
                'type' => 'poll-vote',
                'from_id' => Auth::user()->id,
                'to_channel_id' => $channelId,
                'message_id' => $messageId,
                'vote_counts' => $voteCounts,
                'user_vote' => $request->options,
                'voters' => $voters
            ]);
    
            return response()->json([
                'status' => '200',
                'success' => true,
                'message_id' => $messageId,
                'vote_counts' => $voteCounts,
                'user_vote' => $request->options,
                'voters' => $voters
            ]);
        } catch (\Exception $e) {
            Log::error('Error voting on poll:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'message_id' => $request->message_id,
            ]);
    
            return response()->json([
                'status' => '500',
                'error' => 'Internal Server Error. Please try again later.',
            ]);
        }
    }

    // đăng nhập bằng mã qr ************************

    public function showScanner()
    {
        return view('auth.scanner');
    }

    public function showQRCode()
    {
        // Lấy thông tin tài khoản người dùng hiện tại
        $user = Auth::user();

        // Tạo token hoặc thông tin đăng nhập mã hóa
        $loginData = [
            'email' => $user->email,
            'token' => base64_encode(Str::random(40)), // Token tạm thời
        ];

        // Lưu token vào session hoặc cache để kiểm tra sau
        $encodedData = base64_encode(json_encode($loginData));
        Cache::put('qr_token_' . $user->id, $loginData['token'], now()->addSeconds(5));

        // Tạo đường dẫn với thông tin mã hóa
        $qrCodeUrl = route('qr.callback', ['qrData' => $encodedData]);

        return view('auth.qr-login', compact('qrCodeUrl'));
    }

    
    public function qrCallback(Request $request)
    {
        // Lấy dữ liệu mã QR đã quét
        $qrData = $request->input('qrData');

        // Giải mã thông tin QR
        $decodedData = json_decode(base64_decode($qrData), true);

        if (!$decodedData || !isset($decodedData['email']) || !isset($decodedData['token'])) {
            return redirect()->route('login')->withErrors(['qr' => 'Invalid QR code']);
        }

        // Tìm người dùng và xác thực
        $user = User::where('email', $decodedData['email'])->first();

        if (!$user) {
            return redirect()->route('login')->withErrors(['qr' => 'User  not found']);
        }

        // *****************
        // kiểm tra lại xem cache có lưu không (lưu ý khi đăng nhập không thành công do vô tình bị dính qr trong lúc nó xóa 5s 1 lần)
        // Kiểm tra token trong cache
        $cachedToken = Cache::get('qr_token_' . $user->id);

        if ($cachedToken !== $decodedData['token']) {
            return redirect()->route('login')->withErrors(['qr' => 'QR code is expired or invalid']);
        }

        // Đăng nhập người dùng
        Auth::login($user);

        // Điều hướng về trang chính sau khi đăng nhập
        return redirect()->route('chatify');
    }


    // chức năng thay đổi background trò chuyện
    public function updateBackground(Request $request, $channel_id)
    {
        // Kiểm tra nếu có file được tải lên
        if ($request->hasFile('background')) {
            // Lấy tên cũ của tệp tải lên
            $oldName = $request->file('background')->getClientOriginalName();
            
            // Tạo tên mới cho tệp
            $newName = (string) Str::uuid() . '.' . $request->file('background')->getClientOriginalExtension();
            
            // Lưu tệp vào thư mục 'attachments' trong storage public
            $imagePath = $request->file('background')->storeAs('attachments', $newName, 'public');
    
            // Lấy đối tượng của channel
            $update = ChChannel::find($channel_id);
            
            // Kiểm tra xem có hình nền cũ không, nếu có thì xóa
            if ($update->backgroundChannel) {
                $backgroundData = json_decode($update->backgroundChannel, true);
                $oldFilePath = public_path('storage/attachments/' . $backgroundData['new_name']);
                
                // Kiểm tra tệp cũ có tồn tại không và xóa nó
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath); // Xóa tệp cũ
                }
            }
    
            // Cập nhật thông tin vào cơ sở dữ liệu với tên mới
            $update->backgroundChannel = json_encode([
                'new_name' => $newName,
                'old_name' => $oldName
            ]); // Lưu thông tin tên mới và cũ dưới dạng JSON
            $update->save();
        }
    
        return redirect()->back();
    }

    // Chặn người dùng
    public function blockChannel(Request $request)
    {
        $channel_id = $request['channel_id'];
        $currentUserId = Auth::id(); // Lấy ID người dùng hiện tại

        // Kiểm tra xem kênh có tồn tại không
        $channel = ChChannel::find($channel_id);
        if (!$channel) {
            return Response::json([
                'status' => 'error',
                'message' => 'Kênh không tồn tại.',
            ], 404);
        }

        // Kiểm tra trạng thái chặn (block/unblock)
        $existingBlock = channel_blocks::where('channel_id', $channel_id)
                                        ->where('blocker_id', $currentUserId)
                                        ->first();

        $blockStatus = $existingBlock ? 0 : 1; // 0: unblock, 1: block

        if ($blockStatus) {
            // Nếu chưa chặn, tạo bản ghi mới
            if (!$existingBlock) {
                $channelBlock = new channel_blocks();
                $channelBlock->channel_id = $channel_id;
                $channelBlock->blocker_id = $currentUserId;
                $channelBlock->save();
            }
        } else {
            // Nếu đã chặn, xóa bản ghi
            $existingBlock->delete();
        }

        // Gửi phản hồi
        return Response::json([
            'status' => $blockStatus, // 1: đã chặn, 0: đã bỏ chặn
        ], 200);
    }


    // chuyển tiếp tin nhắn
//     public function shareMessage(Request $request)
// {
//     $messageId = $request->message_id;
//     $sender_id = Auth::id();

//     $listData = json_decode($request->input('list_data'), true);
//     if (empty($listData)) {
//         toastr()->warning('Bạn chưa chọn người hoặc nhóm chia sẻ');
//         return redirect()->back();
//     }

//     $originalMessage = ChMessage::findOrFail($messageId);
//     $originalBody = $originalMessage->body;
//     $originalAttachment = $originalMessage->attachment;

//     foreach ($listData as $target) {
//         $list_id = $target['id'];
//         $list_type = $target['type'];
//         $channel = null;

//         // Trường hợp chuyển tiếp tới user
//         if ($list_type === 'user') {
//             $receiver = User::find($list_id);
//             if (!$receiver) continue;

//             $channel = ChChannel::whereNull('name')
//                 ->whereNull('owner_id')
//                 ->whereHas('users', fn($q) => $q->where('user_id', $sender_id))
//                 ->whereHas('users', fn($q) => $q->where('user_id', $list_id))
//                 ->withCount('users')->having('users_count', 2)
//                 ->first();

//             // Nếu tồn tại channel 1-1, kiểm tra block
//             if ($channel) {
//                 $isBlocked = DB::table('channel_blocks')
//                     ->where('channel_id', $channel->id)
//                     ->exists();
//                 if ($isBlocked) {
//                     toastr()->warning('Đã bị chặn không thể chuyển tiếp tin nhắn');
//                     continue;
//                 }
//             }

//             // Nếu chưa có channel thì tạo mới
//             if (!$channel) {
//                 $channel = new ChChannel();
//                 $channel->save();

//                 $channel->users()->attach([
//                     $sender_id => ['can_add_users' => false],
//                     $list_id => ['can_add_users' => false],
//                 ]);
//             }
//         }

//         // Trường hợp chuyển tiếp tới group
//         if ($list_type === 'group') {
//             $channel = ChChannel::where('id', $list_id)
//                 ->whereNotNull('name')
//                 ->whereNotNull('owner_id')
//                 ->first();

//             if (!$channel) continue;
//         }

//         // Tạo bản ghi tin nhắn mới
//         $newMessage = new ChMessage();
//         $newMessage->from_id = $sender_id;
//         $newMessage->to_channel_id = $channel->id;
//         $newMessage->body = $originalBody;
//         $newMessage->attachment = $originalAttachment;
//         $newMessage->seen = 0;
//         $newMessage->status_delete = 1;
//         $newMessage->like = 0;
//         $newMessage->created_at = now();
//         $newMessage->updated_at = now();
//         $newMessage->save();
//     }

//     toastr()->success('Chuyển tiếp tin nhắn thành công');
//     return redirect()->back();
// }

    public function shareMessage(Request $request)
    {
        $messageId = $request->message_id;
        $sender_id = Auth::id();

        $listData = json_decode($request->input('list_data'), true);
        if (empty($listData)) {
            return response()->json([
                'status' => '400',
                'error' => 'Bạn chưa chọn người hoặc nhóm chia sẻ',
            ]);
        }

        $originalMessage = ChMessage::findOrFail($messageId);
        $originalBody = $originalMessage->body;
        $originalAttachment = $originalMessage->attachment;

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

            // Gửi tin nhắn mới
            $newMessage = Chatify::newMessage([
                'from_id' => $sender_id,
                'to_channel_id' => $channel->id,
                'body' => $originalBody,
                'attachment' => $originalAttachment,
            ]);

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
            'message' => 'Tin nhắn đã được chuyển tiếp thành công!',
            'results' => $results
        ]);
    }



   /**
    *   Trang tin tức
    */
    public function systemNotification(Request $request) {

        $news = News::query()->orderBy('created_at', 'desc')->paginate(8);

        // kiểm tra ô tìm kiếm. nếu có dữu liệu thì thực hiện search
        if($request->has('searchNews') && !empty($request->searchNews)) {
            $news = News::query()->whereLike('title', '%'.$request->searchNews.'%')->paginate(8);
        }

        return view('systemNotification', compact('news'));
    }

    public function contentNotification($id) {
        $news = News::where('id', $id)->get();
        $user = Auth::id();

        // kiểm tra sự tồn tại tránh trùng lặp
        $existNewsUser = NewsUser::where('id_user', $user)
                                ->where('id_news', $id)
                                ->exists();
    
        if(!$existNewsUser) {
            $newsUser = new NewsUser();
            $newsUser->id_user = $user;
            $newsUser->id_news = $id;
            $newsUser->save();
        }        
        return view('contentNotification', compact('news'));
    }

    // bị spam quá nhiều request
    // public function count_news() {
    //     $id_user = Auth::id();
    //     $totalNews = DB::table('news')->count();

    //     $newsUser = DB::table('news_users')->where('id_user', $id_user)->count();
        
    //     $countNews = $totalNews - $newsUser;

    //     return response()->json(['count' => $countNews]);
    // }


}
