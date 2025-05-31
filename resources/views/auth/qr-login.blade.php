<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>QR đăng nhập</title>
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
    
    <div style="height: 100%">

        <!-- Hiển thị mã QR -->
        <div id="qr-code">
            <div class="qr-container">
                <h2 class="text-xl font-bold">Đăng nhập bằng QR Code <i class="fas fa-qrcode"></i></h2>
                {!! QrCode::size(200)->generate($qrCodeUrl) !!}

                <div style="margin-top: 15px">
                    <a href="{{ route('chatify') }}" class="btn btn-success">Quay về</a>
                </div>
            </div>
        </div>
        
    </div>
    
</body>


<script>
    // Function to fetch new QR Code
    function fetchQRCode() {
        fetch("{{ route('qr.show') }}")
            .then(response => response.text())
            .then(data => {
                // Cập nhật mã QR sau mỗi lần nhận dữ liệu từ server
                document.getElementById('qr-code').innerHTML = data;  // Thay thế nội dung của div chứa mã QR
            })
            .catch(error => console.error('Error fetching QR Code:', error));
    }

    // Ban đầu gọi hàm để lấy mã QR đầu tiên
    fetchQRCode();

    // Cập nhật mã QR mỗi 5 giây
    setInterval(fetchQRCode, 5000); // 5000ms = 5 giây
</script>

</html>






