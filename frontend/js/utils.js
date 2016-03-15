/**
 * Created by stas on 29.01.16.
 */
Array.prototype.searchById = function (idForSearch) {
    return $.grep(
        this,
        function (item, index) {
            return item.id === idForSearch;
        }
    )[0];
};

Array.prototype.pullById = function (idForSearch) {
    this.splice( this.indexOf(this.searchById(idForSearch)), 1);
};

var pad = function (num, size) {
    var s = "000000000" + num;
    return s.substr(s.length-size);
};

function formatTime(timeMs, dateFilter) {
    var date = new Date(1970, 0, 1);
    date.setMilliseconds(timeMs);

    switch (true) {
        case timeMs >= 1000 * 60 * 60 - 1:
            return {
                days: parseInt(timeMs / (24 * 60 * 60 * 1000)),
                time: dateFilter(date, 'HH:mm:ss'),
                ms: timeMs
            };
        default:
            var seconds = parseInt(timeMs / 1000);
            return {
                time: dateFilter(date, 'mm:ss'),
                ms: timeMs,
                beforeComaMs: seconds,
                afterComaMs: (timeMs / 100) - (10 * seconds)
            };
    }
}