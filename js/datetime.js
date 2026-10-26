function runTime() {
    const now = new Date();
    const day = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
    const mo = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    const daynow = day[now.getDay()];
    const date = now.getDate();
    const monow = mo[now.getMonth()];
    const year = now.getFullYear();
    
    let hour = now.getHours().toString().padStart(2, "0");
    let min = now.getMinutes().toString().padStart(2, "0");
    let sec = now.getSeconds().toString().padStart(2, "0");
    
    const dateTime = `${daynow}, ${date} ${monow} ${year} | ${hour}:${min}:${sec}`;
    document.getElementById("time").innerHTML = dateTime;
} 
setInterval(runTime, 1000);
runTime();
