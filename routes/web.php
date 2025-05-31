<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\DriveController;
use App\Http\Controllers\Error404Controller;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;

Route::get('/',  [HomeController::class, 'home']);

// middleware xác thực 2FA
Route::middleware(['2fa'])->group(function() {

    Route::get('/dashboard', function () {
        return redirect()->route('chatify');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });


    // Admin *******************************************************
    Route::get('admin/index', [AdminController::class, 'index'])->middleware(['auth', 'admin']);

    // phân quyền admin
    Route::middleware(['admin'])->group(function () {
        // quản lý người dùng
        Route::get('user_manager', [AdminController::class, 'user_manager'])->middleware(['auth', 'verified']);
        Route::get('search_user', [AdminController::class, 'search_user'])->middleware(['auth', 'verified']);
        Route::get('SearchLockAcount', [AdminController::class, 'SearchLockAcount'])->middleware(['auth', 'verified']);
        Route::get('searchNotVerified', [AdminController::class, 'searchNotVerified'])->middleware(['auth', 'verified']);
        Route::get('lockAcount/{userId}', [AdminController::class, 'lockAcount'])->middleware(['auth', 'verified']);
        Route::get('unlockAcount/{userId}', [AdminController::class, 'unlockAcount'])->middleware(['auth', 'verified']);
        Route::get('delele_userNotVer/{userId}', [AdminController::class, 'delele_userNotVer'])->middleware(['auth', 'verified']);
        Route::post('restorePassword', [AdminController::class, 'restorePassword'])->middleware(['auth', 'verified'])->name('admin.restore');

        // trục xuất người dùng đang online
        Route::get('expulsion/{id}', [AdminController::class, 'expulsion'])->middleware(['auth', 'verified']);

        // quản lý nhóm người dùng (chat ground)
        Route::get('group_manager', [AdminController::class, 'group_manager'])->middleware(['auth', 'verified']);
        Route::get('seenMember/{id}', [AdminController::class, 'seenMember'])->middleware(['auth', 'verified']);
        Route::get('searchGroup', [AdminController::class, 'searchGroup'])->middleware(['auth', 'verified']);
        Route::get('conversationGroup', [AdminController::class, 'conversationGroup'])->middleware(['auth', 'verified']);


        // Danh Mục
        Route::get('list_conversation', [AdminController::class, 'list_conversation'])->middleware(['auth', 'verified']);
        Route::get('list_imageFile', [AdminController::class, 'list_imageFile'])->middleware(['auth', 'verified']);
        Route::get('list_map', [AdminController::class, 'list_map'])->middleware(['auth', 'verified']);
        // Route::get('search_listMessages', [AdminController::class, 'search_listMessages'])->middleware(['auth', 'verified']);

        // Quản lý thông báo
        Route::get('manager_news', [AdminController::class, 'manager_news'])->middleware(['auth', 'verified'])->name('manager_news');
        Route::post('add_news', [AdminController::class, 'add_news'])->middleware(['auth', 'verified'])->name('add_news');
        Route::get('delete_news/{id}', [AdminController::class, 'delete_news'])->middleware(['auth', 'verified']);
        Route::get('viewUpdate_news/{id}', [AdminController::class, 'viewUpdate_news'])->middleware(['auth', 'verified']);
        Route::post('update_news/{id}', [AdminController::class, 'update_news'])->middleware(['auth', 'verified']);


        // Trang quản lý lịch sử đăng nhập của người dùng
        Route::get('/loginHistory', [AdminController::class, 'loginHistory'])->middleware(['auth', 'verified']);
        Route::get('/deleteLoginHistory/{id}', [AdminController::class, 'deleteLoginHistory'])->middleware(['auth', 'verified']);
        // Route::get('/searchLoginHistory', [AdminController::class, 'searchLoginHistory'])->middleware(['auth', 'verified']);


        // Trang quản lý kho lưu trữ tệp tin của người dùng
        Route::get('/manager_drives', [AdminController::class, 'managerDrive'])->middleware(['auth', 'verified'])->name('drive.manager');
    });





    // User **********************************************************
    //Thêm người dùng vào nhóm
    Route::post('/chatify/{id}/join', [HomeController::class, 'joinGroup'])->middleware(['auth', 'verified']);
    Route::post('/chatify/{id}/search', [HomeController::class, 'searchUsers'])->middleware(['auth', 'verified']);
    Route::get('remove_group/{user_id}/{channel_id}', [HomeController::class, 'remove_group'])->middleware(['auth', 'verified']);
    Route::get('/chatify/{channel_id}/seen', [HomeController::class, 'seen'])->middleware(['auth', 'verified']);
    Route::get('channel/toggle-permission/{channelId}/{userId}', [HomeController::class, 'toggleAddUserPermission'])->name('channel.togglePermission');

    // gửi audio và video
    Route::post('/send-audio', [HomeController::class, 'sendAudio'])->name('send.audio');
    Route::post('/send-video', [HomeController::class, 'sendVideo'])->name('send.video');
    Route::post('/send-drawing', [HomeController::class, 'sendDrawing'])->name('send.drawing');
    Route::post('/send-location', [HomeController::class, 'sendLocation'])->name('send.location');

    // chuyển tiếp tin nhắn
    Route::post('/shareMessage', [HomeController::class, 'shareMessage'])->name('share.message');

    // gửi bình chọn
    Route::post('/send-poll', [HomeController::class, 'sendPoll'])->name('send.poll');
    Route::post('/poll/vote', [HomeController::class, 'vote'])->name('poll.vote');

    // đăng nhập bằng mã qr
    Route::get('/qr-login', [HomeController::class, 'showQRCode'])->name('qr.show')->middleware(['auth', 'verified']);
    Route::get('/qr-login/callback', [HomeController::class, 'qrCallback'])->name('qr.callback');
    Route::get('/qr-scanner', [HomeController::class, 'showScanner'])->name('qr.scanner');

    // Hiển thị background giao diện trò chuyện
    Route::post('/updateBackground/{channel_id}', [HomeController::class, 'updateBackground'])->name('updateBackground');

    // chặn tin nhắn người dùng
    Route::post('/chatify/blockChannel', [HomeController::class, 'blockChannel'])->name('blockChannel');

    // chat bot 
    Route::match(['get', 'post'], '/botman', [ChatBotController::class, 'index']);

    // trang thông báo tin tức
    Route::get('/system-notification', [HomeController::class, 'systemNotification'])->middleware(['auth', 'verified'])->name('system.notify');
    Route::get('/content-notification/{id}', [HomeController::class, 'contentNotification'])->middleware(['auth', 'verified']);
    // Route::get('/count-news', [HomeController::class, 'count_news'])->middleware(['auth', 'verified']); // bị spam quá nhiều request


    
    /**
     *  Trang lưu trữ tài liệu cá nhân
     */
    // Kho lưu trữ tài liệu
    Route::get('/myStorage', [DriveController::class, 'myStorage'])->middleware(['auth', 'verified'])->name('myStorage');
    Route::get('/folder/{id}', [DriveController::class, 'showFolder'])->middleware(['auth', 'verified'])->name('folders.show');
    Route::post('/upload', [DriveController::class, 'store'])->middleware(['auth', 'verified'])->name('file.upload');
    Route::get('/files/download/{id}', [DriveController::class, 'download'])->middleware(['auth', 'verified'])->name('files.download');
    Route::post('createFolder', [DriveController::class, 'createFolder'])->middleware(['auth', 'verified'])->name('folder.create');
    Route::get('/deleteDrive', [DriveController::class, 'delete'])->middleware(['auth', 'verified'])->name('drive.delete');
    Route::post('/renameDrive', [DriveController::class, 'renameDrive'])->middleware(['auth','verified'])->name('drive.rename');

    // Chia sẻ tài liệu
    Route::post('/shareFiles', [DriveController::class, 'shareFiles'])->middleware(['auth', 'verified'])->name('drive.share');
    Route::get('/searchUserDrive', [DriveController::class, 'searchUserDrive'])->name('drive.searchUsers');

    // Di chuyển thư mục
    Route::post('/folders/move', [DriveController::class, 'move'])->name('folders.move');
    Route::post('/files/moveFile', [DriveController::class, 'moveFile'])->name('files.moveFile');


    // // thùng rác tài liệu
    Route::get('/trashDrive', [DriveController::class, 'trashDrive'])->middleware(['auth', 'verified'])->name('drive.trash');
    Route::get('/restoreDrive', [DriveController::class, 'restoreDrive'])->middleware(['auth', 'verified'])->name('drive.store');
    Route::get('/deleteForeverDrive', [DriveController::class, 'deleteForeverDrive'])->middleware(['auth', 'verified'])->name('drive.deleteForever');

    // tìm kiếm trong Drive
    Route::get('/searchDrive', [DriveController::class, 'searchDrive'])->middleware(['auth', 'verified'])->name('drive.search');

    // trang to-do list
    Route::get('to-do-list', [DriveController::class, 'toDoList'])->middleware(['auth', 'verified'])->name('drive.todo');

    // ** logout toàn bộ phiên đăng nhập khác
    Route::post('/logout-other-sessions', [ProfileController::class, 'logoutOtherSessions'])->name('logout.other.sessions');

});


// Two factor authentication
Route::get('/2fa', [TwoFactorController::class, 'index'])->name('2fa.verify');
Route::post('/2fa', [TwoFactorController::class, 'store'])->name('2fa.check');
Route::post('/resend2fa', [TwoFactorController::class, 'resend'])->name('2fa.resend');

Route::post('add-two-factor-key', [TwoFactorController::class, 'addTwoFactorKey'])->name('2fa.create');

//Xử lý lỗi 404
Route::fallback([Error404Controller::class, 'error404']);


require __DIR__.'/auth.php';
