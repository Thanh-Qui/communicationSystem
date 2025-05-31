<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductConversation extends Conversation
{
    protected $products = [];
    protected $productKey;

    public function __construct(){
        $filePath = public_path('fileData/products.xlsx');
        $this->loadProductsFromExcel($filePath);
    }

    private function loadProductsFromExcel($filePath){
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        
        foreach ($sheet->getRowIterator(2) as $row) { // Bỏ qua dòng tiêu đề
            $cells = $row->getCellIterator();
            $cells->setIterateOnlyExistingCells(false);

            $data = [];
            foreach ($cells as $cell) {
                $data[] = $cell->getValue();
            }

            // Giả sử các cột theo thứ tự: Tên sản phẩm, Giá, Bảo hành, Khuyến mãi, Tính năng
            $this->products[strtolower($data[0])] = [
                'giá' => $data[1],
                'bảo hành' => $data[2],
                'khuyến mãi' => $data[3],
                'tính năng' => $data[4]
            ];
        }
    }

    public function askProduct(){
        $this->ask('Chào bạn, bạn muốn tìm hiểu về sản phẩm nào? Hãy nhập tên sản phẩm bạn quan tâm.', function(Answer $answer) {
            $userInput = strtolower($answer->getText());

            if (strpos($userInput, 'hướng dẫn') !== false) {
                
                $this->bot->startConversation(new InstructConversation);
                return;
            }

            // Tách các từ trong câu của người dùng
            $words = explode(' ', $userInput);
            $found = false;

            // Tìm kiếm sản phẩm dựa trên từng từ khóa trong $products
            foreach ($this->products as $key => $details) {
                foreach ($words as $word) {
                    if (strpos(strtolower($key), $word) !== false) {
                        $this->productKey = $key;
                        $found = true;
                        break 2; // Thoát khỏi cả hai vòng lặp khi tìm thấy sản phẩm
                    }
                }
            }

            if ($found) {
                $this->askProductDetail();
            } else {
                $this->say('Xin lỗi, tôi không tìm thấy sản phẩm bạn đang tìm kiếm. Bạn có thể thử lại với tên sản phẩm chính xác.');
                $this->askProduct(); // Hỏi lại nếu không tìm thấy sản phẩm
            }
        });
    }

    public function askProductDetail(){
        
        $this->ask('Bạn muốn biết thông tin gì về sản phẩm ' . ucfirst($this->productKey) . '? Bạn có thể hỏi về giá, bảo hành, khuyến mãi, hoặc tính năng.', function(Answer $answer) {
            $detail = strtolower($answer->getText());

            if (strpos($detail, 'cách sử dụng') !== false) {
                
                $this->bot->startConversation(new InstructConversation);
                return;
            }

            if (preg_match('/giá|cả|cost|price|bao|nhiêu/', $detail)) {
                $this->say('Giá của ' . ucfirst($this->productKey) . ' hiện tại là ' . $this->products[$this->productKey]['giá'] . '.');
            } elseif (preg_match('/bảo|hành/', $detail)) {
                $this->say(ucfirst($this->productKey) . ' được bảo hành trong ' . $this->products[$this->productKey]['bảo hành'] . '.');
            } elseif (preg_match('/khuyến|mãi|promotion|discount/', $detail)) {
                $this->say('Khuyến mãi hiện tại cho ' . ucfirst($this->productKey) . ' là: ' . $this->products[$this->productKey]['khuyến mãi'] . '.');
            } elseif (preg_match('/tính|năng|feature/', $detail)) {
                $this->say('Tính năng của ' . ucfirst($this->productKey) . ': ' . $this->products[$this->productKey]['tính năng'] . '.');
            } elseif (preg_match('/sản phẩm khác|new product|sản phẩm mới|sản phẩm thịnh hành|sản phẩm đang hot| sản phẩm hot trend/', $detail)) {
                $this->askProduct(); // Quay lại hỏi sản phẩm khác nếu người dùng muốn
                return; // Dừng lại ở đây, không tiếp tục hỏi chi tiết nữa
            } else {
                $this->say('Xin lỗi, tôi chưa hiểu ý của bạn. Bạn có thể chọn lại giữa các mục: Giá cả, Bảo hành, Khuyến mãi, Tính năng.');
            }

            // Sau khi trả lời chi tiết, tiếp tục hỏi về chi tiết khác
            // Tiếp tục hỏi thông tin về sản phẩm
            $this->askProductDetail();
        });
    }

    public function run()
    {
        $this->askProduct();
    }
}
