function shareLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
            const { latitude, longitude } = position.coords;
            const locationLink = `https://maps.google.com/?q=${latitude},${longitude}`;

            // Create a clickable link in HTML format
            const clickableLink = `<a href="${locationLink}" target="_blank">${locationLink}</a>`;

            // Append the clickable link to the textarea
            const messageTextarea = document.getElementById('message');
            messageTextarea.value = messageTextarea.value.trim() + `\n${clickableLink}`;
        });
    } else {
        console.error('Geolocation is not supported by this browser.');
    }
}
