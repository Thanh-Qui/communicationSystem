<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <title>Đăng nhập bằng QR Code</title>
   <link rel="shortcut icon" type="image/x-icon" href=" {{ asset('assets/img/logoChat.png') }} ">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
   <link rel="stylesheet" href="{{ asset('stylecss.css') }}">
   
   <style>

       html, body {
           margin: 0;
           padding: 0;
           width: 100%;
           height: 100%;
           box-sizing: border-box;
       }

   </style>
   
</head>
<body>
   <div class="container-scanner">
       <button id="start-qr-scanner">Mở Camera Quét QR</button>
       <div id="qr-reader" style="width: 100%; height: 400px; display: none"></div>

       <div style="margin-top: 15px">
           <a href="{{ route('chatify') }}" class="btn btn-success">Quay về</a>
       </div>
   </div>

</body>
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode/minified/html5-qrcode.min.js"></script>
<script>

   document.getElementById('start-qr-scanner').addEventListener('click', function() {
       var qrReader = document.getElementById('qr-reader');
       var start = document.getElementById('start-qr-scanner');
       if (qrReader.style.display === 'none') {
           qrReader.style.display = 'block';  // Hiển thị QR Reader
           document.getElementById("start-qr-scanner").innerHTML = "Đóng Camera Quét QR";
       } else {
           qrReader.style.display = 'none';   // Ẩn QR Reader
           document.getElementById("start-qr-scanner").innerHTML = "Mở Camera Quét QR";

       }
   });
   // Khởi tạo QR Code Scanner
   const html5QrCode = new Html5Qrcode("qr-reader");
   
   // Lưu trạng thái quét mã QR
   let isScanning = false;
   
   // Lắng nghe sự kiện nhấn nút để mở/dừng camera quét mã QR
   document.getElementById("start-qr-scanner").addEventListener("click", () => {
       // Kiểm tra trạng thái quét
       if (!isScanning) {
           // Bắt đầu quét mã QR từ camera
           html5QrCode.start(
               { facingMode: "environment" },  // Sử dụng camera sau
               { fps: 10, qrbox: 250 },        // Cấu hình FPS và kích thước vùng quét
               (decodedText, decodedResult) => {
                   // Xử lý mã QR đã quét
                   console.log(decodedText);  // In ra mã QR quét được
                   window.location.href = decodedText;  // Ví dụ: chuyển hướng đến URL quét được
               },
               (errorMessage) => {
                   // Xử lý lỗi khi quét không thành công
                   console.error(errorMessage);
               }
           ).then(() => {
               // Đánh dấu đã bắt đầu quét
               isScanning = true;
           }).catch((err) => {
               console.error("Không thể mở camera: ", err);
           });
       } else {
           // Nếu đang quét, dừng quét
           html5QrCode.stop().then(() => {
               console.log("Đã dừng quét QR");
               // Đánh dấu đã dừng quét
               isScanning = false;
           }).catch((err) => {
               console.error("Không thể dừng quét: ", err);
           });
       }
   });
   
   
   </script>
</html>




   