function savePendingMessage(message) {
    const pendingMessages = JSON.parse(localStorage.getItem('pendingMessages')) || [];
    pendingMessages.push(message);
    localStorage.setItem('pendingMessages', JSON.stringify(pendingMessages));
    
}



const message = {
    from_id: 1,
    to_channel_id: 123,
    body: "Nội dung gửi",
    attachment: null,
    create_at: new Date().toISOString(),
    tempID: "temporary-id",
}

savePendingMessage(message);


// Gửi tin nhắn khi server hoạt động lại
async function resendPendingMessages() {
    const pendingMessages = JSON.parse(localStorage.getItem('pendingMessages')) || [];
    for (const message of pendingMessages) {
        let attempts = 0;
        const maxAttempts = 3;
        
        while (attempts < maxAttempts) {
            try {
                const response = await axios.post('/sendMessage', message);
                const remainingMessages = pendingMessages.filter(msg => msg.tempID !== message.tempID);
                localStorage.setItem('pendingMessages', JSON.stringify(remainingMessages));
                console.log("Tin nhắn đã được gửi lại thành công");
                break; // Tin nhắn đã gửi thành công, thoát vòng lặp
            } catch (error) {
                attempts++;
                console.error(`Lỗi khi gửi lại tin nhắn, thử lại lần ${attempts}:`, error);
                if (attempts === maxAttempts) {
                    console.error("Không thể gửi tin nhắn sau 3 lần thử.");
                    break; // Nếu đã thử tối đa lần mà không thành công thì bỏ qua tin nhắn này
                }
            }
        }
    }
}


// Lấy to_channel_id từ URL
const urlParts = window.location.pathname.split('/');
const to_channel_id = urlParts[urlParts.length - 1];

// Gán to_channel_id vào một input ẩn trong form
document.getElementById('message-form').insertAdjacentHTML(
    'beforeend',
    `<input type="hidden" name="channel_id" value="${to_channel_id}">`
);

// function resendPendingMessage() {
//     const pendingMessages = JSON.parse(localStorage.getItem('pendingMessages')) || [];
//     pendingMessages.forEach(message =>{
//         sendMessageToServer(message);
//     });

//     localStorage.removeItem('pendingMessages');
// }

