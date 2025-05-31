<?php

namespace App\Http\Controllers;

use App\Conversations\InstructConversation;
use App\Conversations\ProductConversation;

use Illuminate\Http\Request;

class ChatBotController extends Controller
{
    public function index(){

        $botman = app('botman');

        $botman->fallback(function ($bot) {
            // $message = $bot->getMessage();
            $bot->reply('Xin lỗi, Tôi chưa hiểu được ý của bạn, bạn có thể nói chi tiết hơn không?');
        });

        $botman->hears('.*Xin chào|Hello|Hi|Há Lô|chào nhe.*', function($bot) {
            // Bắt đầu cuộc hội thoại chào hỏi
            // $bot->startConversation(new OnboardingConversation);
            $bot->reply('Xin chào! Tôi có thể giúp gì cho bạn?');
        });

    
        $botman->hears('.*sản phẩm.*', function($bot) {
            // Bắt đầu cuộc hội thoại về sản phẩm
            $bot->startConversation(new ProductConversation);
        });

        $botman->hears('.*chức năng.*', function($bot) {
            // Bắt đầu cuộc hội thoại về sản phẩm
            $bot->startConversation(new InstructConversation);
        });

        $botman->hears('.*hệ thống.*', function($bot) {
            // Bắt đầu cuộc hội thoại về sản phẩm
            $bot->startConversation(new InstructConversation);
        });

        $botman->hears('.*hướng dẫn|chỉ dẫn|cách sử dụng.*', function($bot) {
            // Bắt đầu cuộc hội thoại về hướng dẫn sử dụng hệ thống
            $bot->startConversation(new InstructConversation);
        });

        // Khi người dùng hỏi về bóng đá
        // $botman->hears('.*bóng đá.*', function($bot) {
        //     // $bot->startConversation(new FootballConversation);
        // });

        $botman->hears('.*hỗ trợ|giúp đỡ.*', function($bot) {
            $bot->reply('Tôi có thể giúp bạn về những vấn đề như: sản phẩm, đơn hàng, hoặc thông tin tài khoản.');
        });

        $botman->listen();
    }
}
