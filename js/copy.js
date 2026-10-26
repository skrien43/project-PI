function copyOTP() {
    const code = document.querySelector('.code-box').textContent;
    const textToCopy = "OTP SekDos Anda: " + code;
    navigator.clipboard.writeText(textToCopy).then(() => {
        alert("Kode OTP berhasil disalin!");
    });
}
