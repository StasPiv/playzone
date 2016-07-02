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

function getBlitzTimeObject(timeMs) {
    var ms = timeMs;
    var minutes = parseInt(ms / 60000);
    ms -= minutes * 60000;
    var seconds = parseInt(ms / 1000);
    ms -= seconds * 1000;
    var deciSeconds = ms / 100;

    return {
        minutes: minutes < 10 ? "0" + minutes : minutes,
        seconds: seconds < 10 ? "0" + seconds : seconds,
        deciSeconds: deciSeconds,
        ms: timeMs
    };
}

var highlightLastMove = function (scope, element, lastMove, game) {
    window.setTimeout(
        function () {
            var highlightClass = scope.boardConfig ? scope.boardConfig.highlightClass : 'highlight1-32417';

            if (element.game) {
                game = element.game;
            }

            $(element).find('[class*="square"]').removeClass(highlightClass);
            var history = game.history({verbose: true});

            !lastMove && (lastMove = history[history.length - 1]);

            $(element).find('.square-' + lastMove.from).addClass(highlightClass);
            $(element).find('.square-' + lastMove.to).addClass(highlightClass);
        },
        0
    );
};