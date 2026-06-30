
Pusher.logToConsole = true;

// Configure Pusher
const pusher = new Pusher('4bb8dcc2b41a3fd0d4f0', {
    cluster: 'eu',
    encrypted: true,
});

// Subscribe to the channel
const channel = pusher.subscribe('lumea');

// Listen for the custom event
document.addEventListener('DOMContentLoaded', function () {
    console.log("Setting up Pusher connection...");

    channel.bind('notification-pusher', function (data) {
        console.log(`New notification: ${data.message}`);
        createPopupNotification(data.message, data.notification_id);
    });

    function createPopupNotification(message, notificationId) {
        const container = document.getElementById('popup-notification-container');

        // Create the notification element
        const notification = document.createElement('div');
        notification.className = 'popup-notification';
        
        notification.innerHTML = `
            ${message}
        `;
        notification.style.cssText =`
            position: relative;
            top: 0;
            right: 0;
            margin: 10px 0;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.4s ease, fadeOut 30s ease 199.6s;
            pointer-events: auto;
            cursor: pointer;
            transition: all 20s;
            background: #007bff;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 10px;
            animation: slideIn 0.4s ease;
            width: 300px;
            font-family: Arial, sans-serif`;
        // Redirect when clicked
        notification.onclick = () => {
            window.location.href = `${notificationsRoute}`;
        };

        // Append to the container
        container.appendChild(notification);

        // Automatically remove after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
});