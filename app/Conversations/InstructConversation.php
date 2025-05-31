<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;

class InstructConversation extends Conversation
{
    public function askGreeting(){

        $this->ask('Chào bạn, bạn cần hỗ trợ về phần nào của hệ thống?', function(Answer $answer) {
            $userInput = strtolower($answer->getText());

            if (strpos($userInput, 'sản phẩm') !== false) {
                $this->bot->startConversation(new ProductConversation);
            } elseif (strpos($userInput, 'thông tin cá nhân') !== false) {
                $this->askPersonalInfo();
            } elseif (strpos($userInput, 'thay đổi giao diện') !== false) {
                $this->askChangeAppearance();
            } elseif (strpos($userInput, 'tạo nhóm') !== false) {
                $this->askGroupCreation();
            } elseif (strpos($userInput, 'chức năng') !== false) {
                $this->askFunctionSystem();
            }
             else {
                $this->say('Xin lỗi, tôi không hiểu yêu cầu của bạn. Bạn có thể cho tôi biết rõ hơn không?');
            }
            
        });
    }

    public function askFunctionSystem() {
        $this->ask('Bạn muốn hỏi về chức năng nào của hệ thống', function(Answer $answer) {
            $info = strtolower($answer->getText());
            if (strpos($info, 'sản phẩm') !== false) {
                
                $this->bot->startConversation(new ProductConversation);
                return;
            }

            if (preg_match('/ghi âm/', $info)) {
                $this->say('Chức năng ghi âm nhanh chóng gọn lẹ để gửi nội dung cho người mình cần bằng đoạn hội thoại');
            }elseif (preg_match('/chuyển giọng nói thành văn bản|văn bản/', $info)) {
                $this->say('chức năng chuyển lời nói của mình thành văn bản chữ');
            }elseif (preg_match('/gửi định vị/', $info)) {
                $this->say('Bạn cần phải bật chức năng vị trí ở thiết bị ở mình. Sau đó thực hiện việc sử dụng chức năng gửi định vị, vị trí hiện tại cho người bạn cần gửi');
            }elseif (preg_match('/vị trí/', $info)) {
                $this->say('Bạn cần phải bật chức năng vị trí ở thiết bị ở mình. Sau đó thực hiện việc sử dụng chức năng gửi định vị, vị trí hiện tại cho người bạn cần gửi');
            }elseif (preg_match('/gửi file/', $info)) {
                $this->say('Hệ thống hỗ trợ gửi mọi loại tệp tin đính kèm và giới hạn kích cở 100MB trở xuống');
            }elseif (preg_match('/tệp tin/', $info)) {
                $this->say('Hệ thống hỗ trợ gửi mọi loại tệp tin đính kèm và giới hạn kích cở 100MB trở xuống');
            }

        $this->askFunctionSystem();    
        });

        
    }

    public function askPersonalInfo() {
        
        $this->ask('Bạn muốn hỏi về việc thay đổi thông tin cá nhân. chức năng nằm ở góc trên bên trái có hình cây viết.', function(Answer $answer) {
            $info = strtolower($answer->getText());
            if (strpos($info, 'sản phẩm') !== false) {
                
                $this->bot->startConversation(new ProductConversation);
                return;
            }

            $this->askGreeting();
        });
    }

    public function askChangeAppearance(){

        $this->ask('Bạn muốn thay đổi gì về giao diện chat? (Avatar, màu nền, khung chat). Chức năng bạn muốn nằm ở góc trên bên trái hình bánh răng.', function(Answer $answer) {
            $appearance = strtolower($answer->getText());

            if (strpos($appearance, 'sản phẩm') !== false) {
                
                $this->bot->startConversation(new ProductConversation);
                return;
            }

            if(preg_match('/avatar/', $appearance)) {
                $this->say('Hình đại diện bạn có thể thay đổi tùy thích, theo sở thích của bạn');
            }else if(preg_match('/màu nền/', $appearance)) {
                $this->say('Hệ thống hỗ trợ 2 loại, 1 loại giao diện màn hình trắng và 1 loại và giao diện màn hình màu đen');
            }else if(preg_match('/khung chat/', $appearance)) {
                $this->say('Hệ thống hỗ trợ 10 màu sắc cho bạn có thể thay đổi tủy thích');
            }else if(preg_match('/hướng dẫn|cách sử dụng|chỉ dẫn/', $appearance)) {
                $this->askGreeting();
            }

            if (strpos($appearance, 'thông tin cá nhân') !== false) {
                    $this->askPersonalInfo();
                return;
            }

            if (strpos($appearance, 'tạo nhóm') !== false) {
                $this->askGroupCreation();
            return;
        }

        $this->askChangeAppearance();
        });
    }

    public function askGroupCreation(){

        $this->ask('Bạn muốn tạo nhóm chat với tên gì?. Chức năng bạn muốn đang ở góc trên bên phải hình 1 nhóm người.', function(Answer $answer) {
            $groupName = $answer->getText();

            if (strpos($groupName, 'sản phẩm') !== false) {
                
                $this->bot->startConversation(new ProductConversation);
                return;
            }

            $this->askGreeting();
        });
    }

    public function run(){

        // Bắt đầu hỏi người dùng với câu chào
        $this->askGreeting();
    }
}
