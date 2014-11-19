
// Redirect to HTTPS if not localhost
if (window.location.protocol != "https:" && window.location.hostname !== '0.0.0.0') {
    window.location.href = "https:" + window.location.href.substring(window.location.protocol.length);
}
