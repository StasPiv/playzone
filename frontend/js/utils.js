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