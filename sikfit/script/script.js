function twoDigits(d) {
    if(0 <= d && d < 10) return "0" + d.toString();
    if(-10 < d && d < 0) return "-0" + (-1*d).toString();
    return d.toString();
}

Date.prototype.toApiDate = function() {
    return this.getUTCFullYear() + "" + twoDigits(1 + this.getUTCMonth()) + "" + twoDigits(this.getUTCDate()); //+ " " + twoDigits(this.getUTCHours()) + ":" + twoDigits(this.getUTCMinutes()) + ":" + twoDigits(this.getUTCSeconds());
};

Date.prototype.toTrDate = function() {
	var aylar = {"01": "Ocak", "02": "Şubat", "03": "Mart", "04": "Nisan", "05":"Mayıs", "06":"Haziran", "07":"Temmuz", "08":"Ağustos", "09":"Eylül", "10":"Ekim", "11":"Kasım", "12":"Aralık"};
    return twoDigits(this.getUTCDate()) + " " + aylar[twoDigits(1 + this.getUTCMonth())]; //+ " " + twoDigits(this.getUTCHours()) + ":" + twoDigits(this.getUTCMinutes()) + ":" + twoDigits(this.getUTCSeconds());
};