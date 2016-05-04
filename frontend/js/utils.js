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

function getBlitzTimeObject(timeMs, dateFilter) {
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

var insufficient_material_white = function (fen) {
    // "k7/8/n7/8/8/8/8/7K b - - 0 1"
    /**
     * small letters are black
     * big letters are white
     */
    var lastPieces = fen.split(" ")[0].match(/[RNBQP]/g);

    if (!lastPieces) {
        return true;
    }

    if (lastPieces.length > 1) {
        return false;
    }

    var lastPiece = lastPieces[0];

    return !!(lastPiece === "N" || lastPiece === "B");
};

var insufficient_material_black = function (fen) {
    // "k7/8/n7/8/8/8/8/7K b - - 0 1"
    /**
     * small letters are black
     * big letters are white
     */
    var lastPieces = fen.split(" ")[0].match(/[rnbqp]/g);

    if (!lastPieces) {
        return true;
    }

    if (lastPieces.length > 1) {
        return false;
    }

    var lastPiece = lastPieces[0];

    return !!(lastPiece === "n" || lastPiece === "b");
};