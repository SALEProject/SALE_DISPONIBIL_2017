var Dictionary = function() {
	this.storage = [];
	this.keys = [];
	this.idx = 0;
};

if (!Array.prototype.indexOf)
{
  Array.prototype.indexOf = function(elt /*, from*/)
  {
    var len = this.length >>> 0;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
      ? Math.ceil(from)
      : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++)
    {
      if (from in this &&
        this[from] === elt)
        return from;
    }
    return -1;
  };
}

Dictionary.prototype.setData = function(data) {
	this.storage = [];
	this.keys = [];
	this.idx = 0;
	for(var i=0;i<data.length;i++) {
		try {
			this.storage.push(data[i]);
			this.keys.push(data[i].key);
			this.idx = this.keys.length;
		}
		catch(err) {
			console.log('dictionary error: ' + err.message);
			delete(this.storage[this.idx]);
			delete(this.keys[this.idx]);
			this.idx--;
		}
	}
};
Dictionary.prototype.getValue = function(key) {
	var idx = this.keys.indexOf(key);
	return idx > -1 ? this.storage[idx]['value'] : key;
};
