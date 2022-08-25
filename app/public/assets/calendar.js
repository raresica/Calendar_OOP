var date = new Date();
var curYear = date.getFullYear();
var curMonth = date.getMonth();
var curDay = date.getDate();
console.log(curYear + " " + curMonth + " " + curDay);
var todayY = curYear;
var todayM = curMonth;
const open = document.getElementById('open');
const modal_container = document.getElementById('modal_container');
const close = document.getElementById('close');

var mm = ['JAN ', 'FEB ', 'MAR ', 'APR ', 'MAY ', 'JUN ', 'JUL ', 'AUG ', 'SEPT ', 'OCT ', 'NOV ', 'DEC '];

window.onload = function () {
    console.log(date + "," + curYear + "," + curMonth + "," + curDay);

    var m = document.getElementById("months");
    var y = document.getElementById("years");
    y.innerHTML = " " + curYear;
    m.innerHTML = mm[curMonth];

    showDate(curYear, curMonth);
    var prevMonth = document.getElementById("prev");
    var nextMonth = document.getElementById("next");

    prevMonth.addEventListener("click", prevShow);
    nextMonth.addEventListener("click", nextShow);

    function prevShow() {
        var div = document.getElementById("calendar-content");
        while (div.hasChildNodes()) {
            div.removeChild(div.lastChild);
        }

        if (curMonth === 0) {
            let tempY = Number(y.innerHTML);
            y.innerHTML = tempY - 1;
            m.innerHTML = 'DEC ';
            showDate(curYear - 1, 11);
        } else {
            m.innerHTML = mm[curMonth - 1];
            showDate(curYear, curMonth - 1);
        }
    }

    function nextShow() {
        var div = document.getElementById("calendar-content");
        while (div.hasChildNodes()) {
            div.removeChild(div.lastChild);
        }
        if (curMonth === 11) {
            let tempY = Number(y.innerHTML);
            y.innerHTML = tempY + 1;
            m.innerHTML = 'JAN ';
            showDate(curYear + 1, 0);
        } else {
            m.innerHTML = mm[curMonth + 1];
            showDate(curYear, curMonth + 1);

        }
    }

}

var showDate = function (year, month) {
    curYear = year;
    curMonth = month;
    console.log(curYear + " " + curMonth);

    var firstDayOfMonth = new Date(curYear, curMonth, 1);
    var lastDayOfMonth = new Date(curYear, curMonth + 1, 0);
    var blank = firstDayOfMonth.getDay();

    var i = 0;
    var div = document.getElementById("calendar-content");
    var week = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    var d = new Date().getDate();

    if (month === 0 || month === 2 || month === 4 || month === 6 || month === 7 || month === 9 || month === 11) {
        i = 31;
    } else if (month === 3 || month === 5 || month === 8 || month === 10) {
        i = 30;
    } else if ((year % 400 === 0) || (year % 100 !== 0 && year % 4 === 0)) {
        i = 29;
    } else {
        i = 28;
    }

    for (let j = 0; j < 7; j++) {
        var node = document.createElement("DIV");
        var textnode = document.createTextNode(week[j]);
        node.appendChild(textnode);
        node.className = "weekdays";
        div.appendChild(node);
    }

    for (let j = 0; j < blank; j++) {
        var node = document.createElement("DIV");
        var textnode = document.createTextNode('');
        node.appendChild(textnode);
        node.className = "weekdays";
        div.appendChild(node);
    }

    for (let j = 1; j <= i; j++) {
        var node = document.createElement("DIV");
        var textnode = document.createTextNode(j);
        node.appendChild(textnode);
        node.className = "weekdays";
        div.appendChild(node);

        if (j === d && curMonth == todayM && curYear == todayY) {
            document.getElementsByClassName("weekdays")[j + 6 + blank].style.color = "yellow";
        }
    }
    var currentMonth = curMonth + 1;
    document.querySelectorAll('.weekdays').forEach(day => {
        day.addEventListener('click', () => {
            const currentMonthAsStr = currentMonth < 10 ? ("0"+currentMonth) : currentMonth;
            const currentDayAsStr = day.innerHTML < 10 ? ("0"+String(day.innerHTML)) : day.innerHTML;
            window.location.search = 'date=' + curYear + "-" + currentMonthAsStr + "-" + String(currentDayAsStr);
        })
    })

    close.addEventListener('click', () => {
        modal_container.classList.remove('show');
        window.location.search = '';

    })
    if (window.location.search != '') {
        modal_container.classList.add('show');

        const urlParams = new URLSearchParams(window.location.search);
        const date = urlParams.get('date');
        const curMonth = date.split("-")[1];
        const day = date.split("-")[2]
        document.getElementById('modalTitle').innerHTML = "Date: " + day + " " + mm[curMonth - 1] + " " + curYear;
        document.getElementById('selected_date').setAttribute("value", date);


    }

}